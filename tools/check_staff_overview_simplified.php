<?php
declare(strict_types=1);

$overview = file_get_contents(dirname(__DIR__) . '/site/staff_overview.php') ?: '';
$style = file_get_contents(dirname(__DIR__) . '/site/style.css') ?: '';
$errors = [];

foreach ([
    '<h1>Übersicht</h1>',
    'overview-sort-buttons',
    'aria-label="Auswahl"',
    'aria-label="Sortierung"',
    'grid-template-columns: minmax(0, 1fr) minmax(0, 1fr)',
] as $marker) {
    if (!str_contains($overview . $style, $marker)) {
        $errors[] = "Missing simplified overview marker: {$marker}";
    }
}

foreach ([
    '<h1>Übersicht Abwesenheiten</h1>',
    'Auswahl:',
    'Sortierung:',
    'overview_refresh',
    'Aktualisieren',
    '<legend>Auswahl</legend>',
    'overview-sort-popup',
    '<summary>Sortieren</summary>',
] as $forbidden) {
    if (str_contains($overview . $style, $forbidden)) {
        $errors[] = "Forbidden old overview marker still present: {$forbidden}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Staff overview simplified check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Staff overview simplified check: OK\n";
