<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$scanDirectories = [
    $root . '/site',
    $root . '/tools',
];

$forbiddenPatterns = array_map('base64_decode', [
    'ZGlybmFtZShfX0RJUl9fKSAuICcvc3JjLw==',
    'ZGlybmFtZShfX0RJUl9fKSAuICcvdGVtcGxhdGVzLw==',
    'X19ESVJfXyAuICcvYXBwLw==',
    'L3NpdGUvYXBwLw==',
    'L2Fzc2V0cy9jc3Mv',
    'L2Fzc2V0cy9qcy8=',
    'L2NvbmZpZy9jb25maWcucGhw',
    'Q29uZmlnLnBocA==',
    'QXBwSW5mby5waHA=',
    'U2Vzc2lvbi5waHA=',
]);

$errors = [];

foreach ($scanDirectories as $directory) {
    if (!is_dir($directory)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile()) {
            continue;
        }

        $extension = $file->getExtension();

        if (!in_array($extension, ['php', 'md', 'conf', 'json'], true)) {
            continue;
        }

        $path = $file->getPathname();
        $content = file_get_contents($path);

        if ($content === false) {
            continue;
        }

        foreach ($forbiddenPatterns as $pattern) {
            if (str_contains($content, $pattern)) {
                $errors[] = $path . ' contains old path reference: ' . $pattern;
            }
        }
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Old flat-site path references found:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Flat path check: OK\n";
