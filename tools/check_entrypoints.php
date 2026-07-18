<?php
declare(strict_types=1);

$site = dirname(__DIR__) . '/site';

$entrypoints = [
    'index.php',
    'personal.php',
    'logout.php',
    'change_password.php',
    'reasons.php',
];

$errors = [];

foreach ($entrypoints as $entrypoint) {
    $path = $site . '/' . $entrypoint;

    if (!is_file($path)) {
        $errors[] = "Missing {$entrypoint}";
        continue;
    }

    $content = file_get_contents($path);

    if ($content === false) {
        $errors[] = "Cannot read {$entrypoint}";
        continue;
    }

    if (!preg_match("/require(?:_once)?\s+__DIR__\s*\.\s*'\/bootstrap\.php';/", $content, $match, PREG_OFFSET_CAPTURE)) {
        $errors[] = "{$entrypoint} does not require bootstrap.php";
        continue;
    }

    $bootstrapPosition = $match[0][1];

    foreach (['Session::', 'new Auth', 'new PersonRepository', 'new PersonRepository', 'new ReasonRepository', 'new AbsenceRepository'] as $marker) {
        $markerPosition = strpos($content, $marker);

        if ($markerPosition !== false && $markerPosition < $bootstrapPosition) {
            $errors[] = "{$entrypoint} uses {$marker} before bootstrap.php";
        }
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Entrypoint check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Entrypoint check: OK\n";
