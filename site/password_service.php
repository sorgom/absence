<?php
declare(strict_types=1);

namespace AbsenceApp;

use PDO;

/**
 * Handles password changes for patients and staff members.
 */
final class PasswordService
{
    public function __construct(private readonly PDO $db)
    {
    }

    /**
     * Changes the password for the selected role and user ID.
     */
    public function changePassword(
        string $role,
        string $userId,
        string $oldPassword,
        string $newPassword,
        string $repeatPassword
    ): void {
        if ($newPassword === '') {
            throw new \InvalidArgumentException('Das neue Passwort darf nicht leer sein.');
        }

        if ($newPassword !== $repeatPassword) {
            throw new \InvalidArgumentException('Die neuen Passwörter stimmen nicht überein.');
        }

        $table = $this->tableForRole($role);

        $stmt = $this->db->prepare("SELECT password_hash FROM {$table} WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $row = $stmt->fetch();

        if (!$row || !password_verify($oldPassword, (string) $row['password_hash'])) {
            throw new \RuntimeException('Das alte Passwort ist nicht korrekt.');
        }

        $stmt = $this->db->prepare(
            "UPDATE {$table}
             SET password_hash = :password_hash, first_login = 0
             WHERE id = :id"
        );
        $stmt->execute([
            'id' => $userId,
            'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
        ]);

        Session::set('first_login', false);
    }

    private function tableForRole(string $role): string
    {
        return match ($role) {
            Auth::ROLE_PATIENT => 'patients',
            Auth::ROLE_STAFF => 'staff',
            default => throw new \InvalidArgumentException('Ungültige Rolle.'),
        };
    }
}
