<?php
/*
Plugin Name: Seneca Housing Dependencies
Description: Manages Composer dependencies for the Seneca Housing Platform.
Version: 1.0
Author: Nicholas Ilechie
*/

// Load Composer autoloader
if (file_exists(plugin_dir_path(__FILE__) . 'vendor/autoload.php')) {
    require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';
}