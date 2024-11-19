<?php
// generate CSV file for download
if ( isset( $_REQUEST['csv'] ) && $_REQUEST['csv'] == 'signatures' ) {

	ini_set('max_execution_time', 300); // ensure adequate maximum execution time - restore default at end of script 

	// make sure it executes before headers are sent
	add_action( 'admin_menu', 'dk_speakout_signatures_csv' );
	function dk_speakout_signatures_csv() {
		// check security: ensure user has authority and intention
		if ( ! current_user_can( 'publish_posts' ) ) wp_die( __( 'Insufficient privileges: You need to be an editor to do that.', 'speakout' ) );
		check_admin_referer( 'dk_speakout-download_signatures' );

		include_once( 'class.signature.php' );
		include_once( 'class.petition.php' );
    	include_once( 'class.wpml.php' );
    	$signatures = new dk_speakout_Signature();
    	$petition  = new dk_speakout_Petition();
		

		$petition_id = isset( $_REQUEST['pid'] ) ? abs ( $_REQUEST['pid'] ) : 1 ; // petition id
        $petition->retrieve( $petition_id );
        
		// retrieve signatures from the database
		$csv_data = $signatures->all( $petition_id, 0, 0, 'csv' );

		// display error message if query returns no results
		if ( count( $csv_data ) < 1 ) {
			echo '<h1>' . __( "No signatures found.", "speakout" ) . '</h1>';
			die();
		}

		// construct CSV filename
		$counter = 0;
		foreach ( $csv_data as $file ) {
			if ( $counter < 1 ) {
				$filename_title = stripslashes( str_replace( ' ', '-', $file->title ) );
				$filename_date  = date( 'Y-m-d', strtotime( current_time( 'mysql', 0 ) ) );
				$filename = $filename_title . '_' . $filename_date . '.csv';
			}
			$counter ++;
		}

		// set up CSV file headers
		header( 'Content-Type: text/octet-stream; charset=UTF-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		header( 'Pragma: public' ); // supposed to make stuff work over https

		// get the column headers translated
		$id		        = __( 'ID', 'speakout' );
        $petition_id    = __( 'Petition ID', 'speakout' );
		$honorific		= __( 'Honorific', 'speakout' );
		$firstname      = __( 'First Name', 'speakout' );
		$lastname       = __( 'Last Name', 'speakout' );
		$email          = __( 'Email Address', 'speakout' );
		$street         = __( 'Address', 'speakout' );
		$city           = __( 'City', 'speakout' );
		$state          = __( 'State', 'speakout' );
		$postcode       = __( 'Postal Code', 'speakout' );
		$country        = __( 'Country', 'speakout' );
        $custom_field1  = $petition->custom_field_label;
        $custom_field2  = $petition->custom_field2_label;
        $custom_field3  = $petition->custom_field3_label;
        $custom_field4  = $petition->custom_field4_label;
        $custom_field5  = $petition->custom_field5_label;
        $custom_field6  = $petition->custom_field6_label;
        $custom_field7  = $petition->custom_field7_label;
        $email_optin    = __( 'Email Opt-in', 'speakout' );
		$date           = __( 'Date Signed', 'speakout' );
        $confirmation_code      = __( 'Confirmation code', 'speakout' );
		$confirmed      = __( 'Confirmed', 'speakout' );
        $custom_message = __( 'Custom Message', 'speakout' );
        $language       = __( 'Language', 'speakout' );
        $IP_address     = __( 'IP Address', 'speakout' );
        $anonymise      = __( 'Anonymise', 'speakout' );
		$petition_title = __( 'Petition Title', 'speakout' );
		$petitions_id   = __( 'Petition ID', 'speakout' );

		// If set, use the custom field label as column header instead of "Custom Field"
		$counter = 0;
		foreach ( $csv_data as $label ) {
			if ( $counter < 1 ) {
				if ( $label->custom_field_label != '' ) {
					$custom_field_label = stripslashes( $label->custom_field_label );
				}
				else {
					$custom_field_label = __( 'Custom Field', 'speakout' );
				}
			}
			$counter ++;
		}

		// construct CSV file header row
		// must use double quotes and separate with tabs    
		$csv = "Signature ID,$petitions_id,$honorific,$firstname,$lastname,$email,$street,$city,$state,$postcode,$country,$custom_field1,$custom_field2,$custom_field3,$custom_field4,$custom_field5,$custom_field6,$custom_field7,$email_optin,$date,$confirmation_code,$confirmed,$custom_message,$language,$IP_address,$anonymise";
		$csv .= "\n";

		// construct CSV file data rows
		foreach ( $csv_data as $signature ) {
			
			$csv .=  stripslashes('"' . 
            trim($signature->id) . '","' .
			trim($signature->petitions_id) . '","' . 
			trim($signature->honorific) . '","' . 
			trim($signature->first_name) . '","' . 
			trim($signature->last_name) . $anonName . '","' . 
			trim($signature->email) . '","' . 
			trim($signature->street_address) . '","' . 
			trim($signature->city) . '","' . 
			trim($signature->state) . '","' . 
			trim($signature->postcode) . '","' . 
			trim($signature->country) . '","' . 
			trim($signature->custom_field) . '","' .
            trim($signature->custom_field2) . '","' .
			trim($signature->custom_field3) . '","' .
            trim($signature->custom_field4) . '","' .
            trim($signature->custom_field5) . '","' .
            $signature->custom_field6 . '","' .
            $signature->custom_field7 . '","' .
			$signature->optin . '","' . 
            trim($signature->date) . '","' . 
			trim($signature->confirmation_code) . '","' .
            trim($is_confirmed) . '","' .  		
			trim($signature->custom_message) . '","' . 
            trim($signature->language) . '","' . 
			trim($signature->IP_address) . '"' );
			$csv .= "\n";
		}

		// output CSV file in a UTF-8 format that Excel can understand
		echo chr( 255 ) . chr( 254 ) . mb_convert_encoding( $csv, 'UTF-16LE', 'UTF-8' );
	}		
	ini_restore('max_execution_time'); // reset max_execution_time
}

?>