<?php
/**
 * Plugin Name: Exsae Multivendor
 * Plugin URI: https://github.com/fshangala/exsaemultivendor
 * Description: Enables multivendor functionality on your WordPress site.
 * Version: 1.0.0
 * Author: Funduluka Shangala
 * Author URI: https://github.com/fshangala
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

// Manually include necessary files
require_once EXSAEMULTIVENDOR_PLUGIN_DIR . 'includes/class-exsaemultivendor.php';

// Activation hook
function exsaemultivendor_activate() {
  ExsaeMultivendor::activate();
}
register_activation_hook( __FILE__, 'exsaemultivendor_activate' );

// Deactivation hook
function exsaemultivendor_deactivate() {
  ExsaeMultivendor::deactivate();
}
register_deactivation_hook( __FILE__, 'exsaemultivendor_deactivate' );

// Uninstallation hook (optional)
function exsaemultivendor_uninstall() {
  ExsaeMultivendor::uninstall();
}
register_uninstall_hook( __FILE__, 'exsaemultivendor_uninstall' );

// Initialize the main plugin class
function exsaemultivendor_init() {
  if ( class_exists( 'ExsaeMultivendor' ) ) {
    $exsaemultivendor = new ExsaeMultivendor();
    $exsaemultivendor->init();
  }
}
add_action( 'plugins_loaded', 'exsaemultivendor_init' );

require_once EXSAEMULTIVENDOR_PLUGIN_DIR . 'includes/class-exsaemultivendor-login.php';