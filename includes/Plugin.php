<?php

namespace Ekelly\InteractiveMap;

use Ekelly\InteractiveMap\Admin\Admin;
use Ekelly\InteractiveMap\Frontend\Shortcode;

class Plugin
{
    public function boot()
    {
        $admin = new Admin;
        add_action('admin_menu', [$admin, 'registerMenu']);

        $shortcode = new Shortcode();
        add_action('init', [$shortcode, 'register']);
    }
}