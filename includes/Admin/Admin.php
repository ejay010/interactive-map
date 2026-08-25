<?php

namespace Ekelly\InteractiveMap\Admin;

class Admin {
    public function registerMenu() {

        add_menu_page(
            'Interactive Maps',
            'Interactive Maps',
            'manage_options',
            'interactive-map',
            [$this, 'dashboard'],
            'dashicons-location',
            25
        );

        (new ManagePlantsPage())->register();
        (new ImportPage())->register();
    }

    public function dashboard() {
        require IM_PLUGIN_PATH . '/templates/admin/dashboard.php';
    }
}