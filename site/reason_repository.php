<?php
declare(strict_types=1);

namespace AbsenceApp;

use PDO;

/**
 * Repository for managing absence reasons / destinations.
 *
 * Reasons are only templates for new absences. The selected/free text is
 * copied into absences.reason when an absence starts.
 */
final class ReasonRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    /**
     * Returns reasons in the staff-defined order.
     *
     * @return array<int,array{id:int,name:string,sort_order:int}>
     */
    public function listAll(): array
    {
        $stmt = $this->db->query(
            'SELECT id, name, sort_order
             FROM reasons
             ORDER BY sort_order ASC, id ASC'
        );

        return $stmt->fetchAll();
    }

    /**
     * Compatibility alias.
     *
     * @return array<int,array{id:int,name:string,sort_order:int}>
     */
    public function all(): array
    {
        return $this->listAll();
    }

    public function asText(): string
    {
        $names = array_map(
            static fn (array $row): string => (string) $row['name'],
            $this->listAll()
        );

        return implode("\n", $names);
    }

    public function replaceFromText(string $text): void
    {
        $lines = preg_split('/\R/u', $text) ?: [];
        $names = [];
        $seen = [];

        foreach ($lines as $line) {
            $name = trim((string) $line);

            if ($name === '') {
                continue;
            }

            $key = strtolower($name);

            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $names[] = $name;
        }

        $this->db->beginTransaction();

        try {
            $this->db->exec('DELETE FROM reasons');
            $stmt = $this->db->prepare('INSERT INTO reasons (name, sort_order) VALUES (:name, :sort_order)');

            foreach ($names as $index => $name) {
                $stmt->execute([
                    'name' => $name,
                    'sort_order' => $index + 1,
                ]);
            }

            $this->db->commit();
        } catch (\Throwable $exception) {
            $this->db->rollBack();
            throw $exception;
        }
    }
}
