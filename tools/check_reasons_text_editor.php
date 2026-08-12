<?php
declare(strict_types=1);

$reasons = file_get_contents(dirname(__DIR__) . '/site/reasons.php') ?: '';
$template = file_get_contents(dirname(__DIR__) . '/site/templates_reasons.php') ?: '';
$repository = file_get_contents(dirname(__DIR__) . '/site/reason_repository.php') ?: '';
$app = file_get_contents(dirname(__DIR__) . '/site/app.js') ?: '';
$errors = [];

foreach (['replaceFromText', '$reasonsText = $repository->asText();'] as $marker) {
    if (!str_contains($reasons, $marker)) {
        $errors[] = "Missing reasons page marker: {$marker}";
    }
}

foreach ([
    '<textarea id="reasons_text"',
    'data-dirty-form',
    'data-dirty-watch',
    'data-dirty-message="Änderungen verwerfen?"',
    'Speichern',
    'data-dirty-leave>Abbruch</a>',
    'Jede Zeile ist ein Eintrag',
] as $marker) {
    if (!str_contains($template, $marker)) {
        $errors[] = "Missing reasons template marker: {$marker}";
    }
}

foreach (['preg_split', 'DELETE FROM reasons', 'INSERT INTO reasons (name, sort_order)', 'ORDER BY sort_order ASC'] as $marker) {
    if (!str_contains($repository, $marker)) {
        $errors[] = "Missing reasons repository marker: {$marker}";
    }
}

foreach (['beforeunload', 'window.AbsenceConfirm.open', '[data-dirty-form]'] as $marker) {
    if (!str_contains($app, $marker)) {
        $errors[] = "Missing dirty warning marker: {$marker}";
    }
}

foreach (['Hinzufügen', 'Löschen', 'name="reason_id"'] as $forbidden) {
    if (str_contains($template, $forbidden)) {
        $errors[] = "Old reasons UI still present: {$forbidden}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Reasons text editor check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Reasons text editor check: OK\n";
