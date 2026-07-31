<?php
declare(strict_types=1);

$overview = file_get_contents(dirname(__DIR__) . '/site/staff_overview.php') ?: '';
$style = file_get_contents(dirname(__DIR__) . '/site/style.css') ?: '';
$errors = [];

foreach ([
    'overview-sort-buttons',
    'input type="hidden" name="sort" value="patient"',
    'input type="hidden" name="sort" value="departure"',
] as $marker) {
    if (!str_contains($overview, $marker)) {
        $errors[] = "Missing direct sort marker: {$marker}";
    }
}

foreach ([
    'overview-sort-popup',
    'overview-sort-popup__panel',
    '<summary>Sortieren</summary>',
    'width: max-content',
] as $forbidden) {
    if (str_contains($overview . $style, $forbidden)) {
        $errors[] = "Sort popup marker must be removed: {$forbidden}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Sort button check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Sort button check: OK\n";
