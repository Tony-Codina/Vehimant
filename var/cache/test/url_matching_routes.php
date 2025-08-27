<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/api/(?'
                    .'|vehicles/([^/]++)(*:32)'
                    .'|owners/([^/]++)/vehicles(*:63)'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        32 => [[['_route' => 'api_vehicle_get', '_controller' => 'App\\UI\\Controller\\GetVehicleController::get'], ['id'], ['GET' => 0], null, false, true, null]],
        63 => [
            [['_route' => 'api_vehicles_by_owner', '_controller' => 'App\\UI\\Controller\\ListVehiclesByOwnerController::list'], ['ownerId'], ['GET' => 0], null, false, false, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
