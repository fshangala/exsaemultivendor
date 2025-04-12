<?php
// This file contains functions to manage the shopping cart for the Exsae Multivendor plugin.
// It uses transients to store cart data for each user, allowing for a simple and efficient way to manage cart items.
function exsae_add_to_cart( $listing_id, $quantity = 1 ) {
    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        return false; // User not logged in, you should handle this case.
    }

    $cart_name = 'cart_user_' . $user_id;
    $cart = get_transient( $cart_name );

    if ( ! is_array( $cart ) ) {
        $cart = array();
    }

    // Add or update the cart item
    if ( isset( $cart[ $listing_id ] ) ) {
        $cart[ $listing_id ] += $quantity;
    } else {
        $cart[ $listing_id ] = $quantity;
    }

    set_transient( $cart_name, $cart, 24 * HOUR_IN_SECONDS ); // Expires in 24 hours
    return true;
}

function exsae_get_cart() {
    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        return array();
    }
    $cart_name = 'cart_user_' . $user_id;
    return get_transient( $cart_name );
}

function exsae_update_cart_item_quantity( $listing_id, $quantity ) {
    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        return false;
    }

    $cart_name = 'cart_user_' . $user_id;
    $cart = get_transient( $cart_name );

    if ( is_array( $cart ) && isset( $cart[ $listing_id ] ) ) {
        $cart[ $listing_id ] = $quantity;
        set_transient( $cart_name, $cart, 24 * HOUR_IN_SECONDS );
        return true;
    }

    return false;
}

function exsae_remove_cart_item( $listing_id ) {
    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        return false;
    }

    $cart_name = 'cart_user_' . $user_id;
    $cart = get_transient( $cart_name );

    if ( is_array( $cart ) && isset( $cart[ $listing_id ] ) ) {
        unset( $cart[ $listing_id ] );
        set_transient( $cart_name, $cart, 24 * HOUR_IN_SECONDS );
        return true;
    }

    return false;
}

function exsae_clear_cart() {
    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        return false;
    }

    $cart_name = 'cart_user_' . $user_id;
    delete_transient( $cart_name );
    return true;
}

?>