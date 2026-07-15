<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$site = $root . '/site';

$legacyFiles = [];
foreach (['patient', 'staff'] as $prefix) {
    $legacyFiles[] = $prefix . '_repository.php';
}

$errors = [];

foreach ($legacyFiles as $file) {
    if (is_file($site . '/' . $file)) {
        $errors[] = "Legacy repository still exists: site/{$file}";
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
            if (str_contains($content, $legacyFile)) {
                $errors[] = "Legacy repository reference {$legacyFile} found in " . $fileInfo->getFilename();
            }
        }

        foreach (['PatientRepository', 'StaffRepository'] as $legacyClass) {
            if (str_contains($content, $legacyClass)) {
                $errors[] = "Legacy repository class {$legacyClass} found in " . $fileInfo->getFilename();
            }
        }
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Legacy repository check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Legacy repository check: OK\n";
