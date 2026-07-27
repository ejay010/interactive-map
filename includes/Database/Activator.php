<?php

namespace Ekelly\InteractiveMap\Database;

class Activator
{
    public static function activate()
    {
        global $wpdb;

        $table = $wpdb->prefix . 'interactive_map_plants';

        $charset = $wpdb->get_charset_collate();

        $sql = "
        CREATE TABLE {$table} (
        id BIGINT UNSIGNED NOT NULL,
        page_id BIGINT UNSIGNED NOT NULL,
        island_code VARCHAR(10) NOT NULL,
        plant_family VARCHAR(255),
        last_import datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY page_id (page_id),
        KEY island_code (island_code)
        ) {$charset};
        ";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        dbDelta($sql);
    }
}