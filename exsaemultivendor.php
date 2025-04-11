<?php
/**
 * Plugin Name: Exsae Multivendor
 * Plugin URI: https://yourwebsite.com/exsae-multivendor
 * Description: Enables multivendor functionality on your WordPress site.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: exsae-multivendor
 * Domain Path: /languages
 */

// Prevent direct access to the plugin file
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'EXSAEMULTIVENDOR_VERSION', '1.0.0' );
define( 'EXSAEMULTIVENDOR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'EXSAEMULTIVENDOR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include autoloader (if using Composer) or manually include files
if ( file_exists( EXSAEMULTIVENDOR_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
    require_once EXSAEMULTIVENDOR_PLUGIN_DIR . 'vendor/autoload.php';
} else {
    // Manually include necessary files
    require_once EXSAEMULTIVENDOR_PLUGIN_DIR . 'includes/class-exsaemultivendor.php';
    require_once EXSAEMULTIVENDOR_PLUGIN_DIR . 'includes/admin/class-exsaemultivendor-admin.php';
    require_once EXSAEMULTIVENDOR_PLUGIN_DIR . 'includes/public/class-exsaemultivendor-public.php';
    require_once EXSAEMULTIVENDOR_PLUGIN_DIR . 'includes/database/class-exsaemultivendor-database.php';
    // Add more includes as needed
}

// Activation hook
function exsaemultivendor_activate() {
    require_once EXSAEMULTIVENDOR_PLUGIN_DIR . 'includes/class-exsaemultivendor-activator.php';
    ExsaeMultivendor_Activator::activate();
}
register_activation_hook( __FILE__, 'exsaemultivendor_activate' );

// Deactivation hook
function exsaemultivendor_deactivate() {
    require_once EXSAEMULTIVENDOR_PLUGIN_DIR . 'includes/class-exsaemultivendor-deactivator.php';
    ExsaeMultivendor_Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'exsaemultivendor_deactivate' );

// Uninstallation hook (optional)
function exsaemultivendor_uninstall() {
    require_once EXSAEMULTIVENDOR_PLUGIN_DIR . 'includes/class-exsaemultivendor-uninstaller.php';
    ExsaeMultivendor_Uninstaller::uninstall();
}
register_uninstall_hook( __FILE__, 'exsaemultivendor_uninstall' );

// Initialize the main plugin class
function exsaemultivendor_init() {
    $admin = new ExsaeMultivendor_Admin();
    $public = new ExsaeMultivendor_Public();
    $database = new ExsaeMultivendor_Database();
    $main_plugin = new ExsaeMultivendor( $admin, $public, $database );
    $main_plugin->run();
}
add_action( 'plugins_loaded', 'exsaemultivendor_init' );

// Load plugin text domain for translation
function exsaemultivendor_load_textdomain() {
    load_plugin_textdomain( 'exsae-multivendor', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'exsaemultivendor_load_textdomain' );

// Example of a custom action hook
function exsaemultivendor_do_something_on_init() {
    do_action( 'exsaemultivendor_plugin_initialized' );
}
add_action('plugins_loaded', 'exsaemultivendor_do_something_on_init');

// Example of a custom filter hook
function exsaemultivendor_filter_some_data($data){
    return apply_filters('exsaemultivendor_filter_data', $data);
}