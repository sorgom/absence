<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/src/Config.php';
require_once dirname(__DIR__) . '/src/Database.php';

use AbsenceApp\Config;
use AbsenceApp\Database;

$databasePath = (string) Config::get('database_path');
$databaseDirectory = dirname($databasePath);
if (!is_dir($databaseDirectory)) {
    mkdir($databaseDirectory, 0775, true);
}
$db = Database::getConnection();
$sql = file_get_contents(__DIR__ . '/create_database.sql');
if ($sql === false) {
    throw new RuntimeException('Could not read create_database.sql');
}
$db->exec($sql);

$statement = $db->prepare('INSERT OR IGNORE INTO staff (id, password_hash, first_login) VALUES (:id, :password_hash, 1)');
$statement->execute(['id' => 'anfang', 'password_hash' => password_hash('anfang', PASSWORD_DEFAULT)]);

foreach (['Arzt', 'Einkaufen', 'Lidl', 'Edeka', 'Ortserkundung'] as $reason) {
    $statement = $db->prepare('INSERT OR IGNORE INTO reasons (name) VALUES (:name)');
    $statement->execute(['name' => $reason]);
}

$statement = $db->prepare('INSERT OR IGNORE INTO settings (name, value) VALUES (:name, :value)');
$statement->execute(['name' => 'delete_after_hours', 'value' => (string) Config::get('default_absence_retention_hours')]);

echo "Database initialized: {$databasePath}\n";
echo "Initial staff login: anfang / anfang\n";
