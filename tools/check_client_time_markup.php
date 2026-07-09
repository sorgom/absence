<?php
declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

use AbsenceApp\Utils;

$sample = '2026-07-09 18:30:00';
$markup = Utils::localTimeElement($sample);

echo $markup . PHP_EOL;

if (!str_contains($markup, 'datetime="2026-07-09T18:30:00Z"')) {
    fwrite(STDERR, "Missing or invalid UTC datetime attribute.\n");
    exit(1);
}

if (!str_contains($markup, 'data-local-time')) {
    fwrite(STDERR, "Missing data-local-time attribute.\n");
    exit(1);
}

echo "Client time markup check: OK\n";
