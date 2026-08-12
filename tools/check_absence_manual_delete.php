<?php
declare(strict_types=1);

$site = dirname(__DIR__) . '/site';
$errors = [];

$repository = file_get_contents($site . '/absence_repository.php') ?: '';
$personal = file_get_contents($site . '/personal.php') ?: '';
$template = file_get_contents($site . '/staff_overview.php') ?: '';
$style = file_get_contents($site . '/style.css') ?: '';

foreach (['deleteById', 'DELETE FROM absences'] as $marker) {
    if (!str_contains($repository, $marker)) {
        $errors[] = "Missing repository marker: {$marker}";
    }
}

foreach (['delete_absence', 'absence_id'] as $marker) {
    if (!str_contains($personal, $marker)) {
        $errors[] = "Missing personal page marker: {$marker}";
    }
}

foreach (['value="delete_absence"', 'icon-button danger-icon', 'Möchten Sie die Abwesenheit von {value} wirklich löschen?'] as $marker) {
    if (!str_contains($template, $marker)) {
        $errors[] = "Missing overview marker: {$marker}";
    }
}

foreach (['background-image: url("/trash_b.svg")', 'background-image: url("/trash_w.svg")'] as $marker) {
    if (!str_contains($style, $marker)) {
        $errors[] = "Missing overview style marker: {$marker}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Absence manual delete check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Absence manual delete check: OK\n";
