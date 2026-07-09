<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/site/bootstrap.php';

use AbsenceApp\Config;
use AbsenceApp\Database;

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

$pdo->exec($sql);

/*
 * Idempotent initial data.
 *
 * The initial staff login is required by the specification:
 *   Personal-ID: anfang
 *   Passwort:   anfang
 *
 * first_login remains 1 so the user must change the password after login.
 */
$staffStatement = $pdo->prepare(
    'INSERT OR IGNORE INTO staff (id, password_hash, first_login)
     VALUES (:id, :password_hash, 1)'
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

$reasonStatement = $pdo->prepare('INSERT OR IGNORE INTO reasons (name) VALUES (:name)');

foreach ($defaultReasons as $reason) {
    $reasonStatement->execute(['name' => $reason]);
}

echo "Database initialized: {$databasePath}\n";
echo "Initial staff member ensured: anfang / anfang\n";
echo "Default reasons ensured: " . implode(', ', $defaultReasons) . "\n";
