<?php
declare(strict_types=1);

namespace AbsenceApp;

use PDO;

/**
 * Repository for staff-side staff member management.
 */
final class StaffRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    /**
     * Returns all staff IDs sorted alphabetically.
     *
     * @return array<int,string>
     */
    public function listIds(): array
    {
        $stmt = $this->db->query('SELECT id FROM staff ORDER BY id COLLATE NOCASE ASC');

        return array_map(
            static fn (array $row): string => (string) $row['id'],
            $stmt->fetchAll()
        );
    }

    /**
     * Checks whether a staff ID already exists.
     */
    public function exists(string $id): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM staff WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Creates a staff member with a generated initial password.
     *
     * @return string Generated password, shown once to the staff member.
     */
    public function create(string $id): string
    {
        $id = trim($id);

        if ($id === '') {
            throw new \InvalidArgumentException('Die Personal-ID darf nicht leer sein.');
        }

        if ($this->exists($id)) {
            throw new \RuntimeException('Diese Personal-ID ist bereits vorhanden.');
        }

        $password = $this->generatePassword();
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare(
            'INSERT INTO staff (id, password_hash, first_login) VALUES (:id, :password_hash, 1)'
        );
        $stmt->execute([
            'id' => $id,
            'password_hash' => $hash,
        ]);

        return $password;
    }

    /**
     * Deletes one staff member.
     */
    public function delete(string $id, string $currentUserId): void
    {
        if ($id === '') {
            throw new \InvalidArgumentException('Keine Personal-ID ausgewählt.');
        }

        if ($id === $currentUserId) {
            throw new \RuntimeException('Der eigene Personal-Account kann nicht gelöscht werden.');
        }

        $stmt = $this->db->prepare('DELETE FROM staff WHERE id = :id');
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() < 1) {
            throw new \RuntimeException('Personal-Member wurde nicht gefunden.');
        }
    }

    /**
     * Generates a readable random initial password.
     */
    private function generatePassword(): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';
        $length = 10;
        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $password .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        return $password;
    }
}
