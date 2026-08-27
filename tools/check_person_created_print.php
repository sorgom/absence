<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$site = $root . '/site';

$pagePath = $site . '/person_created.php';
$page = file_get_contents($pagePath) ?: '';
$create = file_get_contents($site . '/person_create.php') ?: '';
$template = file_get_contents($site . '/templates_person_create.php') ?: '';
$style = file_get_contents($site . '/style.css') ?: '';

$errors = [];

if (!is_file($pagePath)) {
    $errors[] = 'Missing site/person_created.php.';
}

foreach ([
    "Session::set('created_person'",
    "header('Location: /person_created.php')",
    "'initial_password' => \$generatedPassword",
] as $marker) {
    if (!str_contains($create, $marker)) {
        $errors[] = "Missing person_create marker: {$marker}";
    }
}

foreach ([
    "Session::get('created_person')",
    'HTTP_X_FORWARDED_PROTO',
    'HTTP_HOST',
    'src="/qr_code.php"',
    'window.print()',
    '$title = \'Zugangsdaten\';',
    '<h1>Zugangsdaten</h1>',
    'class="print-brand"',
    'class="print-brand-icon"',
    'src="/icon.svg"',
    'class="print-app-name"',
    'Config::get(\'app_name\')',
    '<h1 class="print-title">Zugangsdaten</h1>',
    'Das Passwort muss beim nächsten Login geändert werden.',
    '<dl class="credential-list">',
] as $marker) {
    if (!str_contains($page, $marker)) {
        $errors[] = "Missing person_created marker: {$marker}";
    }
}

foreach ([
    '@media print',
    'body *',
    'visibility: hidden',
    '.print-card',
    '.qr-placeholder',
    '.print-brand',
    '.print-brand-icon',
    '.print-app-name',
    '.print-title',
    'filter: invert(1)',
    'filter: none',
] as $marker) {
    if (!str_contains($style, $marker)) {
        $errors[] = "Missing print style marker: {$marker}";
    }
}

if (str_contains($template, 'Initiales Passwort:')) {
    $errors[] = 'templates_person_create.php still displays the initial password inline.';
}

foreach ([
    'Person angelegt',
    'wurde erfolgreich angelegt',
    'Diese Seite kann für die Übergabe der Zugangsdaten gedruckt werden',
    'nicht erneut angezeigt',
    '<h2>Zugangsdaten</h2>',
    'unset($_SESSION[\'created_person\'])',
    'Das Passwort muss beim ersten Login geändert werden',
    'Weitere Person anlegen',
] as $forbidden) {
    if (str_contains($page, $forbidden)) {
        $errors[] = "Forbidden person_created text still present: {$forbidden}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Person created print check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Person created print check: OK\n";
