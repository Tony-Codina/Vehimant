<?php

namespace App\Application\Query\Vehicle\GetVehicle;

use Symfony\Component\Uid\Ulid;

final class GetVehicleQuery
{
    public function __construct(private readonly Ulid $id) {}

    public function id(): Ulid
    {
        return $this->id;
    }
}
