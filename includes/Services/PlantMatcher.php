<?php 

namespace Ekelly\InteractiveMap\Services;

use Ekelly\InteractiveMap\Repository\PageRepository;
use Ekelly\InteractiveMap\Repository\PlantRepository;

class PlantMatcher
{
    public function find(string $plantName): ?int
    {
        return (new PageRepository())->findByTitle($plantName);
    }
}