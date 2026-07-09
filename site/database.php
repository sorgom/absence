<?php
declare(strict_types=1);
namespace AbsenceApp;
use PDO;
final class Database
{
    private static ?PDO $connection = null;
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $databasePath = (string) Config::get('database_path');
            $databaseDirectory = dirname($databasePath);
            if (!is_dir($databaseDirectory)) { mkdir($databaseDirectory, 0775, true); }
            self::$connection = new PDO('sqlite:' . $databasePath);
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            self::$connection->exec('PRAGMA foreign_keys = ON;');
        }
        return self::$connection;
    }
}
