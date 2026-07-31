<?php
declare(strict_types=1);

$overview = file_get_contents(dirname(__DIR__) . '/site/staff_overview.php') ?: '';
$style = file_get_contents(dirname(__DIR__) . '/site/style.css') ?: '';
$errors = [];

foreach ([
    'overview-sort-buttons',
    'empty-on-mobile',
] as $marker) {
    $haystack = $marker === 'empty-on-mobile' ? $overview : $style . $overview;
    if (!str_contains($haystack, $marker)) {
        $errors[] = "Missing mobile marker: {$marker}";
    }
}

foreach ([
    '@media (max-width: 42rem)',
    'grid-template-columns: minmax(0, 1fr) minmax(0, 1fr)',
    '.overview-sort-buttons',
    'width: 100%',
    '.empty-on-mobile',
    'display: none',
] as $marker) {
    if (!str_contains($style, $marker)) {
        $errors[] = "Missing mobile style marker: {$marker}";
    }
}

foreach ([
    'overview-sort-popup',
    '<summary>Sortieren</summary>',
    'overview-sort-popup__panel',
    'overview_refresh',
    'Aktualisieren',
] as $forbidden) {
    if (str_contains($overview . $style, $forbidden)) {
        $errors[] = "Forbidden mobile marker still present: {$forbidden}";
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
