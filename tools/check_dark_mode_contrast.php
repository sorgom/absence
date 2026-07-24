<?php
declare(strict_types=1);

$style = file_get_contents(dirname(__DIR__) . '/site/style.css') ?: '';
$errors = [];

foreach ([
    '@media (prefers-color-scheme: dark)',
    '--field-border:',
    '--menu-border:',
    '.menu-toggle',
    'border: 1px solid var(--menu-border)',
    'input,',
    'select,',
    'textarea',
    'border-color: var(--field-border)',
] as $marker) {
    if (!str_contains($style, $marker)) {
        $errors[] = "Missing dark mode contrast marker: {$marker}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Dark mode contrast check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Dark mode contrast check: OK\n";
