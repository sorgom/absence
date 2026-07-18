<?php
declare(strict_types=1);

$bootstrap = file_get_contents(dirname(__DIR__) . '/site/bootstrap.php') ?: '';
$legacyNames = [
    'create' . '_patient.php',
    'patient' . '_create.php',
    'patient' . '_delete.php',
    'patient' . '_repository.php',
    'staff' . '_create.php',
    'staff' . '_delete.php',
    'staff' . '_repository.php',
];
$errors = [];
foreach ($legacyNames as $legacyName) {
    if (str_contains($bootstrap, $legacyName)) {
        $errors[] = "bootstrap.php still references {$legacyName}";
    }
}
if ($errors !== []) {
    fwrite(STDERR, "Bootstrap legacy check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}
echo "Bootstrap legacy check: OK\n";
