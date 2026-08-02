<?php
declare(strict_types=1);

namespace AbsenceApp;

use PDO;

/**
 * Repository for the shared persons table.
 */
final class PersonRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    /**
     * @return array<int,string>
     */
    public function listIds(?bool $isStaff = null): array
    {
        if ($isStaff === null) {
            $stmt = $this->db->query('SELECT id FROM persons ORDER BY id COLLATE NOCASE ASC');
        } else {
            $stmt = $this->db->prepare('SELECT id FROM persons WHERE is_staff = :is_staff ORDER BY id COLLATE NOCASE ASC');
            $stmt->execute(['is_staff' => $isStaff ? 1 : 0]);
        }

        return array_map(
            static fn (array $row): string => (string) $row['id'],
            $stmt->fetchAll()
        );
    }


    /**
     * @return array<int,string>
     */
    public function listIdsExcept(?bool $isStaff, string $excludedId): array
    {
        $ids = $this->listIds($isStaff);

        return array_values(array_filter(
            $ids,
            static fn (string $id): bool => $id !== $excludedId
        ));
    }

    public function exists(string $id): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM persons WHERE id = :id');
        $stmt->execute(['id' => trim($id)]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function create(string $id, bool $isStaff): string
    {
        $id = trim($id);

        if ($id === '') {
            throw new \InvalidArgumentException('Die ID darf nicht leer sein.');
        }

        if ($this->exists($id)) {
            throw new \RuntimeException('Diese ID ist bereits vorhanden.');
        }

        $password = $this->generatePin($isStaff);

        $stmt = $this->db->prepare(
            'INSERT INTO persons (id, password_hash, is_staff, first_login)
             VALUES (:id, :password_hash, :is_staff, 1)'
        );
        $stmt->execute([
            'id' => $id,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'is_staff' => $isStaff ? 1 : 0,
        ]);

        return $password;
    }


    public function resetPassword(string $id): string
    {
        $id = trim($id);

        if ($id === '') {
            throw new \InvalidArgumentException('Keine ID ausgewählt.');
        }

        $stmt = $this->db->prepare('SELECT is_staff FROM persons WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $person = $stmt->fetch();

        if ($person === false) {
            throw new \RuntimeException('Person wurde nicht gefunden.');
        }

        $isStaff = ((int) $person['is_staff']) === 1;
        $password = $this->generatePin($isStaff);

        $stmt = $this->db->prepare(
            'UPDATE persons
             SET password_hash = :password_hash, first_login = 1
             WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        if ($stmt->rowCount() < 1) {
            throw new \RuntimeException('Person wurde nicht gefunden.');
        }

        return $password;
    }

    public function delete(string $id, ?string $currentUserId = null): void
    {
        $id = trim($id);

        if ($id === '') {
            throw new \InvalidArgumentException('Keine ID ausgewählt.');
        }

        if ($currentUserId !== null && $id === $currentUserId) {
            throw new \RuntimeException('Der eigene Account kann nicht gelöscht werden.');
        }

        $stmt = $this->db->prepare('DELETE FROM persons WHERE id = :id');
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() < 1) {
            throw new \RuntimeException('Person wurde nicht gefunden.');
        }
    }

    /**
     * Generates a random initial password for a new or reset account.
     *
     * Uses lowercase letters (excluding 'l', which is easily confused with
     * '1' or 'I' in some fonts/handwriting) plus digits 0-9, at the same
     * length as the role's minimum password length - so the generated
     * password already satisfies PasswordService's own policy.
     */
    private function generatePin(bool $isStaff): string
    {
        $alphabet = 'abcdefghijkmnopqrstuvwxyz0123456789';
        $length = PasswordService::minLengthForRole($isStaff ? Auth::ROLE_STAFF : Auth::ROLE_PATIENT);
        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $password .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        return $password;
    }

}
