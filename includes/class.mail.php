<?php

/**
 * Class for sending emails in SpeakOut! Email Petitions plugin for WordPress
 */
class dk_speakout_Mail
{

	/**
	 * Sends confirmation email
	 * includes a link to confirm ownership of email account used to sign petition
	 *
	 * @param object $petition the petition being signed
	 * @param object $signature the signature
	 * @param array $options custom wp_options for this plugin
	 */
	public static function send_confirmation( $petition, $signature, $options )
	{
		$email   = stripslashes( $signature->email );
		$subject = stripslashes( $options['confirm_subject'] );
		$message = stripslashes( $options['confirm_message'] );
		$message = nl2br($message);

		$doBCC = isset($_POST['bcc']) && $_POST['bcc']=="on" ? 1 : 0;
                
		// construct confirmation URL
		$confirmation_url = '<a href="' . home_url() . '/?dkspeakoutconfirm=' . $signature->confirmation_code . '&b=' . $doBCC  . '&lang=' . get_bloginfo( 'language' ) . '">' . home_url() . '/?dkspeakoutconfirm=' . $signature->confirmation_code . '&b=' . $doBCC . '&lang=' . get_bloginfo( 'language' ) . '</a>'; 

		// add confirmation link to email if user left it out
		if ( strpos( $message, '%confirmation_link%' ) == false ) {
			$message = $message . "\r\n" . $confirmation_url;
		}

		// replace user-supplied variables
		$search  = array( '%honorific%', '%first_name%', '%last_name%', '%petition_title%', '%confirmation_link%' );
		$replace = array( stripslashes($signature->honorific), stripslashes($signature->first_name), stripslashes($signature->last_name), stripslashes($petition->title), $confirmation_url );
		$message = str_replace( $search, $replace, $message );

		// construct email headers
		$headers = "From: " . $options['confirm_email'] . "\r\n";
		$headers .= "MIME-Version: 1.0" . "\r\n";
		$charset = get_bloginfo( 'charset' );
		$headers .= "Content-Type: text/html; charset=" . $charset . "\r\n";

		// send the confirmation email
		self::send( $email, $subject, $message, $headers );
        
		if ( $options['webhooks'] == 'on' ) {
            $theId = $petition->id;
            $theTitle = $petition->title;
            do_action( 'speakout_after_confirmation_sent', $theId, $theTitle, $email );
        }
	}
    
    
	/**
	 * Sends petition email
	 *
	 * @param object $petition the petition being signed
	 * @param object $signature the signature
	 */
	public static function send_petition( $petition, $signature, $doBCC )
	{
        //to avoid clashing with other resources using parsedown
        if ( ! class_exists( 'Parsedown' ) ) {
            include_once( 'parsedown.php' );
        }

	    $Parsedown = new Parsedown();

		$subject = esc_html( $petition->email_subject );

		// use custom petition message if provided
		$message = $petition->petition_message;
		if ( $signature->custom_message != '' ) {
			$message = $signature->custom_message;
		}
		
		// replace user-supplied variables
		$search  = array( '%honorific%', '%first_name%', '%last_name%', '%petition_title%' );
		$replace = array( esc_html($signature->honorific), esc_html($signature->first_name), esc_html($signature->last_name), esc_html($petition->title));
		$message = str_replace( $search, $replace, $message );
		$message = $Parsedown->text($message);
		
		$footer = $petition->petition_footer;

		// add new line after greeting if provided
		$greeting = '';
		if ( $petition->greeting != '' ) {
			$greeting = $petition->greeting . "\r\n\r\n";
		}
        
        // build custom fields to tail address
        // custom_fields gets their own line
        $customFields="";
		if ( $petition->displays_custom_field == 1 && $petition->custom_field_included == 1) {
		    $customField1 = $signature->custom_field > "" ? esc_html( $signature->custom_field) : "-";
			$customFields = "\r\n" . "<br>" . $petition->custom_field_label . ": " . $customField1;
		}

		if ( $petition->displays_custom_field2 == 1  && $petition->custom_field2_included == 1) {
		    $customField2 =  $signature->custom_field2 > "" ? esc_html( $signature->custom_field2) : "-";
			$customFields .= "\r\n" . "<br>" . $petition->custom_field2_label . ": " . $customField2;
		}
        
        if ( $petition->displays_custom_field3 == 1  && $petition->custom_field3_included == 1) {
            $customField3 =  $signature->custom_field3 > "" ? esc_html( $signature->custom_field3) : "-";
			$customFields .= "\r\n" . "<br>" . $petition->custom_field3_label . ": " . $customField3;
		}
        
        if ( $petition->displays_custom_field4 == 1  && $petition->custom_field4_included == 1) {
            $customField4 = $signature->custom_field4 > "" ? esc_html( $signature->custom_field4) : "-";
			$customFields .= "\r\n" . "<br>" . $petition->custom_field4_label . ": " . $customField4;
		}
        
        if ( $petition->displays_custom_field5 == 1  && $petition->custom_field5_included == 1) {
            $customField5 = $signature->custom_field5 > "" ? esc_html( $signature->custom_field5) : "-";
			$customFields .= "\r\n" . "<br>" . $petition->custom_field5_label . ": " . $customField5;
		}

		// construct email message
		$email_message  = stripslashes( $greeting );
		$email_message .= stripslashes( $message );
		$email_message .= "\r\n\r\n--";
		$email_message .= "\r\n" . esc_html( $signature->honorific . " " . $signature->first_name . ' ' . $signature->last_name );
		$email_message .= "\r\n" . "<br>" . esc_html( $signature->email );
		$email_message .=  "<br>" . self::format_street_address( $signature );
        $email_message .=  "<br>" . esc_html( $customFields );
		$email_message .= "\r\n\r\n" .  "<br><br>" . esc_html( $footer );

		// construct email headers
        // To send HTML mail, the Content-type header must be set
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		$headers .= "From: " . esc_html( $signature->first_name ) . " " . esc_html( $signature->last_name ) . " <" . esc_html( $signature->email ) . ">" . "\r\n";
        $headers .= 'Reply-To: ' . esc_html( $signature->email ) . "\r\n" . 'X-Mailer: PHP/' . phpversion() . "\r\n";
		
        // if BCC box is checked, $doBCC is returned from confirmations.php
			if((isset($_POST['bcc']) && $_POST['bcc']=="on") || $doBCC == 1 ){
			$headers .= "Bcc:" . $signature->email . "\r\n";
		}
        
        //only add CC header if there is value in CC field
		if( $petition->target_email_CC > "" ) {
            
		    $headers .= "CC:" . esc_html( $petition->target_email_CC ) . "\r\n";
		}
		        
		// send the petition email
		self::send( $petition->target_email, $subject, $email_message, $headers );
        
        if ( $options['webhooks'] == 'on' ) {
            $id = $petition->target_email;
            $title = $petition->title;
            $email = $petition->target_email;
            do_action( 'speakout_after_petition_sent', $id, $title, $doBCC, $from, $email,  $subject, $email_message );
        }
	}

	//********************************************************************************
	//* Private
	//********************************************************************************

	/**
	 * Formats address portion of email signature using appropriate commas, spaces, and new-line characters
	 *
	 * @param object $signature the signature
	 * @return string address
	 */
	public static function format_street_address( $signature )
	{
		$address  = '';

		// street address gets its own line
		if ( $signature->street_address != '' ) {
			$address .=  "\r\n" . esc_html( $signature->street_address );
		}

		// format 'city, state postcode' line with appropriate line-break, comma and spaces
		if ( $signature->city != '' || $signature->state != '' || $signature->postcode != '' ) {
			$address .= "\r\n";

			if ( $signature->city != '' ) {
				$address .= esc_html( $signature->city );
			}

			// if both city & state are present, separate with a comma
			if ( $signature->city != '' && $signature->state != '' ) {
				$address .= ", " ;
			}

			if ( $signature->state != '' ) {
				$address .= esc_html( $signature->state );
			}

			if ( $signature->postcode != '' ) {
				if ( $signature->city != '' || $signature->state != '' ) {
					$address .= " ";
				}
				$address .= esc_html( $signature->postcode );
			}
		}

		// country gets its own line
		if ( $signature->country != '' ) {
			$address .= "\r\n" . esc_html( $signature->country );
		}
        
		return $address;
	}

	/**
	 * Sends email using WordPress' wp_mail()
	 *
	 * @param string $to email address
	 * @param string $subject email subject
	 * @param string $message email message
	 * @param string $headers email headers, should end in newline character "\r\n"
	 */
	public static function send( $to, $subject, $message, $headers )
	{
		wp_mail( $to, $subject, $message, $headers );
		
	}
   
}

?>