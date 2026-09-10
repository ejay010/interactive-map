<?php

namespace Ekelly\InteractiveMap\PostTypes;

class Island 
{
    public function register()
    {
        register_taxonomy(
            'island',
            'plant',
            [
                'labels' => [
                    'name' => 'Islands',
                    'singular_name' => 'Island'
                ],

                'hierarchical' => false,
                'show_in_rest' => true,
                'rewrite' => [
                    'slug' => 'island'
                ]
            ]
        );
    }
}

