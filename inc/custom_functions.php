<?php

// Display additional information in single product page
function display_custom_information_in_additional_tab( $product_attributes ) {

    global $product;

    // Get additional information
    $color       = get_post_meta( $product->get_id(), '_color', true );
    $session     = get_post_meta( $product->get_id(), '_season', true );
    $size        = get_post_meta( $product->get_id(), '_size', true );
    $mag         = get_post_meta( $product->get_id(), '_mag', true );
    $desc_mod_id = get_post_meta( $product->get_id(), '_desc_mod_id', true );
    $promo       = get_post_meta( $product->get_id(), '_promo', true );

    // Add custom information to the attributes array
    $product_attributes['jalal-addition-information1'] = array(
        'label' => __( 'Color' ),
        'value' => esc_html( $color ),
    );

    $product_attributes['jalal-addition-information2'] = array(
        'label' => __( 'PREZZO' ),
        'value' => esc_html( $session ),
    );

    $product_attributes['jalal-addition-information3'] = array(
        'label' => __( 'Size' ),
        'value' => esc_html( $size ),
    );

    $product_attributes['jalal-addition-information4'] = array(
        'label' => __( 'Mag' ),
        'value' => esc_html( $mag ),
    );

    $product_attributes['jalal-addition-information5'] = array(
        'label' => __( 'Promo' ),
        'value' => esc_html( $promo ),
    );


    return $product_attributes;
}

add_filter( 'woocommerce_display_product_attributes', 'display_custom_information_in_additional_tab' );


// create a custom tab for products
function add_custom_tab( $tabs ) {

    $tabs['product-infos'] = array(
        'title'    => __( 'Additional Informations' ),
        'priority' => 50,
        'callback' => 'display_custom_tab_content',
    );
    return $tabs;
}

function display_custom_tab_content() {
    global $product;

    // Get additional information
    $color       = get_post_meta( $product->get_id(), '_color', true );
    $session     = get_post_meta( $product->get_id(), '_season', true );
    $size        = get_post_meta( $product->get_id(), '_size', true );
    $mag         = get_post_meta( $product->get_id(), '_mag', true );
    $desc_mod_id = get_post_meta( $product->get_id(), '_desc_mod_id', true );
    $promo       = get_post_meta( $product->get_id(), '_promo', true );

    // Output the content in a table
    echo '<h4>More Information</h4>';
    echo '<table>';
    echo '<tr><td>Color:</td><td>' . esc_html( $color ) . '</td></tr>';
    echo '<tr><td>Size:</td><td>' . esc_html( $size ) . '</td></tr>';
    echo '<tr><td>Type:</td><td>' . esc_html( $desc_mod_id ) . '</td></tr>';
    echo '<tr><td>Promo:</td><td>' . esc_html( $promo ) . '</td></tr>';
    echo '</table>';
}

add_filter( 'woocommerce_product_tabs', 'add_custom_tab' );
