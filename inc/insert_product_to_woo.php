<?php

// Include necessary files
require_once JALAL_PLUGIN_PATH . '/vendor/autoload.php';
use Automattic\WooCommerce\Client;

// Function to insert products into WooCommerce
function product_insert_woocommerce() {

    // Get global $wpdb object
    global $wpdb;

    // Define table names
    $table_name = $wpdb->prefix . 'sync_products';

    // WooCommerce store information
    $website_url     = home_url();
    $consumer_key    = 'ck_1d3c3981897b00cd3904f6a805bbe023f5b03dd4';
    $consumer_secret = 'cs_2ee2e885bcecb478f822fc4222fdbc837ed9121d';

    // Retrieve pending products from the database
    $products = $wpdb->get_results( "SELECT * FROM $table_name WHERE status = 'pending' LIMIT 1" );

    foreach ( $products as $product ) {

        // Retrieve product data
        $serial_id = isset( $product->id ) ? $product->id : '';
        $sku       = isset( $product->product_id ) ? $product->product_id : '';
        $p_num     = isset( $product->sku ) ? $product->sku : '';

        // Modified product title
        $title = isset( $product->title ) ? $product->title : '';
        $title = str_replace( $p_num, '', $title );

        $variant_code = isset( $product->variant_code ) ? $product->variant_code : '';
        $color        = isset( $product->color ) ? $product->color : '';
        $desc_prod    = isset( $product->desc_prod ) ? $product->desc_prod : '';
        $category     = isset( $product->category ) ? $product->category : '';
        $desc_fam_en  = isset( $product->desc_fam_en ) ? $product->desc_fam_en : '';
        $desc_mod_id  = isset( $product->desc_mod_id ) ? $product->desc_mod_id : '';
        $season       = isset( $product->season ) ? $product->season : '';
        $promo        = isset( $product->promo ) ? $product->promo : '';
        $price        = isset( $product->price ) ? $product->price : '';
        $price_promo  = isset( $product->price_promo ) ? $product->price_promo : '';
        $size         = isset( $product->size ) ? $product->size : '';
        $quantity     = isset( $product->quantity ) ? $product->quantity : '';
        $mag          = isset( $product->mag ) ? $product->mag : '';
        $warehouse    = isset( $product->warehouse ) ? $product->warehouse : '';

        // Extract images
        $img_1 = isset( $product->img_1 ) ? $product->img_1 : '';
        $img_2 = isset( $product->img_2 ) ? $product->img_2 : '';
        $img_3 = isset( $product->img_3 ) ? $product->img_3 : '';

        // Concatenate images with a comma
        $images = $img_1 . ',' . $img_2 . ',' . $img_3;

        // Convert images to an array
        $images_arr = explode( ',', $images );

        // Set up the API client with WooCommerce store URL and credentials
        $client = new Client(
            $website_url,
            $consumer_key,
            $consumer_secret,
            [
                'verify_ssl' => false,
            ]
        );

        // Check if the product already exists in WooCommerce
        $args = array(
            'post_type'  => 'product',
            'meta_query' => array(
                array(
                    'key'     => '_sku',
                    'value'   => $sku,
                    'compare' => '=',
                ),
            ),
        );

        // Check if the product already exists
        $existing_products = new WP_Query( $args );

        if ( $existing_products->have_posts() ) {
            $existing_products->the_post();

            // Get product id
            $product_id = get_the_ID();

            // Update the status of the processed product in your database
            $wpdb->update(
                $table_name,
                [ 'status' => 'completed' ],
                [ 'id' => $serial_id ]
            );

            // Update the variable product if it already exists
            $product_data = [
                'name'        => $title,
                'sku'         => $sku,
                'type'        => 'variable',
                'description' => '',
                'attributes'  => [
                    [
                        'name'        => 'Color',
                        'options'     => explode( separator: '|', string: $color ),
                        'position'    => 0,
                        'visible'     => true,
                        'variation'   => true,
                        'is_taxonomy' => false,
                    ],
                    [
                        'name'        => 'Size',
                        'options'     => explode( separator: '|', string: $size ),
                        'position'    => 1,
                        'visible'     => true,
                        'variation'   => true,
                        'is_taxonomy' => false,
                    ],
                ],
            ];

            // Update product
            $client->put( 'products/' . $product_id, $product_data );

            // Add variations
            foreach ( explode( '|', $color ) as $color_option ) {
                foreach ( explode( '|', $size ) as $size_option ) {
                    $variation_data = [
                        'attributes'     => [
                            [
                                'name'  => 'Color',
                                'value' => $color_option,
                            ],
                            [
                                'name'  => 'Size',
                                'value' => $size_option,
                            ],
                        ],
                        'regular_price'  => $price,
                        'stock_quantity' => $quantity,
                    ];

                    // Add variation
                    $client->post( 'products/' . $product_id . '/variations', $variation_data );
                }
            }

        } else {
            // Create a new variable product if it does not exist
            $product_data = [
                'name'        => $title,
                'sku'         => $sku,
                'type'        => 'variable',
                'description' => '',
                'attributes'  => [
                    [
                        'name'        => 'Color',
                        'options'     => explode( '|', $color ),
                        'position'    => 0,
                        'visible'     => true,
                        'variation'   => true,
                        'is_taxonomy' => false,
                    ],
                    [
                        'name'        => 'Size',
                        'options'     => explode( '|', $size ),
                        'position'    => 1,
                        'visible'     => true,
                        'variation'   => true,
                        'is_taxonomy' => false,
                    ],
                ],
            ];

            // Create the product
            $product    = $client->post( 'products', $product_data );
            $product_id = $product->id;

            // Set product information
            wp_set_object_terms( $product_id, 'variable', 'product_type' );
            update_post_meta( $product_id, '_visibility', 'visible' );
            update_post_meta( $product_id, '_stock_status', 'instock' );

            // set products additional information
            update_post_meta( $product_id, '_color', $color );
            update_post_meta( $product_id, '_season', $season );
            update_post_meta( $product_id, '_size', $size );
            update_post_meta( $product_id, '_mag', $mag );
            update_post_meta( $product_id, '_desc_mod_id', $desc_mod_id );
            update_post_meta( $product_id, '_promo', $promo );

            // Update product meta data in WordPress
            update_post_meta( $product_id, '_stock', $quantity );

            // display out of stock message if stock is 0
            if ( $quantity <= 0 ) {
                update_post_meta( $product_id, '_stock_status', 'outofstock' );
            } else {
                update_post_meta( $product_id, '_stock_status', 'instock' );
            }
            update_post_meta( $product_id, '_manage_stock', 'yes' );

            // Add variations
            foreach ( explode( '|', $color ) as $color_option ) {
                foreach ( explode( '|', $size ) as $size_option ) {
                    $variation_data = [
                        'attributes'     => [
                            [
                                'name'  => 'Color',
                                'value' => $color_option,
                            ],
                            [
                                'name'  => 'Size',
                                'value' => $size_option,
                            ],
                        ],
                        'regular_price'  => $price,
                        'stock_quantity' => $quantity,
                    ];

                    // Add variation
                    $client->post( 'products/' . $product_id . '/variations', $variation_data );
                }
            }

            // Set product categories
            wp_set_object_terms( $product_id, $category, 'product_cat' );

            // Set product gallery images
            foreach ( $images_arr as $image_url ) {
                // Extract image name
                $image_name = basename( $image_url );
                // Get WordPress upload directory
                $upload_dir = wp_upload_dir();

                // Download the image from URL and save it to the upload directory
                $image_data = file_get_contents( $image_url );

                // Set specific image as product thumbnail
                $specific_image_attached = false; // Flag to track the attachment of the specific image

                if ( $image_data !== false ) {
                    $image_file = $upload_dir['path'] . '/' . $image_name;
                    file_put_contents( $image_file, $image_data );

                    // Prepare image data to be attached to the product
                    $file_path = $upload_dir['path'] . '/' . $image_name;
                    $file_name = basename( $file_path );

                    // Insert the image as an attachment
                    $attachment = [
                        'post_mime_type' => mime_content_type( $file_path ),
                        'post_title'     => preg_replace( '/\.[^.]+$/', '', $file_name ),
                        'post_content'   => '',
                        'post_status'    => 'inherit',
                    ];

                    $attach_id = wp_insert_attachment( $attachment, $file_path, $product_id );

                    // Add the image to the product gallery
                    $gallery_ids   = get_post_meta( $product_id, '_product_image_gallery', true );
                    $gallery_ids   = explode( ',', $gallery_ids );
                    $gallery_ids[] = $attach_id;
                    update_post_meta( $product_id, '_product_image_gallery', implode( ',', $gallery_ids ) );

                    // Check if this image should be set as the product thumbnail
                    if ( strpos( $image_url, 'CAPPOTTI-1' ) !== false ) {
                        set_post_thumbnail( $product_id, $attach_id );
                        $specific_image_attached = true; // Flag the attachment of specific image as product thumbnail
                    }
                    
                }
            }

            // Update the status of the processed product in your database
            $wpdb->update(
                $table_name,
                [ 'status' => 'completed' ],
                [ 'id' => $serial_id ]
            );

            return "Product Inserted Successfully";
        }
    }
}

add_shortcode( 'insert_product_api', 'product_insert_woocommerce' );
