<?php
/**
*  Plugin Name: WooCommerce Send CSVs to your Fullfillment-Partner
 * Plugin URI: http://www.schmeidt.de/
 * Description: Sends automatic e-mails to your Fullfilment partner every day.
 * Version: 1.0
 * Author: Schmeidt
 * Author URI: www.schmeidt.de
 * License: GPL12
 */


// Start
register_activation_hook( __FILE__, 'WC_FFM_csv_activate' );
add_action( 'init', 'WC_FFM_csv_fe_cronjob' );
//add_action( 'admin_init', 'WC_FFM_deletelist' );
add_action('admin_menu', 'WC_FFM_main_menu');



function WC_FFM_csv_fe_cronjob(){
	
if ( empty( $_REQUEST['csv_cronjob'] ) ) return;

$csv_cronjob = $_REQUEST['csv_cronjob'];

if ( $csv_cronjob == get_option('WC_FFM_code') ){


		run_csv();

}
}



				
			
function run_csv(){
		
				
	$sendingtime = get_option('WC_FFM_sendingtime');
				
	

//echo $date->format('Y-m-d H:i:s');

//$utc_offset =  date('Z') / 3600;
//$offset = $utc_offset * 60 * 60;
//$sendingtime = date('Y-m-d H:i:s', strtotime($sendingtime)- $offset);
//echo $utc_offset;

	
	// Get orders 
	//https://github.com/woocommerce/woocommerce/wiki/wc_get_orders-and-WC_Order_Query
	$args = array(
		'type' => 'shop_order', //Accepts a string: 'shop_order', 'shop_order_refund', or a custom order type.
		'date_completed' => '>' . strtotime($sendingtime), // date_completed, date_paid, date_created --> rechnet mit offset weil date_created mit UTC rechnet und damit immer eine Stunde verschiebung ist, gilt nur bei date_created--> strtotime($sendingtime . $utc_offset . ' hour')
		'status' => 'completed', //Accepts a string: one of 'pending', 'processing', 'on-hold', 'completed', 'refunded, 'failed', 'cancelled', or a custom order status.
	);
	
//	if ( ! empty( $sendingtime ) ) {
//		$args[] = array(
//			'date_created' => '>' . strtotime($sendingtime), // date_completed, date_paid
//		);
//	}
	
	$fe_order_default = WC_FFM_defaultparameters();	
	$csv = '';
			
	$fe_fields_order = get_option('WC_FFM_fields_order', $fe_order_default); 
	
            foreach($fe_fields_order as $value) { 
				
				if ( $value['check'] == 1 ){
				$csv .= $value['name'] . "; ";
				}
				//$variables[] = array( 'name' => $value['name'], 'slug' => $value['slug'] );
			}
			
			// Check if any value is checked
			if(!empty($csv)){
			
			$csv.= "\n";
			
			} else {
				
			return;	
			
			}
			//$firstrow = implode('; ', array_map(function ($entry) {
//						  return $entry['name'];
//						}, $variables));
			
	// Erste Reihe des CSV
	//$csv = "KundenNr.;BestellNr.	;Kauf;Email;Anrede;Vorname;Nachname;Firma;Adresse;Adresse2;PLZ;Stadt;Land;Produkt;Menge \n";
	
	

	$orders = wc_get_orders( $args );
					
						foreach ($orders as $order){
							
							
							$order_data = $order->get_data(); // The Order data
							
							$order_id = $order_data['id'];
							$order_parent_id = $order_data['parent_id'];
							$order_status = $order_data['status'];
							$order_currency = $order_data['currency'];
							$order_version = $order_data['version'];
							$order_payment_method = $order_data['payment_method'];
							$order_payment_method_title = $order_data['payment_method_title'];
							$order_payment_method = $order_data['payment_method'];
							$order_payment_method = $order_data['payment_method'];
							
							## Creation and modified WC_DateTime Object date string ##
							
							// Using a formated date ( with php date() function as method)
							$order_date_created = $order_data['date_created']->date('Y-m-d H:i:s');
							$order_date_modified = $order_data['date_modified']->date('Y-m-d H:i:s');
							
							// Using a timestamp ( with php getTimestamp() function as method)
							$order_timestamp_created = $order_data['date_created']->getTimestamp();
							$order_timestamp_modified = $order_data['date_modified']->getTimestamp();
							
							
							$order_discount_total = $order_data['discount_total'];
							$order_discount_tax = $order_data['discount_tax'];
							$order_shipping_total = $order_data['shipping_total'];
							$order_shipping_tax = $order_data['shipping_tax'];
							$order_total = $order_data['cart_tax'];
							$order_total_tax = $order_data['total_tax'];
							$order_customer_id = $order_data['customer_id']; // ... and so on
							
							## BILLING INFORMATION:
							
							$order_billing_first_name = $order_data['billing']['first_name'];
							$order_billing_last_name = $order_data['billing']['last_name'];
							$order_billing_company = $order_data['billing']['company'];
							$order_billing_address_1 = $order_data['billing']['address_1'];
							$order_billing_address_2 = $order_data['billing']['address_2'];
							$order_billing_city = $order_data['billing']['city'];
							$order_billing_state = $order_data['billing']['state'];
							$order_billing_postcode = $order_data['billing']['postcode'];
							$order_billing_country = $order_data['billing']['country'];
							$order_billing_email = $order_data['billing']['email'];
							$order_billing_phone = $order_data['billing']['phone'];
							
							## SHIPPING INFORMATION:
							
							$order_shipping_first_name = $order_data['shipping']['first_name'];
							$order_shipping_last_name = $order_data['shipping']['last_name'];
							$order_shipping_company = $order_data['shipping']['company'];
							$order_shipping_address_1 = $order_data['shipping']['address_1'];
							$order_shipping_address_2 = $order_data['shipping']['address_2'];
							$order_shipping_city = $order_data['shipping']['city'];
							$order_shipping_state = $order_data['shipping']['state'];
							$order_shipping_postcode = $order_data['shipping']['postcode'];
							$order_shipping_country = $order_data['shipping']['country'];
							
							
							
												// Get an instance of the WC_Order object
												$order = wc_get_order($order_id);
												$user_id = $order->get_user_id(); // or $order->get_customer_id();
												
												// Iterating through each WC_Order_Item_Product objects
												foreach ($order->get_items() as $item_key => $item_values):
												
													## Using WC_Order_Item methods ##
												
													// Item ID is directly accessible from the $item_key in the foreach loop or
													$item_id = $item_values->get_id();
												
													## Using WC_Order_Item_Product methods ##
												
													$item_name = $item_values->get_name(); // Name of the product
													$item_type = $item_values->get_type(); // Type of the order item ("line_item")
												
													$product_id = $item_values->get_product_id(); // the Product id
													$wc_product = $item_values->get_product(); // the WC_Product object
													## Access Order Items data properties (in an array of values) ##
													$item_data = $item_values->get_data();
												
													$product_name = $item_data['name'];
													$product_id = $item_data['product_id'];
													$variation_id = $item_data['variation_id'];
													$quantity = $item_data['quantity'];
													$tax_class = $item_data['tax_class'];
													$line_subtotal = $item_data['subtotal'];
													$line_subtotal_tax = $item_data['subtotal_tax'];
													$line_total = $item_data['total'];
													$line_total_tax = $item_data['total_tax'];
												
												
													//$csv.= $user_id.';'.$order_id.';'.''.';'.$order_billing_email.';'.''.';'.$order_shipping_first_name.';'.$order_shipping_last_name.';'.$order_shipping_company.';'.$order_shipping_address_1.';'.$order_shipping_address_2.';'.$order_shipping_postcode.';'.$order_shipping_city.';'.$order_shipping_country.';'.$product_id.';'.$quantity.';'."\n"; //Append data to csv
													
													//$rows = implode('; ', array_map(function ($entry) {
//													  return $entry['slug'];
//													}, $variables));
													
													
													foreach($fe_fields_order as $value) { 
														$slug = $value['slug'];
														
														if($slug=='user_id'){$slug=$user_id;}
														elseif($slug=='order_id'){$slug=$order_id;}
														elseif($slug=='order_billing_email'){$slug=$order_billing_email;}
														elseif($slug=='order_shipping_first_name'){$slug=$order_shipping_first_name;}
														elseif($slug=='order_shipping_last_name'){$slug=$order_shipping_last_name;}
														elseif($slug=='order_shipping_company'){$slug=$order_shipping_company;}
														elseif($slug=='order_shipping_address_1'){$slug=$order_shipping_address_1;}
														elseif($slug=='order_shipping_address_2'){$slug=$order_shipping_address_2;}
														elseif($slug=='order_shipping_postcode'){$slug=$order_shipping_postcode;}
														elseif($slug=='order_shipping_city'){$slug=$order_shipping_city;}
														elseif($slug=='order_shipping_country'){$slug=$order_shipping_country;}
														elseif($slug=='product_id'){$slug=$product_id;}
														elseif($slug=='quantity'){$slug=$quantity;}
														elseif($slug=='emptyfield'){$slug='';}
														
														if ( $value['check'] == 1 ){
														$csv.= $slug . "; "; //Append data to csv
														}
													}
													
													$csv.= "\n";
													
													
												endforeach;
							
							

							
							
							
							}// foreach end
							
						
				$upload = wp_upload_dir();
							
				// Check if there are new orders available since the last cronjob
				if(! empty($orders) && ! empty($sendingtime)){
					
						
						$list = get_option( 'WC_FFM_log' );
						$date = current_time('d-m-Y H:i:s');
						$list[] = array( 'senddate' => $date, 'csvlink' => '/fe_csv/' . preg_replace('/\s+/', '_', WC_FFM_replace_variables(get_option( 'WC_FFM_csvname' )) ) . '_' . current_time('d-m-Y') . '_' . current_time('H-i-s') . '.csv'); // to database
							
						$csvname = preg_replace('/\s+/', '_', WC_FFM_replace_variables(get_option( 'WC_FFM_csvname' )) ) . '_' . current_time('d-m-Y') . '_' . current_time('H-i-s') . '.csv';
							
						$csv_handler = fopen ( $upload['basedir'] . '/fe_csv/' . $csvname,'w');	
						
						if (get_option('WC_FFM_encoding') == 'UTF-8'){
						fwrite ($csv_handler, chr(239) . chr(187) . chr(191) . $csv); // kodiere zu 'UTF-8' oder 'Windows-1252' --> http://www.webmaster-seo.com/questions/unicode-textdatei-mit-php-excel/
						} elseif (get_option('WC_FFM_encoding') == 'Excel'){
						fwrite($csv_handler, chr(255) . chr(254) . mb_convert_encoding( $csv, 'UTF-16LE', 'UTF-8')); // kodiere für Excel
						}
						
						fclose ($csv_handler);
						
						
						// Check encoding
						//$content = file_get_contents($upload['basedir'] . '/fe_csv/' . $csvname);
						
						// Checkt ob UTF-16
						//if ($content[0]==chr(0xff) && $content[1]==chr(0xfe)) {
//						  echo 'UTF-16';
//						} else if ($content[0]==chr(0xfe) && $content[1]==chr(0xff)) {
//						  echo 'UTF-16BE';
//						}

						//mb_detect_encoding(file_get_contents($upload['basedir'] . '/fe_csv/' . $csvname)) // funktioniert nur für UTF-8, nicht für UTF-16



						
						$attachments = array( $upload['basedir'] . '/fe_csv/' . $csvname);


					update_option( 'WC_FFM_sendingtime', current_time( 'Y-m-d H:i:s' ) );
					update_option( 'WC_FFM_log', $list );
					
					wp_mail( get_option('WC_FFM_fullfilment'), get_option('WC_FFM_subject'), get_option('WC_FFM_content') . "<br><br>", array('Content-Type: text/html; charset=UTF-8', 'From: "' . get_option('WC_FFM_name') . '" <' . get_option('WC_FFM_sender') . '>'), $attachments);
					error_log("[E-Mail sent] success! Fullfilment: " . get_option('WC_FFM_fullfilment') . ' - Encoding: ' . WC_FFM_detect_utf_encoding($upload['basedir'] . '/fe_csv/' . $csvname) . ' - ' . current_time( 'G:i:s' ) .PHP_EOL, 3, $upload['basedir'] .  '/fe_csv/logs/fullfillment_e-mail-logs-' . current_time( 'Y-m-d' ) . '.log');
				} else {
					error_log("[E-Mail sent] error! No orders available" . ' - ' . current_time( 'G:i:s' ) .PHP_EOL, 3, $upload['basedir'] .  '/fe_csv/logs/fullfillment_e-mail-logs-' . current_time( 'Y-m-d' ) . '.log');
				}
				
			}
				
				




require_once plugin_dir_path( __FILE__ ) . 'options.php';
require_once plugin_dir_path( __FILE__ ) . 'functions.php';


  ?>