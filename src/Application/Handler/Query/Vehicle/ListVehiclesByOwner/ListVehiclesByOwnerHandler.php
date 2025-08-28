<?php

namespace App\Application\Handler\Query\Vehicle\ListVehiclesByOwner;

use App\Domain\Vehicle\Repository\VehicleRepositoryInterface;
use App\Application\Query\Vehicle\ListVehiclesByOwner\ListVehiclesByOwnerQuery;
use App\Domain\User\Repository\UserRepositoryInterface;

final class ListVehiclesByOwnerHandler {
    public function __construct(
        private readonly VehicleRepositoryInterface $vehicles,
        private readonly UserRepositoryInterface $users
    ) {}
    public function __invoke(ListVehiclesByOwnerQuery $q): array {
        $owner = $this->users->findByIdUser((string)$q->ownerId);
        if (!$owner) {
            throw new \DomainException('Owner not found');
        }
        return $this->vehicles->listByOwner($owner, $q->page, $q->perPage);
    }
}
