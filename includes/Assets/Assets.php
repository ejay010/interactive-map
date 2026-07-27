<?php

namespace Ekelly\InteractiveMap\Assets;

class Assets
{
    public function register()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue']);
    }

    public function enqueue()
    {
        wp_enqueue_script(
            'interactive-map',
            IM_PLUGIN_URL . 'assets/js/map.js',
            [],
            IM_VERSION,
            true
        );

        wp_enqueue_style(
            'interactive-map',
            IM_PLUGIN_URL . 'assets/css/map.css',
            [],
            IM_VERSION
        );
    }
}