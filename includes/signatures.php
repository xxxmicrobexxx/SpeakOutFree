<?php

function dk_speakout_signatures_page() {
	// check security: ensure user has authority
	if ( ! current_user_can( 'publish_posts' ) ) wp_die( __( 'Insufficient privileges: You need to be an editor to do that.', 'speakout' ) );

	include_once( 'class.speakout.php' );
	include_once( 'class.signature.php' );
	include_once( 'class.petition.php' );
    
	$the_signatures = new dk_speakout_Signature();
	$the_petitions  = new dk_speakout_Petition();
    
    $options        = get_option( 'dk_speakout_options' );

	$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ): ''; 
	$pid    = isset( $_REQUEST['pid'] ) ? sanitize_text_field( $_REQUEST['pid'] ): ''; // petition id
	$sid    = isset( $_REQUEST['sid'] ) ? sanitize_text_field( $_REQUEST['sid'] ): ''; // signature id
    
	$the_petitions->retrieve( $pid );
    $hideUnconfirmed = $the_petitions->requires_confirmation == 1 ? true : false ;
    
	// set variables for paged record display and for limit values in db query
	$paged        = isset( $_REQUEST['paged'] ) ? sanitize_text_field( $_REQUEST['paged'] ): '1';
	$total_pages  = isset( $_REQUEST['total_pages'] ) ? sanitize_text_field( $_REQUEST['total_pages'] ): '1';
	$current_page = dk_speakout_SpeakOut::current_paged( $paged, $total_pages );
	$query_limit  = $options['signatures_rows'] > "" ? $options['signatures_rows'] : 50 ;
	$query_start  = ( $current_page * $query_limit ) - $query_limit;
	
	$petitioncount = $the_petitions->count();
	$petitionword = $petitioncount == 1 ? "petition" : "petitions";

    $searchString = isset( $_REQUEST["sigsearch"] )? sanitize_text_field( $_REQUEST["sigsearch"] ): "";
    
	switch ( $action ) {
		case 'petition' :
		    // count number of signatures in database
			$count = $the_signatures->count( $pid, "", "" );
            
            $table_label = __( 'Total', 'speakout' ) . ' = <span class="count">' . $count . '</span> in <span class="count">' . $petitioncount . '</span> ' . $petitionword;
			// get all signatures for display
			$signatures = $the_signatures->all( $pid, $query_start, $query_limit );

			// set up display strings
			$base_url      = site_url( 'wp-admin/admin.php?page=dk_speakout_signatures&action=petition&pid=' . $pid );
			$message_update = '';
		break;
		
		case 'displayunconfirmed' :
		    check_admin_referer( 'dk_speakout-resend_confirmations' . $pid );
			
			// count number of unconfirmed signatures in database
			$count = $the_signatures->countunconfirmed( $pid, "unconfirmed" );
			
			// get unconfirmed signatures
			$signatures = $the_signatures->retrieve_unconfirmed( $pid );

		break;
		
		case 'reconfirm' :
			check_admin_referer( 'dk_speakout-resend_confirmations' . $pid );

			include_once( 'class.mail.php' );
			$petition_to_confirm = new dk_speakout_Petition();
            $petition_to_confirm->retrieve( $pid );

			// get unconfirmed signatures
			$unconfirmed = $the_signatures->unconfirmed( $pid , sanitize_text_field( $_GET["siglist"] ) );

			foreach( $unconfirmed as $signature ) {
				$unconfirmed_signature = new dk_speakout_signature();
				$unconfirmed_signature->honorific = $signature->honorific;
				$unconfirmed_signature->first_name = $signature->first_name;
				$unconfirmed_signature->last_name = $signature->last_name;
				$unconfirmed_signature->email = $signature->email;
				$unconfirmed_signature->confirmation_code = $signature->confirmation_code;
				
				dk_speakout_Mail::send_confirmation( $petition_to_confirm, $unconfirmed_signature, $options );

				// destroy temporary object so we can re-use the variable
				unset( $unconfirmed_signature );
			}

			// count number of signatures in database
			$count = $the_signatures->count( $pid, "", ""  );
            $table_label = __( 'Total', 'speakout' ) . ' = <span class="count">' . $count . '</span> in <span class="count">' . $petitioncount . '</span> ' . $petitionword;

			// get all signatures for display
			$signatures = $the_signatures->all( $pid, $query_start, $query_limit );

			// set up display strings
			$base_url       = site_url( 'wp-admin/admin.php?page=dk_speakout_signatures&action=petition&pid=' . $pid );
			$message_update = __( 'Confirmation emails sent.', 'speakout' );
		break;
		
		case 'reconfirmselected' :
			check_admin_referer( 'dk_speakout-resend_confirmations' . $pid );

			include_once( 'class.mail.php' );
			$petition_to_confirm = new dk_speakout_Petition();
            $petition_to_confirm->retrieve( $pid );

			// get unconfirmed signatures
			$unconfirmed = $the_signatures->unconfirmed( $pid , sanitize_text_field( $_GET["siglist"] ) );

			foreach( $unconfirmed as $signature ) {
				$unconfirmed_signature = new dk_speakout_signature();
				$unconfirmed_signature->honorific = $signature->honorific;
				$unconfirmed_signature->first_name = $signature->first_name;
				$unconfirmed_signature->last_name = $signature->last_name;
				$unconfirmed_signature->email = $signature->email;
				$unconfirmed_signature->confirmation_code = $signature->confirmation_code;
				
				dk_speakout_Mail::send_confirmation( $petition_to_confirm, $unconfirmed_signature, $options );

				// destroy temporary object so we can re-use the variable
				unset( $unconfirmed_signature );
			}

			// count number of signatures in database
			$count = $the_signatures->count( $pid, "", ""  );

			// get all signatures for display
			$signatures = $the_signatures->all( $pid, $query_start, $query_limit );
            $table_label = __( 'Total', 'speakout' ) . ' = <span class="count">' . $count . '</span> in <span class="count">' . $petitioncount . '</span> ' . $petitionword;

			// set up display strings
			$base_url       = site_url( 'wp-admin/admin.php?page=dk_speakout_signatures&action=petition&pid=' . $pid );
			$message_update = __( 'Confirmation emails sent.', 'speakout' );
		break;
		
		case 'forceconfirm' :  // this sends the petition
			check_admin_referer( 'dk_speakout-resend_confirmations' . $pid );
            
            //get the petition
            $petition_to_confirm  = new dk_speakout_petition();				
            $petition_to_confirm->retrieve( $pid );
            
            
            // if option to send emails is disabled, don't send when forcing confirm
            if ($petition_to_confirm->sends_email == 1){
                include_once( 'class.mail.php' );
                // get unconfirmed signatures
                $unconfirmed = $the_signatures->unconfirmed( $pid , sanitize_text_field( $_GET["siglist"] ) );

                foreach( $unconfirmed as $signature ) {
                    $unconfirmed_signature 					= new dk_speakout_signature();
                    $unconfirmed_signature->honorific 		= $signature->honorific;
                    $unconfirmed_signature->first_name 		= $signature->first_name;
                    $unconfirmed_signature->last_name 		= $signature->last_name;
                    $unconfirmed_signature->email 			= $signature->email;
                    $unconfirmed_signature->custom_field 	= $signature->custom_field;
                    $unconfirmed_signature->custom_field2 	= $signature->custom_field2;
                    $unconfirmed_signature->custom_field3 	= $signature->custom_field3;
                    $unconfirmed_signature->custom_field4 	= $signature->custom_field4;
                    $unconfirmed_signature->custom_field5 	= $signature->custom_field5;
                    $unconfirmed_signature->custom_field6 	= $signature->custom_field6;
                    $unconfirmed_signature->custom_field7 	= $signature->custom_field7;
                    $unconfirmed_signature->confirmation_code = $signature->confirmation_code;
                    $unconfirmed_signature->petitions_id 	= $signature->petitions_id;
                    dk_speakout_Mail::send_petition( $petition_to_confirm, $unconfirmed_signature, "" );

                    // destroy temporary object so we can re-use the variable
                    unset( $unconfirmed_signature );
                }
            }
            
			// change confirmed flag AFTER getting sending unconfirmed petitions
			$signatures = $the_signatures->confirmSelected( sanitize_text_field( $_GET["siglist"] ) );
			
			$base_url = site_url( 'wp-admin/admin.php?page=dk_speakout_signatures' );
			$base_url      = site_url( 'wp-admin/admin.php?page=dk_speakout_signatures&action=petition&pid=' . $pid );			
		break;
		case 'deleteselected' :

			// security: ensure user has intention
			check_admin_referer( 'dk_speakout-resend_confirmations' . $pid );

			$signatures = $the_signatures->deleteSelected( sanitize_text_field( $_GET["siglist"] ) );

			$base_url = site_url( 'wp-admin/admin.php?page=dk_speakout_signatures&action=petition&pid=' . $pid );


		break;
		case 'search' :
		    // count number of signatures in database
			$count = $the_signatures->count( $pid, "", ""  );
			// get all signatures for display
			$signatures = $the_signatures->search( $pid, sanitize_text_field( $_POST['searchString'] ), $query_start, $query_limit );
			// set up display strings
			$petitionword = $petitioncount == 1 ? __( 'petition', 'speakout' ): __( 'petitions', 'speakout' );
			$table_label = __( 'Total', 'speakout' ) . ' = <span class="count">' . $count . '</span> in <span class="count">' . $petitioncount . '</span> ' . $petitionword;
			$base_url = site_url( 'wp-admin/admin.php?page=dk_speakout_signatures' );
			$message_update = '';
		break;
		
		default :
			// count number of signatures in database
			$count = $the_signatures->count( $pid ,"", $hideUnconfirmed);

			// get all signatures for display
			$signatures = $the_signatures->all( $pid, $query_start, $query_limit );

			// set up display strings
			$petitionword = $petitioncount == 1 ? __( 'petition', 'speakout' ): __( 'petitions', 'speakout' );
			$table_label = __( 'Total', 'speakout' ) . ' = <span class="count">' . $count . '</span> in <span class="count">' . $petitioncount . '</span> ' . $petitionword;
			$base_url = site_url( 'wp-admin/admin.php?page=dk_speakout_signatures' );
			$message_update = '';
	}

			//$table_label = __( 'Total', 'speakout' ) . ' <span class="count"> = ' . $the_signatures->count( 0 ) . '</span>';
			if($petitioncount > 1) {
			    $table_label .= ' (in <span class="count">' . $petitioncount . '</span> ' . $petitionword .")";
			}
		
	// get list of petitions to populate select box navigation
	$petitions_list = $the_petitions->quicklist(); 

	// Set up URLs for 'Download as CSV' and 'Resend confirmations' and 'Search' buttons
	// Show CSV and Resend buttons only when we are viewing signatures from a single petition
	if ( count( $petitions_list ) == 1 || $pid != '' ) {
		// if $pid (petition id) wasn't sent through the URL, then create it from the query
		if ( $pid == '' ) {
			$pid = $petitions_list[0]->id;
		}
		$csv_url = site_url( 'wp-admin/admin.php?page=dk_speakout_signatures&action=petition&pid=' . $pid );
        $importcsv_url = site_url( 'wp-admin/admin.php?page=dk_speakout_signatures&action=import' );
		$reconfirm_url = site_url( 'wp-admin/admin.php?page=dk_speakout_signatures' );
		$forceconfirm_url = site_url( 'wp-admin/admin.php?page=dk_speakout_signatures&action=petition&pid=' . $pid );
		$search_url = admin_url( 'admin.php?page=dk_speakout_signatures&action=search&pid=' . $pid  );
	}
	else{
	    $csv_url = "";
        $importcsv_url = site_url( 'wp-admin/admin.php?page=dk_speakout_signatures&action=import' );
		$reconfirm_url = "";
		$forceconfirm_url = site_url( 'wp-admin/admin.php?page=dk_speakout_signatures&action=petition&pid=' . $pid );
		$search_url = site_url( 'wp-admin/admin.php?page=dk_speakout_signatures&action=search&searchstring=' . $searchString );
	}
	/**
	 * Constructs an array of user-allowed HTML tags for use with wp_kses()
	 */
	function allowed_html_tags()
	{
		$allowed_tags = array(
			'a'      => array( 'href' => array(),'title' => array() ),
			'em'     => array(),
			'strong' => array(),
			'span'   => array(
			    'class'  => array()
			 ),
			'p'      => array()
		);
	}
    
	// display the Signatures table
	include_once( dirname( __FILE__ ) . '/signatures.view.php' );
}

?>