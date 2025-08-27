<?php

namespace App\Application\Handler\Query\Vehicle\GetVehicle;

use App\Domain\Vehicle\Repository\VehicleRepositoryInterface;
use App\Application\Query\Vehicle\GetVehicle\GetVehicleQuery;
use Symfony\Component\Uid\Ulid;

final class GetVehicleHandler
{
    public function __construct(private readonly VehicleRepositoryInterface $vehicles) {}

    public function __invoke(GetVehicleQuery $query): array
    {
        $id = $query->id();
        $vehicle = $this->vehicles->findById($id);

        if (!$vehicle) {
            throw new \RuntimeException(sprintf('Vehicle %s not found', (string) $id));
        }

        return [
            'id' => (string) $vehicle->id(),
            'name' => $vehicle->name(),
            'plate' => $vehicle->plate(),
            'createdAt' => $vehicle->createdAt()->format(\DateTimeInterface::ATOM),
            'odometerKm' => $vehicle->odometerKm(),
            'lastMaintenanceType' => $vehicle->lastMaintenanceType()->value,
            'lastMaintenanceAt' => $vehicle->lastMaintenanceAt()->format(\DateTimeInterface::ATOM),
            'nextMaintenanceDueKm' => $vehicle->nextMaintenanceDueKm(),
            'ownerId' => (string) $vehicle->ownerId(),
        ];
    }
}