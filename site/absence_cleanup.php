<?php
declare(strict_types=1);

namespace AbsenceApp;

use PDO;

final class AbsenceCleanup
{
    public static function run(PDO $db): int
    {
        $retentionHours = (int) Config::get('default_absence_retention_hours', 48);

        return (new AbsenceRepository($db))->deleteExpired($retentionHours);
    }

    public static function countExpired(PDO $db): int
    {
        $retentionHours = (int) Config::get('default_absence_retention_hours', 48);

        return (new AbsenceRepository($db))->countExpired($retentionHours);
    }
}
