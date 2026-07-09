<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/src/bootstrap.php';
use AbsenceApp\Database;
if ($argc !== 3) { fwrite(STDERR, "Usage: php sql/create_patient.php <patient-id> <password>
"); exit(1); }
$db = Database::getConnection();
$stmt = $db->prepare('INSERT INTO patients (id, password_hash, first_login) VALUES (:id, :hash, 1)');
try { $stmt->execute(['id' => $argv[1], 'hash' => password_hash($argv[2], PASSWORD_DEFAULT)]); echo "Patient created: {$argv[1]}
"; }
catch (PDOException $e) { fwrite(STDERR, "Could not create patient. Does the ID already exist?
"); exit(1); }
