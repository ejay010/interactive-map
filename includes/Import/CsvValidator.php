<?php 

namespace Ekelly\InteractiveMap\Import;

class CsvValidator
{
    private const REQUIRED_CONCEPTS = [
        'plant_name' => ['plant_name', 'Species'],
        'plant_family' => ['plant_family', 'Family'],
        'region_id' => ['region_id', 'Island Grouping']
    ];

    public function validate(array $rows): void
    {
        if (empty($rows)) {
            throw new \Exception('CSV contains no data.');
        }

        $headers = array_keys($rows[0]);

        foreach (self::REQUIRED_CONCEPTS as $concept => $aliases) {
            $found = false;
            foreach ($aliases as $alias) {
                if (in_array($alias, $headers, true)) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $aliasList = implode(' or ', $aliases);
                throw new \Exception("Missing required column for {$concept} (expected: {$aliasList}).");
            }
        }
    }
}