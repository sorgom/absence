<?php
declare(strict_types=1);

$page = file_get_contents(dirname(__DIR__) . '/site/person_created.php') ?: '';
$errors = [];

foreach ([
    "Session::get('created_person')",
    "header('Location: /person_create.php')",
    '$title = \'Zugangsdaten\';',
] as $marker) {
    if (!str_contains($page, $marker)) {
        $errors[] = "Missing credentials page marker: {$marker}";
    }
}

foreach ([
    "unset(\$_SESSION['created_person'])",
    "Session::remove('created_person')",
] as $forbidden) {
    if (str_contains($page, $forbidden)) {
        $errors[] = "Forbidden refresh-breaking marker still present: {$forbidden}";
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Credentials refresh check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Credentials refresh check: OK\n";
