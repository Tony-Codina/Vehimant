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
            'id' => (string) $vehicle->getId(),
            'name' => $vehicle->getName(),
            'plate' => $vehicle->getPlate(),
            'createdAt' => $vehicle->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'odometerKm' => $vehicle->getOdometerKm(),
            'lastMaintenanceType' => $vehicle->getLastMaintenanceType()->value,
            'lastMaintenanceAt' => $vehicle->getLastMaintenanceAt()->format(\DateTimeInterface::ATOM),
            'nextMaintenanceDueKm' => $vehicle->getNextMaintenanceDueKm(),
            'ownerId' => (string) $vehicle->getOwner()->getId(),
        ];
    }
}