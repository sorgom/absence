<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$overview = file_get_contents($root . '/site/staff_overview.php') ?: '';
$style = file_get_contents($root . '/site/style.css') ?: '';
$errors = [];

foreach ([
    'aria-label="Status"',
    'overview-status-dot',
    'aria-label="Aktion"',
    'icon-button danger-icon',
    'aria-label="Abwesenheit löschen"',
] as $marker) {
    if (!str_contains($overview, $marker)) {
        $errors[] = "Missing table icon marker: {$marker}";
    }
}

foreach ([
    '.overview-status-dot',
    'var(--status-active)',
    'var(--status-inactive)',
    '.icon-button',
    'background-image: url("/trash_b.svg")',
    'background-image: url("/trash_w.svg")',
    'background-size: 1rem 1rem',
    'height: 1.45rem',
    'width: 1.45rem',
] as $marker) {
    if (!str_contains($style, $marker)) {
        $errors[] = "Missing table icon style marker: {$marker}";
    }
}

foreach ([
    '<th>Status</th>',
    '<th>Aktion</th>',
    'Unterwegs',
    '>Zurück<',
    '>Löschen<',
] as $forbidden) {
    if (str_contains($overview, $forbidden)) {
        $errors[] = "Old table text still present: {$forbidden}";
    }
}

if (str_contains($overview, '<img')) {
    $errors[] = 'Delete icon button must not contain a visible img element.';
}

if (str_contains($style, 'tr.is-active td:first-child') || str_contains($style, 'border-left: 0.25rem solid var(--accent)')) {
    $errors[] = 'Active overview rows must not have a blue left border.';
}

if ($errors !== []) {
    fwrite(STDERR, "Staff overview table icons check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Staff overview table icons check: OK\n";
