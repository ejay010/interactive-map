<?php 

namespace Ekelly\InteractiveMap\Api;

use Ekelly\InteractiveMap\Repository\PlantRepository;

class PlantsApi
{
    public function register()
    {
        add_action('rest_api_init', [$this, 'routes']);
    }

    public function routes()
    {
        register_rest_route(
            'interactive-map/v1',
            '/plants/(?P<island>[A-Z0-9]+)',
            [
                'methods' => 'GET',
                'callback' => [$this, 'getPlants'],
                'permission_callback' => '__return_true',
            ]
            );
    }

    public function getPlants($request)
    {
        $repository = new PlantRepository();

        return $repository->getPlantsByIsland(
            $request['island']
        );
    }
}