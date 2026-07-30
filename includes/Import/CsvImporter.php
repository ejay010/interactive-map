<?php

namespace Ekelly\InteractiveMap\Import;

use Ekelly\InteractiveMap\Repository\PlantRepository;
use Ekelly\InteractiveMap\Services\PlantMatcher;

class CsvImporter
{
    public function import(string $file): ImportResult
    {
        $reader = new CsvReader();
        $rows = $reader->read($file);
        
        (new CsvValidator())->validate($rows);
        
        $repository = new PlantRepository();
        $matcher = new PlantMatcher();

        $result = new ImportResult();

        foreach ($rows as $row) {
            $result->processed();

            $pageId = $matcher->find(
                $row['plant_name']
            );

            if (!$pageId) {
                $result->missing($row['plant_name']);

                continue;
            }

            $repository->upsertRelationship(
                $pageId,
                $row['region_id'],
                $row['plant_family']
            );

            $result->imported();
        }

        return $result;

    }
}