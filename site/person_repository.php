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

        $password = $this->generatePin();

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

    private function generatePin(): string
    {
        return str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    }

}
