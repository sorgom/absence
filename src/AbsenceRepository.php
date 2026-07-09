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
     * Returns the currently active absence for one patient, if any.
     *
     * @return array<string,mixed>|null
     */
    public function activeForPatient(string $patientId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT a.id, a.patient_id, a.reason_id, r.name AS reason_name, a.departure_time, a.return_time
             FROM absences a
             JOIN reasons r ON r.id = a.reason_id
             WHERE a.patient_id = :patient_id AND a.return_time IS NULL
             ORDER BY a.departure_time DESC
             LIMIT 1'
        );
        $stmt->execute(['patient_id' => $patientId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    /**
     * Starts a new absence for a patient.
     */
    public function start(string $patientId, int $reasonId): void
    {
        if ($this->activeForPatient($patientId) !== null) {
            throw new \RuntimeException('There is already an active absence.');
        }

        $stmt = $this->db->prepare(
            'INSERT INTO absences (patient_id, reason_id, departure_time)
             VALUES (:patient_id, :reason_id, CURRENT_TIMESTAMP)'
        );
        $stmt->execute([
            'patient_id' => $patientId,
            'reason_id' => $reasonId,
        ]);
    }

    /**
     * Ends the active absence for a patient.
     */
    public function end(string $patientId): void
    {
        $active = $this->activeForPatient($patientId);

        if ($active === null) {
            throw new \RuntimeException('There is no active absence.');
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
            'patient' => "a.patient_id COLLATE NOCASE {$direction}, a.departure_time DESC",
            default => "a.departure_time {$direction}, a.patient_id COLLATE NOCASE ASC",
        };

        $sql = "SELECT
                    a.id,
                    a.patient_id,
                    r.name AS reason_name,
                    a.departure_time,
                    a.return_time
                FROM absences a
                JOIN reasons r ON r.id = a.reason_id
                {$where}
                ORDER BY {$orderBy}";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }
}
