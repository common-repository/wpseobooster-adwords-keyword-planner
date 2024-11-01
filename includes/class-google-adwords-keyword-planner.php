<?php

defined('ABSPATH') || exit;

final class Google_AdWords_Keyword_Planner
{
    /**
     * The single instance of Plugin.
     * @var     object
     * @access  private
     * @since     1.0.0
     */
    private static $instance;

    /**
     * Settings class object
     * @var     object
     * @access  public
     * @since   1.0.0
     */
    public $settings = null;
    /**
     * The version number.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_version;

    /**
     * The token.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_token;

    /**
     * The main plugin file.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $file;

    /**
     * The main plugin directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $dir;

    /**
     * The plugin assets directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_dir;

    /**
     * The plugin assets URL.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_url;

    /**
     * Suffix for Javascripts.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $image_path;

    /**
     * Suffix for Javascripts.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $script_suffix;
    public $data;

    /**
     * The main plugin object.
     * @var     object
     * @access  public
     * @since     1.0.0
     */
    public $parent = null;

    // public $aaa = 1;

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor function.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function __construct()
    {
        $this->init();
        $this->includes();
        if (is_admin()) {
            $this->admin = new GOOGLE_AWORDS_KEYWORD_PLANNER_ADMIN_API();
        }
    }

    public function init()
    {
        add_action('admin_menu', array($this, 'add_menu_item'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
    }
    public function includes()
    {
        include_once GOOGLE_AWORDS_KEYWORD_PLANNER_ABSPATH . 'includes/admin/gakp-admin-api.php';
        include_once GOOGLE_AWORDS_KEYWORD_PLANNER_ABSPATH . 'includes/admin/gakp-settings.php';
        include_once GOOGLE_AWORDS_KEYWORD_PLANNER_ABSPATH . 'includes/class-gakp-get-authenticate-with-google.php';
        include_once GOOGLE_AWORDS_KEYWORD_PLANNER_ABSPATH . 'includes/class-gakp-keyword-planner.php';
        include_once GOOGLE_AWORDS_KEYWORD_PLANNER_ABSPATH . 'includes/class-gakp-get-keyword-ideas.php';
    }
    // adding sub menu in woocommerce dashboard
    public function add_menu_item()
    {
        add_menu_page(
            'Google AdWords Keyword Planner',
            'Google AdWords Keyword Planner',
            'manage_option',
            'google_adwords_keyword_planner',
            array($this, 'plugin_homepage'),
            GOOGLE_AWORDS_KEYWORD_PLANNER_URL . 'assets/img/gakp.png',
            25

        );
    }

    // enqueue scripts
    public function enqueue_scripts()
    {
        if (is_admin()) {
            wp_enqueue_script('gakp_admin_scripts',  GOOGLE_AWORDS_KEYWORD_PLANNER_URL . 'assets/js/gakp_admin_scripts.js', array('jquery', 'jquery-ui-autocomplete', 'jquery-ui-dialog'), '1.0');
            wp_localize_script(
                'gakp_admin_scripts',
                'gakp_admin_ajax_object',
                array(
                    'ajax_url' => admin_url('admin-ajax.php')
                )
            );
            wp_register_script('datatables', GOOGLE_AWORDS_KEYWORD_PLANNER_URL . 'assets/js/jquery.dataTables.min.js', array('jquery'), true);
            wp_enqueue_script('datatables');
            wp_register_script('datatables_bootstrap', GOOGLE_AWORDS_KEYWORD_PLANNER_URL . 'assets/js/bootstrap.bundle.min.js', array('jquery'), true);
            wp_enqueue_script('datatables_bootstrap');
        }
    }

    public function enqueue_styles()
    {
        if (is_admin()) {
            wp_enqueue_style('gakp_styles',  GOOGLE_AWORDS_KEYWORD_PLANNER_URL . 'assets/css/gakp_admin_styles.css');
            wp_register_style('bootstrap_style', GOOGLE_AWORDS_KEYWORD_PLANNER_URL . 'assets/css/bootstrap.min.css');
            wp_enqueue_style('bootstrap_style');
            wp_register_style('datatables_style', GOOGLE_AWORDS_KEYWORD_PLANNER_URL . 'assets/css/jquery.dataTables.min.css');
            wp_enqueue_style('datatables_style');
        }
    }
}
