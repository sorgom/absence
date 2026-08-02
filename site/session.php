<?php
declare(strict_types=1);
namespace AbsenceApp;
final class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => $isHttps,
                'httponly' => true,
                'samesite' => 'Strict',
            ]);

            session_name((string) Config::get('session_name'));
            session_start();
        }
    }
    public static function regenerate(): void { self::start(); session_regenerate_id(true); }
    public static function destroy(): void
    {
        self::start(); $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], (bool)$p['secure'], (bool)$p['httponly']);
        }
        session_destroy();
    }
    public static function set(string $key, mixed $value): void { self::start(); $_SESSION[$key] = $value; }
    public static function get(string $key, mixed $default = null): mixed { self::start(); return $_SESSION[$key] ?? $default; }
    public static function has(string $key): bool { self::start(); return array_key_exists($key, $_SESSION); }
}
