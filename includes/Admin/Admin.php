<?php

namespace Ekelly\InteractiveMap\Admin;

class Admin {
    public function registerMenu() {

        add_menu_page(
            'Interactive Maps',
            'Interactive Maps',
            'manage_options',
            'insteractive-map',
            [$this, 'dashboard'],
            'dashicons-location',
            25
        );

        (new ImportPage())->register();
    }

    public function dashboard() {
        require IM_PLUGIN_PATH . '/templates/admin/dashboard.php';
    }
}