<?php
declare(strict_types=1);

namespace AbsenceApp;

use PDO;

final class Auth
{
    public const ROLE_PATIENT = 'patient';
    public const ROLE_STAFF = 'staff';

    public function __construct(private readonly PDO $db)
    {
    }

    /**
     * Unified login for all persons.
     *
     * The persons.is_staff flag determines the session role and target area.
     */
    public function login(string $id, string $password): bool
    {
        $id = trim($id);
        $limiter = new RateLimiter($this->db);

        if ($limiter->isLocked($id)) {
            throw new \RuntimeException(
                'Zu viele fehlgeschlagene Anmeldeversuche. Bitte versuchen Sie es in einigen Minuten erneut.'
            );
        }

        $stmt = $this->db->prepare(
            'SELECT id, password_hash, is_staff, first_login
             FROM persons
             WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, (string) $user['password_hash'])) {
            $limiter->recordFailure($id);

            return false;
        }

        $limiter->clearFailures($id);

        $role = ((int) $user['is_staff']) === 1 ? self::ROLE_STAFF : self::ROLE_PATIENT;

        Session::regenerate();
        Session::set('user_id', (string) $user['id']);
        Session::set('role', $role);
        Session::set('is_staff', $role === self::ROLE_STAFF);
        Session::set('first_login', (bool) $user['first_login']);

        return true;
    }

    /**
     * Compatibility wrapper for older call sites.
     */
    public function loginWithRole(string $role, string $id, string $password): bool
    {
        if (!$this->login($id, $password)) {
            return false;
        }

        if ($this->currentRole() !== $role) {
            $this->logout();
            return false;
        }

        return true;
    }

    public function logout(): void
    {
        Session::destroy();
    }

    public function isLoggedIn(): bool
    {
        if (!Session::has('user_id') || !Session::has('role')) {
            return false;
        }

        if (!$this->currentAccountExists()) {
            // The account behind this session (e.g. deleted by staff while
            // the person was still logged in) no longer exists. Terminate
            // the stale session instead of letting later queries fail with
            // a raw foreign-key/integrity error.
            $this->logout();

            return false;
        }

        return true;
    }

    private function currentAccountExists(): bool
    {
        $id = Session::get('user_id');

        if ($id === null || $id === '') {
            return false;
        }

        $stmt = $this->db->prepare('SELECT COUNT(*) FROM persons WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return ((int) $stmt->fetchColumn()) > 0;
    }

    public function currentUserId(): ?string
    {
        return Session::get('user_id');
    }

    public function currentRole(): ?string
    {
        return Session::get('role');
    }

    public function isStaff(): bool
    {
        return Session::get('role') === self::ROLE_STAFF;
    }

    public function isFirstLogin(): bool
    {
        return (bool) Session::get('first_login', false);
    }

    public function requireRole(string $role): void
    {
        if (!$this->isLoggedIn()) {
            Utils::redirect('/index.php');
        }

        if (Session::get('role') !== $role) {
            Utils::redirect($role === self::ROLE_STAFF ? '/personal.php' : '/index.php');
        }
    }
}
