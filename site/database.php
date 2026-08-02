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
            self::$connection->exec(
                'CREATE TABLE IF NOT EXISTS login_attempts (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    identifier TEXT NOT NULL,
                    attempted_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
                )'
            );
            self::$connection->exec(
                'CREATE INDEX IF NOT EXISTS idx_login_attempts_identifier
                 ON login_attempts (identifier, attempted_at)'
            );
        }
        return self::$connection;
    }
}
