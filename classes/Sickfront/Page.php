<?php

class Sickfront_Page
{
    public $plugin;
    public $slug;
    public $page_name;

    public function __construct($plugin, $page_name, $slug)
    {
        $this->plugin = $plugin;
        $this->page_name = $page_name;
        $this->slug = $slug;

        add_action('admin_menu', array($this, 'addSubMenu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
    }

    public function displayPage()
    {
        require(SICKFRONT_PLUGIN_PATH . '/pages/page.php');
    }

    public function getPageName()
    {
        return $this->page_name;
    }

    public function getAdminURL()
    {
        return menu_page_url('sickfront-' . $this->slug, false);
    }

    function addSubMenu()
    {
        add_submenu_page(
            'sickfront',
            $this->page_name . ' Sickfront',
            $this->page_name,
            'manage_options',
            'sickfront-' . $this->slug,
            array($this, 'displayPage')
        );
    }

    function enqueueScripts($hook)
    {
        if (strpos($hook, 'sickfront_page_sickfront-' . $this->slug) !== 0) {
            return;
        }

        add_action( 'after_wp_tiny_mce', function($settings) {
            printf('<script type="text/javascript">%s</script>', 'var SickfrontJS = {ajaxURL:"' . admin_url('admin-ajax.php') . '", nonce: "' . wp_create_nonce('sickfront-nonce') . '", site: "' . $this->slug . '"};');
            printf('<script type="text/javascript" src="%s"></script>',  SICKFRONT_PLUGIN_URL . 'assets/js/sickfront.js?ver=' . time());
        });

        wp_enqueue_style('bootstrap', 'http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css');
        wp_enqueue_style('bootstrap_slider', SICKFRONT_PLUGIN_URL . 'assets/css/bootstrap-slider/bootstrap-slider.css', [], '2.0');

        wp_enqueue_style('sickfront-page', SICKFRONT_PLUGIN_URL . 'assets/css/common.css', [], gk_get_rev());

        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-draggable');
        wp_enqueue_script('jquery-ui-droppable');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('jquery-ui-mouse');

        wp_enqueue_media();

        wp_enqueue_script('bootstrap', 'http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js', ['jquery'], null, true);
        wp_enqueue_script('bootstrap-slider', SICKFRONT_PLUGIN_URL . 'assets/js/bootstrap-slider.js', ['jquery'], '1.0.1', true);
        wp_enqueue_script('sickfront-fold', SICKFRONT_PLUGIN_URL . 'assets/js/fold.js', ['jquery'], gk_get_rev(), true);

        wp_enqueue_script('pagination', SICKFRONT_PLUGIN_URL . 'assets/js/jquery.twbsPagination.min.js', ['jquery'], '1.4.1', true);
    }
}
