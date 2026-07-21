<?php
declare(strict_types=1);

$site = dirname(__DIR__) . '/site';
$template = file_get_contents($site . '/templates_person_create.php') ?: '';
$style = file_get_contents($site . '/style.css') ?: '';
$errors = [];

foreach ([
    '<legend>Rolle</legend>',
    'radio-group-compact',
    'name="person_type" value="patient"',
    'name="person_type" value="staff"',
] as $marker) {
    if (!str_contains($template, $marker)) {
        $errors[] = "Missing create template marker: {$marker}";
    }
}

foreach ([
    '.radio-group-compact',
    'justify-items: start',
    'width: 1rem',
    'min-height: 0',
    'input[type="radio"]:focus',
    'outline: none',
    'input[type="radio"]:focus-visible',
] as $marker) {
    if (!str_contains($style, $marker)) {
        $errors[] = "Missing compact radio style marker: {$marker}";
    }
}

if (str_contains($template, 'Personentyp')) {
    $errors[] = 'Old legend "Personentyp" still present on create page.';
}

if ($errors !== []) {
    fwrite(STDERR, "Person create role UI check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Person create role UI check: OK\n";
