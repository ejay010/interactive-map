<?php

namespace Ekelly\InteractiveMap\PostTypes;

class Plant
{
    public function register() {
        register_post_type('plant', [
            'labels' => [
                'name' => 'Plants',
                'singular_name' => 'Plant',
                'add_new_item' => 'Add New Plant',
                'edit_item' => 'Edit Plant',
            ],

            'public' => true,

            'show_in_menu' => true,

            'supports' => [
                'title',
                'description',
                'editor',
                'thumbnail'
            ],

            'has_archive' => true,

            'rewrite' => [
                'slug' => 'plants'
            ],

            'show_in_rest' => true
        ]);
    }
}