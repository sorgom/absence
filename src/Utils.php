<?php
declare(strict_types=1);

namespace AbsenceApp;

/** General helper methods for safe output and redirects. */
final class Utils
{
    public static function h(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
