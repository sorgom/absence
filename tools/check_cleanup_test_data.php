<?php
declare(strict_types=1);

$seed = file_get_contents(dirname(__DIR__) . '/tools/seed_expired_absences.php') ?: '';
$show = file_get_contents(dirname(__DIR__) . '/tools/show_cleanup_test_absences.php') ?: '';
$errors = [];

foreach ([
    'cleanup_done_expired',
    'cleanup_done_fresh',
    'cleanup_active_old',
    'aktiv, Aufbruch älter als N Stunden: bleibt',
    "'return_time' => null",
    '96 * 3600',
    '74 * 3600',
    '72 * 3600',
    '24 * 3600',
] as $marker) {
    if (!str_contains($seed, $marker)) {
        $errors[] = "Missing cleanup seed marker: {$marker}";
    }
}

foreach ([
    'ACTIVE / no return_time',
    'cleanup_active_old',
] as $marker) {
    if (!str_contains($show, $marker)) {
        $errors[] = "Missing cleanup show marker: {$marker}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Cleanup test data check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Cleanup test data check: OK\n";
