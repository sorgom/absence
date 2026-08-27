<?php
declare(strict_types=1);

$repository = file_get_contents(dirname(__DIR__) . '/site/person_repository.php') ?: '';
$errors = [];

foreach ([
    "throw new \\RuntimeException('Die ID „' . \$id . '“ existiert bereits.');",
    'if ($this->exists($id))',
] as $marker) {
    if (!str_contains($repository, $marker)) {
        $errors[] = "Missing duplicate ID marker: {$marker}";
    }
}

if (str_contains($repository, "'Diese ID ist bereits vorhanden.'")) {
    $errors[] = 'Old duplicate ID message without concrete ID is still present.';
}

if ($errors !== []) {
    fwrite(STDERR, "Person duplicate ID check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Person duplicate ID check: OK\n";
