<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/site/bootstrap.php';

use AbsenceApp\AbsenceCleanup;
use AbsenceApp\Config;
use AbsenceApp\Database;

$db = Database::getConnection();
$config = Config::get();
$retentionHours = (int) ($config['default_absence_retention_hours'] ?? 48);

$before = AbsenceCleanup::countExpired($db);
$deleted = AbsenceCleanup::run($db);
$after = AbsenceCleanup::countExpired($db);

echo "Absence cleanup\n";
echo "Retention hours: {$retentionHours}\n";
echo "Expired before: {$before}\n";
echo "Deleted: {$deleted}\n";
echo "Expired after: {$after}\n";
