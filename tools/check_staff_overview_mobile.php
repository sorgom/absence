<?php
declare(strict_types=1);

$overview = file_get_contents(dirname(__DIR__) . '/site/staff_overview.php') ?: '';
$style = file_get_contents(dirname(__DIR__) . '/site/style.css') ?: '';
$errors = [];

foreach ([
    'overview-sort-popup',
    '<summary>Sortieren</summary>',
    'overview-sort-popup__panel',
    'empty-on-mobile',
] as $marker) {
    $haystack = str_contains($marker, 'empty') || str_starts_with($marker, '<') ? $overview : $style . $overview;
    if (!str_contains($haystack, $marker)) {
        $errors[] = "Missing mobile marker: {$marker}";
    }
}

foreach ([
    '@media (max-width: 42rem)',
    '.empty-on-mobile',
    'display: none',
    '.overview-sort-popup__panel',
] as $marker) {
    if (!str_contains($style, $marker)) {
        $errors[] = "Missing mobile style marker: {$marker}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Staff overview mobile check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Staff overview mobile check: OK\n";
