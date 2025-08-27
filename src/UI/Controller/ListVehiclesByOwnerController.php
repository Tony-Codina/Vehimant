<?php

namespace App\UI\Controller;

use App\Application\Handler\Query\Vehicle\ListVehiclesByOwner\ListVehiclesByOwnerHandler;
use App\Application\Query\Vehicle\ListVehiclesByOwner\ListVehiclesByOwnerQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Ulid;

final class ListVehiclesByOwnerController extends AbstractController
{
    #[Route('/api/owners/{ownerId}/vehicles', name: 'api_vehicles_by_owner', methods: ['GET'])]
    public function list(string $ownerId, ListVehiclesByOwnerHandler $handler): JsonResponse
    {
        $ulid = new Ulid($ownerId);
        $vehicles = ($handler)(new ListVehiclesByOwnerQuery($ulid));

        // Si los vehículos son entidades, los convertimos a array para la respuesta JSON
        $result = array_map(fn($v) => [
            'id' => (string) $v->id(),
            'name' => $v->name(),
            'plate' => $v->plate(),
            'odometerKm' => $v->odometerKm(),
            'lastMaintenanceType' => (string) $v->lastMaintenanceType(),
            'nextMaintenanceDueKm' => $v->nextMaintenanceDueKm(),
            'ownerId' => (string) $v->ownerId(),
        ], $vehicles);

        return $this->json($result);
    }
}
