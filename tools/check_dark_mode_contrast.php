<?php
declare(strict_types=1);

$style = file_get_contents(dirname(__DIR__) . '/site/style.css') ?: '';
$errors = [];

foreach ([
    '@media (prefers-color-scheme: dark)',
    '--field-border:',
    '--menu-border:',
    '.drawer-menu',
    'border: 2px solid var(--menu-border)',
    'input,',
    'select,',
    'textarea',
    '.card,',
    '.table-scroll,',
    'box-shadow: 0 0 0 1px',
    'box-shadow: inset 0 0 0 1px',
    '.menu-toggle[aria-expanded="true"]',
    'background: transparent',
    'color: inherit',
    'border-color: var(--field-border)',
] as $marker) {
    if (!str_contains($style, $marker)) {
        $errors[] = "Missing dark mode contrast marker: {$marker}";
    }
}

if (str_contains($style, '.menu-toggle {') && str_contains($style, 'border: 1.5px solid var(--menu-border)')) {
    $errors[] = 'Forbidden menu-toggle special dark border.';
}

if ($errors !== []) {
    fwrite(STDERR, "Dark mode contrast check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Dark mode contrast check: OK\n";
