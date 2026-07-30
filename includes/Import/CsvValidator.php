<?php 

namespace Ekelly\InteractiveMap\Import;

class CsvValidator
{
    private const REQUIRED_HEADERS = [
        'plant_family',
        'plant_name',
        'region_id'
    ];

    public function validate(array $rows): void
    {
        if (empty($rows)) {
            throw new \Exception('CSV contains no data.');
        }

        $headers = array_keys($rows[0]);

        foreach (self::REQUIRED_HEADERS as $required) {
            if(!in_array($required, $headers, true)) {
                throw new \Exception(
                    "Missing required column: {$required}"
                );
            }
        }
    }
}