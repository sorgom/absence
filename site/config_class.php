<?php
declare(strict_types=1);
namespace AbsenceApp;
final class Config
{
    private static ?array $values = null;
    public static function get(string $key): mixed
    {
        if (self::$values === null) {
            self::$values = require __DIR__ . '/app_config.php';
        }
        if (!array_key_exists($key, self::$values)) {
            throw new \RuntimeException('Unknown configuration key: ' . $key);
        }
        return self::$values[$key];
    }
}
