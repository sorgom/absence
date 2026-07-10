<?php
declare(strict_types=1);

$site = dirname(__DIR__) . '/site';
$schema = file_get_contents($site . '/create_database.sql');
$errors = [];

foreach (['CREATE TABLE IF NOT EXISTS persons', 'is_staff', 'person_id'] as $needle) {
    if ($schema === false || !str_contains($schema, $needle)) {
        $errors[] = "Missing schema marker: {$needle}";
    }
}

foreach (['CREATE TABLE IF NOT EXISTS patients', 'CREATE TABLE IF NOT EXISTS staff', 'patient_id TEXT NOT NULL'] as $forbidden) {
    if ($schema !== false && str_contains($schema, $forbidden)) {
        $errors[] = "Old schema marker still present: {$forbidden}";
    }
}

foreach (['auth.php', 'absence_repository.php', 'patient_repository.php', 'staff_repository.php'] as $file) {
    if (!is_file($site . '/' . $file)) {
        $errors[] = "Missing site/{$file}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "v0.9 schema check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "v0.9 schema check: OK\n";
