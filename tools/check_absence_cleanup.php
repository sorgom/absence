<?php
declare(strict_types=1);

$site = dirname(__DIR__) . '/site';
$tools = dirname(__DIR__) . '/tools';

$files = [
    'repository' => file_get_contents($site . '/absence_repository.php') ?: '',
    'cleanup' => file_get_contents($site . '/absence_cleanup.php') ?: '',
    'bootstrap' => file_get_contents($site . '/bootstrap.php') ?: '',
    'index' => file_get_contents($site . '/index.php') ?: '',
    'personal' => file_get_contents($site . '/personal.php') ?: '',
    'run_tool' => file_get_contents($tools . '/run_absence_cleanup.php') ?: '',
    'seed_tool' => file_get_contents($tools . '/seed_expired_absences.php') ?: '',
    'config' => file_get_contents($site . '/app_config.php') ?: '',
];

$errors = [];

foreach (['default_absence_retention_hours', '48'] as $marker) {
    if (!str_contains($files['config'], $marker)) {
        $errors[] = "Missing config marker: {$marker}";
    }
}

foreach (['deleteExpired', 'countExpired', 'DELETE FROM absences', 'return_time IS NOT NULL', "return_time < datetime('now', :modifier)", 'garbageCollectDeleted'] as $marker) {
    if (!str_contains($files['repository'], $marker)) {
        $errors[] = "Missing repository marker: {$marker}";
    }
}

foreach (['final class AbsenceCleanup', "Config::get('default_absence_retention_hours', 48)", 'deleteExpired', 'countExpired'] as $marker) {
    if (!str_contains($files['cleanup'], $marker)) {
        $errors[] = "Missing cleanup helper marker: {$marker}";
    }
}

if (!str_contains($files['bootstrap'], "absence_cleanup.php")) {
    $errors[] = 'Bootstrap does not include absence_cleanup.php.';
}

foreach (['index', 'personal'] as $page) {
    if (!str_contains($files[$page], 'AbsenceCleanup::run($db);')) {
        $errors[] = "{$page} is missing automatic cleanup execution.";
    }
}

foreach (['Absence cleanup', 'Expired before', 'Deleted', 'Expired after'] as $marker) {
    if (!str_contains($files['run_tool'], $marker)) {
        $errors[] = "Missing cleanup tool marker: {$marker}";
    }
}

foreach (['cleanup_active_old', 'cleanup_done_expired', "'return_time' => null", '96 * 3600', 'cleanup_done_fresh', 'cleanup_done_expired'] as $marker) {
    if (!str_contains($files['seed_tool'], $marker)) {
        $errors[] = "Missing cleanup seed marker: {$marker}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Absence cleanup check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Absence cleanup check: OK\n";
