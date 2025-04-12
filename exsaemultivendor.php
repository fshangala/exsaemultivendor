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
require_once EXSAEMULTIVENDOR_PLUGIN_DIR . 'includes/class-exsaemultivendor-store.php';

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

// Initialize plugin
function exsaemultivendor_init() {
  ExsaeMultivendor::init();
}
add_action('init', 'exsaemultivendor_init');

function exsaemultivendor_insert_post($post_id, $post, $update) {
  ExsaeMultivendor::insert_post($post_id, $post, $update);
}
add_action('wp_insert_post', 'exsaemultivendor_insert_post', 10, 3);

// Restrict access to stores based on user ownership
function exsaemultivendor_restrict_post_access( $query ) {
  ExsaeMultivendor::restrict_post_access( $query );
}
add_action( 'pre_get_posts', 'exsaemultivendor_restrict_post_access' );

require_once EXSAEMULTIVENDOR_PLUGIN_DIR . 'includes/class-exsaemultivendor-login.php';