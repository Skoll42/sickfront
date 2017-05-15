<?php
/*
Plugin Name: SickFront
Plugin URI:
Description: Manage your front page.
Author: N-iX
Version: 0.1
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

define('SICKFRONT_PLUGIN_PATH', dirname(__FILE__));
define('SICKFRONT_PLUGIN_URL', plugin_dir_url(__FILE__));

define('SICKFRONT_CLASS_PATH', SICKFRONT_PLUGIN_PATH.'/classes');

spl_autoload_register(function($class) {
    $class_file = SICKFRONT_CLASS_PATH . '/' . str_replace('_', '/', $class) . '.php';

    if (file_exists($class_file)) {
        require_once $class_file;
    }
});


register_activation_hook(__FILE__, 'Sickfront_Plugin::activate');
register_deactivation_hook(__FILE__, 'Sickfront_Plugin::deactivate');

if( is_admin() ) {
    new Sickfront_Plugin();
}

function sickfront_get_fronted_categories() {
    return get_categories([
        'hide_empty' => false,
        'meta_key' => 'category_has_sickfront',
        'meta_value' => 1,
    ]);
}

add_filter('sickfront_save_stack', function($stack, $site) {
    $purgeUrls = [];

    $purgeUrls[] = home_url() . '/';
    $purgeUrls[] = home_url() . '/page/?vhp-regex';

    $categories = sickfront_get_fronted_categories();
    foreach ($categories as $category) {
        $cat_url = get_category_link($category->term_id);

        $purgeUrls[] = $cat_url;
        $purgeUrls[] = $cat_url . 'page/?vhp-regex';
    }

    bt_purge_varnish_urls($purgeUrls);
}, 10, 2);
