<?php
declare(strict_types=1);

$site = dirname(__DIR__) . '/site';
$errors = [];

$header = file_get_contents($site . '/header.php') ?: '';
$overview = file_get_contents($site . '/staff_overview.php') ?: '';

foreach ([
    '<div class="header-right" aria-label=',
    '<div class="overview-sort-buttons" aria-label=',
] as $forbidden) {
    if (str_contains($header, $forbidden) || str_contains($overview, $forbidden)) {
        $errors[] = "Forbidden validator marker still present: {$forbidden}";
    }
}

foreach ([
    '<div class="header-right">',
    '<div class="overview-sort-buttons" role="group" aria-label="Sortierung">',
    'role="img"',
    'aria-label="<?= $isActive ? \'Aktiv\' : \'Beendet\' ?>"',
] as $marker) {
    if (!str_contains($header, $marker) && !str_contains($overview, $marker)) {
        $errors[] = "Missing valid ARIA marker: {$marker}";
    }
}

$statusSpanPosition = strpos($overview, 'class="overview-status-dot');
$ariaPosition = strpos($overview, 'aria-label="<?= $isActive ? \'Aktiv\' : \'Beendet\' ?>"', $statusSpanPosition === false ? 0 : $statusSpanPosition);
$rolePosition = strpos($overview, 'role="img"', $statusSpanPosition === false ? 0 : $statusSpanPosition);

if ($statusSpanPosition === false || $ariaPosition === false || $rolePosition === false || $rolePosition > $ariaPosition) {
    $errors[] = 'Status dot span with aria-label must include role="img" before the aria-label.';
}

if ($errors !== []) {
    fwrite(STDERR, "HTML ARIA validity check failed:\n");
    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }
    exit(1);
}

echo "HTML ARIA validity check: OK\n";
