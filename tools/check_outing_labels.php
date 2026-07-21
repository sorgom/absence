<?php
declare(strict_types=1);

$index = file_get_contents(dirname(__DIR__) . '/site/index.php') ?: '';
$style = file_get_contents(dirname(__DIR__) . '/site/style.css') ?: '';
$errors = [];

foreach ([
    '<h1>Ausgang</h1>',
    'Grund / Ziel auswählen:',
    'Ausgang starten',
    'active-outing-title',
    'Ausgang beenden',
] as $marker) {
    if (!str_contains($index, $marker)) {
        $errors[] = "Missing outing page marker: {$marker}";
    }
}

foreach ([
    'Ausgang <span>aktiv</span>',
    '.active-outing-title span',
    'var(--status-active)',
] as $marker) {
    $haystack = str_starts_with($marker, '.') || str_contains($marker, 'var(') ? $style : $index;
    if (!str_contains($haystack, $marker)) {
        $errors[] = "Missing active title marker: {$marker}";
    }
}

foreach ([
    '<h1>Abwesenheit</h1>',
    '<h2>Rückkehr</h2>',
    '<h2>Ausgang starten</h2>',
    'Grund / Ziel des Ausgangs',
    '<button type="submit">Start</button>',
    '<button type="submit">Ende</button>',
] as $forbidden) {
    if (str_contains($index, $forbidden)) {
        $errors[] = "Old outing text still present: {$forbidden}";
    }
}

if (substr_count($index, 'active-outing-title') !== 1) {
    $errors[] = 'Expected exactly one active outing title.';
}

if (str_contains($index, '<?php if ($active): ?>' . PHP_EOL . '        <?php else: ?>')) {
    $errors[] = 'Active heading branch must not be empty.';
}

if ($errors !== []) {
    fwrite(STDERR, "Outing labels check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "Outing labels check: OK\n";
