<?php

function sync_products_table_creation() {

    global $wpdb;

    // table name
    $table_name      = $wpdb->prefix . 'sync_products';
    $charset_collate = $wpdb->get_charset_collate();

    // sql query
    $sql = "CREATE TABLE $table_name (
        id int(11) NOT NULL AUTO_INCREMENT,
        product_id VARCHAR(255) NOT NULL,
        title VARCHAR(5000) NOT NULL,
        sku VARCHAR(255) NOT NULL,
        variant_code VARCHAR(255) NULL,
        color VARCHAR(255) NULL,
        desc_prod VARCHAR(255) NULL,
        category VARCHAR(255) NULL,
        desc_fam_en VARCHAR(255) NULL,
        desc_mod_id VARCHAR(255) NULL,
        img_1 VARCHAR(255) NULL,
        img_2 VARCHAR(255) NULL,
        img_3 VARCHAR(255) NULL,
        season VARCHAR(255) NULL,
        promo VARCHAR(255) NULL,
        price VARCHAR(255) NOT NULL,
        price_promo VARCHAR(255) NULL,
        size VARCHAR(255) NULL,
        quantity VARCHAR(255) NULL,
        mag VARCHAR(255) NULL,
        warehouse VARCHAR(255) NULL,
        status VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

function sync_products_table_deletion() {

    global $wpdb;

    $table_name = $wpdb->prefix . 'sync_products';
    $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
}