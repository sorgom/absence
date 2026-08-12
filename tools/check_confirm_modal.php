<?php
declare(strict_types=1);

$site = dirname(__DIR__) . '/site';
$footer = file_get_contents($site . '/footer.php') ?: '';
$js = file_get_contents($site . '/app.js') ?: '';
$css = file_get_contents($site . '/style.css') ?: '';
$personDelete = file_get_contents($site . '/templates_person_delete.php') ?: '';
$overview = file_get_contents($site . '/staff_overview.php') ?: '';
$reasons = file_get_contents($site . '/templates_reasons.php') ?: '';

$errors = [];

foreach ([
    'v0.9.6.2: solid confirmation modal',
    '.confirm-modal__backdrop',
    '.confirm-modal__panel',
    '.confirm-modal__dialog',
    'background: var(--card-background, var(--card-bg, Canvas))',
    'opacity: 1',
    'body.confirm-modal-open',
] as $marker) {
    if (!str_contains($css, $marker)) {
        $errors[] = "Missing modal CSS marker: {$marker}";
    }
}

if (str_contains($js, 'window.confirm') || str_contains($js, 'native confirm')) {
    $errors[] = 'window.confirm should not be used anymore.';
}

foreach (['Möchten Sie Person {value} wirklich löschen?', 'data-confirm-value-source="#id"'] as $marker) {
    if (!str_contains($personDelete, $marker)) {
        $errors[] = "Missing person delete marker: {$marker}";
    }
}

if (!str_contains($overview, 'Möchten Sie die Abwesenheit von {value} wirklich löschen?')) {
    $errors[] = 'Missing dynamic absence delete message.';
}

foreach (['data-dirty-message="Änderungen verwerfen?"', 'data-dirty-leave>Abbruch</a>'] as $marker) {
    if (!str_contains($reasons, $marker)) {
        $errors[] = "Missing reasons discard marker: {$marker}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Confirm modal check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Confirm modal check: OK\n";
