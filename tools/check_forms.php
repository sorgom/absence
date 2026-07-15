<?php
declare(strict_types=1);

/**
 * Checks PHP templates and site entry points for accidentally broken opening form tags.
 */

$root = dirname(__DIR__);
$site = $root . '/site';
$errors = [];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($site, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if (!$file instanceof SplFileInfo || $file->getExtension() !== 'php') {
        continue;
    }

    $path = $file->getPathname();
    $content = file_get_contents($path);

    if ($content === false) {
        continue;
    }

    if (preg_match_all('/<form\b[^>]*>/is', $content, $matches, PREG_OFFSET_CAPTURE)) {
        foreach ($matches[0] as [$formTag, $offset]) {
            if (str_contains($formTag, '<input')) {
                $line = substr_count(substr($content, 0, $offset), "\n") + 1;
                $errors[] = $path . ':' . $line . ' contains <input> markup inside the opening <form> tag.';
            }
        }
    }

    foreach ([
        'personal.php        <input',
        'action="<?= Utils::h((string) $action) ?>        <input',
        'action="/personal.php        <input',
        'action="/index.php        <input',
    ] as $brokenMarker) {
        if (str_contains($content, $brokenMarker)) {
            $errors[] = $path . ' contains known broken marker: ' . $brokenMarker;
        }
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Broken form markup found:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Form markup check: OK\n";
