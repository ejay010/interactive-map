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
        wp_enqueue_style(
            'interactive-map',
            IM_PLUGIN_URL . 'assets/css/map.css',
            [],
            IM_VERSION
        );

        wp_enqueue_script(
            'interactive-map',
            IM_PLUGIN_URL . 'assets/js/map.js',
            [],
            IM_VERSION,
            true
        );

        wp_localize_script(
            'interactive-map',
            'interactiveMapConfig',
            [
                'apiUrl' => esc_url_raw(rest_url('interactive-map/v1')),
                'nonce'  => wp_create_nonce('wp_rest'),
            ]
        );
    }
}