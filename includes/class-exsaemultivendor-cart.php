<?php

class ExsaeMultivendor_Cart {

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

    static function extras() {
        add_shortcode( 'exsaemultivendor_cart', array( __CLASS__, 'render_cart_shortcode' ) );
    }

    static function create_orders_from_cart(array $cart) {
      $orders = [];
      foreach ($cart['items'] as $item) {
        $listing = get_post($item['listing_id']);
        $product_price = get_post_meta($listing->ID, 'product_price', true);
        $product_id = get_post_meta($listing->ID, 'listing_product', true);
        $product = get_post($product_id);
        $store_id = get_post_meta($product->ID, 'product_store', true);
        $store = get_post($store_id);

        $order_item = array(
          'listing' => $listing,
          'product' => $product,
          'quantity' => $item['quantity'],
          'price' => $product_price,
          'total' => $item['quantity'] * $product_price
        );

        $current_order_index=-1;
        foreach ($orders as $key => $value) {
          if($orders[$key]['store']->ID == $store_id) {
            $current_order_index = $key;
            break;
          }
        }
        
        if($current_order_index < 0) {
          $current_order = array(
            'store' => $store,
            'items' => [$order_item]
          );
          array_push($orders,$current_order);
        } else {
          array_push($orders[$current_order_index]['items'],$order_item);
        }
      }
      return $orders;
    }

    static function render_cart_shortcode() {
      ob_start();
      if(isset($_POST['cart']) && $_POST['cart'] == 'checkout'){
        $cart_json = stripslashes($_POST['cart_data']);
        $cart = json_decode($cart_json,true);
        $user_id = get_current_user_id();
        $user = $user_id ? get_userdata($user_id) : null;
        $user_metadata = $user ? get_user_meta($user_id) : null;

        $orders = self::create_orders_from_cart($cart);
        ?>
        <div>
          <h2>Orders</h2>
          <div>
            <?php
            foreach ($orders as $order) {
              ?>
              <div>
                <h4><?php echo $order['store']->post_title; ?></h4>
                <table class="w-100">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Price</th>
                      <th>Quantity</th>
                      <th>Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    foreach ($order['items'] as $order_item) {
                      ?>
                      <tr>
                        <td><?php echo $order_item['product']->post_title; ?></td>
                        <td><?php echo $order_item['price'] ?></td>
                        <td><?php echo $order_item['quantity'] ?></td>
                        <td><?php echo $order_item['total'] ?></td>
                      </tr>
                      <?php
                    }
                    ?>
                  </tbody>
                </table>
              </div>
              <?php
            }
            ?>
          </div>
        </div>
        <div>
          <h2>Billing information</h2>
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
              <div>
                <label>Email</label>
                <input class="w-100 p-1" type="email" name="email" placeholder="Email" value="<?php echo $user->data->user_email ?? ''; ?>" required>
              </div>
              <div>
                <label>Address</label>
                <input class="w-100 p-1" type="text" name="address" placeholder="Address" value="<?php echo $user_metadata['billing_address'][0] ?? ''; ?>" required>
              </div>
              <div>
                <label>City</label>
                <input class="w-100 p-1" type="text" name="city" placeholder="City" value="<?php echo $user_metadata['billing_city'][0] ?? ''; ?>" required>
              </div>
              <div>
                <label>State/Province</label>
                <input class="w-100 p-1" type="text" name="state" placeholder="State" value="<?php echo $user_metadata['billing_state'][0] ?? ''; ?>" required>
              </div>
              <div>
                <label>Zip Code</label>
                <input class="w-100 p-1" type="text" name="zip" placeholder="Zip Code" value="<?php echo $user_metadata['billing_postcode'][0] ?? ''; ?>" required>
              </div>
              <div>
                <label>Country</label>
                <input class="w-100 p-1" type="text" name="country" placeholder="Country" value="<?php echo $user_metadata['billing_country'][0] ?? ''; ?>" required>
              </div>
              <div>
                <label>Phone Number</label>
                <input class="w-100 p-1" type="text" name="phone" placeholder="Phone Number" value="<?php echo $user_metadata['billing_phone'][0] ?? ''; ?>" required>
              </div>

              <input type="hidden" name="cart" value="submit">
              <input type="hidden" name="cart_data" value="<?php echo base64_encode($cart_json); ?>">
              <button type="submit" class="btn btn-success">Place Order</button>
            </div>
          </form>
        </div>
        <?php
      } elseif (isset($_POST['cart']) && $_POST['cart'] == 'submit') {
        $cart_json = base64_decode($_POST['cart_data']);
        $cart = json_decode($cart_json,true);
        $orders = self::create_orders_from_cart($cart);
        foreach ($orders as $order) {
          $order['billing_first_name'] = $_POST['first_name'];
          $order['billing_last_name'] = $_POST['last_name'];
          $order['billing_email'] = $_POST['email'];
          $order['billing_address'] = $_POST['address'];
          $order['billing_city'] = $_POST['city'];
          $order['billing_state'] = $_POST['state'];
          $order['billing_zip'] = $_POST['zip'];
          $order['billing_country'] = $_POST['country'];
          $order['billing_phone'] = $_POST['phone'];
          do_action('create_order', $order);
        }
        ?>
        <p>Your order has been successfully submitted, please check your email inbox for an invoice!</p>
        <?php
      } else {
        ?>
        <div class="cart-container"></div>
        <?php
      }
      return ob_get_clean();
    }
}
