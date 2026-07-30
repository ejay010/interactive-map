<?php

namespace Ekelly\InteractiveMap;

use Ekelly\InteractiveMap\Admin\Admin;
use Ekelly\InteractiveMap\Admin\ImportPage;
use Ekelly\InteractiveMap\Api\PlantsApi;
use Ekelly\InteractiveMap\Assets\Assets;
use Ekelly\InteractiveMap\Frontend\Shortcode;
use Ekelly\InteractiveMap\PostTypes\Island;
use Ekelly\InteractiveMap\PostTypes\Plant;

class Plugin
{
    public function boot()
    {
        $admin = new Admin;
        add_action('admin_menu', [$admin, 'registerMenu']);

        $shortcode = new Shortcode();
        add_action('init', [$shortcode, 'register']);

        $plant = new Plant;
        add_action('init', [$plant, 'register']);

        $island = new Island;
        add_action('init', [$island, 'register']);

        $assets = new Assets;
        add_action('init', [$assets, 'register']);

        $api = new PlantsApi();
        add_action('rest_api_init', [$api, 'register']);
     }
}