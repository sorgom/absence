<?php
declare(strict_types=1);

namespace AbsenceApp;

/**
 * Provides a small CSRF token mechanism for all state-changing forms.
 */
final class Csrf
{
    private const SESSION_KEY = 'csrf_token';

    /**
     * Returns the current CSRF token or creates a new one.
     */
    public static function token(): string
    {
        Session::start();

        $token = Session::get(self::SESSION_KEY);

        if (!is_string($token) || $token === '') {
            $token = bin2hex(random_bytes(32));
            Session::set(self::SESSION_KEY, $token);
        }

        return $token;
    }

    /**
     * Returns a hidden input field for forms.
     */
    public static function field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . Utils::h(self::token()) . '">';
    }

    /**
     * Validates a submitted CSRF token.
     */
    public static function validate(?string $submittedToken): bool
    {
        Session::start();

        $token = Session::get(self::SESSION_KEY);

        return is_string($token)
            && is_string($submittedToken)
            && hash_equals($token, $submittedToken);
    }

    /**
     * Validates a submitted token and throws on failure.
     */
    public static function requireValid(?string $submittedToken): void
    {
        if (!self::validate($submittedToken)) {
            throw new \RuntimeException('Ungültige Formularsitzung. Bitte erneut versuchen.');
        }
    }
}
