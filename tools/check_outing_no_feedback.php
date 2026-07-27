<?php
declare(strict_types=1);

$page = file_get_contents(dirname(__DIR__) . '/site/index.php') ?: '';
$errors = [];

foreach ([
    "Ausgang wurde gestartet.",
    "Ausgang wurde verworfen.",
    "Rückkehr wurde gespeichert.",
    '$message',
    '<p class="alert success"',
] as $forbidden) {
    if (str_contains($page, $forbidden)) {
        $errors[] = "Forbidden outing feedback marker still present: {$forbidden}";
    }
}

foreach ([
    '$error = null;',
    '<p class="alert error"><?= Utils::h($error) ?></p>',
    '$absences->start($personId',
    '$absences->endOrDeleteShort(',
] as $marker) {
    if (!str_contains($page, $marker)) {
        $errors[] = "Missing outing marker: {$marker}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Outing no feedback check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Outing no feedback check: OK\n";
