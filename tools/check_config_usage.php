<?php
declare(strict_types=1);

$configClass = file_get_contents(dirname(__DIR__) . '/site/config_class.php') ?: '';
$cleanup = file_get_contents(dirname(__DIR__) . '/site/absence_cleanup.php') ?: '';
$errors = [];

if (!str_contains($configClass, 'public static function get(string $key')) {
    $errors[] = 'Config::get signature is expected to require a key.';
}

if (str_contains($cleanup, 'Config::get();')) {
    $errors[] = 'absence_cleanup.php must not call Config::get() without key.';
}

if (!str_contains($cleanup, "Config::get('default_absence_retention_hours', 48)")) {
    $errors[] = 'absence_cleanup.php must read default_absence_retention_hours via Config::get key.';
}

if ($errors !== []) {
    fwrite(STDERR, "Config usage check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Config usage check: OK\n";
