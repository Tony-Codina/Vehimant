<?php

namespace App\Application\Handler\Query\Vehicle\ListVehiclesByOwner;

use App\Domain\Vehicle\Repository\VehicleRepositoryInterface;
use App\Application\Query\Vehicle\ListVehiclesByOwner\ListVehiclesByOwnerQuery;

final class ListVehiclesByOwnerHandler {
    public function __construct(private readonly VehicleRepositoryInterface $vehicles) {}
    public function __invoke(ListVehiclesByOwnerQuery $q): array {
        return $this->vehicles->listByOwner($q->ownerId, $q->page, $q->perPage);
    }
}
