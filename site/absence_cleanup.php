<?php
declare(strict_types=1);

namespace AbsenceApp;

use PDO;

final class AbsenceCleanup
{
    public static function run(PDO $db): int
    {
        $retentionHours = (int) Config::get('default_absence_retention_hours', 48);

        $deleted = (new AbsenceRepository($db))->deleteExpired($retentionHours);

        $windowMinutes = (int) Config::get('login_lockout_window_minutes');
        (new RateLimiter($db))->purgeOlderThan(max(60, $windowMinutes * 4));

        $protocolRetentionHours = (int) Config::get('protocol_retention_hours');
        (new AuditLogRepository($db))->deleteExpired($protocolRetentionHours);

        return $deleted;
    }

    public static function countExpired(PDO $db): int
    {
        $retentionHours = (int) Config::get('default_absence_retention_hours', 48);

        return (new AbsenceRepository($db))->countExpired($retentionHours);
    }
}
