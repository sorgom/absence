<?php
declare(strict_types=1);

$site = dirname(__DIR__) . '/site';
$names = [];
$errors = [];

foreach (new DirectoryIterator($site) as $file) {
    if ($file->isDot()) {
        continue;
    }

    $lower = strtolower($file->getFilename());
    $names[$lower][] = $file->getFilename();
}

foreach ($names as $lower => $originals) {
    $unique = array_values(array_unique($originals));

    if (count($unique) > 1) {
        $errors[] = implode(', ', $unique);
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Windows filename collision found:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Windows filename check: OK\n";
