<?php
class ExsaeMultivendor {
  public static function activate() {
    ExsaeMultivendor_Store::activate();
    flush_rewrite_rules();
  }
  public static function deactivate() {
    ExsaeMultivendor_Store::deactivate();
  }
  public static function uninstall() {}
  
  public static function init() {
    ExsaeMultivendor_Store::init();
  }

  public static function insert_post($post_id, $post, $update) {
    ExsaeMultivendor_Store::insert_post($post_id, $post, $update);
  }
}