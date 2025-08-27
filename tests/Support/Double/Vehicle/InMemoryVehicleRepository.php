<?php

namespace App\Tests\Support\Double\Vehicle;

use App\Domain\Vehicle\Entity\Vehicle;
use App\Domain\Vehicle\Repository\VehicleRepositoryInterface;
use Symfony\Component\Uid\Ulid;

final class InMemoryVehicleRepository implements VehicleRepositoryInterface
{
    /** @var array<string, Vehicle> */
    private array $items = [];

    public function findById(Ulid $id): ?Vehicle
    {
        return $this->items[(string) $id] ?? null;
    }

    public function save(Vehicle $vehicle): void
    {
        $this->items[(string) $vehicle->id()] = $vehicle;
    }

    public function listByOwner(Ulid $ownerId, int $page = 1, int $perPage = 25): array
    {
        $filtered = array_filter(
            $this->items,
            fn (Vehicle $v) => (string) $v->ownerId() === (string) $ownerId
        );
        $filtered = array_values($filtered);
        usort($filtered, fn (Vehicle $a, Vehicle $b) => $a->plate() <=> $b->plate());
        $offset = max(0, ($page - 1) * $perPage);
        return array_slice($filtered, $offset, $perPage);
    }

    public function remove(Vehicle $vehicle): void
    {
        unset($this->items[(string) $vehicle->id()]);
    }
}