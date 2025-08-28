<?php

namespace App\UI\Controller;

use App\Application\Handler\Query\Vehicle\ListVehiclesByOwner\ListVehiclesByOwnerHandler;
use App\Application\Query\Vehicle\ListVehiclesByOwner\ListVehiclesByOwnerQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

final class ListVehiclesByOwnerController extends AbstractController
{
    #[Route('/api/owners/{ownerId}/vehicles', name: 'api_vehicles_by_owner', methods: ['GET'])]
    public function list(string $ownerId, ListVehiclesByOwnerHandler $handler): JsonResponse
    {
    $uuid = new Uuid($ownerId);
    $vehicles = ($handler)(new ListVehiclesByOwnerQuery($uuid));

        // Si los vehículos son entidades, los convertimos a array para la respuesta JSON
        $result = array_map(fn($v) => [
            'id' => (string) $v->id(),
            'name' => $v->name(),
            'plate' => $v->plate(),
            'odometerKm' => $v->odometerKm(),
            'lastMaintenanceType' => (string) $v->lastMaintenanceType(),
            'nextMaintenanceDueKm' => $v->nextMaintenanceDueKm(),
            'ownerId' => $v->getOwnerId(),
        ], $vehicles);

        return $this->json($result);
    }
}
