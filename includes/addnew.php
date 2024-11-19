<?php

if( isset($_POST['ajax']) && isset($_POST['listID']) ){
// echo $_POST['listID'] . "";
 exit;
}

// page used for creating new petitions
	$options = get_option( 'dk_speakout_options' );
// and for editing existing petitions
function dk_speakout_addnew_page() {
	// check security: ensure user has authority
	if ( ! current_user_can( 'publish_posts' ) ) wp_die( 'Insufficient privileges: You need to be an editor to do that.' );

	include_once( 'class.petition.php' );
	include_once( 'class.wpml.php' );
	$petition     = new dk_speakout_Petition();
	$wpml         = new dk_speakout_WPML();
	$options = get_option( 'dk_speakout_options' );
	$action       = isset( $_REQUEST['action'] ) ? sanitize_text_field($_REQUEST['action']) : 'edit';
	$petition->id = isset( $_REQUEST['id'] ) ? absint( sanitize_text_field($_REQUEST['id']) ) : '1';
	$tab    = isset( $_REQUEST['tab'] ) ? sanitize_text_field($_REQUEST['tab']) : 'dk-petition-tab-01';
	
	switch( $action ) {

		// displays existing petition for alteration and submits with 'update' action
		case 'edit' :
			// security: ensure user has intention
			//check_admin_referer( 'dk_speakout-edit_petition' . $petition->id );

			$petition->retrieve( $petition->id );

			// set up page display variables
			$page_title     = __( 'Edit Petition', 'speakout' );
			$nonce          = 'dk_speakout-update_petition' . $petition->id;
			$action         = 'update';
			$x_date         = $petition->get_expiration_date_components();
			$button_text    = __( 'Update Petition', 'speakout' );
			$message_update = '';

			break;
		
		
		// alter an existing petition
		case 'update' :
			// security: ensure user has intention
			check_admin_referer( 'dk_speakout-update_petition' . $petition->id );

			$petition->populate_from_post();
			$petition->update( $petition->id );
			$wpml->register_petition( $petition );

			// set up page display variables
			$page_title     = __( 'Edit Petition', 'speakout' );
			$nonce          = 'dk_speakout-update_petition' . $petition->id;
			$action         = 'update';
			$x_date         = $petition->get_expiration_date_components();
			$button_text    = __( 'Update Petition', 'speakout' );
			$message_update = __( 'Petition '. $petition->id . ' updated.'  );

			break;

		
	}

	if ( $petition->return_url === '' || $petition->return_url === 0 ) {
		$petition->return_url = home_url();
		error_log($petition->return_url);
	}

	// display the form
	include_once( 'addnew.view.php' );
}

?>