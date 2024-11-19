<div class="wrap" id="dk-speakout">

	<div id="icon-dk-speakout" class="icon32"><br /></div>
	<h2><?php _e( 'Signatures', 'speakout' ); ?><span class='signature-table-label'> - <?php allowed_html_tags(); echo wp_kses($table_label ,$allowed_tags ); ?></span></h2>
	<?php if ( $message_update ) echo '<div id="message" class="updated"><p>' . esc_html( $message_update ) . '</p></div>' ?>

	<div class="tablenav">
		<div class="dk_speakout_clear">
			<div class="alignleft">
				<form name="petitionSelect" action="" method="get" class="dk-sigform">
					<select id="dk-speakout-switch-petition">
					    <?php
					    	if( isset( $_GET["pid"] ) > ""){
						?>
						        <option value=""><?php _e( 'Display all signatures', 'speakout' ); ?></option>
						<?php
						    }
						    else{
						?>
						        <option value=""><?php _e( 'Select petition', 'speakout' ); ?></option>
						<?php
						    }
							 foreach( $petitions_list as $petition ) :
						    	$selected = $petition->id == $pid ? "SELECTED" : ""; 
								$signatureCount =  " (". $the_signatures->count( $petition->id,"","" ) . ")";
						
						?>
								<option value="<?php echo abs( $petition->id ); ?>" <?php echo $selected; ?>><?php echo esc_html( $petition->title ) . $signatureCount; ?></option>
						<?php endforeach; ?>
					</select>
				</form>

                
               <form id='dk-speakout-bulk-action-form' action="" method="get" class="dk-sigform">
                   <div id="dk-speakout-reconfirm-confirmation" class="dk-speakout-hidden"><?php _e( "Are you sure you want to do this? A separate confirmation email will be sent for each unconfirmed signature.", "speakout" ); ?></div>
                   <div id="dk-speakout-reconfirm-selected-confirmation" class="dk-speakout-hidden"><?php _e( "Are you sure you want to do this? A separate confirmation email will be sent for each selected signature.", "speakout" ); ?></div>
                   <div id="dk-speakout-forceconfirm-confirmation-open" class="dk-speakout-hidden"><?php _e( "Are you sure you want to do this? You will manually confirm ", "speakout" ); ?></div>
                   <div id="dk-speakout-delete-confirmation-open" class="dk-speakout-hidden"><?php _e( "Are you sure you want to do this? You will delete ", "speakout" ); ?></div>
                   <div id="dk-speakout-confirmation-close" class="dk-speakout-hidden"> <?php _e( "selected signature", "speakout" ); ?></div>
                   
                   <?php 
                        $option_disabled = "disabled='disabled'";
                        $option_notice = ": select petition";
                        
                        $nonceURL = wp_nonce_url( $reconfirm_url, 'dk_speakout-resend_confirmations' . $pid ); 
                        $nonce = explode("=", $nonceURL);
                        echo "<input type='hidden' id='confnonce' value='" . trim(end($nonce)) . "'>";
                        echo "<input type='hidden' id='confurl' value='" . site_url( 'wp-admin/admin.php?page=dk_speakout_signatures&action=reconfirm&pid=' . $pid ). "'>";

                        //set up for reconfirm selected
                        $reconfirm_selected_text = __( "Reconfirm selected", "speakout");
                        echo "<input type='hidden' id='dk-speakout-reconfirm-selected-text' value='" . esc_html( $reconfirm_selected_text ) . "'>";
                        $reconfirm_unselected_no_petition_text = __( "Reconfirm selected: choose petition first", "speakout"); 
                        if(isset($_GET["pid"]) && $_GET["pid"] > ""){
                            $reconfirm_unselected_no_petition_text = __( "Reconfirm selected: choose signatures", "speakout");
                        }
                        echo "<input type='hidden' id='dk-speakout-reconfirm-unselected-no-petition-text' value='" .  esc_html( $reconfirm_unselected_no_petition_text ) . "'>";
                        $reconfirm_unselected_no_signatures_text = __( "Reconfirm selected: choose signatures", "speakout");
                        echo "<input type='hidden' id='dk-speakout-reconfirm-unselected-no-signatures-text' value='" .  esc_html( $reconfirm_unselected_no_signatures_text ) . "'>";

                        
                        //set up for force confirm selected
                        $force_confirm_selected_text = __( "Force confirm selected", "speakout");
                        echo "<input type='hidden' id='dk-speakout-force-confirm-selected-text' value='" .  esc_html( $force_confirm_selected_text ) . "'>";
                        $force_confirm_unselected_text = __( "Force confirm selected - choose signatures", "speakout");
                        echo "<input type='hidden' id='dk-speakout-force-confirm-unselected-text' value='" .  esc_html( $force_confirm_unselected_text ) . "'>";
                        $force_confirm_unselected_no_petition_text = __( "Force confirm selected: choose petition first", "speakout"); 
                        if(isset($_GET["pid"]) && $_GET["pid"] > ""){
                            $force_confirm_unselected_no_petition_text = __( "Force confirm selected: choose signatures", "speakout");
                        }
                        $force_confirm_unselected_no_signatures_text = __( "Force confirm selected: choose signatures", "speakout");
                        echo "<input type='hidden' id='dk-speakout-force-confirm-unselected-text' value='" .  esc_html( $force_confirm_unselected_no_signatures_text ) . "'>";
                        
                        //set up for delete selected
                        $delete_text = __( "Delete selected", "speakout");
                        echo "<input type='hidden' id='dk-speakout-delete-selected-text' value='" .  esc_html( $delete_text ) . "'>";
                        $nodelete_text = __( "Delete selected: choose signatures", "speakout");
                        echo "<input type='hidden' id='dk-speakout-delete-unselected-text' value='" .  esc_html( $nodelete_text ) . "'>";

                        //for some actions we need a petition selected
                        if( (isset( $_REQUEST['pid'] ) && $pid > '') || $petitioncount == 1 ) {
                            $option_disabled="";  
                            $option_notice="";
                            
                            // set up for csv export
                            $csvURL = wp_nonce_url( $csv_url . '&csv=signatures', 'dk_speakout-download_signatures' ); 
                            $nonce = explode("=", $csvURL);
                            echo "<input type='hidden' id='csvnonce' value='" . trim(end($nonce)) . "'>";

                            //set up for confirm emails
                            $confirm_text = __("Resend selected confirmations", "speakout");
                        }
                      
                      //apply button is disabled if no petition selected - enabled via JS if sig is selected
                      $actionStatus = (isset($_GET["pid"]) && ($_GET["pid"] > "")) || $petitioncount == 1 ? "": " disabled='disabled'" ;
 
                   ?>
                    <label for="dk-speakout-action-selector" class="screen-reader-text">Select action</label>
                    <select name="dk-speakout-action-selector" id="dk-speakout-action-selector">
                        <option value="-1"><?php _e("Choose signatures action", "speakout"); ?></option>
                        <option value="csv-download" <?php echo esc_html( $option_disabled ); ?>><?php _e( "Download CSV - see settings/admin sig list", "speakout") .  __( $option_notice, "speakout") ; ?></option>
                        <option value="display-unconfirmed" <?php echo esc_html( $option_disabled ); ?>><?php _e("Display only unconfirmed", "speakout") . __( $option_notice, "speakout") ; ?></option>
                        <option value="confirm-resend" <?php echo esc_html( $option_disabled ); ?>><?php _e("Resend all unconfirmed", "speakout") . __( $option_notice, "speakout") ; ?></option>
                        <option value="reconfirm-selected" disabled="disabled"><?php echo esc_html( $reconfirm_unselected_no_petition_text ); ?></option>
                        <option value="forceconfirm-selected" disabled="disabled"><?php echo esc_html( $force_confirm_unselected_no_petition_text ); ?></option>
                        <option value="delete-selected"  disabled="disabled"><?php echo esc_html( $nodelete_text ); ?></option>
                    </select>
                    <input type="submit" id="dk-speakout-doaction" class="button action" value=<?php _e("Apply", "speakout") .  esc_html( $actionStatus ); ?>  />
                </form>
 
                <?php $searchString = isset($_REQUEST["searchString"]) ? sanitize_text_field($_REQUEST["searchString"]) : ""; ?> 
				<form id="dk_sig_search_form" name="dk_sig_search_form" action="<?php echo  wp_nonce_url( $search_url, 'dk_speakout-search_signatures' . $pid ); ?>" method="POST"  class="dk-sigform">
				    <input type="search" name="searchString" id="searchString" value="<?php echo esc_html( $searchString ) ?>" placeholder="<?php _e("Search signatures", "speakout"); ?>" /> 
				    <button id="search-button">
                        <svg id="search-icon" class="search-icon" viewBox="0 0 24 24">
                            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                            <path d="M0 0h24v24H0z" fill="none"/>
                        </svg>
                     </button>
                </form>

			</div>
			<div class="alignright">
				<?php echo dk_speakout_SpeakOut::pagination( $query_limit, $count, 'dk_speakout_signatures', $current_page, $base_url, true ); ?>
			</div>
		</div>
	</div>
	

<?php
// set up the table header based on options selected
    $emailheader    	= $options['sigtab_email'] == "on" ?              	"<th>" . __( 'Email', 'speakout' ) . "</th>" 			: "" ;
    $petitionheader 	= $options['sigtab_petition_info'] == "on" ?    	"<th>" . __( 'Petition', 'speakout' ) . "</th>" 		: "" ;
    $streetheader   	= $options['sigtab_street_address'] == "on" ?   	"<th>" . __( 'Street', 'speakout' ) . "</th>" 			: "" ;
    $cityheader     	= $options['sigtab_city'] == "on" ?             	"<th>" . __( 'City', 'speakout' ) . "</th>" 			: "" ;
    $stateheader    	= $options['sigtab_state'] == "on" ?            	"<th>" . __( 'State', 'speakout' ) . "</th>" 			: "" ;
    $postalheader   	= $options['sigtab_postalcode'] == "on" ?       	"<th>" . __( 'Postal Code', 'speakout' ) . "</th>" 		: "" ;
    $countryheader  	= $options['sigtab_country'] == "on" ?          	"<th>" . __( 'Country', 'speakout' ) . "</th>" 			: "" ;
    $customfield1header = $options['sigtab_custom_field1'] == "on" ?    	"<th>" . __( 'Custom field 1', 'speakout' ) . "</th>" 	: "" ;
    $customfield2header = $options['sigtab_custom_field2'] == "on" ?    	"<th>" . __( 'Custom field 2', 'speakout' ) . "</th>" 	: "" ;
    $customfield3header = $options['sigtab_custom_field3'] == "on" ?    	"<th>" . __( 'Custom field 3', 'speakout' ) . "</th>" 	: "" ;
    $customfield4header = $options['sigtab_custom_field4'] == "on" ?    	"<th>" . __( 'Custom field 4', 'speakout' ) . "</th>" 	: "" ;
    $customfield5header = $options['sigtab_custom_field5'] == "on" ?    	"<th>" . __( 'Custom field 5', 'speakout' ) . "</th>" 	: "" ;
    $customfield6header = $options['sigtab_custom_field6'] == "on" ?    	"<th>" . __( 'Checkbox 1', 'speakout' ) . "</th>" 		: "" ;
    $customfield7header = $options['sigtab_custom_field7'] == "on" ?    	"<th>" . __( 'Checkbox 2', 'speakout' ) . "</th>" 		: "" ;
    $optinheader    	= $options['sigtab_optin'] == "on" ?            	'<th style="white-space:nowrap">' . __( 'Opt-in', 'speakout' ). '</th>' : "" ;
    $confirmedheader	= $options['sigtab_confirmed_status'] == "on" ? 	"<th>" . __( 'Confirmed', 'speakout' ) .  "</th>" 		: "";
    $dateheader     	= $options['sigtab_date_signed'] == "on" ?      	"<th>" . __( 'Date', 'speakout' ) . "</th>" 			: "" ;
    $ipheader       	= $options['sigtab_IP_address'] == "on" ?       	"<th>" . __( 'IP Address', 'speakout' ) . "</th>" 		: "" ;
?>
<form id="dk-speakout-signaturelist" name="dk-speakout-signaturelist" action="">
	<table class="widefat">
		<thead>
			<tr>
				<th style="padding:0;width:5px"><input type="checkbox" id="dk-speakout-signatures-checkAll" name="dk-speakout-signatures-checkAll" ></th>
				<th></th>
				<th><?php echo _e( 'Name', 'speakout' ); ?></th>
			    <?php
    			   //these are simply translated strings from line 132
    			   echo $emailheader .
    			    $petitionheader .
    			    $streetheader .
			        $cityheader .
			        $stateheader .
			        $postalheader .
			        $countryheader .
    			    $customfield1header .
                    $customfield2header .
                    $customfield3header .
                    $customfield4header .
                    $customfield5header .
                    $customfield6header .
                    $customfield7header .
    			    $confirmedheader .
    			    $optinheader .
    			    $dateheader .
    			    $ipheader;
			    ?>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th width="5px"></th>
				<th></th>
				<th><?php echo _e( 'Name', 'speakout' ); ?></th>
			    <?php
    			    echo  $emailheader .
    			    $petitionheader .
    			    $streetheader .
			        $cityheader .
			        $stateheader .
			        $postalheader .
			        $countryheader .
    			    $customfield1header .
    			    $customfield2header .
                    $customfield3header .
                    $customfield4header .
                    $customfield5header .
                    $customfield6header .
                    $customfield7header .
                    $confirmedheader .
    			    $optinheader .
    			    $dateheader .
    			    $ipheader ;
			    ?>
			</tr>
		</tfoot>
		<tbody>
			<?php 
			
			//be nice if no signatures yet
			if ( $count == 0 ) echo '<tr><td colspan="8">' . __( "No signatures found.", "speakout" ) . ' </td></tr>'; 
			
			$current_row = ( $count - $query_start ) + 1; 
			//set a flag for if we find an anonymous name
			$foundanon = false;
			foreach ( $signatures as $signature ) : $pid_string = ( $pid ) ? '&pid=' . $pid : '' ; 
			
                 // set up the table content based on options selected
                 
                 // are we displaying honorific?
                    $honorificdisplay = $options["display_honorific"] == "enabled" ? $signature->honorific . ' ' : "" ;

					$current_row --;

                    $emailcontent = $options['sigtab_email'] == "on" ? '<td style="white-space:nowrap">' . esc_html( $signature->email ) . '</td>' : "" ;
                    
                    //depending on how many petitions, we craft the info
                    if($options['sigtab_petition_info'] == "on"){ 	
                        $petitioncontent = '<td style="white-space:nowrap">' . esc_html( $signature->title ); 
                            if($petitions_list && count( $petitions_list) > 1 ){ 
                                $petitioncontent .= "(#" . $signature->petitions_id . ")"; 
                            }
                        $petitioncontent .= '</td>';
                        }
                    else{ 	
                        $petitioncontent = "";
                    }
                    
                    $streetcontent = $options['sigtab_street_address'] == "on" ? '<td style="white-space:nowrap">' . esc_html( $signature->street_address ) . '</td>' : "" ;
                    $citycontent = $options['sigtab_city'] == "on" ? '<td style="white-space:nowrap">' . esc_html( $signature->city ) . '</td>' : "" ;
                    $statecontent = $options['sigtab_state'] == "on" ? '<td style="white-space:nowrap">' . esc_html( $signature->state ) . '</td>' : "";
                    $postalcontent = $options['sigtab_postalcode'] == "on" ? '<td style="white-space:nowrap">' . esc_html( $signature->postcode ) . '</td>' : "";
                    $countrycontent = $options['sigtab_country'] == "on" ? '<td style="white-space:nowrap">' . esc_html( $signature->country ) . '</td>' : "";
                    
                    // are we displaying custom field columns?
                    if($options['sigtab_custom_field1'] == "on"){
                        
                        // is the custom field even active?
                        if( $signature->displays_custom_field == '1' ){
                            //does it have a value
    					    if( $signature->custom_field > ""){
    					        // if so, truncate the custom field if it is too long
                                $customfield1content = substr($signature->custom_field, 0, 20);
                                $title = "";
                                if (strlen($signature->custom_field) > 20){
                                    //include all the field content as a title for mouseover
                                    $title = " title='" . esc_html( $signature->custom_field ) . "' ";
                                    $customfield1content .= "...";   
                                }
																															  
    					    }
    					    // if no value, display a dash
    					    elseif(strlen($signature->custom_field) == 0){
    					        $customfield1content = "-";
    					    }
    					}
    					else{
    					    //if it is disabled, indicate
    						$customfield1content = '(disabled)';
    					}
    					//now format for table
    				    $customfield1content = '<td style="white-space:nowrap">' .  esc_html( $customfield1content ). '</td>';
                        
                }
                    else{
                       $customfield1content = "";  
                    }
            
                    if($options['sigtab_custom_field2'] == "on"){
                        
                        // is the custom field even active?
                        if( $signature->displays_custom_field2 == '1' ){
                            //does it have a value
    					    if( $signature->custom_field2 > ""){
    					        // if so, truncate the custom field if it is too long
                                $customfield2content = substr($signature->custom_field2, 0, 20);
                                $title = "";
                                if (strlen($signature->custom_field2) > 20){
                                    //include all the field content as a title for mouseover
                                    $title = " title='" . esc_html( $signature->custom_field2 ) . "' ";
                                    $customfield2content .= "...";   
                                }																						  
    					    }
    					    // if no value, display a dash
    					    elseif(strlen($signature->custom_field2) == 0){
    					        $customfield2content = "-";
    					    }
    					}
    					else{
    					    //if it is disabled, indicate
    						$customfield2content = '(disabled)';
    					}
    					//now format for table
    				    $customfield2content = '<td style="white-space:nowrap">' .  esc_html( $customfield2content ) . '</td>';
                        
                    }
                    else{
                       $customfield2content = "";  
                    }
            
                    if($options['sigtab_custom_field3'] == "on"){
                        
                        // is the custom field even active?
                        if( $signature->displays_custom_field3 == '1' ){
                            //does it have a value
    					    if( $signature->custom_field3 > ""){
    					        // if so, truncate the custom field if it is too long
                                $customfield3content = substr($signature->custom_field3, 0, 20);
                                $title = "";
                                if (strlen($signature->custom_field3) > 20){
                                    //include all the field content as a title for mouseover
                                    $title = " title='" . esc_html( $signature->custom_field3 ) . "' ";
                                    $customfield3content .= "...";   
                                }
																															  
    					    }
    					    // if no value, display a dash
    					    elseif(strlen($signature->custom_field3) == 0){
    					        $customfield3content = "-";
    					    }
    					}
    					else{
    					    //if it is disabled, indicate

    						$customfield3content = '(disabled)';
    					}
    					//now format for table
    				    $customfield3content = '<td style="white-space:nowrap">' .  esc_html( $customfield3content ) . '</td>';
                        
                }
                    else{
                       $customfield3content = "";  
                    }
            
                    if($options['sigtab_custom_field4'] == "on"){
                        
                        // is the custom field even active?
                        if( $signature->displays_custom_field4 == '1' ){
                            //does it have a value
    					    if( $signature->custom_field4 > ""){
    					        // if so, truncate the custom field if it is too long
                                $customfield4content = substr($signature->custom_field4, 0, 20);
                                $title = "";
                                if (strlen($signature->custom_field4) > 20){
                                    //include all the field content as a title for mouseover
                                    $title = " title='" . esc_html( $signature->custom_field4 ) . "' ";
                                    $customfield4content .= "...";   
                                }
																															  
    					    }
    					    // if no value, display a dash
    					    elseif(strlen($signature->custom_field4) == 0){
    					        $customfield4content = "-";
    					    }
    					}
    					else{
    					    //if it is disabled, indicate
    						$customfield4content = '(disabled)';
    					}
    					//now format for table
    				    $customfield4content = '<td style="white-space:nowrap">' .  esc_html( $customfield4content ) . '</td>';
                        
                }
                    else{
                       $customfield4content = "";  
                    }
                    
                    if($options['sigtab_custom_field5'] == "on"){
                        
                        // is the custom field even active?
                        if( $signature->displays_custom_field5 == '1' ){
                            //does it have a value
    					    if( $signature->custom_field5 > ""){
    					        // if so, truncate the custom field if it is too long
                                $customfield5content = substr($signature->custom_field5, 0, 20);
                                $title = "";
                                if (strlen($signature->custom_field5) > 20){
                                    //include all the field content as a title for mouseover
                                    $title = " title='" . esc_html( $signature->custom_field5 ) . "' ";
                                    $customfield5content .= "...";   
                                }
																															  
    					    }
    					    // if no value, display a dash
    					    elseif(strlen($signature->custom_field5) == 0){
    					        $customfield5content = "-";
    					    }
    					}
    					else{
    					    //if it is disabled, indicate
    						$customfield5content = '(disabled)';
    					}
    					//now format for table
    				    $customfield5content = '<td style="white-space:nowrap">' .  esc_html( $customfield5content ) . '</td>';
                        
                }
                    else{
                       $customfield5content = "";  
                    }
                    
                if($options['sigtab_custom_field6'] == "on"){
                        
                    // is the custom field even active?
                    if( $signature->displays_custom_field6 == 1 ){
                        //does it have a value
					    if( $signature->custom_field6 == 1){
					        $customfield6content = "&#10004;";
					    }
					    // if no value, display a dash
					    else{
					        $customfield6content = "-";
					    }
					}
					else{
					    //if it is disabled, indicate
						$customfield6content = '(disabled)';
					}
					//now format for table
				    $customfield6content = '<td style="white-space:nowrap">' .  esc_html( $customfield6content ) . '</td>';
                        
                }
                else{
                   $customfield6content = "";  
                }
                    
                if($options['sigtab_custom_field7'] == "on"){
                        
                    // is the custom field even active?
                    if( $signature->displays_custom_field7 == 1 ){
                        //does it have a value
					    if( $signature->custom_field7 == 1){
					        $customfield7content = "&#10004;";
					    }
					    // if no value, display a dash
					    else{
					        $customfield7content = "-";
					    }
					}
					else{
					    //if it is disabled, indicate
						$customfield7content = '(disabled)';
					}
					//now format for table
				    $customfield7content = '<td style="white-space:nowrap">' .  esc_html( $customfield7content ) . '</td>';
                        
                }
                else{
                   $customfield7content = "";  
                }
            
            // make confirmed values readable
				$confirmed = $signature->is_confirmed;
				if ( $confirmed == '1' ){
					$isconfirmed = '<span class="dk-speakout-green">&#10004;</span>';
				}
				else {
				    $isconfirmed = "-";
				}

				$confirmedcontent = $options['sigtab_confirmed_status'] == "on" ? '<td style="white-space:nowrap">' .   $isconfirmed . '</td>' : "" ;
				
				
		 
				// make email opt-in values readable
				$optin = $signature->optin;
				if ( $optin == '1' ){
					$isoptin = '<span class="dk-speakout-green">&#10004;</span>';
				}
				else{
					$isoptin = "-";
				}

				$optincontent = $options['sigtab_optin'] == "on" ? '<td style="white-space:nowrap">' .   $isoptin .'</td>' : "-" ;
                $datecontent = $options['sigtab_date_signed'] == "on" ? '<td style="white-space:nowrap">' . ucfirst( date_i18n( 'M d, Y', strtotime( $signature->date ) ) ) . '</td>' : "" ;
                $ipcontent = $options['sigtab_IP_address'] == "on" ? '<td style="white-space:nowrap">' . esc_html( $signature->IP_address ) . '</td>' : "" ;
					
                ?>
				
			<tr class="dk-speakout-tablerow">
			    <td width="5px"><input type="checkbox" name="signature[]" id="dk-speakout-signature-<?php echo abs( $signature->id );  ?> " class="dk-speakout-signaturecheck" value="<?php echo $signature->id;  ?>" ></td>
				<td class="dk-speakout-right"><?php echo number_format( $current_row, 0, '.', ',' ); ?></td>
				<?php // if anonymous, tag name
				    $anonymous = $signature->anonymise == 1 ?'<sup>&#9830;</sup>' : "";
				    $foundanon = true;
				?>
				<td style="white-space:nowrap"><?php echo esc_html( $honorificdisplay . $signature->first_name . ' ' . $signature->last_name ) . $anonymous ; ?></td>
				<?php
				
				// these are all escaped starting line 225
				echo  $emailcontent . 
				    $petitioncontent .
    			    $streetcontent .
			        $citycontent .
			        $statecontent .
			        $postalcontent .
			        $countrycontent .
    			    $customfield1content .
                    $customfield2content .
                    $customfield3content .
                    $customfield4content .
                    $customfield5content .
                    $customfield6content .
                    $customfield7content .
    			    $confirmedcontent .
    			    $optincontent .
    			    $datecontent .
    			    $ipcontent ;
                ?>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</form>
<div>
    <?php if($foundanon){echo "&#9830; = publicly anonymous";}; ?>
</div>
	<div class="tablenav">
		<?php echo dk_speakout_SpeakOut::pagination( $query_limit, $count, 'dk_speakout_signatures', $current_page, $base_url, false ); ?>
	</div>

</div>