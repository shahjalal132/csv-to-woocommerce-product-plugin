<?php

// require autoloader
require_once JALAL_PLUGIN_PATH . '/vendor/autoload.php';

// Add menu page named 'CSV to Product' in admin panel
function csv_to_product_menu() {
    add_menu_page( 'CSV to Product', 'CSV to Product', 'manage_options', 'csv-to-product', 'csv_to_product_page_callback', 'dashicons-upload', 6 );
}

// callback function for menu page
function csv_to_product_page_callback() {

    if ( isset( $_POST['upload_csv'] ) ) {

        // get the file extension 
        $extension = pathinfo( $_FILES['csv_file']['name'], PATHINFO_EXTENSION );

        // check if the file extension is csv
        if ( !empty( $_FILES['csv_file']['name'] ) && ( $extension == 'csv' || $extension == 'xlsx' ) ) {

            // create the "uploads" directory if it doesn't exist
            $uploads_dir = JALAL_PLUGIN_PATH . '/uploads/';
            wp_mkdir_p( $uploads_dir );

            // upload the file in uploads folder
            $uploaded = move_uploaded_file( $_FILES['csv_file']['tmp_name'], JALAL_PLUGIN_PATH . '/uploads/' . $_FILES['csv_file']['name'] );

            // get the file name
            $get_file_name = $_FILES['csv_file']['name'];

            if ( $uploaded ) {

                // get path for the file 
                $excelFilePath = JALAL_PLUGIN_PATH . '/uploads/' . $get_file_name;

                // Load the Excel file
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load( $excelFilePath );

                // Get the first worksheet
                $worksheet = $spreadsheet->getActiveSheet();

                // Convert the worksheet to an associative array
                $data = [];
                foreach ( $worksheet->getRowIterator() as $row ) {
                    $rowData = [];
                    foreach ( $row->getCellIterator() as $cell ) {
                        $rowData[] = $cell->getValue();
                    }
                    $data[] = $rowData;
                }
                // Convert the array to JSON
                $json = json_encode( $data, JSON_PRETTY_PRINT );

                $product_data = json_decode( $json, true );

                // Insert data to database
                if ( !empty( $product_data ) && is_array( $product_data ) ) {

                    foreach ( $product_data as $single_data ) {

                        // Extract product data to variables
                        $product_id   = $single_data[0];
                        $title        = $single_data[8];
                        $sku          = $single_data[1];
                        $variant_code = $single_data[2];
                        $color        = $single_data[3];
                        $desc_prod    = $single_data[4];
                        $category     = $single_data[5];
                        $desc_fam_en  = $single_data[6];
                        $desc_mod_id  = $single_data[7];
                        $img_1        = $single_data[9];
                        $img_2        = $single_data[10];
                        $img_3        = $single_data[11];
                        $season       = $single_data[12];
                        $promo        = $single_data[13];
                        $price        = $single_data[14];
                        $price_promo  = $single_data[15];
                        $size         = $single_data[16];
                        $quantity     = $single_data[17];
                        $mag          = $single_data[18];
                        $warehouse    = $single_data[19];

                        // Insert to database
                        global $wpdb;
                        $table_name = $wpdb->prefix . 'sync_products';
                        // $wpdb->query( "TRUNCATE TABLE $table_name" );

                        // Prepare data for insertion
                        $data = array(
                            'product_id'   => $product_id,
                            'title'        => $title,
                            'sku'          => $sku,
                            'variant_code' => $variant_code,
                            'color'        => $color,
                            'desc_prod'    => $desc_prod,
                            'category'     => $category,
                            'desc_fam_en'  => $desc_fam_en,
                            'desc_mod_id'  => $desc_mod_id,
                            'img_1'        => $img_1,
                            'img_2'        => $img_2,
                            'img_3'        => $img_3,
                            'season'       => $season,
                            'promo'        => $promo,
                            'price'        => $price,
                            'price_promo'  => $price_promo,
                            'size'         => $size,
                            'quantity'     => $quantity,
                            'mag'          => $mag,
                            'warehouse'    => $warehouse,
                            'status'       => 'pending',
                        );

                        // Insert data into the database
                        $wpdb->insert( $table_name, $data );

                    }
                }

                // echo htmlspecialchars( $json );

            } else {
                // if file not uploaded
                $uploadError = 'There is some problem in uploading file.';
            }
        } else {
            // if the file extension is not csv
            $uploadError = 'Only CSV or xlsx file allowed to upload.';
        }
    }


    // Save the JSON to a file
    ?>
    <div class="wrap">

        <h2>CSV to Product</h2>

        <?php if ( isset( $json ) ) : ?>
            <h3>JSON Data:</h3>
            <pre><?php
            // echo htmlspecialchars($json); 
            ?></pre>
        <?php endif; ?>

        <?php if ( isset( $uploadError ) ) : ?>
            <p>
                <?php echo $uploadError; ?>
            </p>
        <?php endif; ?>

        <form method="post" action="" enctype="multipart/form-data">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Upload CSV File</th>
                    <td><input type="file" name="csv_file" /></td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="upload_csv" id="submit" class="button button-primary" value="Upload CSV" />
            </p>
        </form>
    </div>
    <?php
}


add_action( 'admin_menu', 'csv_to_product_menu' );