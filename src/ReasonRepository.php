<?php
declare(strict_types=1);

namespace AbsenceApp;

use PDO;

/**
 * Repository for managing absence reasons / destinations.
 */
final class ReasonRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    /**
     * Returns all reasons sorted alphabetically.
     *
     * @return array<int,array{id:int,name:string}>
     */
    public function listAll(): array
    {
        $stmt = $this->db->query('SELECT id, name FROM reasons ORDER BY name COLLATE NOCASE ASC');

        return $stmt->fetchAll();
    }

    /**
     * Compatibility alias for older patient workflow code.
     *
     * @return array<int,array{id:int,name:string}>
     */
    public function all(): array
    {
        return $this->listAll();
    }

    /**
     * Adds a new reason / destination.
     */
    public function add(string $name): void
    {
        $name = trim($name);

        if ($name === '') {
            throw new \InvalidArgumentException('Grund / Ziel darf nicht leer sein.');
        }

        $stmt = $this->db->prepare('SELECT COUNT(*) FROM reasons WHERE lower(name) = lower(:name)');
        $stmt->execute(['name' => $name]);

        if ((int) $stmt->fetchColumn() > 0) {
            throw new \RuntimeException('Dieser Eintrag ist bereits vorhanden.');
        }

        $stmt = $this->db->prepare('INSERT INTO reasons (name) VALUES (:name)');
        $stmt->execute(['name' => $name]);
    }

    /**
     * Deletes one reason if it is not referenced by an absence.
     */
    public function delete(int $id): void
    {
        if ($id < 1) {
            throw new \InvalidArgumentException('Kein Eintrag ausgewählt.');
        }

        $stmt = $this->db->prepare('SELECT COUNT(*) FROM absences WHERE reason_id = :id');
        $stmt->execute(['id' => $id]);

        if ((int) $stmt->fetchColumn() > 0) {
            throw new \RuntimeException('Dieser Eintrag wird bereits von Abwesenheiten verwendet und kann nicht gelöscht werden.');
        }

        $stmt = $this->db->prepare('DELETE FROM reasons WHERE id = :id');
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() < 1) {
            throw new \RuntimeException('Eintrag wurde nicht gefunden.');
        }
    }
}
