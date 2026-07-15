<?php
declare(strict_types=1);

require dirname(__DIR__) . '/site/bootstrap.php';

use AbsenceApp\Config;
use AbsenceApp\Session;
use AbsenceApp\Utils;

$appName = Config::get('app_name');
$time = Utils::localTimeElement('2026-07-09 18:30:00');

if (!is_string($appName) || $appName === '') {
    fwrite(STDERR, "Config check failed.\n");
    exit(1);
}

if (!str_contains($time, 'data-local-time')) {
    fwrite(STDERR, "Utils check failed.\n");
    exit(1);
}

if (!method_exists(Session::class, 'start')) {
    fwrite(STDERR, "Session check failed.\n");
    exit(1);
}

echo "Bootstrap check: OK\n";
