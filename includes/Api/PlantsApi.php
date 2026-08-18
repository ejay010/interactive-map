<?php 

namespace Ekelly\InteractiveMap\Api;

use Ekelly\InteractiveMap\Repository\PlantRepository;

class PlantsApi
{
    public function routes()
    {
        register_rest_route(
            'interactive-map/v1',
            '/plants/(?P<region>[A-Za-z0-9_-]+)',
            [
                'methods' => 'GET',
                'callback' => [$this, 'plantsByRegion'],
                'permission_callback' => '__return_true',
            ]
            );

        register_rest_route(
            'interactive-map/v1',
            '/plants',
            [
                'methods' => 'GET',
                'callback' => [$this, 'allPlants'],
                'permission_callback' => '__return_true'
            ]
            );
    }

    public function allPlants()
    {
        return (new PlantRepository())->getAllPlants();
    }

    public function plantsByRegion($request)
    {
        return (new PlantRepository())->getPlantsByRegion(
            $request['region']
        );
    }
}