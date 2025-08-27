<?php

namespace App\Tests\Unit\Query\Vehicle\GetVehicle;

use App\Application\Handler\Query\Vehicle\GetVehicle\GetVehicleHandler;
use App\Application\Query\Vehicle\GetVehicle\GetVehicleQuery;
use App\Domain\Vehicle\Entity\MaintenanceType;
use App\Domain\Vehicle\Entity\Vehicle;
use App\Tests\Support\Double\Vehicle\InMemoryVehicleRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Ulid;

final class GetVehicleHandlerTest extends TestCase
{
    public function test_it_returns_vehicle_data(): void
    {
        $repo = new InMemoryVehicleRepository();
        $ownerId = new Ulid();

        $v = new Vehicle(
            'Truck A',
            '1234-ABC',
            150_000,
            MaintenanceType::B,
            new \DateTimeImmutable('2025-01-15'),
            $ownerId
        );
        $repo->save($v);

        $handler = new GetVehicleHandler($repo);
        $result = ($handler)(new GetVehicleQuery($v->id()));

        $this->assertSame('Truck A', $result['name']);
        $this->assertSame('1234-ABC', $result['plate']);
        $this->assertSame((string) $v->id(), $result['id']);
        $this->assertSame(150_000, $result['odometerKm']);
        $this->assertSame('B', $result['lastMaintenanceType']);
        $this->assertSame(170_000, $result['nextMaintenanceDueKm']); // 150k + 20k
        $this->assertSame((string) $ownerId, $result['ownerId']);
    }
}