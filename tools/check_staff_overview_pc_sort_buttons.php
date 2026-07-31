<?php
declare(strict_types=1);

$overview = file_get_contents(dirname(__DIR__) . '/site/staff_overview.php') ?: '';
$style = file_get_contents(dirname(__DIR__) . '/site/style.css') ?: '';
$errors = [];

foreach ([
    '<h1>Übersicht</h1>',
    '<th>ID</th>',
    '<th>Aufbruch</th>',
    'overview-sort-buttons',
    'input type="hidden" name="sort" value="patient"',
    'input type="hidden" name="sort" value="departure"',
] as $marker) {
    if (!str_contains($overview, $marker)) {
        $errors[] = "Missing PC overview marker: {$marker}";
    }
}

foreach ([
    '.overview-sort-buttons',
    'display: grid',
    'width: 12rem',
    '.overview-sort-buttons .compact',
    'width: 100%',
] as $marker) {
    if (!str_contains($style, $marker)) {
        $errors[] = "Missing PC sort button style marker: {$marker}";
    }
}

foreach ([
    'class="sort-form"',
    'table-sort-button',
    '<button class="table-sort-button"',
] as $forbidden) {
    if (str_contains($overview . $style, $forbidden)) {
        $errors[] = "Forbidden table-header sort marker still present: {$forbidden}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Staff overview PC sort buttons check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Staff overview PC sort buttons check: OK\n";
