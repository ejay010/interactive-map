<?php

/**
 * Plugin Name: Interactive Map
 * Description: Dynamic SVG map plugin.
 * Version: 1.0.0
 * Author: Eulond Kelly III
 */


defined('ABSPATH') || exit;

define('IM_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('IM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('IM_VERSION', '1.0.0');

require_once __DIR__ . '/vendor/autoload.php';
(new Ekelly\InteractiveMap\Plugin())->boot();