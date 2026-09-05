<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/site/bootstrap.php';

use AbsenceApp\Config;
use AbsenceApp\Database;

$databasePath = (string) Config::get('database_path');
$csvPath = $argv[1] ?? dirname(__DIR__) . '/personen.csv';

if (!is_file($databasePath)) {
    fwrite(STDERR, "Database not found: {$databasePath}\n");
    fwrite(STDERR, "Run first: php tools/init_database.php\n");
    exit(1);
}

if (!is_file($csvPath)) {
    fwrite(STDERR, "CSV file not found: {$csvPath}\n");
    fwrite(STDERR, "Default path: personen.csv in the project root\n");
    exit(1);
}

function csvUtf8(string $value): string
{
    if (function_exists('mb_convert_encoding')) {
        return mb_convert_encoding($value, 'UTF-8', 'UTF-8, Windows-1252, ISO-8859-1');
    }

    $converted = @iconv('Windows-1252', 'UTF-8//IGNORE', $value);

    return $converted === false ? $value : $converted;
}

function csvMarked(?string $value): bool
{
    return strtoupper(trim((string) $value)) === 'X';
}

/**
 * Reads a semicolon separated CSV.
 *
 * Expected columns:
 * UID;is Staff;Passwort;muss PW ändern;Aufbruch;Rückkehr
 *
 * @return array<int,array<string,string>>
 */
function readPersonsCsv(string $csvPath): array
{
    $handle = fopen($csvPath, 'rb');

    if ($handle === false) {
        throw new RuntimeException("Could not open CSV: {$csvPath}");
    }

    $header = fgetcsv($handle, 0, ';', '"', '\\');

    if ($header === false) {
        fclose($handle);
        throw new RuntimeException('CSV is empty.');
    }

    $header = array_map(static fn (string $value): string => trim(csvUtf8($value)), $header);
    $required = ['UID', 'is Staff', 'Passwort', 'muss PW ändern', 'Aufbruch', 'Rückkehr'];

    foreach ($required as $column) {
        if (!in_array($column, $header, true)) {
            fclose($handle);
            throw new RuntimeException("CSV column missing: {$column}");
        }
    }

    $rows = [];

    while (($values = fgetcsv($handle, 0, ';', '"', '\\')) !== false) {
        $values = array_map(static fn (string $value): string => trim(csvUtf8($value)), $values);

        if (count(array_filter($values, static fn (string $value): bool => $value !== '')) === 0) {
            continue;
        }

        $row = array_combine($header, array_pad($values, count($header), ''));

        if ($row === false) {
            fclose($handle);
            throw new RuntimeException('CSV row could not be mapped to header.');
        }

        $rows[] = $row;
    }

    fclose($handle);

    return $rows;
}

$rows = readPersonsCsv($csvPath);

if ($rows === []) {
    fwrite(STDERR, "CSV contains no persons.\n");
    exit(1);
}

$pdo = Database::getConnection();
$pdo->beginTransaction();

try {
    $ids = [];
    $loginLines = [];
    $activeCount = 0;
    $completedCount = 0;

    $reasons = ['Arzt', 'Einkaufen', 'Lidl', 'Edeka', 'Ortserkundung', 'Apotheke', 'Spaziergang', 'Therapie', 'Besuch'];
    $reasonStatement = $pdo->prepare('INSERT OR IGNORE INTO reasons (name, sort_order) VALUES (:name, :sort_order)');

    foreach ($reasons as $index => $reason) {
        $reasonStatement->execute([
            'name' => $reason,
            'sort_order' => $index + 1,
        ]);
    }


    $insertPerson = $pdo->prepare(
        'INSERT INTO persons (id, password_hash, is_staff, first_login)
         VALUES (:id, :password_hash, :is_staff, :first_login)'
    );
    $updatePerson = $pdo->prepare(
        'UPDATE persons
         SET password_hash = :password_hash,
             is_staff = :is_staff,
             first_login = :first_login
         WHERE id = :id'
    );
    $existsPerson = $pdo->prepare('SELECT COUNT(*) FROM persons WHERE id = :id');

    foreach ($rows as $row) {
        $id = trim((string) $row['UID']);
        $password = (string) $row['Passwort'];

        if ($id === '') {
            throw new RuntimeException('CSV contains a row without UID.');
        }

        if ($password === '') {
            throw new RuntimeException("CSV row {$id} has no password.");
        }

        $isStaff = csvMarked($row['is Staff'] ?? '') ? 1 : 0;
        $firstLogin = csvMarked($row['muss PW ändern'] ?? '') ? 1 : 0;

        $params = [
            'id' => $id,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'is_staff' => $isStaff,
            'first_login' => $firstLogin,
        ];

        $existsPerson->execute(['id' => $id]);

        if ((int) $existsPerson->fetchColumn() > 0) {
            $updatePerson->execute($params);
        } else {
            $insertPerson->execute($params);
        }

        $ids[] = $id;
        $loginLines[] = sprintf(
            '  %s / %s%s%s',
            $id,
            $password,
            $isStaff === 1 ? ' [Team]' : ' [Patient/-in]',
            $firstLogin === 1 ? ' [PW ändern]' : ''
        );
    }

    /*
     * Delete previous absences for CSV persons, so repeated runs do not duplicate test data.
     */
    $deleteAbsence = $pdo->prepare('DELETE FROM absences WHERE person_id = :person_id');

    foreach ($ids as $id) {
        $deleteAbsence->execute(['person_id' => $id]);
    }

    /*
     * Aufbruch = X, Rückkehr empty -> active absence
     * Aufbruch = X, Rückkehr = X -> completed absence
     * Aufbruch empty -> no absence
     */
    $insertAbsence = $pdo->prepare(
        'INSERT INTO absences (person_id, reason, departure_time, return_time, created_at)
         VALUES (:person_id, :reason, :departure_time, :return_time, :created_at)'
    );

    $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
    $absenceIndex = 0;

    foreach ($rows as $row) {
        if (!csvMarked($row['Aufbruch'] ?? '')) {
            continue;
        }

        $id = trim((string) $row['UID']);
        $reason = $reasons[$absenceIndex % count($reasons)];

        if (csvMarked($row['Rückkehr'] ?? '')) {
            $departure = $now->modify(sprintf('-%d hours', 2 + $absenceIndex));
            $return = $departure->modify('+45 minutes');
            $completedCount++;
        } else {
            $departure = $now->modify(sprintf('-%d minutes', 15 + ($absenceIndex * 7)));
            $return = null;
            $activeCount++;
        }

        $insertAbsence->execute([
            'person_id' => $id,
            'reason' => $reason,
            'departure_time' => $departure->format('Y-m-d H:i:s'),
            'return_time' => $return?->format('Y-m-d H:i:s'),
            'created_at' => $departure->format('Y-m-d H:i:s'),
        ]);

        $absenceIndex++;
    }


    $pdo->commit();
} catch (Throwable $exception) {
    $pdo->rollBack();
    throw $exception;
}

echo "Test data seeded into: {$databasePath}\n";
echo "CSV used: {$csvPath}\n";
echo "Persons: " . count($rows) . "\n";
echo "Active absences: {$activeCount}\n";
echo "Completed absences: {$completedCount}\n";
echo "Demo logins:\n";
echo implode("\n", $loginLines) . "\n";
echo "Initial staff remains: anfang / anfang\n";
