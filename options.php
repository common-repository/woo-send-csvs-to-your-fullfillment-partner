<?php




function WC_FFM_main_menu() {

	//Icons:
	//https://developer.wordpress.org/resource/dashicons/
	
	// 1. Anzeige oben im Browser, 2. Anzeige Menüpunkt, 3. Slug für das Menü, 4. Verbindung zu menü und URL, 5. Funktion, 6. icon_url, 7. position
	add_menu_page('Fullfilment-Emailer', 'WC Fullfilment-Emailer', 'manage_options', 'theme-options', 'WC_FFM_options_mainpage', 'dashicons-email'  );
	// 1. Verbindung zu Hauptmenü, 2. Anzeige oben im Browser, 3. Anzeige im Menü, 4. Slug für Menü, sollte das Gleiche wie im Hauptmenü sein, 5. URL für Submenü, 6. Funktion
	add_submenu_page( 'theme-options', 'Send daily E-Mails to your Fullfilment Partner', 'History', 'manage_options', 'send_email_to_fullfilment', 'WC_FFM_options_submenupage' );

	//call register settings function
	add_action( 'admin_init', 'WC_FFM_register_mainsettings' );
	add_action( 'admin_init', 'WC_FFM_register_subsettings' );
}




// Hier beginnt das Submenü
function WC_FFM_register_subsettings() {
	
	//register our settings
	//
	
}

function WC_FFM_options_submenupage(){ ?>

	<?php 
	
	wp_register_script( 'WC_FFM_scripts', plugin_dir_url(__FILE__) . 'assets/scripts.js' );
	wp_enqueue_script( 'WC_FFM_scripts' );
	
	?>
    

                <div class="wrap">
<h1>Statistic</h1>
<span><strong>Please note:</strong></span><br>
<span>- At this page you can see all the csv you created.</span><br>
<span>- Cronjob is running only if there is a new order for sending.</span><br>
<span>- You can check the logging data at /uploads/fe_csv/logs to see if your cronjob has been run.</span>


 <?php
					
					if (!empty(get_option('WC_FFM_sendingtime'))){
						$lastsendingtime = get_option('WC_FFM_sendingtime');
					} else {
						$lastsendingtime = 'has not run yet';
					}
						
							echo '<p><em style="color:red;">Last Cronjob runs at: ' . $lastsendingtime . '</em></p>';
							
	
	
							$list = get_option( 'WC_FFM_log' );
							$upload = wp_upload_dir();
							
							if (empty($list)){
								echo '<p><em>No CSV files available.</em></p>';
							}
							
							if (is_array($list) || is_object($list)){
							
							foreach ($list as $item){
								
								echo '<p>' . $item['senddate']  . ' - <a href="' . $upload['baseurl'] . $item['csvlink'] . '" download>CSV-File</a> - (Encoding: ' . WC_FFM_detect_utf_encoding($upload['basedir'] . $item['csvlink']) . ')</p>';
								
							}
							
							}
							
		?>
        
        
        <?php if (!empty($list)){ ?>
        
        <form method="POST" action="" id="deletelist">
        
        <?php submit_button( 'Delete List', 'delete', 'delete_list', true, array( 'onclick' => 'WC_FFM_deletelistnow();return false;' ) ); ?>
		
        </form>
        
        <?php } ?>
        
</div>

<?php }






// Hier beginnt das Hauptmenü

function WC_FFM_register_mainsettings() {
	//register our settings
	register_setting( 'WC_FFM_plugin-settings-group', 'WC_FFM_code' );
	register_setting( 'WC_FFM_plugin-settings-group', 'WC_FFM_sender' );
	register_setting( 'WC_FFM_plugin-settings-group', 'WC_FFM_fullfilment' );
	register_setting( 'WC_FFM_plugin-settings-group', 'WC_FFM_name' );
	register_setting( 'WC_FFM_plugin-settings-group', 'WC_FFM_subject' );
	register_setting( 'WC_FFM_plugin-settings-group', 'WC_FFM_content' );
	register_setting( 'WC_FFM_plugin-settings-group', 'WC_FFM_fields_order' );
	register_setting( 'WC_FFM_plugin-settings-group', 'WC_FFM_encoding' );
	register_setting( 'WC_FFM_plugin-settings-group', 'WC_FFM_csvname' );

}


function WC_FFM_options_mainpage() {
	
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_register_style( 'jquery-ui', 'https://code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css' );
    wp_enqueue_style( 'jquery-ui' ); 
	
	wp_enqueue_script('sortable_script', plugin_dir_url(__FILE__) . 'assets/sortable.js');
	wp_register_style('sortable_css', plugin_dir_url(__FILE__) . 'assets/sortable.css');
	wp_enqueue_style( 'sortable_css' ); 
	
?>

<div class="wrap">
<h1>Send E-Mails to your Fullfilment Partner</h1>
<!--<p>Hier kannst du den Text eintragen, der am jeweiligen Tag dem User zugesendet werden soll.</p>-->

<form method="post" action="options.php">
	<!-- Setting Fields setzen, die im Formular verwendet werden -->
	<?php settings_fields( 'WC_FFM_plugin-settings-group' ); ?>
    <?php do_settings_sections( 'WC_FFM_plugin-settings-group' ); ?>
    
    
    <?php 
				$fe_order_default = WC_FFM_defaultparameters();
				
				$cronjobcode = get_option('WC_FFM_code');
		
				if (empty($cronjobcode)) {
					$cronjobcode = "PLEASE_ENTER_A_CODE"	;
				}
		?>
        
            
             <p><strong>Your Cronjob-Link: <h2><?php echo get_home_url() . '?csv_cronjob=' . $cronjobcode; ?></h2></strong></p>
             
             
            
    <table class="form-table">
    
    
        
        
         <tr valign="top">
        <th scope="row">Encoding of CSV-File:</th>
        <td><select name="WC_FFM_encoding">
  <option value="UTF-8" <?php selected( get_option('WC_FFM_encoding'), "UTF-8" ) ?>>UTF-8</option>
  <option value="Excel" <?php selected( get_option('WC_FFM_encoding'), "Excel" ) ?>>Excel (UTF-16LE)</option>
</select></td>
        </tr>
        
      
        
        <tr valign="top">
        <th scope="row">Name of CSV-File:</th>
        <td><input style="width: 100%;" type="text" name="WC_FFM_csvname" value="<?php echo esc_attr( get_option('WC_FFM_csvname') ); ?>" /><p>%%bloginfo_name%% replaced with the name of the blog. (Please note: the date is appended to the name.)</p></td>
        </tr>
        
        
     <!--   <tr valign="top">
        <th scope="row">Sandbox Apple</th>
        <td><input type="checkbox" name="sandbox" value="1" <?php checked( get_option('sandbox'), "1" ) ?>>
        </td>
        </tr>-->
        
       <!-- <tr valign="top">
        <th scope="row">Choose the customer data:</th>
        <select name="<value>" multiple="multiple" disabled="disabled" size="<value>">
     		<option label="value" selected="selected" value="<value>" disabled="disabled">Text to Display</option>
		</select>
   		</td>
        </tr>
   -->
   

        
          <tr valign="top">
        <th scope="row">Your Cronjob-Code:</th>
        <td><input style="width: 100%;" type="text" name="WC_FFM_code" value="<?php echo esc_attr( get_option('WC_FFM_code') ); ?>" /></td>
      	</tr>
        
        
     <!--   <tr valign="top">
        <th scope="row">Sort columns of data:</th>
         <td><ul id="sortable">
          <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><p><input type="checkbox" name="sandbox" value="1"></p><p><input style="width: 100%;" type="text" name="fe_sender" value="" /></p><p class="variable">Item 1</p></li>
          <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>Item 2</li>
          <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>Item 3</li>
          <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>Item 4</li>
          <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>Item 5</li>
          <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>Item 6</li>
          <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>Item 7</li>
        </ul></td>
      	</tr>-->
        
        <tr valign="top">
        <th scope="row">Sort columns of data:</th>
         <td><p>You can change the name of the required columns</p>
        <ul id="sortable">
                    <?php 
                        $fe_fields_order = get_option('WC_FFM_fields_order', $fe_order_default); 
                        foreach($fe_fields_order as $value) { ?>

                            <?php
                                if(isset($value['id'])) { $id = $value['id']; }
                                if(isset($value['check'])) { $check = $value['check']; }
								if(isset($value['name'])) { $name = $value['name']; }
                                if(isset($value['slug'])) { $slug = $value['slug']; }
                            ?>

                            <li class="ui-state-default">
                            	<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                                <input type="hidden" name="WC_FFM_fields_order[<?php echo $id; ?>][id]" value="<?php echo $id; ?>" />
                                <input type="hidden" name="WC_FFM_fields_order[<?php echo $id; ?>][check]" value="0" />
                                <span><input type="checkbox" name="WC_FFM_fields_order[<?php echo $id; ?>][check]" value="1" <?php checked( $check, "1" ) ?>></span>
                                <span><input style="width: 100%;" type="text" name="WC_FFM_fields_order[<?php echo $id; ?>][name]" value="<?php echo $name; ?>" /></span>
                                <input type="hidden" name="WC_FFM_fields_order[<?php echo $id; ?>][slug]" value="<?php echo $slug; ?>" />
                                <span class="variable"><?php echo $slug; ?></span>
                            </li>
                    <?php } ?>
                </ul>
                </td>
      	</tr>
                

      
        <tr valign="top">
        <th scope="row">E-Mail of sender<br> (comma seperated):</th>
        <td><input style="width: 100%;" type="text" name="WC_FFM_sender" value="<?php echo esc_attr( get_option('WC_FFM_sender') ); ?>" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">E-Mail of Fullfilment Partner<br> (comma seperated):</th>
        <td><input style="width: 100%;" type="text" name="WC_FFM_fullfilment" value="<?php echo esc_attr( get_option('WC_FFM_fullfilment') ); ?>" /></td>
        </tr>
        
         <tr valign="top">
        <th scope="row">E-Mail Sender Name:</th>
        <td><input style="width: 100%;" type="text" name="WC_FFM_name" value="<?php echo esc_attr( get_option('WC_FFM_name') ); ?>" /></td>
        </tr>
        
         <tr valign="top">
        <th scope="row">Subject of E-Mail:</th>
        <td><input style="width: 100%;" type="text" name="WC_FFM_subject" value="<?php echo esc_attr( get_option('WC_FFM_subject') ); ?>" /></td>
        </tr>
        
        <!-- <tr valign="top">
        <th scope="row">Content of E-Mail:</th>
        <td><textarea cols="50" rows="10" style="width: 100%;" name="fe_content"><?php echo get_option('WC_FFM_content'); ?></textarea></td>
        </tr>
       -->
       
    	<tr valign="top">
        <th scope="row">Content of E-Mail:</th>
        <td>
		<?php 
       
        $settings = array( 'textarea_name' => 'WC_FFM_content', 'wpautop' => false );
    
        wp_editor( get_option('WC_FFM_content'), 'WC_FFM_content', $settings );
    
        ?>
        </td>
        </tr>
        
    </table>
    
    <?php submit_button(); ?>
    
   

</form>
</div>
<?php } ?>