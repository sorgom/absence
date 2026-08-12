<?php
declare(strict_types=1);

$site = dirname(__DIR__) . '/site';
$tools = dirname(__DIR__) . '/tools';
$schema = file_get_contents($site . '/create_database.sql') ?: '';
$repository = file_get_contents($site . '/absence_repository.php') ?: '';
$init = file_get_contents($tools . '/init_database.php') ?: '';
$errors = [];

foreach (['reason TEXT NOT NULL', 'idx_absences_person_id'] as $marker) {
    if (!str_contains($schema, $marker)) {
        $errors[] = "Missing schema marker: {$marker}";
    }
}

foreach ([
    'INSERT INTO absences (person_id, reason, departure_time)',
    'a.reason AS reason_name',
    'migrateAbsencesToReasonText',
    'absences_denormalized',
] as $marker) {
    $haystack = str_starts_with($marker, 'migrate') || $marker === 'absences_denormalized' ? $init : $repository;
    if (!str_contains($haystack, $marker)) {
        $errors[] = "Missing denormalized marker: {$marker}";
    }
}

foreach (['reason_id INTEGER NOT NULL', 'FOREIGN KEY (reason_id)', 'JOIN reasons r ON r.id = a.reason_id', 'garbageCollectDeleted'] as $forbidden) {
    if (str_contains($schema . $repository, $forbidden)) {
        $errors[] = "Old reason reference still present: {$forbidden}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Absence reason denormalized check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Absence reason denormalized check: OK\n";
