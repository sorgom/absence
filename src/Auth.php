<?php
declare(strict_types=1);

namespace AbsenceApp;

use PDO;

/** Handles login, logout and the current authenticated user. */
final class Auth
{
    public const ROLE_PATIENT = 'patient';
    public const ROLE_STAFF = 'staff';

    public function __construct(private readonly PDO $db) {}

    public function login(string $role, string $id, string $password): bool
    {
        $table = $this->tableForRole($role);
        $statement = $this->db->prepare("SELECT id, password_hash, first_login FROM {$table} WHERE id = :id");
        $statement->execute(['id' => $id]);
        $user = $statement->fetch();
        if (!$user || !password_verify($password, (string)$user['password_hash'])) {
            return false;
        }
        Session::regenerate();
        Session::set('user_id', (string)$user['id']);
        Session::set('role', $role);
        Session::set('first_login', (bool)$user['first_login']);
        return true;
    }

    public function logout(): void { Session::destroy(); }
    public function isLoggedIn(): bool { return Session::has('user_id') && Session::has('role'); }
    public function currentUserId(): ?string { return Session::get('user_id'); }
    public function currentRole(): ?string { return Session::get('role'); }
    public function isFirstLogin(): bool { return (bool)Session::get('first_login', false); }

    private function tableForRole(string $role): string
    {
        return match ($role) {
            self::ROLE_PATIENT => 'patients',
            self::ROLE_STAFF => 'staff',
            default => throw new \InvalidArgumentException('Invalid role.'),
        };
    }
}
