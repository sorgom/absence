<?php
declare(strict_types=1);

$site = dirname(__DIR__) . '/site';
$errors = [];

$required = [
    'person_create.php',
    'templates_person_create.php',
    'person_delete.php',
    'templates_person_delete.php',
    'person_repository.php',
];

foreach ($required as $file) {
    if (!is_file($site . '/' . $file)) {
        $errors[] = "Missing site/{$file}";
    }
}

$create = file_get_contents($site . '/templates_person_create.php') ?: '';
$delete = file_get_contents($site . '/templates_person_delete.php') ?: '';
$menu = file_get_contents($site . '/menu.php') ?: '';

foreach (['value="patient"', 'value="staff"', 'Person anlegen'] as $marker) {
    if (!str_contains($create, $marker)) {
        $errors[] = "Missing create marker: {$marker}";
    }
}

foreach (['value="patient"', 'value="staff"', 'Person löschen'] as $marker) {
    if (!str_contains($delete, $marker)) {
        $errors[] = "Missing delete marker: {$marker}";
    }
}

foreach (['/person_create.php', '/person_delete.php'] as $marker) {
    if (!str_contains($menu, $marker)) {
        $errors[] = "Missing menu marker: {$marker}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Person management check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Person management check: OK\n";
