<?php
declare(strict_types=1);

if (!extension_loaded('pdo_sqlite')) {
    echo "Initial seed check: SKIPPED (pdo_sqlite extension not loaded in this PHP runtime)\n";
    exit(0);
}

$root = dirname(__DIR__);
$site = $root . '/site';
$db = $site . '/database.sqlite';

@unlink($db);
@unlink($db . '-shm');
@unlink($db . '-wal');

$command = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($root . '/tools/init_database.php');
$output = [];
$returnCode = 0;
exec($command . ' 2>&1', $output, $returnCode);

if ($returnCode !== 0) {
    fwrite(STDERR, "tools/init_database.php failed:\n" . implode("\n", $output) . "\n");
    exit(1);
}

$pdo = new PDO('sqlite:' . $db);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$staff = $pdo->query("SELECT id, password_hash, is_staff, first_login FROM persons WHERE id = 'anfang'")->fetch();

if (!$staff) {
    fwrite(STDERR, "Initial person/staff member 'anfang' is missing.\n");
    exit(1);
}

if (!password_verify('anfang', (string) $staff['password_hash'])) {
    fwrite(STDERR, "Initial staff password for 'anfang' is not 'anfang'.\n");
    exit(1);
}

if ((int) $staff['is_staff'] !== 1) {
    fwrite(STDERR, "Initial staff member must have is_staff = 1.\n");
    exit(1);
}

if ((int) $staff['first_login'] !== 1) {
    fwrite(STDERR, "Initial staff member must have first_login = 1.\n");
    exit(1);
}

$requiredReasons = ['Arzt', 'Einkaufen', 'Lidl', 'Edeka', 'Ortserkundung'];
$stmt = $pdo->prepare('SELECT COUNT(*) FROM reasons WHERE name = :name');

foreach ($requiredReasons as $reason) {
    $stmt->execute(['name' => $reason]);

    if ((int) $stmt->fetchColumn() !== 1) {
        fwrite(STDERR, "Missing default reason: {$reason}\n");
        exit(1);
    }
}

@unlink($db);
@unlink($db . '-shm');
@unlink($db . '-wal');

echo "Initial seed check: OK\n";
