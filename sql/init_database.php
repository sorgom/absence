<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/src/bootstrap.php';
use AbsenceApp\Config; use AbsenceApp\Database;
$db = Database::getConnection();
$sql = file_get_contents(__DIR__ . '/create_database.sql');
if ($sql === false) { throw new RuntimeException('Could not read create_database.sql'); }
$db->exec($sql);
$stmt = $db->prepare('INSERT OR IGNORE INTO staff (id, password_hash, first_login) VALUES (:id, :password_hash, 1)');
$stmt->execute(['id' => 'anfang', 'password_hash' => password_hash('anfang', PASSWORD_DEFAULT)]);
foreach (['Arzt', 'Einkaufen', 'Lidl', 'Edeka', 'Ortserkundung'] as $reason) {
    $stmt = $db->prepare('INSERT OR IGNORE INTO reasons (name) VALUES (:name)'); $stmt->execute(['name' => $reason]);
}
$stmt = $db->prepare('INSERT OR IGNORE INTO settings (name, value) VALUES (:name, :value)');
$stmt->execute(['name' => 'delete_after_hours', 'value' => (string)Config::get('default_absence_retention_hours')]);
echo 'Database initialized: ' . Config::get('database_path') . PHP_EOL;
echo 'Initial staff login: anfang / anfang' . PHP_EOL;
