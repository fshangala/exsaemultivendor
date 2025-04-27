<?php
class ExsaeMultivendor_Listing {
  public static function activate() {
    self::register_post_type();
    $roles = array('subscriber','contributor','author','editor','administrator');
    foreach ($roles as $role_name) {
      $role = get_role($role_name);
      if($role) {
        $role->add_cap('edit_listing');
        $role->add_cap('read_listing');
        $role->add_cap('delete_listing');
        $role->add_cap('edit_listing');
        $role->add_cap('edit_others_listing');
        $role->add_cap('publish_listing');
        $role->add_cap('read_private_listing');
      }
    }
  }

  public static function deactivate() {
    unregister_post_type( 'listing' );
  }

  public static function register_post_type() {
    $labels = array(
        'name'               => _x( 'Listings', 'post type general name', 'exsae-multivendor-listings' ),
        'singular_name'      => _x( 'Listing', 'post type singular name', 'exsae-multivendor-listings' ),
        'menu_name'          => _x( 'Listings', 'admin menu', 'exsae-multivendor-listings' ),
        'name_admin_bar'     => _x( 'Listing', 'add new on admin bar', 'exsae-multivendor-listings' ),
        'add_new'            => _x( 'Add New', 'listing', 'exsae-multivendor-listings' ),
        'add_new_item'       => __( 'Add New Listing', 'exsae-multivendor-listings' ),
        'new_item'           => __( 'New Listing', 'exsae-multivendor-listings' ),
        'edit_item'          => __( 'Edit Listing', 'exsae-multivendor-listings' ),
        'view_item'          => __( 'View Listing', 'exsae-multivendor-listings' ),
        'all_items'          => __( 'All Listings', 'exsae-multivendor-listings' ),
        'search_items'       => __( 'Search Listings', 'exsae-multivendor-listings' ),
        'parent_item_colon'  => __( 'Parent Listings:', 'exsae-multivendor-listings' ),
        'not_found'          => __( 'No listings found.', 'exsae-multivendor-listings' ),
        'not_found_in_trash' => __( 'No listings found in Trash.', 'exsae-multivendor-listings' ),
    );
    
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'listings' ),
        'capability_type'    => 'listing',
        'capabilities'       => array(
          'edit_post'          => 'edit_listing',
          'read_post'          => 'read_listing',
          'delete_post'        => 'delete_listing',
          'edit_posts'         => 'edit_listing',
          'edit_others_posts'  => 'edit_others_listing',
          'publish_posts'      => 'publish_listing',
          'read_private_posts' => 'read_private_listing',
        ),
        'hierarchical'       => false,
        'menu_icon'          => 'dashicons-list-view', // Dashicon for the menu item
        'menu_position'      => 5,
        'supports'           => false,
        'show_in_rest'       => true,
        'rest_base'          => 'listings',
    );
    register_post_type( 'listing', $args );
  }

  public static function init() {
    self::register_post_type();
  }

  public static function add_meta_boxes() {
    add_meta_box(
      'listing_product',
      __('Listing Product','exsae-multivendor-listing'),
      function($post) {
        $selected_product = get_post_meta( $post->ID, 'listing_product', true );
    
        $products = get_posts( array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
        ) );
    
        if ( $products ) {
            echo '<select name="listing_product" id="listing_product">';
            echo '<option value="">' . __( 'Select a Product', 'exsae-multivendor-listing' ) . '</option>';
            foreach ( $products as $product ) {
                $selected = '';
                if ( $selected_product == $product->ID ) {
                    $selected = 'selected="selected"';
                }
                echo '<option value="' . esc_attr( $product->ID ) . '" ' . $selected . '>' . esc_html( $product->post_title ) . '</option>';
            }
            echo '</select>';
        } else {
            echo '<p>' . __( 'No products available.', 'exsae-multivendor-listing' ) . '</p>';
        }
      },
      'listing'
    );

    add_meta_box(
      'product_price',
      __( 'Product Price', 'exsae-multivendor-listing' ),
      function($post){
        $product_price = get_post_meta( $post->ID, 'product_price', true );
        echo '<label for="product_price">' . __( 'Price:', 'exsae-multivendor-listing' ) . '</label>';
        echo '<input type="number" placeholder="0.0" step="0.5" min="0.0" id="product_price" name="product_price" value="' . esc_attr( $product_price ) . '" />';
        echo '<p class="description">' . __( 'Enter the product price.', 'exsae-multivendor-listing' ) . '</p>';
      },
      'listing'
    );
  }

  public static function save_post( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
      return;
    }

    if ( isset( $_POST['listing_product'] ) ) {
      update_post_meta( $post_id,'listing_product', sanitize_text_field( $_POST['listing_product'] ) );
    } else {
      // update_post_meta( $post_id,'listing_product', null );
      delete_post_meta( $post_id,'listing_product');
    }

    if ( isset( $_POST['product_price'] ) ) {
      update_post_meta( $post_id, 'product_price', sanitize_text_field( $_POST['product_price'] ) );
    } else {
      // update_post_meta( $post_id, 'product_price', 0);
      delete_post_meta( $post_id, 'product_price');
    }
  }

  static function enqueue_scripts() {
    wp_enqueue_style(
      'exsaemultivendor-listing-style',
      EXSAEMULTIVENDOR_PLUGIN_URL . 'assets/css/exsaemultivendor-listing.css',
      array(),
      EXSAEMULTIVENDOR_VERSION
    );
    wp_enqueue_script(
      'exsaemultivendor-listing-script',
      EXSAEMULTIVENDOR_PLUGIN_URL . 'assets/js/exsaemultivendor-listing.js',
      array( 'jquery' ),
      EXSAEMULTIVENDOR_VERSION,
      true
    );
  }

  static function listing_shortcode( $atts) {
    $atts = shortcode_atts( array(
      'number' => 5,
    ), $atts, 'exsaemultivendor_listing' );

    $number_of_listings = absint( $atts['number'] );

    $listings = get_posts( array(
      'post_type' => 'listing',
      'posts_per_page' => $number_of_listings,
    ) );

    if ( empty( $listings ) ) {
      return '<p>' . __( 'No listings found.', 'exsae-listings' ) . '</p>';
    }

    ob_start();
    ?>
    <div class="exsaemultivendor-listings-shortcode">
        <div class="listings-flex">
            <?php foreach ( $listings as $listing ) : ?>
              <?php 
              $product_id = get_post_meta($listing->ID, 'listing_product', true);
              $product = get_post($product_id);
              $product_price = get_post_meta($listing->ID, 'product_price', true);
              $product_price = number_format((float)$product_price, 2, '.', '');
              $featured_image_url = get_the_post_thumbnail_url($product_id, 'thumbnail');
              $featured_image_url = $featured_image_url ? $featured_image_url : EXSAEMULTIVENDOR_PLUGIN_URL . 'assets/images/box.png';
              ?>
              <div class="listings-item">
                <div class="listings-item-header">
                  <h3><a href="<?php echo esc_url( get_permalink( $listing->ID ) ); ?>"><?php echo esc_html( $product->post_title ); ?></a></h3>
                </div>
                <img src="<?php echo esc_url( $featured_image_url ); ?>" alt="<?php echo esc_attr( $product->post_title ); ?>" />
                <div class="listings-item-content">
                    <div class="listing-price"><?php echo esc_html( $product_price ); ?></div>
                    <button class="add-to-cart" data-product-id="<?php echo esc_attr( $product_id ); ?>"><?php _e( 'Add to Cart', 'exsae-multivendor-listing' ); ?></button>
                </div>
              </div>
            <?php endforeach; ?>
            </div>
    </div>
    <?php
    return ob_get_clean();
  }

  static function single_listing_shortcode($atts) {
    global $post;
    if ( ! is_singular( 'listing' ) ) {
      return;
    }
    ob_start();
    $product_id = get_post_meta($post->ID, 'listing_product', true);
    $product = get_post($product_id);
    $product_price = get_post_meta($post->ID, 'product_price', true);
    $product_price = number_format((float)$product_price, 2, '.', '');
    $featured_image_url = get_the_post_thumbnail_url($product_id, 'thumbnail');
    $featured_image_url = $featured_image_url ? $featured_image_url : EXSAEMULTIVENDOR_PLUGIN_URL . 'assets/images/box.png';
    ?>
    <div class="container">
      <div class="flex flex-row flex-wrap w-full">
        <div class="flex-shrink-0">
          <img src="<?php echo esc_url( $featured_image_url ); ?>" alt="<?php echo esc_attr( $product->post_title ); ?>" />
        </div>
        <div class="flex-grow-1">
          <h3><?php echo esc_html( $product->post_title ); ?></h3>
          <div class="listing-price"><?php echo esc_html( $product_price ); ?></div>
          <button class="btn" data-product-id="<?php echo esc_attr( $product_id ); ?>"><?php _e( 'Add to Cart', 'exsae-multivendor-listing' ); ?></button>
        </div>
      </div>
      <div class="product-details">
        <?php echo wp_kses_post( apply_filters( 'the_content', $product->post_content ) ); ?>
      </div>
    </div>
    <?php
    return ob_get_clean();
  }

  public static function extras() {
    function add_product_name_column( $columns ) {
      $columns = array(
        'product_name' => __('Product Name', 'exsae-multivendor-listing'),
        'product_price' => __('Product Price', 'exsae-multivendor-listing'),
      );
      return $columns;
    }
    add_filter( 'manage_listing_posts_columns', 'add_product_name_column' );
    function display_product_name_column( $column, $post_id ) {
      $product_id = get_post_meta( $post_id, 'listing_product', true );
      $product = null;
      if($product_id) {
        $product = get_post($product_id);
      }

      if ( $column == 'product_name' ) {
        if($product) {
          $product = get_post($product_id);
          echo esc_html( $product->post_title );
        } else {
          echo __('No product associated!','exsae-multivendor-listing');
        }
      }

      if ($column == 'product_price') {
        if($product) {
          $product_price = get_post_meta($post_id, 'product_price', true);
          echo esc_html( $product_price );
        } else {
          echo __('0.0','exsae-multivendor-listing');
        }
      }
    }
    add_action( 'manage_listing_posts_custom_column', 'display_product_name_column', 10, 2 );
    add_shortcode( 'exsaemultivendor_listing', [__CLASS__,'listing_shortcode'] );
    add_shortcode( 'exsaemultivendor_single_listing', [__CLASS__,'single_listing_shortcode'] );
    add_shortcode( 'exsaemultivendor_cart', [ExsaeMultivendor_Cart::class,'render_cart_shortcode'] );
  }
}

