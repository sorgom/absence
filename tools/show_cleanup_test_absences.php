<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/site/bootstrap.php';

use AbsenceApp\Database;
use AbsenceApp\Utils;

$db = Database::getConnection();

$stmt = $db->query(
    "SELECT person_id, departure_time, return_time
     FROM absences
     WHERE person_id IN ('cleanup_done_expired', 'cleanup_done_fresh', 'cleanup_active_old')
     ORDER BY person_id"
);

$rows = $stmt->fetchAll();

if ($rows === []) {
    echo "No cleanup test absences found.\n";
    exit;
}

echo "Cleanup test absences:\n";
foreach ($rows as $row) {
    $returnTime = $row['return_time'] === null ? 'ACTIVE / no return_time' : (string) $row['return_time'];
    echo "- {$row['person_id']}: departure={$row['departure_time']}; return={$returnTime}\n";
}
