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
        $owner = $this->createUser('owner@example.com');

        $v = new Vehicle(
            'Truck A',
            '1234-ABC',
            150_000,
            MaintenanceType::B,
            new \DateTimeImmutable('2025-01-15'),
            $owner
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
        $this->assertSame($owner->getIdAsString(), $result['ownerId']);
    }

    private function createUser(string $email): \App\Domain\User\Entity\User
    {
        $user = new \App\Domain\User\Entity\User();
        $reflection = new \ReflectionClass($user);
        $idProp = $reflection->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($user, \Symfony\Component\Uid\Uuid::v4());
        $emailProp = $reflection->getProperty('email');
        $emailProp->setAccessible(true);
        $emailProp->setValue($user, $email);
        $passwordProp = $reflection->getProperty('password');
        $passwordProp->setAccessible(true);
        $passwordProp->setValue($user, 'test');
        return $user;
    }
}