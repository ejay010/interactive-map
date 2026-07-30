<?php 

namespace Ekelly\InteractiveMap\Repository;

use WP_Query;

class PlantRepository
{
    protected $wpdb;
    protected $table;

    public function __construct() {
        global $wpdb;

        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'interactive_map_plants';
    }

    public function upsertRelationship(
        int $pageId,
        string $regionId,
        string $family
    ): void {
        $exists = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "
                    SELECT id
                    FROM {$this->table}
                    WHERE page_id = %d
                    AND region_id = %s
                ",
                $pageId,
                $regionId
            )
            );

            if ($exists) {
                return;
            }

       $this->wpdb->insert(
            $this->table,
            [
                'page_id' => $pageId,
                'region_id' => $regionId,
                'plant_family' => $family,
            ]
            );
    }

    public function getPlantsByRegion(string $regionId): array
    {
        $results =  $this->wpdb->get_results(
            $this->wpdb->prepare(
                "
                SELECT page_id, plant_family
                FROM {$this->table}
                WHERE region_id = %s
                ORDER BY page_id
                ",
                $regionId
            ),
            
            ARRAY_A

            );

            return $this->formatPlants($results);
    }

    public function getAllPlants(): array
    {
        $results = $this->wpdb->get_results(
            "
            SELECT DISTINCT page_id, plant_family
            FROM {$this->table}
            ORDER BY page_id
            ",
            ARRAY_A
        );

        return $this->formatPlants($results);
    }

    private function formatPlants(array $results): array
    {
        return array_map(function ($plant) {
            return [
                'page_id' => (int) $plant['page_id'],

                'title' => get_the_title($plant['page_id']),

                'url' => get_permalink($plant['page_id']),

                'family' => $plant['plant_family'],

                'thumbnail' => get_the_post_thumbnail_url(
                    $plant['page_id'],
                    'thumbnail'
                )
            ];
        }, $results);
    }
}