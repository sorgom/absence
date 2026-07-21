<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$script = $root . '/tools/seed_test_data.php';
$csv = $root . '/personen.csv';
$errors = [];

if (!is_file($script)) {
    fwrite(STDERR, "Missing tools/seed_test_data.php\n");
    exit(1);
}

if (!is_file($csv)) {
    fwrite(STDERR, "Missing personen.csv\n");
    exit(1);
}

$content = file_get_contents($script) ?: '';

foreach ([
    'fgetcsv($handle, 0, \';\', \'"\', \'\\\\\')',
    'UID',
    'is Staff',
    'Passwort',
    'muss PW ändern',
    'Aufbruch',
    'Rückkehr',
    'INSERT INTO absences',
    'Alter Testgrund',
    'deleted = 1',
    'Run first: php tools/init_database.php',
] as $marker) {
    if (!str_contains($content, $marker)) {
        $errors[] = "Missing seed marker: {$marker}";
    }
}

$csvContent = file_get_contents($csv) ?: '';

foreach (['UID;', '10;X;a;X;;', '49;;b;;X;X'] as $marker) {
    if (!str_contains($csvContent, $marker)) {
        $errors[] = "Missing CSV marker: {$marker}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Seed test data check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Seed test data check: OK\n";
