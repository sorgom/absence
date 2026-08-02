<?php
declare(strict_types=1);

namespace AbsenceApp;

use PDO;

/**
 * Database-backed brute-force protection for the login form.
 *
 * Failed login attempts are recorded per person ID. Once the configured
 * threshold is reached within the configured time window, further login
 * attempts for that ID are rejected until the window passes. This is
 * intentionally keyed on the submitted ID (not the client IP) because the
 * short numeric-PIN-style initial passwords used by this app are otherwise
 * trivially brute-forceable per account.
 */
final class RateLimiter
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function isLocked(string $identifier): bool
    {
        if ($identifier === '') {
            return false;
        }

        $maxAttempts = (int) Config::get('login_max_attempts');
        $windowMinutes = (int) Config::get('login_lockout_window_minutes');

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM login_attempts
             WHERE identifier = :identifier
               AND attempted_at >= datetime('now', :modifier)"
        );
        $stmt->execute([
            'identifier' => $identifier,
            'modifier' => '-' . $windowMinutes . ' minutes',
        ]);

        return ((int) $stmt->fetchColumn()) >= $maxAttempts;
    }

    public function recordFailure(string $identifier): void
    {
        if ($identifier === '') {
            return;
        }

        $stmt = $this->db->prepare(
            'INSERT INTO login_attempts (identifier, attempted_at) VALUES (:identifier, CURRENT_TIMESTAMP)'
        );
        $stmt->execute(['identifier' => $identifier]);
    }

    public function clearFailures(string $identifier): void
    {
        $stmt = $this->db->prepare('DELETE FROM login_attempts WHERE identifier = :identifier');
        $stmt->execute(['identifier' => $identifier]);
    }

    /**
     * Removes stale attempt records so the table does not grow unbounded.
     * Safe to call frequently; intended to be hooked into the existing
     * scheduled cleanup job.
     */
    public function purgeOlderThan(int $minutes): void
    {
        $stmt = $this->db->prepare(
            "DELETE FROM login_attempts WHERE attempted_at < datetime('now', :modifier)"
        );
        $stmt->execute(['modifier' => '-' . $minutes . ' minutes']);
    }
}
