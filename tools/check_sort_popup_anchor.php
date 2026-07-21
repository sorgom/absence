<?php
declare(strict_types=1);

$style = file_get_contents(dirname(__DIR__) . '/site/style.css') ?: '';
$errors = [];

foreach ([
    '.overview-sort-popup__panel',
    'left: 0',
    'right: auto',
    'max-width: calc(100vw - 3rem)',
    'width: max-content',
] as $marker) {
    if (!str_contains($style, $marker)) {
        $errors[] = "Missing sort popup anchor marker: {$marker}";
    }
}

$panelStart = strpos($style, '.overview-sort-popup__panel');
if ($panelStart !== false) {
    $panelBlock = substr($style, $panelStart, 500);
    if (str_contains($panelBlock, 'right: 0;')) {
        $errors[] = 'Sort popup panel must not be anchored with right: 0.';
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Sort popup anchor check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Sort popup anchor check: OK\n";
