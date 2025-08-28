<?php

namespace App\UI\Controller;

use App\Application\Handler\Query\Vehicle\GetVehicle\GetVehicleHandler;
use App\Application\Query\Vehicle\GetVehicle\GetVehicleQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Ulid;

final class GetVehicleController extends AbstractController
{
    #[Route('/api/vehicles/{id}', name: 'api_vehicle_get', methods: ['GET'])]
    public function get(string $id, GetVehicleHandler $handler): JsonResponse
    {
        $ulid = new Ulid($id);
        $data = ($handler)(new GetVehicleQuery($ulid));

        // Si $data es una entidad Vehicle, expone ownerId correctamente
        if (is_object($data) && method_exists($data, 'getOwnerId')) {
            $response = [
                'id' => (string) $data->id(),
                'name' => $data->name(),
                'plate' => $data->plate(),
                'odometerKm' => $data->odometerKm(),
                'lastMaintenanceType' => (string) $data->lastMaintenanceType(),
                'nextMaintenanceDueKm' => $data->nextMaintenanceDueKm(),
                'ownerId' => $data->getOwnerId(),
            ];
            return $this->json($response);
        }
        return $this->json($data);
    }
}