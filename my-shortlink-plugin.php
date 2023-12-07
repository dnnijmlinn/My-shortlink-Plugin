<?php
/*
Plugin Name: My Shortlink Plugin
Description: A plugin to manage short links.
Version: 1.0
Author: Denis Bogdanov
*/
require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/admin/class-metabox.php';
require_once __DIR__ . '/admin/singleton.php';
require_once __DIR__ . '/admin/class-admin-page.php';
require_once __DIR__ . '/admin/class-click-counter.php';


use MyShortlinkPlugin\Admin\Metabox;
use MyShortlinkPlugin\Admin\Admin_Page;
use MyShortlinkPlugin\Admin\Click_Counter;
use MyShortlinkPlugin\Admin\Singleton;


function run_my_shortlink_plugin() {
    Metabox::getInstance();
    Admin_Page::getInstance();
    Click_Counter::getInstance();
}
run_my_shortlink_plugin();


add_action('plugins_loaded', 'my_shortlink_plugin_load_textdomain');

function my_shortlink_plugin_load_textdomain() {
    $path = dirname(plugin_basename(__FILE__)) . '/languages/';
    load_plugin_textdomain('my-shortlink-plugin', false, $path);
}


