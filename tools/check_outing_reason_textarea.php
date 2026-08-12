<?php
declare(strict_types=1);

$site = dirname(__DIR__) . '/site';
$index = file_get_contents($site . '/index.php') ?: '';
$style = file_get_contents($site . '/style.css') ?: '';
$app = file_get_contents($site . '/app.js') ?: '';
$errors = [];

foreach ([
    '<textarea',
    'id="reason"',
    'name="reason"',
    'class="outing-reason-textarea"',
    'rows="1"',
    'data-required-text="#start-outing-button"',
] as $marker) {
    if (!str_contains($index, $marker)) {
        $errors[] = "Missing outing textarea marker: {$marker}";
    }
}

foreach ([
    '.outing-reason-textarea',
    'field-sizing: content',
    'min-height: 2.75rem',
    'resize: none',
    'max-width: 100%',
] as $marker) {
    if (!str_contains($style, $marker)) {
        $errors[] = "Missing outing textarea CSS marker: {$marker}";
    }
}

foreach ([
    'input.value = select.value',
    "input.addEventListener('input', sync)",
    'button.disabled = input.value.trim() ===',
] as $marker) {
    if (!str_contains($app, $marker)) {
        $errors[] = "Missing outing reason JS marker: {$marker}";
    }
}

if (str_contains($index, 'id="reason" name="reason" type="text"')) {
    $errors[] = 'Old single-line reason input is still present.';
}

if ($errors !== []) {
    fwrite(STDERR, "Outing reason textarea check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Outing reason textarea check: OK\n";
