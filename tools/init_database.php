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

function migrateAbsencesToReasonText(PDO $pdo): void
{
    if (!tableExists($pdo, 'absences') || !columnExists($pdo, 'absences', 'reason_id')) {
        return;
    }

    if (!columnExists($pdo, 'absences', 'reason')) {
        $pdo->exec("ALTER TABLE absences ADD COLUMN reason TEXT NOT NULL DEFAULT ''");
    }

    if (tableExists($pdo, 'reasons')) {
        $pdo->exec(
            "UPDATE absences
             SET reason = COALESCE((SELECT name FROM reasons WHERE reasons.id = absences.reason_id), reason, '')
             WHERE reason = ''"
        );
    }

    $pdo->exec(
        'CREATE TABLE absences_denormalized (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            person_id TEXT NOT NULL,
            reason TEXT NOT NULL,
            departure_time TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            return_time TEXT,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (person_id) REFERENCES persons(id) ON DELETE CASCADE
        )'
    );

    $pdo->exec(
        "INSERT INTO absences_denormalized (id, person_id, reason, departure_time, return_time, created_at)
         SELECT id, person_id, COALESCE(NULLIF(reason, ''), 'Unbekannt'), departure_time, return_time, created_at
         FROM absences"
    );

    $pdo->exec('DROP TABLE absences');
    $pdo->exec('ALTER TABLE absences_denormalized RENAME TO absences');
}

function migrateReasonsList(PDO $pdo): void
{
    if (!tableExists($pdo, 'reasons')) {
        return;
    }

    if (!columnExists($pdo, 'reasons', 'sort_order')) {
        $pdo->exec('ALTER TABLE reasons ADD COLUMN sort_order INTEGER NOT NULL DEFAULT 0');
        $pdo->exec('UPDATE reasons SET sort_order = id WHERE sort_order = 0');
    }

    if (!columnExists($pdo, 'reasons', 'deleted')) {
        return;
    }

    $pdo->exec(
        'CREATE TABLE reasons_new (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE,
            sort_order INTEGER NOT NULL DEFAULT 0,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )'
    );

    $pdo->exec(
        'INSERT OR IGNORE INTO reasons_new (id, name, sort_order, created_at)
         SELECT id, name, sort_order, created_at
         FROM reasons
         WHERE deleted = 0
         ORDER BY sort_order ASC, id ASC'
    );

    $pdo->exec('DROP TABLE reasons');
    $pdo->exec('ALTER TABLE reasons_new RENAME TO reasons');
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
                    FOREIGN KEY (person_id) REFERENCES persons(id) ON DELETE CASCADE
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

$pdo->beginTransaction();
try {
    migrateAbsencesToReasonText($pdo);
    migrateReasonsList($pdo);
    $pdo->commit();
} catch (Throwable $exception) {
    $pdo->rollBack();
    throw $exception;
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

$countReasons = (int) $pdo->query('SELECT COUNT(*) FROM reasons')->fetchColumn();

if ($countReasons === 0) {
    $reasonStatement = $pdo->prepare('INSERT INTO reasons (name, sort_order) VALUES (:name, :sort_order)');

    foreach ($defaultReasons as $index => $reason) {
        $reasonStatement->execute([
            'name' => $reason,
            'sort_order' => $index + 1,
        ]);
    }
}

$pdo->exec('CREATE INDEX IF NOT EXISTS idx_reasons_sort_order ON reasons(sort_order)');
$pdo->exec('CREATE INDEX IF NOT EXISTS idx_absences_person_id ON absences(person_id)');
$pdo->exec('CREATE INDEX IF NOT EXISTS idx_absences_departure_time ON absences(departure_time)');
$pdo->exec('CREATE INDEX IF NOT EXISTS idx_absences_return_time ON absences(return_time)');
$pdo->exec('CREATE INDEX IF NOT EXISTS idx_audit_logs_created_at ON audit_logs(created_at)');
$pdo->exec('CREATE INDEX IF NOT EXISTS idx_audit_logs_staff_user_id ON audit_logs(staff_user_id)');

echo "Database initialized: {$databasePath}\n";
echo "Initial staff member ensured: anfang / anfang\n";
echo "Default reasons ensured when list was empty: " . implode(', ', $defaultReasons) . "\n";
