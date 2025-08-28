<?php

namespace App\Domain\Vehicle\Repository;

use App\Domain\Vehicle\Entity\Vehicle;
use Symfony\Component\Uid\Ulid;
use App\Domain\User\Entity\User;

interface VehicleRepositoryInterface
{
    public function findById(Ulid $id): ?Vehicle;
    public function save(Vehicle $vehicle): void;
    public function listByOwner(User $owner, int $page = 1, int $perPage = 25): array;
    public function remove(Vehicle $vehicle): void;
}
