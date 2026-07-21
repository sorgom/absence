<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$personal = file_get_contents($root . '/site/personal.php') ?: '';
$overview = file_get_contents($root . '/site/staff_overview.php') ?: '';
$repository = file_get_contents($root . '/site/absence_repository.php') ?: '';
$errors = [];

foreach ([
    "['active', 'ended', 'all']",
    'listForStaffByView($view',
] as $marker) {
    if (!str_contains($personal, $marker)) {
        $errors[] = "Missing personal marker: {$marker}";
    }
}

foreach ([
    'listForStaffByView',
    "'ended' => 'WHERE a.return_time IS NOT NULL'",
    "'all' => ''",
    "default => 'WHERE a.return_time IS NULL'",
] as $marker) {
    if (!str_contains($repository, $marker)) {
        $errors[] = "Missing repository marker: {$marker}";
    }
}

foreach ([
    '<legend>Auswahl</legend>',
    'value="active"',
    'value="ended"',
    'value="all"',
    'Aktiv',
    'Beendet',
    'Alle',
] as $marker) {
    if (!str_contains($overview, $marker)) {
        $errors[] = "Missing overview filter marker: {$marker}";
    }
}

if (str_contains($overview, 'Nur aktive Abwesenheiten')) {
    $errors[] = 'Old checkbox text still present.';
}

if ($errors !== []) {
    fwrite(STDERR, "Staff overview filters check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Staff overview filters check: OK\n";
