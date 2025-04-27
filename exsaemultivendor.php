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
require_once EXSAEMULTIVENDOR_PLUGIN_DIR . 'includes/class-exsaemultivendor-product.php';
require_once EXSAEMULTIVENDOR_PLUGIN_DIR . 'includes/class-exsaemultivendor-listing.php';
require_once EXSAEMULTIVENDOR_PLUGIN_DIR . 'includes/class-exsaemultivendor-cart.php';

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

function exsaemultivendor_add_meta_boxes() {
  ExsaeMultivendor::add_meta_boxes();
}
add_action( 'add_meta_boxes', 'exsaemultivendor_add_meta_boxes' );

function exsaemultivendor_save_post($post_id) {
  ExsaeMultivendor::save_post($post_id);
}
add_action('save_post', 'exsaemultivendor_save_post');

function exsaemultivendor_enqueue_block_editor_assets() {
  ExsaeMultivendor::enqueue_block_editor_assets();
}
add_action('enqueue_block_editor_assets', 'exsaemultivendor_enqueue_block_editor_assets');

function exsaemultivendor_enqueue_scripts() {
  ExsaeMultivendor::enqueue_scripts();
}
add_action('wp_enqueue_scripts', 'exsaemultivendor_enqueue_scripts');

function exsaemultivendor_admin_menu() {
  ExsaeMultivendor::admin_menu();
}
add_action('admin_menu', 'exsaemultivendor_admin_menu');

ExsaeMultivendor::extras();

require_once EXSAEMULTIVENDOR_PLUGIN_DIR . 'includes/class-exsaemultivendor-login.php';