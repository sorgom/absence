<?php
declare(strict_types=1);

namespace AbsenceApp;

use PDO;

/**
 * Repository for staff protocol entries.
 */
final class AuditLogRepository
{
    public const ACTION_PERSON_CREATED = 'Person angelegt';
    public const ACTION_PERSON_DELETED = 'Person gelöscht';
    public const ACTION_PASSWORD_RESET = 'Passwort zurückgesetzt';
    public const ACTION_REASONS_SAVED = 'Gründe / Ziele gespeichert';

    public function __construct(private readonly PDO $db)
    {
    }

    public function record(string $staffUserId, string $action, ?string $affectedElement = null): void
    {
        $staffUserId = trim($staffUserId);
        $action = trim($action);
        $affectedElement = $affectedElement === null ? null : trim($affectedElement);

        if ($staffUserId === '' || $action === '') {
            return;
        }

        $stmt = $this->db->prepare(
            'INSERT INTO audit_logs (staff_user_id, action, affected_element)
             VALUES (:staff_user_id, :action, :affected_element)'
        );
        $stmt->execute([
            'staff_user_id' => $staffUserId,
            'action' => $action,
            'affected_element' => $affectedElement === '' ? null : $affectedElement,
        ]);
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function listAll(): array
    {
        $stmt = $this->db->query(
            'SELECT id, created_at, staff_user_id, action, affected_element
             FROM audit_logs
             ORDER BY created_at DESC, id DESC'
        );

        return $stmt->fetchAll();
    }

    public function deleteExpired(int $retentionHours): int
    {
        $stmt = $this->db->prepare(
            "DELETE FROM audit_logs
             WHERE created_at < datetime('now', '-' || :hours || ' hours')"
        );
        $stmt->execute(['hours' => max(1, $retentionHours)]);

        return $stmt->rowCount();
    }
}
