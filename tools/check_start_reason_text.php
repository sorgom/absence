<?php
declare(strict_types=1);

$index = file_get_contents(dirname(__DIR__) . '/site/index.php') ?: '';
$app = file_get_contents(dirname(__DIR__) . '/site/app.js') ?: '';
$errors = [];

foreach ([
    '<legend>Grund / Ziel</legend>',
    '<label for="reason_select">Auswählen</label>',
    '<option value=""></option>',
    '<label for="reason">oder Eingeben</label>',
    'name="reason"',
    'data-copy-to="#reason"',
    'data-required-text="#start-outing-button"',
    'disabled>Ausgang starten</button>',
    "start(\$personId, (string) (\$_POST['reason'] ?? ''))",
] as $marker) {
    if (!str_contains($index, $marker)) {
        $errors[] = "Missing outing reason marker: {$marker}";
    }
}

foreach (['[data-copy-to]', 'button.disabled = input.value.trim() ==='] as $marker) {
    if (!str_contains($app, $marker)) {
        $errors[] = "Missing outing reason JS marker: {$marker}";
    }
}

if (str_contains($index, 'name="reason_id"')) {
    $errors[] = 'Outing form must not post reason_id.';
}

if ($errors !== []) {
    fwrite(STDERR, "Start reason text check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Start reason text check: OK\n";
