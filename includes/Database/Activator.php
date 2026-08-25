<?php

namespace Ekelly\InteractiveMap\Database;

class Activator
{
    public static function activate()
    {
        global $wpdb;

        $plantsTable  = $wpdb->prefix . 'interactive_map_plants';
        $regionsTable = $wpdb->prefix . 'interactive_map_plant_regions';

        $charset = $wpdb->get_charset_collate();

        $sqlPlants = "
        CREATE TABLE {$plantsTable} (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        plant_name VARCHAR(255) NOT NULL,
        plant_family VARCHAR(255) DEFAULT NULL,
        url VARCHAR(500) DEFAULT NULL,
        page_id BIGINT UNSIGNED DEFAULT 0,
        last_import datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY plant_name (plant_name)
        ) {$charset};
        ";

        $sqlRegions = "
        CREATE TABLE {$regionsTable} (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        plant_id BIGINT UNSIGNED NOT NULL,
        region_id VARCHAR(50) NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY plant_region (plant_id, region_id),
        KEY region_id (region_id),
        KEY plant_id (plant_id)
        ) {$charset};
        ";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        dbDelta($sqlPlants);
        dbDelta($sqlRegions);
    }
}