<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$errors = [];

$allowedSpecial = [
    '.gitignore' => true,
    '.editorconfig' => true,
];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if (!$file instanceof SplFileInfo || !$file->isFile()) {
        continue;
    }

    $path = $file->getPathname();

    if (str_contains($path, DIRECTORY_SEPARATOR . '.git' . DIRECTORY_SEPARATOR)) {
        continue;
    }

    $name = $file->getFilename();

    if (isset($allowedSpecial[$name])) {
        continue;
    }

    if ($name !== strtolower($name)) {
        $errors[] = $path . ' is not lowercase.';
    }

    if (preg_match('/[A-Z]/', $name) === 1) {
        $errors[] = $path . ' contains uppercase characters.';
    }

    if (preg_match('/[a-z][A-Z]/', $name) === 1) {
        $errors[] = $path . ' contains interCaps.';
    }

    if (str_contains($name, '-')) {
        $errors[] = $path . ' contains hyphen; use underscore.';
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Filename style check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Filename style check: OK\n";
