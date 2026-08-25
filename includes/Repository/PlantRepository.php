<?php 

namespace Ekelly\InteractiveMap\Repository;

class PlantRepository
{
    protected $wpdb;
    protected $plantsTable;
    protected $regionsTable;

    public function __construct() {
        global $wpdb;

        $this->wpdb = $wpdb;
        $this->plantsTable = $wpdb->prefix . 'interactive_map_plants';
        $this->regionsTable = $wpdb->prefix . 'interactive_map_plant_regions';
    }

    /**
     * Find existing plant or create new unique plant record.
     */
    public function findOrCreatePlant(
        string $plantName,
        string $family = '',
        string $url = ''
    ): int {
        $plantName = trim($plantName);
        $family = trim($family);
        $url = trim($url);

        $plantId = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT id FROM {$this->plantsTable} WHERE plant_name = %s",
                $plantName
            )
        );

        if ($plantId) {
            // Update URL or Family if provided and currently empty
            $updateData = [];
            if (!empty($url)) {
                $updateData['url'] = $url;
            }
            if (!empty($family)) {
                $updateData['plant_family'] = $family;
            }

            if (!empty($updateData)) {
                $this->wpdb->update(
                    $this->plantsTable,
                    $updateData,
                    ['id' => (int) $plantId]
                );
            }

            return (int) $plantId;
        }

        $this->wpdb->insert(
            $this->plantsTable,
            [
                'plant_name'   => $plantName,
                'plant_family' => $family,
                'url'          => !empty($url) ? $url : null,
            ]
        );

        return (int) $this->wpdb->insert_id;
    }

    /**
     * Link a plant to an island region ID (Many-to-Many junction).
     */
    public function linkPlantToRegion(int $plantId, string $regionId): bool
    {
        $regionId = trim($regionId);

        $exists = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT id FROM {$this->regionsTable} WHERE plant_id = %d AND region_id = %s",
                $plantId,
                $regionId
            )
        );

        if ($exists) {
            return false;
        }

        $inserted = $this->wpdb->insert(
            $this->regionsTable,
            [
                'plant_id'  => $plantId,
                'region_id' => $regionId,
            ]
        );

        return $inserted !== false;
    }

    /**
     * Update URL for a single plant species by plant ID.
     */
    public function updatePlantUrl(int $plantId, string $url): bool
    {
        $url = trim($url);

        $updated = $this->wpdb->update(
            $this->plantsTable,
            ['url' => !empty($url) ? $url : null],
            ['id' => $plantId]
        );

        return $updated !== false;
    }

    /**
     * Get plants for a specific island region ID.
     */
    public function getPlantsByRegion(string $regionId): array
    {
        $results = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "
                SELECT p.id, p.plant_name, p.plant_family, p.url, p.page_id
                FROM {$this->plantsTable} p
                INNER JOIN {$this->regionsTable} pr ON p.id = pr.plant_id
                WHERE pr.region_id = %s
                ORDER BY p.plant_name ASC
                ",
                $regionId
            ),
            ARRAY_A
        );

        return $this->formatPlants($results);
    }

    /**
     * Get all unique plants in the catalog.
     */
    public function getAllPlants(): array
    {
        $results = $this->wpdb->get_results(
            "
            SELECT p.id, p.plant_name, p.plant_family, p.url, p.page_id
            FROM {$this->plantsTable} p
            ORDER BY p.plant_name ASC
            ",
            ARRAY_A
        );

        return $this->formatPlants($results);
    }

    /**
     * Get plant counts grouped by region ID and total count.
     */
    public function getPlantCountsByRegion(): array
    {
        $results = $this->wpdb->get_results(
            "
            SELECT region_id, COUNT(DISTINCT plant_id) AS total
            FROM {$this->regionsTable}
            GROUP BY region_id
            ",
            ARRAY_A
        );

        $counts = [];
        if (!empty($results)) {
            foreach ($results as $row) {
                $counts[$row['region_id']] = (int) $row['total'];
            }
        }

        $totalPlants = (int) $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->plantsTable}");

        return [
            'regions' => $counts,
            'total'   => $totalPlants,
        ];
    }

    /**
     * Get all unique plants alongside their linked region IDs for admin management.
     */
    public function getAllPlantsWithRegions(): array
    {
        $sql = "
            SELECT 
                p.id, 
                p.plant_name, 
                p.plant_family, 
                p.url,
                GROUP_CONCAT(DISTINCT pr.region_id ORDER BY CAST(pr.region_id AS UNSIGNED), pr.region_id SEPARATOR ', ') AS regions
            FROM {$this->plantsTable} p
            LEFT JOIN {$this->regionsTable} pr ON p.id = pr.plant_id
            GROUP BY p.id
            ORDER BY p.plant_name ASC
        ";

        return $this->wpdb->get_results($sql, ARRAY_A);
    }

    private function formatPlants(array $results): array
    {
        return array_map(function ($plant) {
            $url = !empty($plant['url']) ? $plant['url'] : '';

            // Fallback to get_permalink if page_id exists
            if (empty($url) && !empty($plant['page_id'])) {
                $permalink = get_permalink($plant['page_id']);
                if ($permalink) {
                    $url = $permalink;
                }
            }

            return [
                'id'        => (int) $plant['id'],
                'title'     => $plant['plant_name'],
                'url'       => $url,
                'family'    => $plant['plant_family'],
                'thumbnail' => !empty($plant['page_id']) ? get_the_post_thumbnail_url($plant['page_id'], 'thumbnail') : null
            ];
        }, $results);
    }
}