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

    static function render_cart_page() {
        echo do_shortcode( '[exsaemultivendor_cart]' );
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

    static function enqueue_scripts() {
        wp_enqueue_script(
          'exsaemultivendor-cart-script',
          EXSAEMULTIVENDOR_PLUGIN_URL . 'assets/js/exsaemultivendor-cart.js',
          array( 'jquery' ),
          EXSAEMULTIVENDOR_VERSION,
          true
        );
    }

    static function admin_enqueue_scripts() {
        wp_enqueue_script(
          'exsaemultivendor-cart-admin-script',
          EXSAEMULTIVENDOR_PLUGIN_URL . 'assets/js/exsaemultivendor-cart.js',
          array( 'jquery' ),
          EXSAEMULTIVENDOR_VERSION,
          true
        );
    }

    static function render_cart_shortcode() {
      ob_start();
      if(isset($_POST['cart']) && $_POST['cart'] == 'checkout'){
        $user_id = get_current_user_id();
        $user = $user_id ? get_userdata($user_id) : null;
        $user_metadata = $user ? get_user_meta($user_id) : null;
        echo var_dump($user_metadata);
        ?>
        <div>
          <h2>Checkout</h2>
          <form method="POST">
            <div class="flex flex-column gap-2">
              <div>
                <label>First Name</label>
                <input class="w-100 p-1" type="name" name="first_name" placeholder="First Name" value="<?php echo $user_metadata['first_name'][0] ?? ''; ?>" required>
              </div>
              <div>
                <label>Last Name</label>
                <input class="w-100 p-1" type="name" name="last_name" placeholder="Last Name" value="<?php echo $user_metadata['last_name'][0] ?? ''; ?>" required>
              </div>
              <input type="email" name="email" placeholder="Email" required>
              <input type="text" name="address" placeholder="Address" required>
              <input type="text" name="city" placeholder="City" required>
              <input type="text" name="state" placeholder="State" required>
              <input type="text" name="zip" placeholder="Zip Code" required>
              <input type="text" name="country" placeholder="Country" required>
              <input type="text" name="phone" placeholder="Phone Number" required>

              <input type="hidden" name="cart" value="submit">
              <input type="hidden" name="cart_data" value="<?php echo esc_attr( $_POST['cart_data'] ); ?>">
              <button type="submit" class="btn btn-success">Place Order</button>
            </div>
          </form>
        </div>
        <?php
      } elseif (isset($_POST['cart']) && $_POST['cart'] == 'submit') {
        $cart = stripslashes($_POST['cart_data']);
        do_action('create_order', $cart);
      } else {
        ?>
        <div class="cart-container"></div>
        <?php
      }
      return ob_get_clean();
    }

    static function extras() {
        add_shortcode( 'exsaemultivendor_cart', array( __CLASS__, 'render_cart_shortcode' ) );
    }
}
