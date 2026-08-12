<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/site/bootstrap.php';

use AbsenceApp\Database;

$db = Database::getConnection();
$db->beginTransaction();

try {
    $reasonName = 'Cleanup-Testgrund';
    $password = 'cleanup';

    $persons = [
        'cleanup_done_expired' => 'abgeschlossen, Rückkehr älter als N Stunden: wird gelöscht',
        'cleanup_done_fresh' => 'abgeschlossen, Rückkehr jünger als N Stunden: bleibt',
        'cleanup_active_old' => 'aktiv, Aufbruch älter als N Stunden: bleibt',
    ];

    $personStmt = $db->prepare(
        'INSERT INTO persons (id, password_hash, is_staff, first_login)
         VALUES (:id, :password_hash, 0, 0)
         ON CONFLICT(id) DO UPDATE SET
             password_hash = excluded.password_hash,
             is_staff = 0,
             first_login = 0'
    );

    foreach ($persons as $personId => $description) {
        $personStmt->execute([
            'id' => $personId,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);
    }

    $db->prepare(
        "DELETE FROM absences
         WHERE person_id IN ('cleanup_done_expired', 'cleanup_done_fresh', 'cleanup_active_old')"
    )->execute();

    $insert = $db->prepare(
        'INSERT INTO absences (person_id, reason, departure_time, return_time)
         VALUES (:person_id, :reason, :departure_time, :return_time)'
    );

    // Completed and expired: must be deleted by cleanup.
    $insert->execute([
        'person_id' => 'cleanup_done_expired',
        'reason' => $reasonName,
        'departure_time' => gmdate('Y-m-d H:i:s', time() - 74 * 3600),
        'return_time' => gmdate('Y-m-d H:i:s', time() - 72 * 3600),
    ]);

    // Completed but still fresh: must stay.
    $insert->execute([
        'person_id' => 'cleanup_done_fresh',
        'reason' => $reasonName,
        'departure_time' => gmdate('Y-m-d H:i:s', time() - 26 * 3600),
        'return_time' => gmdate('Y-m-d H:i:s', time() - 24 * 3600),
    ]);

    // Active and old: must stay because return_time is NULL, even though departure_time is older than N hours.
    $insert->execute([
        'person_id' => 'cleanup_active_old',
        'reason' => $reasonName,
        'departure_time' => gmdate('Y-m-d H:i:s', time() - 96 * 3600),
        'return_time' => null,
    ]);

    $db->commit();

    echo "Seeded cleanup test data.\n";
    echo "Password for all cleanup test persons: cleanup\n";
    echo "Cases:\n";
    foreach ($persons as $personId => $description) {
        echo " - {$personId}: {$description}\n";
    }
} catch (Throwable $exception) {
    $db->rollBack();
    throw $exception;
}
