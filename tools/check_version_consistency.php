<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$rootVersion = trim((string) file_get_contents($root . '/VERSION'));
$siteVersion = trim((string) file_get_contents($root . '/site/version'));
$readme = file_get_contents($root . '/README.md') ?: '';
$changelog = file_get_contents($root . '/CHANGELOG.md') ?: '';

$errors = [];

if ($rootVersion === '') {
    $errors[] = 'Root version file is empty.';
}

if ($rootVersion !== $siteVersion) {
    $errors[] = "Root version ({$rootVersion}) and site version ({$siteVersion}) differ.";
}

if (!str_contains($readme, "Version **v{$rootVersion}**")) {
    $errors[] = "README.md does not mention Version **v{$rootVersion}**.";
}

if (!str_contains($changelog, "## v{$rootVersion}")) {
    $errors[] = "CHANGELOG.md does not contain ## v{$rootVersion}.";
}

if ($errors !== []) {
    fwrite(STDERR, "Version consistency check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Version consistency check: OK\n";
