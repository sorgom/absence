<?php
declare(strict_types=1);

/**
 * Checks PHP templates and public files for accidentally broken opening form tags.
 *
 * Run:
 *   php tools/check_forms.php
 */

$root = dirname(__DIR__);
$directories = [
    $root . '/templates',
    $root . '/public',
];

$errors = [];

foreach ($directories as $directory) {
    if (!is_dir($directory)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if (!$file instanceof SplFileInfo || $file->getExtension() !== 'php') {
            continue;
        }

        $content = file_get_contents($file->getPathname());

        if ($content === false) {
            continue;
        }

        if (preg_match_all('/<form\b[^>]*>/is', $content, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as [$formTag, $offset]) {
                if (str_contains($formTag, '<input')) {
                    $line = substr_count(substr($content, 0, $offset), "\n") + 1;
                    $errors[] = $file->getPathname() . ':' . $line . ' contains <input> markup inside the opening <form> tag.';
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
                $errors[] = $file->getPathname() . ' contains known broken marker: ' . $brokenMarker;
            }
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
