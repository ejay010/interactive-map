<?php

namespace Ekelly\InteractiveMap\Import;

use Ekelly\InteractiveMap\Repository\PlantRepository;

class CsvImporter
{
    public function import(string $file): ImportResult
    {
        // Ensure database table schema is created / migrated
        \Ekelly\InteractiveMap\Database\Activator::activate();

        $reader = new CsvReader();
        $rows = $reader->read($file);
        
        (new CsvValidator())->validate($rows);
        
        $plantRepository = new PlantRepository();
        $result = new ImportResult();

        foreach ($rows as $row) {
            $result->processed();

            $plantName   = trim($row['Species'] ?? $row['plant_name'] ?? '');
            $plantFamily = trim($row['Family'] ?? $row['plant_family'] ?? '');
            $rawRegions  = $row['Island Grouping'] ?? $row['region_id'] ?? '';
            $plantUrl    = trim($row['url'] ?? $row['URL'] ?? $row['link'] ?? $row['Link'] ?? '');

            if (empty($plantName)) {
                continue;
            }

            // Find or create unique plant species record
            $plantId = $plantRepository->findOrCreatePlant($plantName, $plantFamily, $plantUrl);

            $regions = array_map('trim', explode(',', $rawRegions));
            $anyLinked = false;

            foreach ($regions as $region) {
                // Strip trailing punctuation (e.g. "20." -> "20")
                $regionId = trim(preg_replace('/[^0-9A-Za-z_-]/', '', $region));

                if (empty($regionId)) {
                    continue;
                }

                if ($plantRepository->linkPlantToRegion($plantId, $regionId)) {
                    $anyLinked = true;
                }
            }

            if ($anyLinked) {
                $result->imported();
            } else {
                $result->skipped();
            }
        }

        return $result;
    }
}