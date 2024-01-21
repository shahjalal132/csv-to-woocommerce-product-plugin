<?php

/*
 * Plugin Name:       csv to woocommerce products
 * Plugin URI:        #
 * Description:       csv file to WooCommerce products upload
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Shah jalal
 * Author URI:        #
 */


// Define plugin path
if ( !defined( 'JALAL_PLUGIN_PATH' ) ) {
    define( 'JALAL_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
}

// Define plugin url
if ( !defined( 'JALAL_PLUGIN_URI' ) ) {
    define( 'JALAL_PLUGIN_URI', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
}


// create table when plugin activate
register_activation_hook( __FILE__, 'sync_products_table_creation' );

// remove table when plugin deactivate
register_deactivation_hook( __FILE__, 'sync_products_table_deletion' );







// include files
require_once JALAL_PLUGIN_PATH . '/inc/db_table_creation.php';
require_once JALAL_PLUGIN_PATH . '/inc/insert_data_to_db.php';