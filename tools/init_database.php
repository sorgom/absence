<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/site/bootstrap.php';

use AbsenceApp\Config;
use AbsenceApp\Database;

function tableExists(PDO $pdo, string $table): bool
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM sqlite_master WHERE type = 'table' AND name = :name");
    $stmt->execute(['name' => $table]);

    return (int) $stmt->fetchColumn() > 0;
}

function columnExists(PDO $pdo, string $table, string $column): bool
{
    if (!tableExists($pdo, $table)) {
        return false;
    }

    $stmt = $pdo->query('PRAGMA table_info(' . $table . ')');

    foreach ($stmt->fetchAll() as $row) {
        if (($row['name'] ?? '') === $column) {
            return true;
        }
    }

    return false;
}

$databasePath = Config::get('database_path');
$databaseDirectory = dirname((string) $databasePath);

if (!is_dir($databaseDirectory)) {
    mkdir($databaseDirectory, 0775, true);
}

$pdo = Database::getConnection();
$sqlPath = dirname(__DIR__) . '/site/create_database.sql';

if (!is_file($sqlPath)) {
    fwrite(STDERR, "SQL file not found: {$sqlPath}\n");
    exit(1);
}

$sql = file_get_contents($sqlPath);

if ($sql === false) {
    fwrite(STDERR, "Could not read SQL file: {$sqlPath}\n");
    exit(1);
}

/*
 * Migrate v0.8.x databases:
 *   patients + staff -> persons
 *   absences.patient_id -> absences.person_id
 */
$hasOldPatients = tableExists($pdo, 'patients');
$hasOldStaff = tableExists($pdo, 'staff');
$hasOldAbsences = tableExists($pdo, 'absences') && columnExists($pdo, 'absences', 'patient_id');
$hasPersons = tableExists($pdo, 'persons');

if (!$hasPersons && ($hasOldPatients || $hasOldStaff || $hasOldAbsences)) {
    $pdo->beginTransaction();

    try {
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS persons (
                id TEXT PRIMARY KEY,
                password_hash TEXT NOT NULL,
                is_staff INTEGER NOT NULL DEFAULT 0,
                first_login INTEGER NOT NULL DEFAULT 1,
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )'
        );

        if ($hasOldPatients) {
            $pdo->exec(
                'INSERT OR IGNORE INTO persons (id, password_hash, is_staff, first_login, created_at)
                 SELECT id, password_hash, 0, first_login, created_at FROM patients'
            );
        }

        if ($hasOldStaff) {
            $pdo->exec(
                'INSERT OR IGNORE INTO persons (id, password_hash, is_staff, first_login, created_at)
                 SELECT id, password_hash, 1, first_login, created_at FROM staff'
            );
        }

        if ($hasOldAbsences) {
            $pdo->exec(
                'CREATE TABLE absences_v090 (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    person_id TEXT NOT NULL,
                    reason_id INTEGER NOT NULL,
                    departure_time TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    return_time TEXT,
                    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (person_id) REFERENCES persons(id) ON DELETE CASCADE,
                    FOREIGN KEY (reason_id) REFERENCES reasons(id) ON DELETE RESTRICT
                )'
            );

            $pdo->exec(
                'INSERT INTO absences_v090 (id, person_id, reason_id, departure_time, return_time, created_at)
                 SELECT id, patient_id, reason_id, departure_time, return_time, created_at FROM absences'
            );

            $pdo->exec('DROP TABLE absences');
            $pdo->exec('ALTER TABLE absences_v090 RENAME TO absences');
        }

        if ($hasOldPatients) {
            $pdo->exec('DROP TABLE patients');
        }

        if ($hasOldStaff) {
            $pdo->exec('DROP TABLE staff');
        }

        $pdo->commit();
        echo "Migrated old patients/staff schema to persons.\n";
    } catch (Throwable $exception) {
        $pdo->rollBack();
        throw $exception;
    }
}

$pdo->exec($sql);

if (!columnExists($pdo, 'reasons', 'deleted')) {
    $pdo->exec('ALTER TABLE reasons ADD COLUMN deleted INTEGER NOT NULL DEFAULT 0');
}

$staffStatement = $pdo->prepare(
    'INSERT OR IGNORE INTO persons (id, password_hash, is_staff, first_login)
     VALUES (:id, :password_hash, 1, 1)'
);
$staffStatement->execute([
    'id' => 'anfang',
    'password_hash' => password_hash('anfang', PASSWORD_DEFAULT),
]);

$defaultReasons = [
    'Arzt',
    'Einkaufen',
    'Lidl',
    'Edeka',
    'Ortserkundung',
];

$reasonStatement = $pdo->prepare('INSERT OR IGNORE INTO reasons (name, deleted) VALUES (:name, 0)');

foreach ($defaultReasons as $reason) {
    $reasonStatement->execute(['name' => $reason]);
}

echo "Database initialized: {$databasePath}\n";
echo "Initial staff member ensured: anfang / anfang\n";
echo "Default reasons ensured: " . implode(', ', $defaultReasons) . "\n";
