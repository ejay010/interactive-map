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

    public function findByTitle(string $plantName, bool $autoCreate = true): ?int
    {
        // 1. Exact match on published post or page
        $pageId = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "
                    SELECT ID
                    FROM {$this->wpdb->posts}
                    WHERE post_title = %s
                    AND post_status = 'publish'
                    LIMIT 1
                ",
                $plantName
            )
        );

        if ($pageId) {
            return (int) $pageId;
        }

        // 2. Try binomial match (e.g. "Stenandrium bracteosum")
        if (preg_match('/^([A-Z][a-z]+\s+[a-z]+)/', $plantName, $matches)) {
            $binomial = $matches[1];
            $pageId = $this->wpdb->get_var(
                $this->wpdb->prepare(
                    "
                        SELECT ID
                        FROM {$this->wpdb->posts}
                        WHERE post_title LIKE %s
                        AND post_status = 'publish'
                        LIMIT 1
                    ",
                    $this->wpdb->esc_like($binomial) . '%'
                )
            );

            if ($pageId) {
                return (int) $pageId;
            }
        }

        // 3. Auto-create published page if it does not exist yet
        if ($autoCreate && function_exists('wp_insert_post')) {
            $newId = wp_insert_post([
                'post_title'   => $plantName,
                'post_type'    => 'page',
                'post_status'  => 'publish',
            ]);

            if ($newId && !is_wp_error($newId)) {
                return (int) $newId;
            }
        }

        return null;
    }
}