<?php
declare(strict_types=1);

$template = file_get_contents(dirname(__DIR__) . '/site/templates_reasons.php') ?: '';
$errors = [];

foreach ([
    'data-confirm-message="Möchten Sie diesen Grund / dieses Ziel wirklich löschen?"',
    'data-confirm-template="Möchten Sie „{value}“ wirklich löschen?"',
    'data-confirm-value-source="#reason_id"',
] as $marker) {
    if (!str_contains($template, $marker)) {
        $errors[] = "Missing reasons confirm marker: {$marker}";
    }
}

if (!str_contains($template, 'delete_reason') && !str_contains($template, 'reason_id')) {
    $errors[] = 'Missing reason delete form marker.';
}

if ($errors !== []) {
    fwrite(STDERR, "Reasons confirm check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Reasons confirm check: OK\n";
