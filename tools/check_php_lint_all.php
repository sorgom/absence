<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$directories = [$root . '/site', $root . '/tools'];
$errors = [];

foreach ($directories as $directory) {
    $iterator = new DirectoryIterator($directory);

    foreach ($iterator as $file) {
        if (!$file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }

        $path = $file->getPathname();
        $command = escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($path);
        $output = [];
        $exitCode = 0;
        exec($command . ' 2>&1', $output, $exitCode);

        if ($exitCode !== 0) {
            $errors[] = $path . ': ' . implode("\n", $output);
        }
    }
}

if ($errors !== []) {
    fwrite(STDERR, "PHP lint all check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "PHP lint all check: OK\n";
