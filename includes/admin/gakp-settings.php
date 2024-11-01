<?php

if (!defined('ABSPATH')) {
    exit;
}
// require __DIR__ . '/../../vendor/autoload.php';
class Google_AdWords_Keyword_Planner_Settings
{
    /**
     * The single instance of Google_AdWords_Keyword_Planner_Plugin_Settings.
     * @var     object
     * @access  private
     * @since     1.0.0
     */
    private static $_instance = null;

    /**
     * The main plugin object.
     * @var     object
     * @access  public
     * @since     1.0.0
     */
    public $parent = null;

    /**
     * Prefix for plugin settings.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $base = '';

    /**
     * Available settings for plugin.
     * @var     array
     * @access  public
     * @since   1.0.0
     */
    public $settings = array();

    public function __construct($parent)
    {
        $this->parent = $parent;

        $this->base = 'gakp_';

        // Initialize settings

        add_action('init', array($this, 'init_settings'), 11);

        // Register plugin settings
        add_action('admin_init', array($this, 'register_settings'));

        // Add settings page to menu
        add_action('admin_menu', array($this, 'add_menu_item'));
        /**
         * Have to include all others page here .
         */
        require_once 'gakp-info-page.php';
        require_once 'gakp-pro-link.php';

        // Add settings link to plugins page
        //add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ) , array( $this, 'add_settings_link' ) );
    }

    /**
     * Initialise settings
     * @return void
     */
    public function init_settings()
    {
        $this->settings = $this->settings_fields();
    }

    /**
     * Add settings page to admin menu
     * @return void
     */
    public function add_menu_item()
    {
        add_submenu_page(
            'google_adwords_keyword_planner',
            'Settings',
            'Settings',
            'import',
            'gakp-settings',
            array($this, 'settings_page')
        );
    }


    /**
     * Add settings link to plugin list table
     * @param  array $links Existing links
     * @return array         Modified links
     */
    public function add_settings_link($links)
    {
        $settings_link = '<a href="options-general.php?page=' . $this->parent->_token . '_settings">' . __('Settings', 'google_adwords_keyword_planner') . '</a>';
        array_push($links, $settings_link);
        return $links;
    }

    /**
     * Build settings fields
     * @return array Fields to be displayed on settings page
     */
    public function settings_fields()
    {
        $settings['gakp_keyword_planner'] = array(
            'title' => __('Keyword Planner', 'google_adwords_keyword_planner'),
            'description' => __('Google Adwords Keyword Planner Configuration.', 'google_adwords_keyword_planner'),
            'fields' => array(
                array(
                    'id' => 'customer_client_id',
                    'label' => __('Customer Client Id', 'google_adwords_keyword_planner'),
                    'type' => 'text',
                    'default' => '',
                    'placeholder' => 'XXX-XXX-XXXX',
                    'description' => 'For customer client id <a href="http://adwords.google.com/">click here </a> <br>& For video tutorial <a href="https://www.youtube.com/watch?v=_3IGVOFtmbY&feature=youtu.be"> click here </a>',
                    'tooltip' => 'set your customer client id',

                ),
                array(
                    'id' => 'client_id',
                    'label' => __('Client Id', 'google_adwords_keyword_planner'),
                    'type' => 'text',
                    'placeholder' => '',
                    'description' => 'For client id, secret id & redirect url <a href="https://console.developers.google.com/">click here </a> <br>& see video tutorial <a href="https://www.youtube.com/watch?v=7RRxVqdmGvE&feature=youtu.be"> click here </a>',
                    'tooltip' => 'set your client id'
                ),
                array(
                    'id' => 'client_secret',
                    'label' => __('Client Secret', 'google_adwords_keyword_planner'),
                    'type' => 'text',
                    'placeholder' => '',
                    'description' => 'For client id, secret id & redirect url <a href="https://console.developers.google.com/">click here </a> <br>& see video tutorial <a href="https://www.youtube.com/watch?v=7RRxVqdmGvE&feature=youtu.be"> click here </a>',
                    'tooltip' => 'set client secret id'
                ),
                array(
                    'id' => 'redirect_url',
                    'label' => __('Redirect url', 'google_adwords_keyword_planner'),
                    'type' => 'text',
                    'placeholder' => '',
                    'description' => 'For client id, secret id & redirect url <a href="https://console.developers.google.com/">click here </a> <br>& see video tutorial <a href="https://www.youtube.com/watch?v=7RRxVqdmGvE&feature=youtu.be"> click here </a>',
                    'tooltip' => 'set redirect url'
                )
            )
        );
        //import templates settings
        /*if (class_exists('TESTING_TM')) {
        $options_class= new TESTING_TM();
        $option=$options_class->general_options();
        array_push($settings['wooain_general']['fields'],$option);
        }*/
        $settings = apply_filters($this->parent->_token . '_settings_fields', $settings);
        return $settings;
    }

    /**
     * Register plugin settings
     * @return void
     */
    public function register_settings()
    {
        if (is_array($this->settings)) {

            // Check posted/selected tab
            $current_section = '';
            if (isset($_POST['tab']) && $_POST['tab']) {
                $current_section = sanitize_text_field($_POST['tab']);
            } else {
                if (isset($_GET['tab']) && $_GET['tab']) {
                    $current_section = sanitize_text_field($_GET['tab']);
                }
            }

            foreach ($this->settings as $section => $data) {

                if ($current_section && $current_section != $section) {
                    continue;
                }
                // var_dump(get_option('google_adwords_keyword_planner_own_api_key'));exit;
                // Add section to page
                add_settings_section($section, $data['title'], array($this, 'settings_section'), $this->parent->_token . '_settings');
                foreach ($data['fields'] as $field) {
                    // var_dump($field);exit;
                    // Validation callback for field
                    $validation = '';
                    if (isset($field['callback'])) {
                        $validation = $field['callback'];
                    }
                    // Register field
                    $option_name = $this->base . $field['id'];
                    register_setting($this->parent->_token . '_settings', $option_name, $validation);
                    // Add field to page
                    if (isset($field['class'])) {
                        if (empty(get_option('google_adwords_keyword_planner_own_api_key'))) {
                            add_settings_field($field['id'], $field['label'], array($this->parent->admin, 'display_field'), $this->parent->_token . '_settings', $section,  array('field' => $field, 'prefix' => $this->base, 'class' => 'hidden gakp_own_api'));
                        } else {
                            add_settings_field($field['id'], $field['label'], array($this->parent->admin, 'display_field'), $this->parent->_token . '_settings', $section,  array('field' => $field, 'prefix' => $this->base, 'class' => 'gakp_own_api'));
                        }
                    } else {
                        add_settings_field($field['id'], $field['label'], array($this->parent->admin, 'display_field'), $this->parent->_token . '_settings', $section, array('field' => $field, 'prefix' => $this->base));
                    }
                }
                if (!$current_section) {
                    break;
                }
            }
        }
    }

    public function settings_section($section)
    {
        $html = '<p> ' . $this->settings[$section['id']]['description'] . '</p>' . "\n";
        echo $html;
    }

    /**
     * Load settings page content
     * @return void
     */
    public function settings_page()
    {
        // var_dump($instance->aaa);exit;
        // Build page HTML
        $html = '<div class="wrap" id="' . $this->parent->_token . '_settings">' . "\n";
        $html .= '<h2>' . __('Google AdWords Keyword Planner Settings', 'google_adwords_keyword_planner') . '</h2>' . "\n";

        $tab = '';
        if (isset($_GET['tab']) && $_GET['tab']) {
            $tab .= sanitize_text_field($_GET['tab']);
        }

        // Show page tabs
        if (is_array($this->settings) && 1 < count($this->settings)) {
            $html .= '<h2 class="nav-tab-wrapper">' . "\n";

            $c = 0;
            foreach ($this->settings as $section => $data) {

                // Set tab class
                $class = 'nav-tab';
                if (!isset($_GET['tab'])) {
                    if (0 == $c) {
                        $class .= ' nav-tab-active';
                    }
                } else {
                    if (isset($_GET['tab']) && $section == $_GET['tab']) {
                        $class .= ' nav-tab-active';
                    }
                }

                // Set tab link
                $tab_link = add_query_arg(array('tab' => $section));
                if (isset($_GET['settings-updated'])) {
                    $tab_link = remove_query_arg('settings-updated', $tab_link);
                }

                // Output tab
                $html .= '<a href="' . $tab_link . '" class="' . esc_attr($class) . '">' . esc_html($data['title']) . '</a>' . "\n";

                ++$c;
            }
            $html .= '</h2>' . "\n";
        }
        $html .= '<form method="post" class="form_settings" action="options.php" enctype="multipart/form-data">' . "\n";
        // Get settings fields
        ob_start();
        settings_fields($this->parent->_token . '_settings');
        do_settings_sections($this->parent->_token . '_settings');

        $html .= ob_get_clean();

        $html .= '<p class="submit">' . "\n";
        $html .= '<input type="hidden" name="tab" value="' . esc_attr($tab) . '" />' . "\n";
        $html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr(__('Save Settings', 'google_adwords_keyword_planner')) . '" />' . "\n";
        $html .= '</p>' . "\n";
        $html .= '</form>' . "\n";
        $html .= '</div>' . "\n";

        echo $html;
    }
    /**
     * Main Google_AdWords_Keyword_Planner_Plugin_Settings Instance
     *
     * Ensures only one instance of Google_AdWords_Keyword_Planner_Plugin_Settings is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see Google_AdWords_Keyword_Planner_Plugin()
     * @return Main Google_AdWords_Keyword_Planner_Plugin_Settings instance
     */
    public static function instance($parent)
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($parent);
        }
        return self::$_instance;
    } // End instance()

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->parent->_version);
    } // End __clone()

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->parent->_version);
    } // End __wakeup()
}
//new ACL_Google_AdWords_Keyword_Planner_Settings($parent);