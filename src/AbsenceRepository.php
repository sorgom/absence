<?php
declare(strict_types=1);
namespace AbsenceApp;
use PDO;
final class AbsenceRepository
{
    public function __construct(private readonly PDO $db) {}
    public function activeForPatient(string $patientId): ?array
    {
        $stmt = $this->db->prepare('SELECT a.id, a.patient_id, a.reason_id, r.name AS reason_name, a.departure_time, a.return_time FROM absences a JOIN reasons r ON r.id = a.reason_id WHERE a.patient_id = :patient_id AND a.return_time IS NULL ORDER BY a.departure_time DESC LIMIT 1');
        $stmt->execute(['patient_id' => $patientId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
    public function start(string $patientId, int $reasonId): void
    {
        if ($this->activeForPatient($patientId) !== null) { throw new \RuntimeException('There is already an active absence.'); }
        $stmt = $this->db->prepare('INSERT INTO absences (patient_id, reason_id, departure_time) VALUES (:patient_id, :reason_id, CURRENT_TIMESTAMP)');
        $stmt->execute(['patient_id' => $patientId, 'reason_id' => $reasonId]);
    }
    public function end(string $patientId): void
    {
        $active = $this->activeForPatient($patientId);
        if ($active === null) { throw new \RuntimeException('There is no active absence.'); }
        $stmt = $this->db->prepare('UPDATE absences SET return_time = CURRENT_TIMESTAMP WHERE id = :id');
        $stmt->execute(['id' => $active['id']]);
    }
}
