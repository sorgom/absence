<?php
declare(strict_types=1);

namespace AbsenceApp;

use PDO;

/**
 * Repository for creating, closing and listing absences.
 */
final class AbsenceRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    /**
     * Returns the currently active absence for one person, if any.
     *
     * @return array<string,mixed>|null
     */
    public function activeForPerson(string $personId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT a.id, a.person_id, a.reason_id, r.name AS reason_name, a.departure_time, a.return_time
             FROM absences a
             JOIN reasons r ON r.id = a.reason_id
             WHERE a.person_id = :person_id AND a.return_time IS NULL
             ORDER BY a.departure_time DESC
             LIMIT 1'
        );
        $stmt->execute(['person_id' => $personId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    /**
     * Compatibility alias.
     *
     * @return array<string,mixed>|null
     */
    public function activeForPatient(string $patientId): ?array
    {
        return $this->activeForPerson($patientId);
    }

    /**
     * Starts a new absence for a person.
     */
    public function start(string $personId, int $reasonId): void
    {
        if ($this->activeForPerson($personId) !== null) {
            throw new \RuntimeException('Es gibt bereits eine aktive Abwesenheit.');
        }

        $stmt = $this->db->prepare(
            'INSERT INTO absences (person_id, reason_id, departure_time)
             VALUES (:person_id, :reason_id, CURRENT_TIMESTAMP)'
        );
        $stmt->execute([
            'person_id' => $personId,
            'reason_id' => $reasonId,
        ]);
    }

    /**
     * Ends the active absence for a person.
     */
    public function end(string $personId): void
    {
        $active = $this->activeForPerson($personId);

        if ($active === null) {
            throw new \RuntimeException('Es gibt keine aktive Abwesenheit.');
        }

        $stmt = $this->db->prepare('UPDATE absences SET return_time = CURRENT_TIMESTAMP WHERE id = :id');
        $stmt->execute(['id' => $active['id']]);
    }

    /**
     * Lists absences for staff overview.
     *
     * @return array<int,array<string,mixed>>
     */
    public function listForStaff(bool $activeOnly = true, string $sort = 'departure', string $order = 'asc'): array
    {
        $where = $activeOnly ? 'WHERE a.return_time IS NULL' : '';

        $direction = $order === 'desc' ? 'DESC' : 'ASC';

        $orderBy = match ($sort) {
            'patient' => "a.person_id COLLATE NOCASE {$direction}, a.departure_time DESC",
            default => "a.departure_time {$direction}, a.person_id COLLATE NOCASE ASC",
        };

        $sql = "SELECT
                    a.id,
                    a.person_id,
                    a.person_id AS patient_id,
                    p.is_staff,
                    r.name AS reason_name,
                    a.departure_time,
                    a.return_time
                FROM absences a
                JOIN persons p ON p.id = a.person_id
                JOIN reasons r ON r.id = a.reason_id
                {$where}
                ORDER BY {$orderBy}";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }
}
