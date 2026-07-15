<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$site = $root . '/site';

$prefixes = ['patient', 'staff'];
$suffixes = ['create', 'delete'];

$legacyFiles = [];
foreach ($prefixes as $prefix) {
    foreach ($suffixes as $suffix) {
        $legacyFiles[] = $prefix . '_' . $suffix . '.php';
    }
}

$errors = [];

foreach ($legacyFiles as $file) {
    if (is_file($site . '/' . $file)) {
        $errors[] = "Legacy entrypoint still exists: site/{$file}";
    }
}

$directories = [$site, $root . '/tools'];
foreach ($directories as $directory) {
    foreach (new DirectoryIterator($directory) as $fileInfo) {
        if (!$fileInfo->isFile() || $fileInfo->getExtension() !== 'php') {
            continue;
        }

        if ($fileInfo->getFilename() === basename(__FILE__)) {
            continue;
        }

        $content = file_get_contents($fileInfo->getPathname()) ?: '';

        foreach ($legacyFiles as $legacyFile) {
            if (str_contains($content, $legacyFile) || str_contains($content, '/' . $legacyFile)) {
                $errors[] = "Legacy reference {$legacyFile} found in " . $fileInfo->getFilename();
            }
        }
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Legacy entrypoint check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Legacy entrypoint check: OK\n";
