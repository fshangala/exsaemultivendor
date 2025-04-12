<?php
class ExsaeMultivendor_Store {
  public static function restrict_post_access($query) {
    global $pagenow;
    if ( is_admin() && $query->is_main_query() && $pagenow == 'edit.php' && isset( $query->query_vars['post_type'] ) && $query->query_vars['post_type'] == 'store' && !current_user_can('edit_others_store') ) {
        $user_id = get_current_user_id();
        $query->set( 'meta_query', array(
            array(
                'key'     => 'store_owner',
                'value'   => $user_id,
                'compare' => '=',
            ),
        ) );
    }
  }

  public static function insert_post( $post_id, $post, $update ) {
    // Check if the post type is 'store' and if it's a new store creation
    if ( $post->post_type == 'store' && ! $update ) { // Only on new store creation
        $user_id = get_current_user_id();
        update_post_meta( $post_id, 'store_owner', $user_id );

        // Grant store admin capabilities to the user
        $user = new WP_User( $user_id );
        $user->add_cap( 'edit_store', true );
        $user->add_cap('edit_others_store', false);
        $user->add_cap('delete_store', true);
        $user->add_cap('delete_others_store', false);
        $user->add_cap('publish_store', true);
        $user->add_cap('read_private_stores', true);
    }
  }

  static function register_post_type() {
    $labels = array(
        'name'               => _x( 'Stores', 'post type general name', 'exsae-multivendor-stores' ),
        'singular_name'      => _x( 'Store', 'post type singular name', 'exsae-multivendor-stores' ),
        'menu_name'          => _x( 'Stores', 'admin menu', 'exsae-multivendor-stores' ),
        'name_admin_bar'     => _x( 'Store', 'add new on admin bar', 'exsae-multivendor-stores' ),
        'add_new'            => _x( 'Add New', 'store', 'exsae-multivendor-stores' ),
        'add_new_item'       => __( 'Add New Store', 'exsae-multivendor-stores' ),
        'new_item'           => __( 'New Store', 'exsae-multivendor-stores' ),
        'edit_item'          => __( 'Edit Store', 'exsae-multivendor-stores' ),
        'view_item'          => __( 'View Store', 'exsae-multivendor-stores' ),
        'all_items'          => __( 'All Stores', 'exsae-multivendor-stores' ),
        'search_items'       => __( 'Search Stores', 'exsae-multivendor-stores' ),
        'parent_item_colon'  => __( 'Parent Stores:', 'exsae-multivendor-stores' ),
        'not_found'          => __( 'No stores found.', 'exsae-multivendor-stores' ),
        'not_found_in_trash' => __( 'No stores found in Trash.', 'exsae-multivendor-stores' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true, // Show in the admin menu
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'stores' ),
        'capability_type'    => 'post',
        'hierarchical'       => false,
        'menu_position'      => 5,
        'supports'           => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
    );

    register_post_type( 'store', $args );
  }

  public static function init() {
    self::register_post_type();
  }

  public static function activate() {
    self::register_post_type();
    add_role(
        'store_admin',
        'Store Admin',
        array(
            'read'         => true,
            'edit_posts'   => true,
            'delete_posts' => true,
            'publish_posts'=> true,
            'upload_files' => true,
        )
    );
  }

  public static function deactivate() {
    unregister_post_type( 'store' );
    remove_role('store_admin');
  }
}