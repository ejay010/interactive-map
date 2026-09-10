<?php

namespace Ekelly\InteractiveMap\Frontend;

use Ekelly\InteractiveMap\Assets\Assets;

class Shortcode
{
    public function register() {
        add_shortcode(
            'interactive_map',
            [$this, 'render']
        );
    }

    public function render() {
        (new Assets())->enqueue();

        ob_start();

        require IM_PLUGIN_PATH . 'templates/frontend/map.php';

        return ob_get_clean();
    }
}