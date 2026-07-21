<?php
declare(strict_types=1);

$site = dirname(__DIR__) . '/site';
$repository = file_get_contents($site . '/person_repository.php') ?: '';
$page = file_get_contents($site . '/person_delete.php') ?: '';
$template = file_get_contents($site . '/templates_person_delete.php') ?: '';
$style = file_get_contents($site . '/style.css') ?: '';
$errors = [];

foreach ([
    'listIdsExcept',
    '$repository->listIdsExcept($isStaffSelection, (string) $auth->currentUserId())',
] as $marker) {
    $haystack = str_contains($marker, '$repository') ? $page : $repository;
    if (!str_contains($haystack, $marker)) {
        $errors[] = "Missing own-user exclusion marker: {$marker}";
    }
}

foreach ([
    'legend>Rolle</legend',
    'radio-group-compact',
] as $marker) {
    if (!str_contains($template, $marker)) {
        $errors[] = "Missing delete template marker: {$marker}";
    }
}

foreach ([
    '.radio-group-compact',
    'justify-items: start',
    'width: 1rem',
    'min-height: 0',
    'input[type="radio"]:focus',
] as $marker) {
    if (!str_contains($style, $marker)) {
        $errors[] = "Missing compact radio style marker: {$marker}";
    }
}

if (str_contains($template, 'Personentyp')) {
    $errors[] = 'Old legend "Personentyp" still present.';
}

if ($errors !== []) {
    fwrite(STDERR, "Person delete role UI check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Person delete role UI check: OK\n";
