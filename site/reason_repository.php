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
     * Returns active reasons for selection and management.
     *
     * @return array<int,array{id:int,name:string,deleted:int}>
     */
    public function listAll(): array
    {
        $stmt = $this->db->query(
            'SELECT id, name, deleted
             FROM reasons
             WHERE deleted = 0
             ORDER BY name COLLATE NOCASE ASC'
        );

        return $stmt->fetchAll();
    }

    /**
     * Compatibility alias.
     *
     * @return array<int,array{id:int,name:string,deleted:int}>
     */
    public function all(): array
    {
        return $this->listAll();
    }

    /**
     * Returns all reasons, including soft-deleted records.
     *
     * @return array<int,array{id:int,name:string,deleted:int}>
     */
    public function listIncludingDeleted(): array
    {
        $stmt = $this->db->query(
            'SELECT id, name, deleted
             FROM reasons
             ORDER BY deleted ASC, name COLLATE NOCASE ASC'
        );

        return $stmt->fetchAll();
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

        $stmt = $this->db->prepare('SELECT id, deleted FROM reasons WHERE lower(name) = lower(:name)');
        $stmt->execute(['name' => $name]);
        $existing = $stmt->fetch();

        if ($existing && (int) $existing['deleted'] === 0) {
            throw new \RuntimeException('Dieser Eintrag ist bereits vorhanden.');
        }

        if ($existing && (int) $existing['deleted'] === 1) {
            $restore = $this->db->prepare('UPDATE reasons SET deleted = 0 WHERE id = :id');
            $restore->execute(['id' => (int) $existing['id']]);
            return;
        }

        $stmt = $this->db->prepare('INSERT INTO reasons (name, deleted) VALUES (:name, 0)');
        $stmt->execute(['name' => $name]);
    }

    /**
     * Soft-deletes one reason. Existing absences keep showing the old name.
     */
    public function delete(int $id): void
    {
        if ($id < 1) {
            throw new \InvalidArgumentException('Kein Eintrag ausgewählt.');
        }

        $stmt = $this->db->prepare('UPDATE reasons SET deleted = 1 WHERE id = :id');
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() < 1) {
            throw new \RuntimeException('Eintrag wurde nicht gefunden.');
        }

        $this->garbageCollectDeleted();
    }

    /**
     * Permanently removes deleted reasons that are no longer referenced.
     */
    public function garbageCollectDeleted(): int
    {
        $stmt = $this->db->prepare(
            'DELETE FROM reasons
             WHERE deleted = 1
               AND id NOT IN (SELECT DISTINCT reason_id FROM absences)'
        );
        $stmt->execute();

        return $stmt->rowCount();
    }
}
