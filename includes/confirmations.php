<?php

// capture confirmation_code variable from links clicked in confirmation emails
if ( isset( $_REQUEST['dkspeakoutconfirm'] ) ) {
	add_action( 'template_redirect', 'dk_speakout_confirm_email' );
}

/**
 * Displays the confirmation page
 */
function dk_speakout_confirm_email() {

	// set WPML language
	global $sitepress;
	$lang = isset( $_REQUEST['lang'] ) ? $_REQUEST['lang'] : '';

	if ( isset( $sitepress ) ) {
		$sitepress->switch_lang( $lang, true );
	}

	include_once( 'class.signature.php' );
	include_once( 'class.petition.php' );
	include_once( 'class.mail.php' );
	include_once( 'class.wpml.php' );
	$the_signature = new dk_speakout_Signature();
	$the_petition  = new dk_speakout_Petition();
	$options = get_option( 'dk_speakout_options' );
	$wpml          = new dk_speakout_WPML();

	// get the confirmation code from url
	$confirmation_code = $_REQUEST['dkspeakoutconfirm'];

	// try to confirm the signature
	$try_confirm = $the_signature->confirm( $confirmation_code );


		// retrieve the signature data
		$the_signature->retrieve_confirmed( $confirmation_code );

		// retrieve the petition data
		$the_petition->retrieve( $the_signature->petitions_id );
		$wpml->translate_petition( $the_petition );

    //set our return URL depending on whether option has been filled
    $returnURL = $the_petition->return_url > "" ? $the_petition->return_url : site_url();

	// if our attempt to confirm the signature was successful
	if ( $try_confirm ) {

		// send the petition email.  Querystring value is whether or not to BCC, called from confirmations.php
		if ( $the_petition->sends_email ) {
			dk_speakout_Mail::send_petition( $the_petition, $the_signature, $_GET["b"] );
		}
    
        //add to ActiveCampaign if enabled and optin checked
        if( $the_petition->activecampaign_enable && $the_signature->optin){
            
           $map1fieldValue = $the_petition->activecampaign_map1field == "" ? "" : $the_signature->honorific;
           $map5fieldValue = $the_petition->activecampaign_map5field == "" ? "" : $the_signature->street_address;
           $map6fieldValue = $the_petition->activecampaign_map6field == "" ? "" : $the_signature->city;
           $map7fieldValue = $the_petition->activecampaign_map7field == "" ? "" : $the_signature->state;
           $map8fieldValue = $the_petition->activecampaign_map8field == "" ? "" : $the_signature->postcode;
           $map9fieldValue = $the_petition->activecampaign_map9field == "" ? "" : $the_signature->country;
           $map10fieldValue = $the_petition->activecampaign_map10field == "" ? "" : $the_signature->custom_field;
           $map11fieldValue = $the_petition->activecampaign_map11field == "" ? "" : $the_signature->custom_field2;
           $map12fieldValue = $the_petition->activecampaign_map12field == "" ? "" : $the_signature->custom_field3;
           $map13fieldValue = $the_petition->activecampaign_map13field == "" ? "" : $the_signature->custom_field4;
           $map14fieldValue = $the_petition->activecampaign_map14field == "" ? "" : $the_signature->custom_field5;

            dk_speakout_Mail::add2ActiveCampaign( 
                $the_petition->activecampaign_api_key, $the_petition->activecampaign_server, $the_petition->activecampaign_list_id,
                $the_petition->activecampaign_map1field, $map1fieldValue, 
                $the_petition->activecampaign_map2field, $the_signature->first_name, 
                $the_petition->activecampaign_map3field, $the_signature->last_name, 
                $the_petition->activecampaign_map4field, $the_signature->email, 
                $the_petition->activecampaign_map5field, $map5fieldValue, 
                $the_petition->activecampaign_map6field, $map6fieldValue, 
                $the_petition->activecampaign_map7field, $map7fieldValue, 
                $the_petition->activecampaign_map8field, $map8fieldValue, 
                $the_petition->activecampaign_map9field, $map9fieldValue, 
                $the_petition->activecampaign_map10field, $map10fieldValue, 
                $the_petition->activecampaign_map11field, $map11fieldValue, 
                $the_petition->activecampaign_map12field, $map12fieldValue, 
                $the_petition->activecampaign_map13field, $map13fieldValue, 
                $the_petition->activecampaign_map14field, $map14fieldValue
            );
        }
        
        //add to MailChimp if enabled and optin checked
        if( $the_petition->mailchimp_enable && $the_signature->optin){
            dk_speakout_Mail::add2MailChimp( $the_signature->email, $the_signature->first_name, $the_signature->last_name, $the_petition->mailchimp_list_id, $the_petition->mailchimp_api_key, $the_petition->mailchimp_server);
        }
        
        //add to Mailerlite if enabled and optin checked
        if( $the_petition->mailerlite_enable && $the_signature->optin){
            dk_speakout_Mail::add2Mailerlite( $the_signature->email, $the_signature->first_name, $the_signature->last_name, $the_petition->mailerlite_group_id, $the_petition->mailerlite_api_key);
        }

        //add to sendy if enabled and optin checked
        if( $the_petition->sendy_enable && $the_signature->optin){
            dk_speakout_Mail::add2Sendy( $the_petition->sendy_server, $the_signature->email, $the_signature->first_name, $the_signature->last_name, $the_petition->sendy_list_id, $the_petition->sendy_api_key);
        }
        
		// set up the status message
		$message = __( 'Thank you. Your signature has been added to the petition.', 'speakout' );
		
        if ( $options['webhooks'] == 'on' ) {
            $id = $the_petion->id;
    		$title = $the_petition->title;
    		$email = $the_signature->email;
    		$firstName = $the_signature->first_name;
    		$lastName = $the_signature->last_name;
    		$activecampaignList = $the_petition->activecampaign_list_id;
    		$mailchimpList = $the_petition->mailchimp_list_id;
    		$mailerliteGroup = $the_petition->mailerlite_group_id;
    		$sendyList = $the_petition->sendy_list_id;
            do_action( 'speakout_signature_confirmed', $id, $title, $email, $firstName, $lastName, $activecampaignList, $mailchimpList, $mailerliteGroup, $sendyList);
        }
	}
	else {

		// has the signature already been confirmed?
		if ( $the_signature->check_confirmation( $confirmation_code ) ) {
			$message = __( 'Your signature has already been confirmed.', 'speakout' );
		}
		else {
			// the confirmation code is fubar or an admin has already deleted the signature
			$message = __( 'The confirmation code you provided is invalid.', 'speakout' );
		}
	}

	// display the confirmation page
	$confirmation_page = '
		<!doctype html>
		<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=' . get_bloginfo( "charset" ) . '" />
			';
	//if we don't have a confirmation URL don't attempt to redirect
	$confirmation_page .= 	'<meta http-equiv="refresh" content="2;' . $returnURL . '">';
//	}
	
	$confirmation_page .= 	'<title>' . get_bloginfo( "name" ) . '</title>
			<style type="text/css">
				body {
					background: #666;
					font-family: arial, sans-serif;
					font-size: 14px;
				}
				#confirmation {
					background: #fff url(' . plugins_url( "speakout/images/mail-stripes.png" ) . ') repeat top left;
					border: 1px solid #fff;
					width: 515px;
					margin: 200px auto 0 auto;
					box-shadow: 0px 3px 5px #333;
				}
				#confirmation-content {
					background: #fff url(' . plugins_url( "speakout/images/postmark.png" ) . ') no-repeat top right;
					margin: 10px;
					padding: 40px 0 20px 100px;
				}
			</style>
		</head>
		<body>
			<div id="confirmation">
				<div id="confirmation-content">
					<h2>' . __( "Email Confirmation", "speakout" ) . '</h2>
					<p>' . $message . '</p>';
			$confirmation_page .= 	'<p>' . __("If you aren't redirected", "speakout" ) . ' <a href="' . $returnURL . '">' . __( "Click here", "speakout") . '</a> ' . __("to return to site ", "speakout" )  . '</p>';

				$confirmation_page .= 	'</div>
			</div>
		</body>
		</html>
	';

	echo $confirmation_page;

	// stop page rendering here
	// without this, the home page will display below the confirmation message
	die();
}

?>