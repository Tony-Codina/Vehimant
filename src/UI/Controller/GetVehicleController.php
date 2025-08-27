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

        return $this->json($data);
    }
}