<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$config = file_get_contents($root . '/site/app_config.php') ?: '';
$repository = file_get_contents($root . '/site/absence_repository.php') ?: '';
$index = file_get_contents($root . '/site/index.php') ?: '';
$errors = [];

foreach ([
    "'short_absence_delete_minutes' => 10",
] as $marker) {
    if (!str_contains($config, $marker)) {
        $errors[] = "Missing config marker: {$marker}";
    }
}

foreach ([
    'endOrDeleteShort',
    'datetime(\'now\', :modifier)',
    "'-' . \$deleteWithinMinutes . ' minutes'",
    'DELETE FROM absences',
    'UPDATE absences SET return_time = CURRENT_TIMESTAMP',
    "return 'deleted'",
    "return 'ended'",
] as $marker) {
    if (!str_contains($repository, $marker)) {
        $errors[] = "Missing repository marker: {$marker}";
    }
}

foreach ([
    "Config::get('short_absence_delete_minutes')",
    'endOrDeleteShort',
] as $marker) {
    if (!str_contains($index, $marker)) {
        $errors[] = "Missing index marker: {$marker}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Short absence delete check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Short absence delete check: OK\n";
