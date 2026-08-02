<?php
declare(strict_types=1);

namespace AbsenceApp;

use PDO;

/**
 * Handles password changes for persons.
 */
final class PasswordService
{
    public function __construct(private readonly PDO $db)
    {
    }

    /**
     * Changes a password without asking for the old password.
     */
    public function changePassword(
        string $role,
        string $userId,
        string $oldPassword,
        string $newPassword,
        string $repeatPassword
    ): void {
        $this->changePasswordForPerson($userId, $newPassword, $repeatPassword, $role);
    }

    public function changePasswordForPerson(
        string $userId,
        string $newPassword,
        string $repeatPassword,
        string $role
    ): void {
        if ($userId === '') {
            throw new \InvalidArgumentException('Nicht angemeldet.');
        }

        $minLength = self::minLengthForRole($role);

        if (strlen($newPassword) < $minLength) {
            throw new \InvalidArgumentException(
                "Das neue Passwort muss mindestens {$minLength} Zeichen lang sein."
            );
        }

        if ($newPassword !== $repeatPassword) {
            throw new \InvalidArgumentException('Die neuen Passwörter stimmen nicht überein.');
        }

        $stmt = $this->db->prepare(
            'UPDATE persons
             SET password_hash = :password_hash, first_login = 0
             WHERE id = :id'
        );
        $stmt->execute([
            'id' => $userId,
            'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
        ]);

        if ($stmt->rowCount() < 1) {
            throw new \RuntimeException('Person wurde nicht gefunden.');
        }

        Session::set('first_login', false);
    }

    public static function minLengthForRole(string $role): int
    {
        return $role === Auth::ROLE_STAFF
            ? (int) Config::get('password_min_length_staff')
            : (int) Config::get('password_min_length_patient');
    }
}
