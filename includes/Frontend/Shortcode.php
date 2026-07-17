<?php

namespace Ekelly\InteractiveMap\Frontend;

class Shortcode

{
    public function register() {
        add_shortcode(
            'interactive_map',
            [$this, 'render']
        );
    }

    public function render() {
        ob_start();

        require IM_PLUGIN_PATH . 'templates/frontend/map.php';

        return ob_get_clean();
    }
}