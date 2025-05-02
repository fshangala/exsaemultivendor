<?php
class ExsaeMultivendor_Order {
  static function activate() {
    self::register_post_type();
    $roles = array('subscriber','contributor','author','editor','administrator');
    foreach ($roles as $role_name) {
      $role = get_role($role_name);
      if($role) {
        $role->add_cap('edit_order');
        $role->add_cap('read_order');
        $role->add_cap('delete_order');
        $role->add_cap('edit_order');
        $role->add_cap('edit_others_order');
        $role->add_cap('publish_order');
        $role->add_cap('read_private_order');
      }
    }
  }

  static function init() {
    self::register_post_type();
  }

  static function deactivate() {
    unregister_post_type('order');
  }

  static function extras() {
    add_action('create_order', array(__CLASS__, 'create_order'), 10, 1);
  }

  static function register_post_type() {
    $labels = array(
        'name'               => _x( 'Orders', 'post type general name', 'exsae-multivendor-order' ),
        'singular_name'      => _x( 'Order', 'post type singular name', 'exsae-multivendor-order' ),
        'menu_name'          => _x( 'Orders', 'admin menu', 'exsae-multivendor-order' ),
        'name_admin_bar'     => _x( 'Order', 'add new on admin bar', 'exsae-multivendor-order' ),
        'add_new'            => _x( 'Add New', 'order', 'exsae-multivendor-order' ),
        'add_new_item'       => __( 'Add New Order', 'exsae-multivendor-order' ),
        'new_item'           => __( 'New Order', 'exsae-multivendor-order' ),
        'edit_item'          => __( 'Edit Order', 'exsae-multivendor-order' ),
        'view_item'          => __( 'View Order', 'exsae-multivendor-order' ),
        'all_items'          => __( 'All Orders', 'exsae-multivendor-order' ),
        'search_items'       => __( 'Search Orders', 'exsae-multivendor-order' ),
        'parent_item_colon'  => __( 'Parent Orders:', 'exsae-multivendor-order' ),
        'not_found'          => __( 'No orders found.', 'exsae-multivendor-order' ),
        'not_found_in_trash' => __( 'No orders found in Trash.', 'exsae-multivendor-order' ),
    );
    
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'orders' ),
        'capability_type'    => 'order',
        'capabilities'       => array(
          'edit_post'          => 'edit_order',
          'read_post'          => 'read_order',
          'delete_post'        => 'delete_order',
          'edit_posts'         => 'edit_order',
          'edit_others_posts'  => 'edit_others_order',
          'publish_posts'      => 'publish_order',
          'read_private_posts' => 'read_private_order',
        ),
        'hierarchical'       => false,
        'menu_icon'          => 'dashicons-list-view', // Dashicon for the menu item
        'menu_position'      => 5,
        'supports'           => false,
        'show_in_rest'       => true,
        'rest_base'          => 'orders',
    );
    register_post_type( 'order', $args );
  }

  static function create_order(array $order) {
    $meta_input = [];
    foreach ($order as $key => $value) {
      if($key == "store") {
        $meta_input['store_id'] = $order['store']->ID; 
      } elseif($key == "items") {
        $items = [];
        foreach($order['items'] as $item) {
          $item_array = array(
            'listing_id' => $item['listing']->ID,
            'quantity' => $item['quantity']
          );
          array_push($items,json_encode($item_array));
        }
        $meta_input['items'] = $items;
      } else {
        $meta_input[$key] = $value;
      }
    }
    $order_object = array(
      'post_title' => 'Order #' . time(),
      'post_type' => 'order',
      'post_status' => 'publish',
      'meta_input' => $meta_input,
    );
    wp_insert_post($order);
  }
}
?>