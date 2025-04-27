<?php

class ExsaeMultivendor_Cart {

    /**
     * Gets the user's cart.
     *
     * @return array The user's cart, or an empty array if no cart exists.
     */
    public static function get() {
        $user_id = get_current_user_id();
        if ( ! $user_id ) {
            return array(); // Return empty array if user not logged in.
        }

        $cart_json = get_user_meta( $user_id, 'cart', true );
        $cart      = json_decode( $cart_json, true );

        return ( is_array( $cart ) ) ? $cart : array(); // Simplified return
    }

    /**
     * Adds an item to the user's cart.
     *
     * @param int $product_id The ID of the product to add.
     * @param int $quantity   The quantity of the product to add.
     *
     * @return bool True on success, false on failure.
     */
    public static function add( $product_id, $quantity = 1 ) {
        $user_id = get_current_user_id();
        if ( ! $user_id ) {
            return false; // User not logged in.  Consider throwing an exception.
        }

        if ( ! is_numeric( $product_id ) || ! is_numeric( $quantity ) || $quantity < 1 ) {
            return false; // Handle invalid input
        }

        $cart = self::get();

        if ( isset( $cart[ $product_id ] ) ) {
            $cart[ $product_id ] += $quantity;
        } else {
            $cart[ $product_id ] = $quantity;
        }

        $cart_json = wp_json_encode( $cart );
        if ( $cart_json ) {
            update_user_meta( $user_id, 'cart', $cart_json );
            return true;
        }
        return false; // Return false if wp_json_encode fails
    }

    /**
     * Updates the quantity of a product in the user's cart.
     *
     * @param int $product_id The ID of the product to update.
     * @param int $quantity   The new quantity.
     *
     * @return bool True on success, false on failure.
     */
    public static function update( $product_id, $quantity ) {
        $user_id = get_current_user_id();
        if ( ! $user_id ) {
            return false; // User not logged in
        }

        if ( ! is_numeric( $product_id ) || ! is_numeric( $quantity ) ) {
            return false; // Handle invalid input
        }

        $cart = self::get();

        if ( isset( $cart[ $product_id ] ) ) {
            $cart[ $product_id ] = $quantity;
            if ( $quantity <= 0 ) {
                unset( $cart[ $product_id ] );
            }
            $cart_json = wp_json_encode( $cart );
            if($cart_json){
               update_user_meta( $user_id, 'cart', $cart_json );
               return true;
            }
            return false;
        }

        return false;
    }

    /**
     * Removes a product from the user's cart.
     *
     * @param int $product_id The ID of the product to remove.
     *
     * @return bool True on success, false on failure.
     */
    public static function remove( $product_id ) {
        $user_id = get_current_user_id();
        if ( ! $user_id ) {
             return false; // User not logged in
        }

        if ( ! is_numeric( $product_id ) ) {
            return false; //handle invalid input
        }

        $cart = self::get();

        if ( isset( $cart[ $product_id ] ) ) {
            unset( $cart[ $product_id ] );
            $cart_json = wp_json_encode( $cart );
            if($cart_json){
                update_user_meta( $user_id, 'cart', $cart_json );
                return true;
            }
            return false;
        }

        return false;
    }

    /**
     * Clears the user's cart.
     *
     * @return bool True on success, false on failure.
     */
    public static function clear() {
        $user_id = get_current_user_id();
        if ( ! $user_id ) {
            return false; // User not logged in
        }

        delete_user_meta( $user_id, 'cart' );

        return true;
    }

    /**
     * Get the number of items in the user's cart.
     *
     * @return int The number of items in the cart.
     */
    public static function get_item_count() {
        $user_id = get_current_user_id();
         if ( ! $user_id ) {
             return 0; // User not logged in
        }
        $cart  = self::get();
        $count = 0;
        foreach ( $cart as $quantity ) {
            $count += $quantity;
        }

        return $count;
    }

    /**
     * Renders the user's cart using a shortcode.
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML representation of the user's cart.
     */
    static function render_cart_shortcode( $atts ) {
      $atts = shortcode_atts(
          array(
              'empty_message' => 'Your cart is currently empty.',
          ),
          $atts,
          'exsaemultivendor_cart'
      );

      $cart      = self::get();
      $user_id   = get_current_user_id();

      if ( ! $user_id ) {
          return '<p>Please log in to view your cart.</p>'; // Or redirect to login page
      }

      if ( empty( $cart ) ) {
          return '<p>' . esc_html( $atts['empty_message'] ) . '</p>';
      }

      ob_start();
      ?>
      <div class="exsaemultivendor-cart">
          <h2>Your Cart</h2>
          <ul>
              <?php
              $total_price = 0; // Initialize total price
              foreach ( $cart as $listing_id => $quantity ) :
                  // Get product details (replace with your actual product data retrieval)
                  $product_id = get_post_meta( $listing_id, 'listing_product', true );
                  $product = get_post($product_id);
                  $product_price = get_post_meta($listing_id, 'product_price', true);
                  if ( ! $product ) {
                      continue; // Skip if product doesn't exist
                  }
                  $subtotal      = $product_price * $quantity;
                  $total_price   += $subtotal;
                  ?>
                  <li class="exsae-cart-item">
                      <span class="exsae-cart-item-name"><?php echo esc_html( $product->post_title ); ?></span>
                      <span class="exsae-cart-item-quantity">Quantity: <?php echo esc_html( $quantity ); ?></span>
                      <span class="exsae-cart-item-price">Price: <?php echo wc_price( $product_price ); ?></span>
                      <span class="exsae-cart-item-subtotal">Subtotal: <?php echo wc_price( $subtotal ); ?></span>
                      <button class="exsae-remove-from-cart" data-product-id="<?php echo esc_attr( $listing_id ); ?>">Remove</button>
                      <div class="exsae-quantity-changer">
                          <button class="exsae-change-quantity" data-product-id="<?php echo esc_attr( $listing_id ); ?>" data-quantity="<?php echo esc_attr( $quantity - 1 ); ?>">-</button>
                          <input type="number" value="<?php echo esc_attr( $quantity ); ?>" class="exsae-cart-quantity-input" data-product-id="<?php echo esc_attr( $product_id ); ?>">
                          <button class="exsae-change-quantity" data-product-id="<?php echo esc_attr( $listing_id ); ?>" data-quantity="<?php echo esc_attr( $quantity + 1 ); ?>">+</button>
                      </div>
                  </li>
              <?php endforeach; ?>
          </ul>
          <p class="exsae-cart-total">Total: <?php echo wc_price( $total_price ); ?></p>
          <a href="" class="btn">Checkout</a>
      </div>
      <?php

      return ob_get_clean();
    }

    static function render_cart_page() {
      $user_id = get_current_user_id();

      $cart = self::get();

      if (empty($cart)) {
          echo '<p>Your cart is empty.</p>';
          return;
      }

      ob_start();
      ?>
      <div>
          <h2>Your Cart</h2>
          <ul>
              <?php
              foreach ($cart as $listing_id => $quantity) {
                  // Get product details (replace with your actual product data retrieval)
                  $product_id = get_post_meta($listing_id, 'listing_product', true);
                  $product = get_post($product_id);
                  if (!$product) {
                      continue; // Skip if product doesn't exist
                  }
                  ?>
                  <li>
                      <span><?php echo esc_html($product->post_title); ?></span>
                      <span>Quantity: <?php echo esc_html($quantity); ?></span>
                      <button class="exsae-remove-from-cart" data-product-id="<?php echo esc_attr($listing_id); ?>">Remove</button>
                  </li>
              <?php } ?>
          </ul>
          <a href="" class="btn">Checkout</a>
      </div>
      <?php
      return ob_get_clean();
    }

    static function admin_menu() {
        add_menu_page(
            'Cart',
            'Cart',
            'manage_options',
            'exsaemultivendor-cart',
            array( __CLASS__, 'render_cart_page' ),
            'dashicons-cart',
            6
        );
    }
}
