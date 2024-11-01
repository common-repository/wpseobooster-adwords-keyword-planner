<?php

/**
 * Plugin Name:       Google AdWords Keyword Planner
 * Plugin URI:        http://wpseobooster.com
 * Description:       Google AdWords Keyword Planner Plugin helps You to find the best low competitive keywords without leaving the WordPress platform. Now you can easily get the top ranking on Google SERP's by using our Google Adwords Keyword Planner.
 * Version:           1.0.0
 * Author:            WPSEOBooster
 * Author URI:        https://wpseobooster.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       google_adwords_keyword_planner
 * Domain Path:       /languages
 * WC requires at least: 3.0.0
 * WC tested up to: 5.4.2
 * Requires PHP: 5.6
 * Stable tag: 3.0.8
 */

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/**
 * Currently plugin version.
 * Rename this for your plugin and update it as you release new versions.
 */
if (!defined('GOOGLE_AWORDS_KEYWORD_PLANNER_VERSION')) {
    define('GOOGLE_AWORDS_KEYWORD_PLANNER_VERSION', '1.0.0');
}

if (!defined('GOOGLE_AWORDS_KEYWORD_PLANNER_PLUGIN_FILE')) {
    define('GOOGLE_AWORDS_KEYWORD_PLANNER_PLUGIN_FILE', __FILE__);
}
if (!defined('GOOGLE_AWORDS_KEYWORD_PLANNER_ABSPATH')) {
    define('GOOGLE_AWORDS_KEYWORD_PLANNER_ABSPATH', dirname(GOOGLE_AWORDS_KEYWORD_PLANNER_PLUGIN_FILE) . '/');
}

if (!defined('GOOGLE_AWORDS_KEYWORD_PLANNER_URL')) {
    define('GOOGLE_AWORDS_KEYWORD_PLANNER_URL', plugin_dir_url(__FILE__));
}
if (!defined('GOOGLE_AWORDS_KEYWORD_PLANNER_IMG_URL')) {
    define('GOOGLE_AWORDS_KEYWORD_PLANNER_IMG_URL', plugin_dir_url(__FILE__) . 'assets/img/');
}

// Load plugin basic class files
include_once ABSPATH . 'wp-admin/includes/plugin.php';
include_once 'includes/class-google-adwords-keyword-planner.php';


function google_adwords_keyword_planner()
{

    // Load dependencies.
    $instance = Google_AdWords_Keyword_Planner::get_instance(__FILE__, GOOGLE_AWORDS_KEYWORD_PLANNER_VERSION);
    // var_dump($instance->aaa);exit;
    if (is_null($instance->settings)) {
        $instance->settings = Google_AdWords_Keyword_Planner_Settings::instance($instance);
    }
    return $instance;
}
add_action('plugins_loaded', 'google_adwords_keyword_planner');


function gakp_admin_notice__error()
{
    $class = 'notice notice-error';
    $message = __("Sorry, you can't active this plugin without WooCommerce. Please  install and active woocommerce plugin first.", 'acl-wooain');
    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
}
function gakp_on_activate()
{
    
    if (!get_option('gakp_keyword_planner')) {
        update_option('gakp_keyword_planner', 'enable');
    }
    if (!get_option('gakp_customer_client_id')) {
        update_option('gakp_customer_client_id', '');
    }
    if (!get_option('gakp_client_id')) {
        update_option('gakp_client_id', '');
    }
    if (!get_option('gakp_client_secret')) {
        update_option('gakp_client_secret', '');
    }
    if (!get_option('gakp_redirect_url')) {
        update_option('gakp_redirect_url', '');
    }
}
// run the install scripts upon plugin activation
register_activation_hook(__FILE__, 'gakp_on_activate');

//Redirect to setting page.
if (!function_exists('gakp_settings_redirect')) {
    function gakp_settings_redirect($plugin)
    {
        if ($plugin == plugin_basename(__FILE__)) {
            wp_redirect(admin_url('admin.php?page=gakp-settings'));
            exit();
        }
    }
    add_action('activated_plugin', 'gakp_settings_redirect');
}
