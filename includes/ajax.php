<?php

/**
 * Handle public petition form submissions
 */
add_action( 'wp_ajax_dk_speakout_sendmail', 'dk_speakout_sendmail' );
add_action( 'wp_ajax_nopriv_dk_speakout_sendmail', 'dk_speakout_sendmail' );
function dk_speakout_sendmail() {

	// set WPML language
	global $sitepress;
	$lang = isset( $_POST['lang'] ) ? sanitize_text_field( $_POST['lang'] ): '';
	if ( isset( $sitepress ) ) {
		$sitepress->switch_lang( $lang, true );
	}

	include_once( 'class.signature.php' );
	include_once( 'class.petition.php' );
	include_once( 'class.mail.php' );
	include_once( 'class.wpml.php' );
	$the_signature = new dk_speakout_Signature();
	$the_petition  = new dk_speakout_Petition();
	$wpml          = new dk_speakout_WPML();
	$options       = get_option( 'dk_speakout_options' );

	// clean posted signature fields
	$the_signature->populate_from_post();

	// get petition data
	$the_petition->retrieve( $the_signature->petitions_id );
	$wpml->translate_petition( $the_petition );
	$options = $wpml->translate_options( $options );

	// check if submitted email address is already in use for this petition
	if ( $the_signature->has_unique_email( $the_signature->email, $the_signature->petitions_id, $the_petition->hide_email_field ) ) {

		// handle custom petition messages
		$original_message = str_replace( "\r", '', $the_petition->petition_message );
		if ( $the_petition->is_editable && $the_signature->submitted_message != $original_message ) {
			$the_signature->custom_message = trim( $the_signature->submitted_message );
		}

		// does petition require email confirmation?
		if ( $the_petition->requires_confirmation ) {

			$the_signature->is_confirmed = 0;
			$the_signature->create_confirmation_code();
			dk_speakout_Mail::send_confirmation( $the_petition, $the_signature, $options );
		}
		else {
			if ( $the_petition->sends_email ) {
			    //email target
				dk_speakout_Mail::send_petition( $the_petition, $the_signature,"" );
			}    
		}

		// add signature to database
		$the_signature->create( $the_signature->petitions_id, $the_petition->increase_goal);

		// display success message
		$success_message = $options['success_message'];
		$success_message = str_replace( '%first_name%', $the_signature->first_name, $success_message );
		$success_message = str_replace( '%last_name%', $the_signature->last_name, $success_message );
        $success_message = str_replace( '%signature_number%', ($the_petition->signatures + 1), $success_message );
		

		if($the_petition->displays_custom_message == 1){
			$success_message .= stripslashes( esc_attr( $the_petition->custom_message_label ) );
		}

		$json_response = array(
			'status'  => 'success',
			'message' => $success_message
		);
		$json_response = json_encode( $json_response );

		echo $json_response;
	}
	else {
		
		$json_response = array(
			'status'  => 'error',
			'message' => $options['already_signed_message']
		);
		$json_response = json_encode( $json_response );

		echo $json_response;
	}

	// end AJAX processing
	die();
}

add_action( 'wp_ajax_dk_speakout_paginate_signaturelist', 'dk_speakout_paginate_signaturelist' );
add_action( 'wp_ajax_nopriv_dk_speakout_paginate_signaturelist', 'dk_speakout_paginate_signaturelist' );
function dk_speakout_paginate_signaturelist() {
	include_once( 'class.signaturelist.php' );
	$list = new dk_speakout_Signaturelist();
	$table = $list->table( sanitize_text_field( $_POST['id']) , 
                          sanitize_text_field( $_POST['start'] ), 
                          sanitize_text_field( $_POST['limit'] ), 
                          'ajax', 
                          sanitize_text_field( $_POST['dateformat'] ),
                          
                          $_POST['hideUnconfirmed']
                         );
	echo esc_html( $table );
    echo wp_kses(
        $table,
            array(
                'tr'     => array(),
                'td'     => array(),
            )
        );
	// end AJAX processing
	die();
}

?>