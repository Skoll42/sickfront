<?php

class Sickfront_Plugin
{
    public $pages = [];

    public function __construct()
    {
        add_action('admin_menu', array($this, 'addMenu'));
        add_action('admin_menu', array($this, 'removeFirstSubmenu'), 100);

		if(0 === strpos($_GET['page'], 'sickfront')) {
            add_filter('pre_site_transient_update_core', array($this, 'hide_update_messages'));
            add_filter('pre_site_transient_update_plugins', array($this, 'hide_update_messages'));
            add_filter('pre_site_transient_update_themes', array($this, 'hide_update_messages'));
        }

        $categories = sickfront_get_fronted_categories();
        foreach ($categories as $category) {
            $this->pages[] = new Sickfront_Page($this, $category->name, $category->slug);
        }

        if( defined('DOING_AJAX') && DOING_AJAX ) {
            new Sickfront_AdminAjaxManager();
        }
    }

    public function hide_update_messages(){
        global $wp_version;
        return(object) array('last_checked'=> time(),'version_checked'=> $wp_version,);
    }


    public function addMenu()
    {
        add_menu_page('Sickfront', 'Sickfront', 'manage_options', 'sickfront', '', SICKFRONT_PLUGIN_URL . 'assets/img/sickfront-icon.png');
    }

    public function removeFirstSubmenu() {
        remove_submenu_page('sickfront','sickfront');
    }

    public static function activate() {
        Sickfront_DbHelper::create_table();
    }

    public static function deactivate(){
        Sickfront_DbHelper::drop_table();
    }
}
