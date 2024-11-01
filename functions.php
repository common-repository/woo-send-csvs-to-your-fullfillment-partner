<?php

function WC_FFM_defaultparameters(){
	
            $fe_order_default = array(
                0 => array(
                    'id' => '0',
                    'check' => '',
					'name' => 'Customer-ID',
                    'slug' => 'user_id'
                ),
                1 => array(
                    'id' => '1',
                    'check' => '',
					'name' => 'Order-ID',
                    'slug' => 'order_id'
                ),
                2 => array(
                    'id' => '2',
                    'check' => '',
					'name' => 'E-Mail',
                    'slug' => 'order_billing_email'
                ),
                3 => array(
                    'id' => '3',
                    'check' => '',
					'name' => 'Firstname',
                    'slug' => 'order_shipping_first_name'
                ),
                4 => array(
                    'id' => '4',
                    'check' => '',
					'name' => 'Lastname',
                    'slug' => 'order_shipping_last_name'
                ),
				5 => array(
                    'id' => '5',
                    'check' => '',
					'name' => 'Company',
                    'slug' => 'order_shipping_company'
                ),
				6 => array(
                    'id' => '6',
                    'check' => '',
					'name' => 'Adress 1',
                    'slug' => 'order_shipping_address_1'
                ),
				7 => array(
                    'id' => '7',
                    'check' => '',
					'name' => 'Adress 2',
                    'slug' => 'order_shipping_address_2'
                ),
				8 => array(
                    'id' => '8',
                    'check' => '',
					'name' => 'Postcode',
                    'slug' => 'order_shipping_postcode'
                ),
				9 => array(
                    'id' => '9',
                    'check' => '',
					'name' => 'City',
                    'slug' => 'order_shipping_city'
                ),
				10 => array(
                    'id' => '10',
                    'check' => '',
					'name' => 'Country',
                    'slug' => 'order_shipping_country'
                ),
				11 => array(
                    'id' => '11',
                    'check' => '',
					'name' => 'Product-ID',
                    'slug' => 'product_id'
                ),
				12 => array(
                    'id' => '12',
                    'check' => '',
					'name' => 'Quantity',
                    'slug' => 'quantity'
                ),
				13 => array(
                    'id' => '13',
                    'check' => '',
					'name' => 'Custom name',
                    'slug' => 'emptyfield'
                ),
				14 => array(
                    'id' => '14',
                    'check' => '',
					'name' => 'Custom name',
                    'slug' => 'emptyfield'
                ),
				15 => array(
                    'id' => '15',
                    'check' => '',
					'name' => 'Custom name',
                    'slug' => 'emptyfield'
                ),
            );
			
			return $fe_order_default;
			
}


function WC_FFM_csv_activate() {
 
 	//add sendingdate for first time
 	update_option( 'WC_FFM_sendingtime', current_time( 'Y-m-d H:i:s' ) );
	update_option( 'WC_FFM_encoding', 'UTF-8' );
	update_option( 'WC_FFM_csvname', 'orders_%%bloginfo_name%%' );
	
	//add folder to uploads directory
    $upload = wp_upload_dir();
    $upload_dir = $upload['basedir'];
    $upload_dir = $upload_dir . '/fe_csv/logs';
    if (! is_dir($upload_dir)) {
       mkdir( $upload_dir, 0755, true );
    }
}
 
 
 
//function WC_FFM_deletelist(){
//	
//	if ( ! isset( $_POST['delete_list'] ) ) return;
//
//	$upload = wp_upload_dir();
//    $upload_dir = $upload['basedir'];
//	$upload_dir = $upload_dir . '/fe_csv/';
//	
//	WC_FFM_removeCSVFiles($upload_dir);
//	
//	update_option( 'WC_FFM_log', '' );
//
//}




add_action( 'wp_ajax_WC_FFM_deletelist', 'WC_FFM_deletelist' );
add_action( 'wp_ajax_nopriv_WC_FFM_deletelist', 'WC_FFM_deletelist' );

function WC_FFM_deletelist() {

	header('Access-Control-Allow-Headers: x-requested-with');
	header('Access-Control-Allow-Origin: *');
	
	$upload = wp_upload_dir();
    $upload_dir = $upload['basedir'];
	$upload_dir = $upload_dir . '/fe_csv/';
	
	foreach(glob("{$upload_dir}/*") as $file){
		
        if(is_file($file)) {
            unlink($file);
        }
		
	}
	
	update_option( 'WC_FFM_log', '' );
	
	$response = array( 'status' => 'success' );
				
	wp_send_json( $response );
	die;
}


//
//function WC_FFM_removeCSVFiles($directory){
//	
//    foreach(glob("{$directory}/*") as $file){
//		
//        if(is_file($file)) {
//            unlink($file);
//        }
//	
//	}
//	
//}

// Check encoding
if(!defined ("UTF32_BIG_ENDIAN_BOM")) { define ('UTF32_BIG_ENDIAN_BOM', chr(0x00) . chr(0x00) . chr(0xFE) . chr(0xFF)); } 
if(!defined ("UTF32_LITTLE_ENDIAN_BOM")) { define ('UTF32_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE) . chr(0x00) . chr(0x00)); }
if(!defined ("UTF16_BIG_ENDIAN_BOM")) { define ('UTF16_BIG_ENDIAN_BOM', chr(0xFE) . chr(0xFF)); }
if(!defined ("UTF16_LITTLE_ENDIAN_BOM")) { define ('UTF16_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE)); }
if(!defined ("UTF8_BOM")) { define ('UTF8_BOM', chr(0xEF) . chr(0xBB) . chr(0xBF)); }


function WC_FFM_detect_utf_encoding($filename) {

    $text = file_get_contents($filename);
    $first2 = substr($text, 0, 2);
    $first3 = substr($text, 0, 3);
    $first4 = substr($text, 0, 3);
    
    if ($first3 == UTF8_BOM) return 'UTF-8';
    elseif ($first4 == UTF32_BIG_ENDIAN_BOM) return 'UTF-32BE';
    elseif ($first4 == UTF32_LITTLE_ENDIAN_BOM) return 'UTF-32LE';
    elseif ($first2 == UTF16_BIG_ENDIAN_BOM) return 'UTF-16BE';
    elseif ($first2 == UTF16_LITTLE_ENDIAN_BOM) return 'UTF-16LE';
}




function WC_FFM_replace_variables( $csvname ){
	$csvname = str_replace( '%%bloginfo_name%%', get_bloginfo( 'name' ), $csvname );
	return $csvname;
}
            ?>