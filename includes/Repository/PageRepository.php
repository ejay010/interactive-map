<?php

namespace Ekelly\InteractiveMap\Repository;

class PageRepository 
{

    protected $wpdb;
    protected $table;

    public function __construct() {
        global $wpdb;

        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'interactive_map_plants';
    }

    public function findByTitle(string $plantName): ?int
    {
        $pageId = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "
                    SELECT ID
                    FROM {$this->wpdb->posts}
                    WHERE post_title = %s
                    AND post_type = 'page'
                    AND post_status = 'publish'
                    LIMIT 1
                ",
                $plantName
            )
            );

            return $pageId ? (int) $pageId : null;
    }
}