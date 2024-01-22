<?php

// Add a filter hook to modify the cart item thumbnail
function custom_cart_item_thumbnail( $product_thumbnail, $cart_item, $cart_item_key ) {
    // Get the product ID from the cart item
    $product_id = $cart_item['product_id'];

    // Get the product thumbnail URL
    $thumbnail_url = get_the_post_thumbnail_url( $product_id, 'thumbnail' );

    // If a thumbnail URL is available, use it
    if ( $thumbnail_url ) {
        $product_thumbnail = '<img src="' . esc_url( $thumbnail_url ) . '" alt="' . esc_attr( get_the_title( $product_id ) ) . '" class="cart-item-thumbnail" />';
    }

    return $product_thumbnail;
}

// Hook the custom function to the 'woocommerce_cart_item_thumbnail' filter
add_filter( 'woocommerce_cart_item_thumbnail', 'custom_cart_item_thumbnail', 10, 3 );



