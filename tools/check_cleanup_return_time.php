<?php
declare(strict_types=1);

$repository = file_get_contents(dirname(__DIR__) . '/site/absence_repository.php') ?: '';
$seed = file_get_contents(dirname(__DIR__) . '/tools/seed_expired_absences.php') ?: '';
$errors = [];

foreach ([
    'return_time IS NOT NULL',
    "return_time < datetime('now', :modifier)",
    'deleteExpired',
    'countExpired',
] as $marker) {
    if (!str_contains($repository, $marker)) {
        $errors[] = "Missing repository marker: {$marker}";
    }
}

if (str_contains($repository, 'WHERE departure_time < datetime')) {
    $errors[] = 'Cleanup must not delete by departure_time anymore.';
}

foreach ([
    'Completed and expired: must be deleted',
    'Completed but still fresh: must stay',
    'Active and old: must stay because return_time is NULL',
    "'return_time' => null",
] as $marker) {
    if (!str_contains($seed, $marker)) {
        $errors[] = "Missing seed marker: {$marker}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Cleanup return-time check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Cleanup return-time check: OK\n";
