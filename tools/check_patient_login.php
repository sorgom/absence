<?php
declare(strict_types=1);

require dirname(__DIR__) . '/site/bootstrap.php';

use AbsenceApp\Database;

$db = Database::getConnection();

$count = (int) $db->query('SELECT COUNT(*) FROM patients')->fetchColumn();

echo "Patients in database: {$count}\n";

if ($count === 0) {
    echo "No patients exist yet. Create one in the staff interface first.\n";
    exit(0);
}

$stmt = $db->query('SELECT id, first_login FROM patients ORDER BY id COLLATE NOCASE ASC');
foreach ($stmt->fetchAll() as $row) {
    echo "- {$row['id']} (first_login={$row['first_login']})\n";
}
