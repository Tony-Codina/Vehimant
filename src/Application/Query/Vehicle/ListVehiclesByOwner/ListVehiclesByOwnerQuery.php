<?php

namespace App\Application\Query\Vehicle\ListVehiclesByOwner;

use Symfony\Component\Uid\Ulid;

final class ListVehiclesByOwnerQuery {
    public function __construct(
        public readonly Ulid $ownerId,
        public readonly int $page = 1,
        public readonly int $perPage = 25
    ) {}
}
