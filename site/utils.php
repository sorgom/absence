<?php
declare(strict_types=1);
namespace AbsenceApp;
final class Utils
{
    public static function h(string $value): string { return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
    public static function redirect(string $target): never { header('Location: ' . $target); exit; }

    /**
     * Returns a message safe to display to the user for a caught exception.
     *
     * InvalidArgumentException and RuntimeException are thrown deliberately
     * throughout this app with German, user-facing text - those are safe to
     * show as-is. Anything else (TypeError, PDOException, ...) is an
     * unexpected failure whose message could contain internal details, so it
     * is logged server-side and a generic message is shown instead.
     */
    public static function safeMessage(\Throwable $exception): string
    {
        $isDeliberateAppException = !($exception instanceof \PDOException)
            && ($exception instanceof \InvalidArgumentException || $exception instanceof \RuntimeException);

        if ($isDeliberateAppException) {
            return $exception->getMessage();
        }

        self::logUnexpected($exception);

        return 'Es ist ein unerwarteter Fehler aufgetreten. Bitte versuchen Sie es erneut.';
    }

    /**
     * Logs an unexpected exception both via error_log() (so it shows up in
     * the server's normal PHP error log if that is configured) and, as a
     * fallback that does not depend on the hoster's php.ini, appends it to
     * a plain-text file inside the HTTP-blocked data/ directory. That file
     * is not rotated automatically - delete or trim it occasionally.
     */
    private static function logUnexpected(\Throwable $exception): void
    {
        $line = sprintf(
            '[%s] %s: %s in %s:%d',
            date('Y-m-d H:i:s'),
            $exception::class,
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );

        error_log($line);

        try {
            $logPath = dirname((string) Config::get('database_path')) . '/app_errors.log';
            file_put_contents($logPath, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
        } catch (\Throwable) {
            // Logging must never break the request; silently ignore.
        }
    }

    /**
     * Converts a SQLite UTC timestamp to an ISO-8601 UTC timestamp.
     */
    public static function utcIsoDateTime(?string $value): string
    {
        if ($value === null || trim($value) === '') {
            return '';
        }

        try {
            $utc = new \DateTimeImmutable($value, new \DateTimeZone('UTC'));

            return $utc->format('Y-m-d\TH:i:s\Z');
        } catch (\Throwable) {
            return '';
        }
    }

    /**
     * Formats a UTC database timestamp as a server-side fallback.
     *
     * JavaScript will replace the visible text with the browser's local
     * date/time representation. This fallback is used when JavaScript is
     * disabled or not yet loaded.
     */
    public static function fallbackDateTime(?string $value): string
    {
        if ($value === null || trim($value) === '') {
            return '';
        }

        try {
            $utc = new \DateTimeImmutable($value, new \DateTimeZone('UTC'));

            return $utc->format((string) Config::get('datetime_display_format')) . ' UTC';
        } catch (\Throwable) {
            return $value;
        }
    }

    /**
     * Returns a safe <time> element that is converted to client-local time
     * by public/app.js.
     */
    public static function localTimeElement(?string $value): string
    {
        $iso = self::utcIsoDateTime($value);

        if ($iso === '') {
            return '';
        }

        return '<time datetime="' . self::h($iso) . '" data-local-time>'
            . self::h(self::fallbackDateTime($value))
            . '</time>';
    }

}

