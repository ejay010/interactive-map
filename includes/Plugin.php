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
       
        $this->registerAdmin();

        $this->registerShortCode();

        $this->registerPlants();

        $this->registerIslands();

        $this->registerAssets();

        $this->registerApi();
     }

     public function registerAdmin(): void
     {
         $admin = new Admin;
        add_action('admin_menu', [$admin, 'registerMenu']);
     }

     public function registerShortCode() : void {
        $shortcode = new Shortcode();
        add_action('init', [$shortcode, 'register']);
     }

     public function registerPlants() : void {
        $plant = new Plant;
        add_action('init', [$plant, 'register']);
     }

    public function registerIslands() : void {
        $island = new Island;
        add_action('init', [$island, 'register']);
     }

     public function registerAssets() : void {
        $assets = new Assets;
        add_action('init', [$assets, 'register']);
     }

     public function registerApi() : void {
        $api = new PlantsApi();
        add_action('rest_api_init', [$api, 'routes']);
     }
}