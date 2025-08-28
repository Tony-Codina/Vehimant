<?php

namespace App\Tests\Unit\Query\Vehicle\ListVehiclesByOwner;

use App\Application\Handler\Query\Vehicle\ListVehiclesByOwner\ListVehiclesByOwnerHandler;
use App\Application\Query\Vehicle\ListVehiclesByOwner\ListVehiclesByOwnerQuery;
use App\Domain\Vehicle\Entity\Vehicle;
use App\Domain\Vehicle\Entity\MaintenanceType;
use App\Tests\Support\Double\Vehicle\InMemoryVehicleRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Ulid;

final class ListVehiclesByOwnerHandlerTest extends TestCase
{
    public function test_it_lists_vehicles_by_owner_with_pagination(): void
    {
        $repo = new InMemoryVehicleRepository();
        $owner1 = $this->createUser('owner1@example.com');
        $owner2 = $this->createUser('owner2@example.com');

        // 3 vehículos para owner1, 2 para owner2
        $v1 = new Vehicle('Truck A', 'A-1', 1000, MaintenanceType::A, new \DateTimeImmutable('2025-01-01'), $owner1);
        $v2 = new Vehicle('Truck B', 'B-1', 2000, MaintenanceType::B, new \DateTimeImmutable('2025-01-02'), $owner1);
        $v3 = new Vehicle('Truck C', 'C-1', 3000, MaintenanceType::C, new \DateTimeImmutable('2025-01-03'), $owner1);
        $v4 = new Vehicle('Truck D', 'D-1', 4000, MaintenanceType::A, new \DateTimeImmutable('2025-01-04'), $owner2);
        $v5 = new Vehicle('Truck E', 'E-1', 5000, MaintenanceType::B, new \DateTimeImmutable('2025-01-05'), $owner2);

        foreach ([$v1, $v2, $v3, $v4, $v5] as $v) {
            $repo->save($v);
        }

        // Mock del repositorio de usuarios
        $userRepo = $this->createMock(\App\Domain\User\Repository\UserRepositoryInterface::class);
        $userRepo->method('findByIdUser')->willReturnCallback(function($id) use ($owner1, $owner2) {
            if ((string)$owner1->getId() === (string)$id) return $owner1;
            if ((string)$owner2->getId() === (string)$id) return $owner2;
            return null;
        });

        $handler = new ListVehiclesByOwnerHandler($repo, $userRepo);

        // Owner1, página 1, perPage 2
        $owner1Id = (function($u) {
            $ref = new \ReflectionClass($u);
            $prop = $ref->getProperty('id');
            $prop->setAccessible(true);
            return $prop->getValue($u);
        })($owner1);
        $owner2Id = (function($u) {
            $ref = new \ReflectionClass($u);
            $prop = $ref->getProperty('id');
            $prop->setAccessible(true);
            return $prop->getValue($u);
        })($owner2);
        $result = ($handler)(new ListVehiclesByOwnerQuery($owner1Id, 1, 2));
        $this->assertCount(2, $result);
        $this->assertSame('Truck A', $result[0]->name());
        $this->assertSame('Truck B', $result[1]->name());

        // Owner1, página 2, perPage 2
    $result2 = ($handler)(new ListVehiclesByOwnerQuery($owner1Id, 2, 2));
        $this->assertCount(1, $result2);
        $this->assertSame('Truck C', $result2[0]->name());

        // Owner2, página 1, perPage 10
    $result3 = ($handler)(new ListVehiclesByOwnerQuery($owner2Id, 1, 10));
        $this->assertCount(2, $result3);
        $this->assertSame('Truck D', $result3[0]->name());
        $this->assertSame('Truck E', $result3[1]->name());
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