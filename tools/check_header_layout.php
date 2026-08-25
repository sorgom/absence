<?php
declare(strict_types=1);

$site = dirname(__DIR__) . '/site';
$header = file_get_contents($site . '/header.php') ?: '';
$style = file_get_contents($site . '/style.css') ?: '';

$errors = [];

foreach ([
    'use AbsenceApp\\AbsenceRepository;',
    'use AbsenceApp\\Database;',
    '$hasActiveAbsence',
    'activeForPerson($userId)',
    'class="header-left"',
    'class="header-right"',
    'class="header-center"',
    'class="app-icon"',
    'status-indicator',
] as $marker) {
    if (!str_contains($header, $marker)) {
        $errors[] = "Missing header marker: {$marker}";
    }
}

if (str_contains($header, 'Aktueller Login:')) {
    $errors[] = 'Header must not show the "Aktueller Login:" label.';
}

if (str_contains($header, '<span class="login-id"')) {
    $errors[] = 'Header must no longer show the login ID directly.';
}

if (str_contains($header, 'class="brand"')) {
    $errors[] = 'Header must not show the app brand next to the menu button.';
}

foreach ([
    '.header-right',
    '.app-icon',
    '.header-center',
    '.status-indicator',
    'border-radius: 999px',
    '--status-active',
    '--status-inactive',
] as $marker) {
    if (!str_contains($style, $marker)) {
        $errors[] = "Missing style marker: {$marker}";
    }
}

if (str_contains($style, '.login-info')) {
    $errors[] = 'Old .login-info style must be removed.';
}

if ($errors !== []) {
    fwrite(STDERR, "Header layout check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Header layout check: OK\n";
