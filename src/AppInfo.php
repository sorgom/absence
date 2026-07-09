<?php
declare(strict_types=1);

namespace AbsenceApp;

/**
 * Provides application metadata such as the currently packaged version.
 */
final class AppInfo
{
    /**
     * Returns the application version from the repository-level VERSION file.
     */
    public static function version(): string
    {
        $versionFile = dirname(__DIR__) . '/VERSION';

        if (!is_file($versionFile)) {
            return 'dev';
        }

        $version = trim((string) file_get_contents($versionFile));

        return $version !== '' ? $version : 'dev';
    }
}
