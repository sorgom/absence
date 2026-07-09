<?php
declare(strict_types=1);

namespace AbsenceApp;

/** Provides central read access to application configuration values. */
final class Config
{
    /** @var array<string,mixed>|null */
    private static ?array $values = null;

    public static function get(string $key): mixed
    {
        if (self::$values === null) {
            self::$values = require dirname(__DIR__) . '/config/config.php';
        }
        if (!array_key_exists($key, self::$values)) {
            throw new \RuntimeException('Unknown configuration key: ' . $key);
        }
        return self::$values[$key];
    }
}
