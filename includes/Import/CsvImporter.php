<?php

namespace Ekelly\InteractiveMap\Import;

use Ekelly\InteractiveMap\Repository\PageRepository;
use Ekelly\InteractiveMap\Repository\PlantRepository;
use Ekelly\InteractiveMap\Services\PlantMatcher;

class CsvImporter
{
    public function import(string $file): ImportResult
    {
        $reader = new CsvReader();
        $rows = $reader->read($file);
        
        (new CsvValidator())->validate($rows);
        
        $plantrepository = new PlantRepository();
        $pagerepository = new PageRepository();

        $result = new ImportResult();

        foreach ($rows as $row) {
            $result->processed();

            $pageId = $pagerepository->findByTitle(
                $row['plant_name']
            );

            if (!$pageId) {
                $result->missing($row['plant_name']);

                continue;
            }

            $regions = array_map('trim', explode(',', $row['region_id']));

            foreach ($regions as $regionId) {
                if($plantrepository->upsertRelationship(
                    $pageId,
                    $regionId,
                    trim($row['plant_family'])
                )) {
                    $result->imported();
                } else {
                    $result->skipped();
                }
            }

        }

        return $result;

    }
}