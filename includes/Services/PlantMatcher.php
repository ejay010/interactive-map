<?php 

namespace Ekelly\InteractiveMap\Services;

use Ekelly\InteractiveMap\Repository\PlantRepository;

class PlantMatcher
{
    public function find(string $plantName): ?int
    {
        return (new PlantRepository())
                ->findPageByPlantName($plantName);
    }
}