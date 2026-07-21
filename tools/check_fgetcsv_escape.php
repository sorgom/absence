<?php
declare(strict_types=1);

$seed = file(dirname(__DIR__) . '/tools/seed_test_data.php') ?: [];
$errors = [];
$found = false;

foreach ($seed as $line) {
    if (!str_contains($line, 'fgetcsv(')) {
        continue;
    }

    $found = true;
    $argumentCount = substr_count($line, ',') + 1;

    if ($argumentCount < 5) {
        $errors[] = 'fgetcsv call must pass length, separator, enclosure and escape explicitly: ' . trim($line);
    }
}

if (!$found) {
    $errors[] = 'No fgetcsv call found in seed_test_data.php.';
}

if ($errors !== []) {
    fwrite(STDERR, "fgetcsv escape check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "fgetcsv escape check: OK\n";
