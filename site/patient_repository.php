<?php
declare(strict_types=1);

namespace AbsenceApp;

use PDO;

/**
 * Repository for staff-side patient management.
 */
final class PatientRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    /**
     * Returns all patient IDs sorted alphabetically.
     *
     * @return array<int,string>
     */
    public function listIds(): array
    {
        $stmt = $this->db->query('SELECT id FROM patients ORDER BY id COLLATE NOCASE ASC');

        return array_map(
            static fn (array $row): string => (string) $row['id'],
            $stmt->fetchAll()
        );
    }

    /**
     * Checks whether a patient ID already exists.
     */
    public function exists(string $id): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM patients WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Creates a patient with a generated initial four-digit PIN.
     *
     * @return string Generated PIN, shown once to the staff member.
     */
    public function create(string $id): string
    {
        $id = trim($id);

        if ($id === '') {
            throw new \InvalidArgumentException('Die Patienten-ID darf nicht leer sein.');
        }

        if ($this->exists($id)) {
            throw new \RuntimeException('Diese Patienten-ID ist bereits vorhanden.');
        }

        $pin = $this->generatePin();
        $hash = password_hash($pin, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare(
            'INSERT INTO patients (id, password_hash, first_login) VALUES (:id, :password_hash, 1)'
        );
        $stmt->execute([
            'id' => $id,
            'password_hash' => $hash,
        ]);

        return $pin;
    }

    /**
     * Deletes one patient and, via foreign key cascade, the related absences.
     */
    public function delete(string $id): void
    {
        if ($id === '') {
            throw new \InvalidArgumentException('Keine Patienten-ID ausgewählt.');
        }

        $stmt = $this->db->prepare('DELETE FROM patients WHERE id = :id');
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() < 1) {
            throw new \RuntimeException('Patient wurde nicht gefunden.');
        }
    }

    /**
     * Generates a cryptographically secure four-digit PIN.
     */
    private function generatePin(): string
    {
        return str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    }
}
