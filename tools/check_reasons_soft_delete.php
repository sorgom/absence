<?php
declare(strict_types=1);

$site = dirname(__DIR__) . '/site';
$errors = [];

$schema = file_get_contents($site . '/create_database.sql') ?: '';
$repository = file_get_contents($site . '/reason_repository.php') ?: '';
$init = file_get_contents(dirname(__DIR__) . '/tools/init_database.php') ?: '';
$template = file_get_contents($site . '/templates_reasons.php') ?: '';

foreach ([
    'deleted INTEGER NOT NULL DEFAULT 0',
    'idx_reasons_deleted',
] as $marker) {
    if (!str_contains($schema, $marker)) {
        $errors[] = "Missing schema marker: {$marker}";
    }
}

foreach ([
    'WHERE deleted = 0',
    'UPDATE reasons SET deleted = 1',
    'garbageCollectDeleted',
    'DELETE FROM reasons',
] as $marker) {
    if (!str_contains($repository, $marker)) {
        $errors[] = "Missing repository marker: {$marker}";
    }
}

if (!str_contains($init, "ALTER TABLE reasons ADD COLUMN deleted")) {
    $errors[] = 'Missing migration for reasons.deleted.';
}

if (!str_contains($template, 'Gründe und Ziele')) {
    $errors[] = 'Reasons page title was not updated.';
}

if ($errors !== []) {
    fwrite(STDERR, "Reasons soft-delete check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Reasons soft-delete check: OK\n";
