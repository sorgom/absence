<?php
declare(strict_types=1);
namespace AbsenceApp;
final class Utils
{
    public static function h(string $value): string { return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
    public static function redirect(string $target): never { header('Location: ' . $target); exit; }
}
