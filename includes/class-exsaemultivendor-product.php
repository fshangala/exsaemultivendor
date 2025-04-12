<?php
class ExsaeMultivendor_Product {
  public static function activate() {
    self::register_post_type();
    $roles = array('subscriber','contributor','author','editor','administrator');
    foreach ($roles as $role_name) {
      $role = get_role($role_name);
      if($role) {
        $role->add_cap('edit_product');
        $role->add_cap('read_product');
        $role->add_cap('delete_product');
        $role->add_cap('edit_product');
        $role->add_cap('edit_others_product');
        $role->add_cap('publish_product');
        $role->add_cap('read_private_product');
      }
    }
  }
  public static function deactivate() {
    unregister_post_type( 'product' );
  }
  
  public static function init() {
    self::register_post_type();
  }

  public static function register_post_type() {
    $labels = array(
        'name'               => _x( 'Products', 'post type general name', 'exsae-multivendor-products' ),
        'singular_name'      => _x( 'Product', 'post type singular name', 'exsae-multivendor-products' ),
        'menu_name'          => _x( 'Products', 'admin menu', 'exsae-multivendor-products' ),
        'name_admin_bar'     => _x( 'Product', 'add new on admin bar', 'exsae-multivendor-products' ),
        'add_new'            => _x( 'Add New', 'product', 'exsae-multivendor-products' ),
        'add_new_item'       => __( 'Add New Product', 'exsae-multivendor-products' ),
        'new_item'           => __( 'New Product', 'exsae-multivendor-products' ),
        'edit_item'          => __( 'Edit Product', 'exsae-multivendor-products' ),
        'view_item'          => __( 'View Product', 'exsae-multivendor-products' ),
        'all_items'          => __( 'All Products', 'exsae-multivendor-products' ),
        'search_items'       => __( 'Search Products', 'exsae-multivendor-products' ),
        'parent_item_colon'  => __( 'Parent Products:', 'exsae-multivendor-products' ),
        'not_found'          => __( 'No products found.', 'exsae-multivendor-products' ),
        'not_found_in_trash' => __( 'No products found in Trash.', 'exsae-multivendor-products' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'products' ),
        'capability_type'    => 'product',
        'capabilities'       => array(
          'edit_post'        => 'edit_product',
          'read_post'          => 'read_product',
          'delete_post'        => 'delete_product',
          'edit_posts'         => 'edit_product',
          'edit_others_posts'  => 'edit_others_product',
          'publish_posts'      => 'publish_product',
          'read_private_posts' => 'read_private_product',
        ),
        'hierarchical'       => false,
        'menu_icon'          => 'dashicons-products',
        'menu_position'      => 5,
        'supports'           => array( 'title', 'editor', 'thumbnail' ),
    );

    register_post_type( 'product', $args );
  }

  public static function add_meta_boxes() {
    add_meta_box(
        'product_store',
        __( 'Store', 'exsae-multivendor-products' ),
        array( __CLASS__, 'render_product_store_meta_box' ),
        'product',
        'side',
        'default'
    );
  }

  public static function render_product_store_meta_box($post) {
    $selected_store = get_post_meta( $post->ID, 'product_store', true );

    $stores = get_posts( array(
        'post_type'      => 'store',
        'posts_per_page' => -1,
    ) );

    if ( $stores ) {
        echo '<select name="product_store" id="product_store">';
        echo '<option value="">' . __( 'Select a Store', 'exsae-multivendor-products' ) . '</option>';
        foreach ( $stores as $store ) {
            $selected = '';
            if ( $selected_store == $store->ID ) {
                $selected = 'selected="selected"';
            }
            echo '<option value="' . esc_attr( $store->ID ) . '" ' . $selected . '>' . esc_html( $store->post_title ) . '</option>';
        }
        echo '</select>';
    } else {
        echo '<p>' . __( 'No stores available.', 'exsae-multivendor-products' ) . '</p>';
    }
  }

  public static function save_post( $post_id ) {
    if ( isset( $_POST['product_store'] ) ) {
        update_post_meta( $post_id, 'product_store', sanitize_text_field( $_POST['product_store'] ) );
    } else {
        delete_post_meta( $post_id, 'product_store' );
    }
  }

  public static function extras() {
  }
}