<?php
declare(strict_types=1);

if (!extension_loaded('pdo_sqlite')) {
    echo "Init database check: SKIPPED (pdo_sqlite extension not loaded in this PHP runtime)\n";
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

if (!is_file($db)) {
    fwrite(STDERR, "Database file was not created: {$db}\n");
    exit(1);
}

@unlink($db);
@unlink($db . '-shm');
@unlink($db . '-wal');

echo "Init database check: OK\n";
