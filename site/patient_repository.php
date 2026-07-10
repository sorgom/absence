<?php
declare(strict_types=1);

namespace AbsenceApp;

use PDO;

/**
 * Compatibility adapter for patient management on top of persons.
 */
final class PatientRepository
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
        return $this->persons->listIds(false);
    }

    public function exists(string $id): bool
    {
        return $this->persons->exists($id);
    }

    public function create(string $id): string
    {
        return $this->persons->create($id, false);
    }

    public function delete(string $id): void
    {
        $this->persons->delete($id);
    }
}
