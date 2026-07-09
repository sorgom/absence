<?php
declare(strict_types=1);
namespace AbsenceApp;
final class Utils
{
    public static function h(string $value): string { return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
    public static function redirect(string $target): never { header('Location: ' . $target); exit; }

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

