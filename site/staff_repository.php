<?php
declare(strict_types=1);

namespace AbsenceApp;

use PDO;

/**
 * Compatibility adapter for staff management on top of persons.
 */
final class StaffRepository
{
    private PersonRepository $persons;

    public function __construct(private readonly PDO $db)
    {
        $this->persons = new PersonRepository($db);
    }

    /**
     * @return array<int,string>
     */
    public function listIds(): array
    {
        return $this->persons->listIds(true);
    }

    public function exists(string $id): bool
    {
        return $this->persons->exists($id);
    }

    public function create(string $id): string
    {
        return $this->persons->create($id, true);
    }

    public function delete(string $id, string $currentUserId): void
    {
        $this->persons->delete($id, $currentUserId);
    }
}
