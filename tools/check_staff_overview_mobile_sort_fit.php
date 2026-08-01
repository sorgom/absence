<?php
declare(strict_types=1);

$style = file_get_contents(dirname(__DIR__) . '/site/style.css') ?: '';
$errors = [];

$sortStart = strpos($style, '@media (max-width: 42rem)');
$overviewMediaStart = strpos($style, '@media (max-width: 42rem)', $sortStart === false ? 0 : $sortStart + 1);
$overviewMediaBlock = $overviewMediaStart === false ? '' : substr($style, $overviewMediaStart, 2200);
$sortBlockStart = strpos($overviewMediaBlock, '.overview-sort-buttons');
$sortBlock = $sortBlockStart === false ? '' : substr($overviewMediaBlock, $sortBlockStart, 900);

foreach ([
    'grid-template-columns: minmax(0, 1fr) minmax(0, 1fr)',
    '.overview-sort-buttons',
    '.overview-sort-buttons form',
    '.overview-sort-buttons .compact',
    'min-width: 0',
    'width: 100%',
    'overflow: hidden',
    'text-overflow: ellipsis',
] as $marker) {
    if (!str_contains($overviewMediaBlock, $marker)) {
        $errors[] = "Missing mobile sort fit marker: {$marker}";
    }
}

if (str_contains($sortBlock, 'width: 12rem')) {
    $errors[] = 'Mobile sort buttons must not keep the desktop 12rem width.';
}

if ($errors !== []) {
    fwrite(STDERR, "Staff overview mobile sort fit check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Staff overview mobile sort fit check: OK\n";
