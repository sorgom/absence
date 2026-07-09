<?php
declare(strict_types=1);
namespace AbsenceApp;
use PDO;
final class Auth
{
    public const ROLE_PATIENT = 'patient';
    public const ROLE_STAFF = 'staff';
    public function __construct(private readonly PDO $db) {}
    public function login(string $role, string $id, string $password): bool
    {
        $table = $this->tableForRole($role);
        $stmt = $this->db->prepare("SELECT id, password_hash, first_login FROM {$table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($password, (string)$user['password_hash'])) { return false; }
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
    public function requireRole(string $role): void
    {
        if (!$this->isLoggedIn() || Session::get('role') !== $role) {
            Utils::redirect($role === self::ROLE_STAFF ? '/personal.php' : '/index.php');
        }
    }
    public function changePassword(string $oldPassword, string $newPassword, string $repeatPassword): ?string
    {
        $role = (string)$this->currentRole(); $userId = (string)$this->currentUserId();
        if ($userId === '' || $role === '') { return 'Nicht angemeldet.'; }
        if ($newPassword === '' || $newPassword !== $repeatPassword) { return 'Die neuen Passwörter stimmen nicht überein.'; }
        $table = $this->tableForRole($role);
        $stmt = $this->db->prepare("SELECT password_hash FROM {$table} WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $row = $stmt->fetch();
        if (!$row || !password_verify($oldPassword, (string)$row['password_hash'])) { return 'Das alte Passwort ist falsch.'; }
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE {$table} SET password_hash = :hash, first_login = 0 WHERE id = :id");
        $stmt->execute(['hash' => $hash, 'id' => $userId]);
        Session::set('first_login', false);
        return null;
    }
    private function tableForRole(string $role): string
    {
        return match ($role) { self::ROLE_PATIENT => 'patients', self::ROLE_STAFF => 'staff', default => throw new \InvalidArgumentException('Invalid role.') };
    }
}
