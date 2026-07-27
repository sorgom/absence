<?php
declare(strict_types=1);

$template = file_get_contents(dirname(__DIR__) . '/site/templates_reasons.php') ?: '';
$errors = [];

if (str_contains($template, 'autofocus')) {
    $errors[] = 'Reasons input must not use autofocus.';
}

foreach ([
    '<label for="name">Neuer Grund / neues Ziel</label>',
    '<input id="name" name="name" type="text" autocomplete="off" required>',
] as $marker) {
    if (!str_contains($template, $marker)) {
        $errors[] = "Missing reasons input marker: {$marker}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Reasons no autofocus check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Reasons no autofocus check: OK\n";
