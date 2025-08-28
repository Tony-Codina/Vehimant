<?php

namespace App\Application\Query\Vehicle\ListVehiclesByOwner;

use Symfony\Component\Uid\Uuid;

final class ListVehiclesByOwnerQuery {
    public function __construct(
        public readonly Uuid $ownerId,
        public readonly int $page = 1,
        public readonly int $perPage = 25
    ) {}
}
