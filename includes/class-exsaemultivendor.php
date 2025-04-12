<?php
class ExsaeMultivendor {
  public static function activate() {
    ExsaeMultivendor_Store::activate();
    ExsaeMultivendor_Product::activate();
    flush_rewrite_rules();
  }

  public static function deactivate() {
    ExsaeMultivendor_Store::deactivate();
    ExsaeMultivendor_Product::deactivate();
    flush_rewrite_rules();
  }

  public static function uninstall() {
    flush_rewrite_rules();
  }
  
  public static function init() {
    ExsaeMultivendor_Store::init();
    ExsaeMultivendor_Product::init();
  }

  public static function insert_post($post_id, $post, $update) {
    ExsaeMultivendor_Store::insert_post($post_id, $post, $update);
  }

  public static function add_meta_boxes() {
    ExsaeMultivendor_Store::add_meta_boxes();
    ExsaeMultivendor_Product::add_meta_boxes();
  }

  public static function save_post($post_id) {
    ExsaeMultivendor_Product::save_post($post_id);
  }

  public static function extras() {
    ExsaeMultivendor_Store::extras();
    ExsaeMultivendor_Product::extras();
  }
}