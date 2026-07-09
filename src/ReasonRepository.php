<?php
declare(strict_types=1);
namespace AbsenceApp;
use PDO;
final class ReasonRepository
{
    public function __construct(private readonly PDO $db) {}
    public function all(): array
    {
        return $this->db->query('SELECT id, name FROM reasons ORDER BY name')->fetchAll();
    }
}
