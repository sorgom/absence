<?php
declare(strict_types=1);

$template = file_get_contents(dirname(__DIR__) . '/site/templates_reasons.php') ?: '';
$errors = [];

foreach ([
    'Gelöschte Gründe verschwinden aus der Auswahl für neue Abwesenheiten',
    'In bestehenden Abwesenheiten bleiben sie sichtbar',
] as $forbidden) {
    if (str_contains($template, $forbidden)) {
        $errors[] = "Forbidden reasons hint still present: {$forbidden}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Reasons hint removal check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Reasons hint removal check: OK\n";
