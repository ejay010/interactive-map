<?php 

namespace Ekelly\InteractiveMap\Repository;

class PlantRepository
{
    protected $wpdb;
    protected $table;

    public function __construct() {
        global $wpdb;

        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'interactive_map_plants';
    }

    public function findPageByPlantName(string $plantName): ?int
    {
        $page = get_page_by_title($plantName, OBJECT, 'page');

        if (!$page) {
            return null;
        }

        return (int) $page->ID;
    }

    public function save(
        int $pageId,
        string $islandCode,
        string $family
    ) {
        global $wpdb;

        $wpdb->insert(
            $this->table,
            [
                'page_id' => $pageId,
                'island_code' => $islandCode,
                'plant_family' => $family,
            ]
            );
    }

    public function getPlantsByIsland(string $islandCode): array
    {
        $results =  $this->wpdb->get_results(
            $this->wpdb->prepare(
                "
                SELECT page_id, plant_family
                FROM {$this->table}
                WHERE island_code = %s
                ",
                $islandCode
            ),
            ARRAY_A
            );

            return array_map(function ($plant) {
                return [
                    'title' => get_the_title($plant['page_id']),
                    'url' => get_permalink($plant['page_id']),
                    'family' => $plant['plant_family']
                ];
            }, $results);
    }
}