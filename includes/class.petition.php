<?php

/**
 * Class for accessing and manipulating petition data in SpeakOut! Email Petitions plugin for WordPress CMS
 */
class dk_speakout_Petition
{
    public $title;
    public $target_email;
    public $target_email_CC;
    public $email_subject;
    public $greeting;
    public $petition_message;
    public $petition_footer;
    public $address_fields = array();
    public $street_required = 0;
    public $city_required = 0;
    public $state_required = 0;
    public $postcode_required = 0;
    public $country_required = 0;
    public $expires = 0;
    public $expiration_date = '0000-00-00 00:00:00';
    public $created_date;
    public $goal = 0;
    public $increase_goal = 0;
    public $goal_bump = 100;
    public $goal_trigger = 95;
    public $sends_email = 1;
    public $x_message;
    public $display_petition_message = 0;
    public $allow_anonymous = 0;
    public $requires_confirmation = 0;
    public $return_url;
    public $displays_optin = 0;
    public $optin_label;
    public $displays_custom_field = 0;
    public $custom_field_included = 0;
    public $custom_field_required = 0;
    public $custom_field_label;
    public $custom_field_location = 1;
    public $displays_custom_field2 = 0;
    public $custom_field2_included = 0;
    public $custom_field2_required = 0;
    public $custom_field2_label;
    public $custom_field2_location = 1;
    public $displays_custom_field3 = 0;
    public $custom_field3_included = 0;
    public $custom_field3_required = 0;
    public $custom_field3_label;
    public $custom_field3_location = 1;
    public $displays_custom_field4 = 0;
    public $custom_field4_included = 0;
    public $custom_field4_required = 0;
    public $custom_field4_label;
    public $custom_field4_location = 1;
    public $displays_custom_field5 = 0;
    public $custom_field5_included = 0;
    public $custom_field5_required = 0;
    public $custom_field5_label;
    public $custom_field5_values;
    public $custom_field5_location = 1;
    public $displays_custom_field6 = 0;
    public $custom_field6_included = 0;
    public $custom_field6_required = 0;
    public $custom_field6_label = "Custom checkbox 1";
    public $custom_field6_location = 1;
    public $displays_custom_field7 = 0;
    public $custom_field7_included = 0;
    public $custom_field7_required = 0;
    public $custom_field7_label = "Custom checkbox 2";
    public $custom_field7_location = 1;
    public $open_message_button = "Read the Petition";
    public $open_editable_message_button = "Read or Edit the Petition";
    public $displays_custom_message = 0;
    public $custom_message_label;
    public $is_editable = 0;
    public $redirect_url_option = 0;
    public $redirect_url = "";
    public $redirect_delay = 5000;
    public $url_target = 0;
    public $hide_email_field = 0;
    public $mailchimp_enable = 0;
    public $mailchimp_api_key = 0;
    public $mailchimp_server = "";
    public $mailchimp_list_id = "";
    public $mailerlite_enable = 0;
    public $mailerlite_api_key = 0;
    public $mailerlite_group_id = "";
    public $activecampaign_enable = 0;
    public $activecampaign_api_key = 0;
    public $activecampaign_server = "";
    public $activecampaign_list_id = "";
    public $activecampaign_map1field = "";
    public $activecampaign_map2field = "";
    public $activecampaign_map3field = "";
    public $activecampaign_map4field = "";
    public $activecampaign_map5field = "";
    public $activecampaign_map6field = "";
    public $activecampaign_map7field = "";
    public $activecampaign_map8field = "";
    public $activecampaign_map9field = "";
    public $activecampaign_map10field = "";
    public $activecampaign_map11field = "";
    public $activecampaign_map12field = "";
    public $activecampaign_map13field = "";
    public $activecampaign_map14field = "";
    public $sendy_enable = 0;
    public $sendy_api_key = "";
    public $sendy_server = "";
    public $sendy_list_id = "";
    public $id; 
    public $signatures;
    

	/**
	 * Retrieves a selection of petition records from the database
	 * 
	 * @param $start (int) the first record to be retrieved
	 * @param $limit (int) the total number of records to be retrieved
	 * @return (object) query results
	 */
	public function all( $start, $limit, $listOrder="DESC" )
	{
		global $wpdb, $db_petitions, $db_signatures;
        
		// query petitions and number of signatures for each
		$sql = "
			SELECT $db_petitions.id, $db_petitions.title, $db_petitions.goal,
				COUNT( $db_signatures.id ) AS 'signatures'
			FROM $db_petitions
			LEFT JOIN $db_signatures
				ON $db_petitions.id = $db_signatures.petitions_id
				AND ( $db_signatures.is_confirmed = '' OR $db_signatures.is_confirmed = '1' )
			GROUP BY $db_petitions.id
			ORDER BY `id` $listOrder
			LIMIT $start, $limit
		";
		$query_results = $wpdb->get_results( $sql );

		return $query_results;
	}

	/**
	 * Counts the number of petitions in the database
	 * 
	 * @return (int) the number of petitions in the database
	 */
	public function count()
	{
		global $wpdb, $db_petitions;

		$sql = "
			SELECT `id`
			FROM `$db_petitions`
		";
		$query_results = $wpdb->get_results( $sql );

		return count( $query_results );
	}

    /**
     *  hides the addnew menu if more than 1 petition
     *  and not upgraded.
     *  Disabline this won't allow more petitions,
     *  it is just housekeeping.
     */
     
    public function hidemenu(){
        remove_submenu_page( 'dk_speakout_top', 'dk_speakout_addnew');
    }
    
/**
 * Creates a new petition record in the database
 */
public function create()
{
    global $wpdb, $db_petitions;

		$data = array(
            
        'title'                         => $this->title,
        'target_email'                  => $this->target_email,
        'target_email_CC'               => $this->target_email_CC,
        'email_subject'                 => $this->email_subject,
        'greeting'                      => $this->greeting,
        'petition_message'              => $this->petition_message,
        'petition_footer'               => $this->petition_footer,
        'address_fields'                => serialize( $this->address_fields ),
        'street_required'               => $this->street_required,
        'city_required'                 => $this->city_required,
            
        'state_required'                => $this->state_required,
        'postcode_required'             => $this->postcode_required,
        'country_required'              => $this->country_required,
        'expires'                       => $this->expires,
        'expiration_date'               => $this->expiration_date,
        'goal'                          => $this->goal,
        'increase_goal'                 => $this->increase_goal,
        'goal_bump'                     => $this->goal_bump,
        'goal_trigger'                  => $this->goal_trigger,
        'sends_email'                   => $this->sends_email,
            
        'x_message'               => $this->x_message,
        'display_petition_message'      => $this->display_petition_message,
        'allow_anonymous'               => $this->allow_anonymous,
        'requires_confirmation'         => $this->requires_confirmation,
        'return_url'                    => $this->return_url,
        'displays_optin'                => $this->displays_optin,
        'optin_label'                   => $this->optin_label,
        'displays_custom_field'         => $this->displays_custom_field,
        'custom_field_included'         => $this->custom_field_included,
        'custom_field_required'         => $this->custom_field_required,
        
        'custom_field_label'            => $this->custom_field_label,
        'custom_field_location'         => $this->custom_field_location,
        'displays_custom_field2'        => $this->displays_custom_field2,
        'custom_field2_included'        => $this->custom_field2_included,
        'custom_field2_required'        => $this->custom_field2_required,
        'custom_field2_label'           => $this->custom_field2_label,
        'custom_field2_location'        => $this->custom_field2_location,
        'displays_custom_field3'        => $this->displays_custom_field3,
        'custom_field3_included'        => $this->custom_field3_included,
        'custom_field3_required'        => $this->custom_field3_required,
 //group 5        
        'custom_field3_label'           => $this->custom_field3_label,
        'custom_field3_location'        => $this->custom_field3_location,
        'displays_custom_field4'        => $this->displays_custom_field4,
        'custom_field4_included'        => $this->custom_field4_included,
        'custom_field4_required'        => $this->custom_field4_required,
        'custom_field4_label'           => $this->custom_field4_label,
        'custom_field4_location'        => $this->custom_field4_location,
        'displays_custom_field5'        => $this->displays_custom_field5,
        'custom_field5_included'        => $this->custom_field5_included,
        'custom_field5_required'        => $this->custom_field5_required,
       
        'custom_field5_label'           => $this->custom_field5_label,
        'custom_field5_values'          => $this->custom_field5_values,
        'custom_field5_location'        => $this->custom_field5_location,
        'open_message_button'           => $this->open_message_button,
        'open_editable_message_button'  => $this->open_editable_message_button,
        'displays_custom_message'       => $this->displays_custom_message,
        'custom_message_label'          => $this->custom_message_label,
        'is_editable'                   => $this->is_editable,
        'redirect_url_option'           => $this->redirect_url_option,
        'redirect_url'                  => $this->redirect_url,
//group 7         
        'redirect_delay'                => $this->redirect_delay,
        'url_target'                    => $this->url_target,
        'hide_email_field'              => $this->hide_email_field,
        'mailchimp_enable'              => $this->mailchimp_enable,
        'mailchimp_api_key'             => $this->mailchimp_api_key,
        'mailchimp_server'              => $this->mailchimp_server,
        'mailchimp_list_id'             => $this->mailchimp_list_id,
        'mailerlite_enable'             => $this->mailerlite_enable,
        'mailerlite_api_key'            => $this->mailerlite_api_key,
        'mailerlite_group_id'           => $this->mailerlite_group_id,
        
        'activecampaign_enable'         => $this->activecampaign_enable,
        'activecampaign_api_key'        => $this->activecampaign_api_key,
        'activecampaign_server'         => $this->activecampaign_server,
        'activecampaign_list_id'        => $this->activecampaign_list_id,
        'created_date'          	    => $this->created_date,
        'activecampaign_map1field'      => $this->activecampaign_map1field,
        'activecampaign_map2field'      => $this->activecampaign_map2field,
        'activecampaign_map3field'      => $this->activecampaign_map3field,
        'activecampaign_map4field'      => $this->activecampaign_map4field, 
//group 9      
        'activecampaign_map5field'      => $this->activecampaign_map5field, 
        'activecampaign_map6field'      => $this->activecampaign_map6field, 
        'activecampaign_map7field'      => $this->activecampaign_map7field, 
        'activecampaign_map8field'      => $this->activecampaign_map8field, 
        'activecampaign_map9field'      => $this->activecampaign_map9field, 
        'activecampaign_map10field'     => $this->activecampaign_map10field,
        'activecampaign_map11field'     => $this->activecampaign_map11field,
        'activecampaign_map12field'     => $this->activecampaign_map12field,
        'activecampaign_map13field'     => $this->activecampaign_map13field,
        'activecampaign_map14field'     => $this->activecampaign_map14field,
        
        'sendy_enable'                  => $this->sendy_enable,
        'sendy_api_key'                 => $this->sendy_api_key,
        'sendy_server'                  => $this->sendy_server,
        'sendy_list_id'                 => $this->sendy_list_id,
        'displays_custom_field6'        => $this->displays_custom_field6,
        'custom_field6_label'           => $this->custom_field6_label,
        'custom_field6_location'        => $this->custom_field6_location,
        'displays_custom_field7'        => $this->displays_custom_field7,
        'custom_field7_label'           => $this->custom_field7_label,
        'custom_field7_location'        => $this->custom_field7_location,
        
        
 			
		);

		//$wpdb->insert( $db_petitions, $data, $format );
		$wpdb->insert( $db_petitions, $data);

		// grab the id of the record we just added to the database
		$this->id = $wpdb->insert_id;
	}
    
/**
 * Updates an existing petition record in the database
 * 
 * @param (int) $id value of the petition's 'id' field in the database
 */
public function update( $id )
{
    global $wpdb, $db_petitions;
    
    $data = array(
        'title'                         => $this->title,
        'target_email'                  => $this->target_email,
        'target_email_CC'               => $this->target_email_CC,
        'email_subject'                 => $this->email_subject,
        'greeting'                      => $this->greeting,
        'petition_message'              => $this->petition_message,
        'petition_footer'               => $this->petition_footer,
        'address_fields'                => serialize( $this->address_fields ),
        'street_required'               => $this->street_required,
        'city_required'                 => $this->city_required,
        'state_required'                => $this->state_required,
        'postcode_required'             => $this->postcode_required,
        'country_required'              => $this->country_required,
        'expires'                       => $this->expires,
        'expiration_date'               => $this->expiration_date,
        'goal'                          => $this->goal,
        'increase_goal'                 => $this->increase_goal,
        'goal_bump'                     => $this->goal_bump,
        'goal_trigger'                  => $this->goal_trigger,
        'sends_email'                   => $this->sends_email,
        'x_message'               => $this->x_message,
        'display_petition_message'      => $this->display_petition_message,
        'allow_anonymous'               => $this->allow_anonymous,
        'requires_confirmation'         => $this->requires_confirmation,
        'return_url'                    => $this->return_url,
        'displays_optin'                => $this->displays_optin,
        'optin_label'                   => $this->optin_label,
        'displays_custom_field'         => $this->displays_custom_field,
        'custom_field_included'         => $this->custom_field_included,
        'custom_field_required'         => $this->custom_field_required,
        'custom_field_label'            => $this->custom_field_label,
        'custom_field_location'         => $this->custom_field_location,
        'displays_custom_field2'        => $this->displays_custom_field2,
        'custom_field2_included'         => $this->custom_field2_included,
        'custom_field2_required'        => $this->custom_field2_required,
        'custom_field2_label'           => $this->custom_field2_label,
        'custom_field2_location'        => $this->custom_field2_location,
        'displays_custom_field3'        => $this->displays_custom_field3,
        'custom_field3_included'         => $this->custom_field3_included,
        'custom_field3_required'        => $this->custom_field3_required,
        'custom_field3_label'           => $this->custom_field3_label,
        'custom_field3_location'        => $this->custom_field3_location,
        'displays_custom_field4'        => $this->displays_custom_field4,
        'custom_field4_included'         => $this->custom_field4_included,
        'custom_field4_required'        => $this->custom_field4_required,
        'custom_field4_label'           => $this->custom_field4_label,
        'custom_field4_location'        => $this->custom_field4_location,
        'displays_custom_field5'        => $this->displays_custom_field5,
        'custom_field5_included'         => $this->custom_field5_included,
        'custom_field5_required'        => $this->custom_field5_required,
        'custom_field5_label'           => $this->custom_field5_label,
        'custom_field5_values'          => $this->custom_field5_values,
        'custom_field5_location'        => $this->custom_field5_location,
        'displays_custom_field6'        => $this->displays_custom_field6,
        'custom_field6_label'           => $this->custom_field6_label,
        'custom_field6_location'        => $this->custom_field6_location,
        'displays_custom_field7'        => $this->displays_custom_field7,
        'custom_field7_label'           => $this->custom_field7_label,
        'custom_field7_location'        => $this->custom_field7_location,
        'open_message_button'           => $this->open_message_button,
        'open_editable_message_button'  => $this->open_editable_message_button,
        'displays_custom_message'       => $this->displays_custom_message,
        'custom_message_label'          => $this->custom_message_label,
        'is_editable'                   => $this->is_editable,
        'redirect_url_option'           => $this->redirect_url_option,
        'redirect_url'                  => $this->redirect_url,
        'redirect_delay'                => $this->redirect_delay,
        'url_target'                    => $this->url_target,
        'hide_email_field'              => $this->hide_email_field,
        'mailchimp_enable'              => $this->mailchimp_enable,
        'mailchimp_api_key'             => $this->mailchimp_api_key,
        'mailchimp_server'              => $this->mailchimp_server,
        'mailchimp_list_id'             => $this->mailchimp_list_id,
        'mailerlite_enable'             => $this->mailerlite_enable,
        'mailerlite_api_key'            => $this->mailerlite_api_key,
        'mailerlite_group_id'           => $this->mailerlite_group_id,
        'activecampaign_enable'         => $this->activecampaign_enable,
        'activecampaign_api_key'        => $this->activecampaign_api_key,
        'activecampaign_server'         => $this->activecampaign_server,
        'activecampaign_list_id'        => $this->activecampaign_list_id,
        'activecampaign_map1field'      => $this->activecampaign_map1field,
        'activecampaign_map2field'      => $this->activecampaign_map2field,
        'activecampaign_map3field'      => $this->activecampaign_map3field,
        'activecampaign_map6field'      => $this->activecampaign_map4field, 
        'activecampaign_map5field'      => $this->activecampaign_map5field, 
        'activecampaign_map6field'      => $this->activecampaign_map6field, 
        'activecampaign_map7field'      => $this->activecampaign_map7field, 
        'activecampaign_map8field'      => $this->activecampaign_map8field, 
        'activecampaign_map9field'      => $this->activecampaign_map9field, 
        'activecampaign_map10field'     => $this->activecampaign_map10field,
        'activecampaign_map11field'     => $this->activecampaign_map11field,
        'activecampaign_map12field'     => $this->activecampaign_map12field,
        'activecampaign_map13field'     => $this->activecampaign_map13field,
        'activecampaign_map14field'     => $this->activecampaign_map14field,
        'sendy_enable'                  => $this->sendy_enable,
        'sendy_api_key'                 => $this->sendy_api_key,
        'sendy_server'                  => $this->sendy_server,
        'sendy_list_id'                 => $this->sendy_list_id
        
    );
   
   $where = array( 'id' => $id );
   
    $wpdb->update( $db_petitions, $data, $where );
    //show the query - this is for debugging
    //$wpdb->show_errors = true;
   // exit( $wpdb->queries . '<hr>' . var_dump($wpdb->last_query) . '<hr>' . var_dump($wpdb->error));
}


/**
 * Deletes a petition and its signatures from the database
 *
 * @param $id (int) value of the petition's 'id' field in the database
 */
public function delete( $id )
{
    global $wpdb, $db_petitions, $db_signatures;

    // delete petition from the db
    $sql_petitions = "
        DELETE FROM `$db_petitions`
        WHERE `id` = '%d'
    ";
    $wpdb->query( $wpdb->prepare( $sql_petitions, $id ) );

    // delete petition's signatures from the db
    $sql_signatures = "
        DELETE FROM `$db_signatures`
        WHERE `petitions_id` = '%d'
    ";
    $wpdb->query( $wpdb->prepare( $sql_signatures, $id ) );
}

/**
 * Breaks expiration date into year, month, day, hour, and minute components
 *
 * @return (array) with keys: year, month, day, hour, and minute
 */
public function get_expiration_date_components()
{
    if ( $this->expires == 1 ) {
        $x_date = array(
            'year'    => date( 'Y', strtotime( $this->expiration_date ) ),
            'month'   => date( 'm', strtotime( $this->expiration_date ) ),
            'day'     => date( 'd', strtotime( $this->expiration_date ) ),
            'hour'    => date( 'H', strtotime( $this->expiration_date ) ),
            'minutes' => date( 'i', strtotime( $this->expiration_date ) )
        );
    }
    else {
        // default expiration date should be one week from today at 4:20
        $next_week = strtotime( current_time( 'mysql', 0 ) ) + ( 60 * 60 * 24 * 7 );
        $x_date = array(
            'month'   => date( 'm', $next_week ),
            'day'     => date( 'd', $next_week ),
            'year'    => date( 'Y', $next_week ),
            'hour'    => '16',
            'minutes' => '20'
        );
    }

    return $x_date;
}

/**
 * Populates the properties of this object with posted form values
 */
public function populate_from_post()
{
// Meta info
    if ( isset( $_POST['id'] ) ) {
        $this->id = sanitize_text_field( $_POST['id'] );
    }
    $this->created_date = current_time( 'mysql', 0 );

// Title Box
    if ( isset( $_POST['title'] ) && $_POST['title'] != '' ) {
        $this->title = sanitize_text_field( $_POST['title'] );
    }
    else {
        $this->title = __( 'No Title', 'speakout' );
    }

// Petition Box
    if ( isset( $_POST['sends_email'] )   ) {
        $this->sends_email = 0;
    }
    if ( isset( $_POST['target_email'] ) ) {       
        //do we have multiple email addresses?
        if( strpos( $_POST['target_email'], "," ) ){
            $strAddresses = "";
            $arrAddresses = explode( "," ,$_POST['target_email'] );
            foreach( $arrAddresses as $address){
                $strAddresses .= sanitize_email( $address ) . ", " ;
            }
            $this->target_email = rtrim( $strAddresses, ', ' );
        }
        else{
            $this->target_email = sanitize_email( $_POST['target_email'] );
        }        
    }
    if ( isset( $_POST['target_email_CC'] ) ) {
        //do we have multiple addresses?
        if( strpos( $_POST['target_email_CC'], "," ) ){
            $strAddresses = "";
            $arrAddresses = explode( "," ,$_POST['target_email_CC'] );
            foreach( $arrAddresses as $address){
                $strAddresses .= sanitize_email( $address ) . ", " ;
            }
            $this->target_email_CC = rtrim( $strAddresses, ', ' );
        }
        else{
            $this->target_email_CC = sanitize_email( $_POST['target_email_CC'] );
        } 
    }
    if ( isset( $_POST['email_subject'] ) ) {
        $this->email_subject = sanitize_text_field( $_POST['email_subject'] );
    }
    if ( isset( $_POST['greeting'] ) ) {
        $this->greeting = sanitize_text_field( $_POST['greeting'] );
    }
    if ( isset( $_POST['petition_message'] ) ) {
        $this->petition_message = sanitize_textarea_field( $_POST['petition_message'], 'post' );
    }
    if ( isset( $_POST['petition_footer'] ) ) {
        $this->petition_footer = sanitize_text_field( $_POST['petition_footer'] );
    }
    if ( isset( $_POST['bcc'] ) ) {
        $this->bcc = sanitize_email( $_POST['bcc'] );
    }
// x Message Box
    if ( isset( $_POST['x_message'] ) ) {
        $this->x_message = sanitize_text_field( $_POST['x_message'] );
    }
// display petition message or not
    if ( isset( $_POST['display_petition_message'] ) ) {
        $this->display_petition_message = 1;
    }
// Allow anonymouse signers
    if ( isset( $_POST['allow_anonymous'] ) ) {
        $this->allow_anonymous = 1;
    }	    
// Petition Options Box
    if ( isset( $_POST['requires_confirmation'] ) ) {
        $this->requires_confirmation = 1;
    }
    if ( isset( $_POST['hide_email_field'] ) ) {
        $this->hide_email_field = 1;
    }
    if ( isset( $_POST['return_url'] ) ) {
        $this->return_url = sanitize_text_field( $_POST['return_url'] );
    }
    if ( isset( $_POST['is_editable'] ) ) {
        $this->is_editable = 1;
    }

    if ( isset( $_POST['has_goal'] ) ) {
        if ( isset( $_POST['goal'] ) && $_POST['goal'] > 0) {
            $this->goal = sanitize_text_field( $_POST['goal'] );
        }
        else {
            $this->goal = 500;
        }
        if ( isset( $_POST['increase_goal'] ) ) {
            $this->increase_goal = 1;
        }
        else {
            $this->increase_goal = 0;
        }
        if ( isset( $_POST['goal_bump'] ) ) {
            $this->goal_bump = sanitize_text_field( $_POST['goal_bump'] );
        }
        else {
            $this->goal_bump = 100;
        }
        if ( isset( $_POST['goal_trigger'] ) ) {
            $this->goal_trigger = sanitize_text_field( $_POST['goal_trigger'] );
        }
        else {
            $this->goal_trigger = 90;
        }
    }
    if ( isset( $_POST['expires'] ) ) {
        $this->expires = 1;
        $this->_set_expiration_date();
    }
    if ( isset( $_POST['redirect_url_option'] ) ) {
        $this->redirect_url_option = 1;
    }
    if ( isset( $_POST['redirect_url'] ) ) {
        $this->redirect_url = sanitize_url( $_POST['redirect_url'] );
    }
    if ( isset( $_POST['redirect_delay'] ) ) {
        $this->redirect_delay = absint( $_POST['redirect_delay'] );
    }
    if ( isset( $_POST['url_target'] ) ) {
        $this->url_target = sanitize_text_field( $_POST['url_target'] );
    }

    // Display Options Box
    $address_fields = array();
    if ( isset( $_POST['street'] ) ) {
        array_push( $address_fields, 'street' );
    }
    if ( isset( $_POST['city'] ) ) {
        array_push( $address_fields, 'city' );
    }
    if ( isset( $_POST['state'] ) ) {
        array_push( $address_fields, 'state' );
    }
    if ( isset( $_POST['postcode'] ) ) {
        array_push( $address_fields, 'postcode' );
    }
    if ( isset( $_POST['country'] ) ) {
        array_push( $address_fields, 'country' );
    }
    $this->address_fields = $address_fields;

    if ( isset( $_POST['street-required'] ) ) {
        $this->street_required = 1;
    }
    if ( isset( $_POST['city-required'] ) ) {
        $this->city_required = 1;
    }
    if ( isset( $_POST['state-required'] ) ) {
        $this->state_required = 1;
    }
    if ( isset( $_POST['postcode-required'] ) ) {
        $this->postcode_required = 1;
    }
    if ( isset( $_POST['country-required'] ) ) {
        $this->country_required = 1;
    }
    if ( isset( $_POST['displays-custom-field'] ) ) {
        $this->displays_custom_field = 1;
    }
    if ( isset( $_POST['custom-field-included'] ) ) {
        $this->custom_field_included = 1;
    }
    if ( isset( $_POST['custom-field-required'] ) ) {
        $this->custom_field_required = 1;
    }
    if ( isset( $_POST['custom-field-label'] ) ) {
        $this->custom_field_label = sanitize_text_field( $_POST['custom-field-label'] );
    }
    if ( isset( $_POST['custom-field-location'] ) ) {
        $this->custom_field_location = sanitize_text_field( $_POST['custom-field-location'] );
    }
    if ( isset( $_POST['displays-custom-field2'] ) ) {
        $this->displays_custom_field2 = 1;
    }
    if ( isset( $_POST['custom-field2-included'] ) ) {
        $this->custom_field2_included = 1;
    }
    if ( isset( $_POST['custom-field2-required'] ) ) {
        $this->custom_field2_required = 1;
    }
    if ( isset( $_POST['custom-field2-label'] ) ) {
        $this->custom_field2_label = sanitize_text_field( $_POST['custom-field2-label'] );
    }
    if ( isset( $_POST['custom-field2-location'] ) ) {
        $this->custom_field2_location = sanitize_text_field( $_POST['custom-field2-location'] );
    }
    if ( isset( $_POST['displays-custom-field3'] ) ) {
        $this->displays_custom_field3 = 1;
    }
    if ( isset( $_POST['custom-field3-included'] ) ) {
        $this->custom_field3_included = 1;
    }
    if ( isset( $_POST['custom-field3-required'] ) ) {
        $this->custom_field3_required = 1;
    }
    if ( isset( $_POST['custom-field3-label'] ) ) {
        $this->custom_field3_label = sanitize_text_field( $_POST['custom-field3-label'] );
    }
    if ( isset( $_POST['custom-field3-location'] ) ) {
        $this->custom_field3_location = sanitize_text_field( $_POST['custom-field3-location'] );
    }
    if ( isset( $_POST['displays-custom-field4'] ) ) {
        $this->displays_custom_field4 = 1;
    }
    if ( isset( $_POST['custom-field4-included'] ) ) {
        $this->custom_field4_included = 1;
    }
    if ( isset( $_POST['custom-field4-required'] ) ) {
        $this->custom_field4_required = 1;
    }
    if ( isset( $_POST['custom-field4-label'] ) ) {
        $this->custom_field4_label = sanitize_text_field( $_POST['custom-field4-label'] );
    }
    if ( isset( $_POST['custom-field4-location'] ) ) {
        $this->custom_field4_location = sanitize_text_field( $_POST['custom-field4-location'] );
    }
    if ( isset( $_POST['displays-custom-field5'] ) ) {
        $this->displays_custom_field5 = 1;
    }
    if ( isset( $_POST['custom-field5-included'] ) ) {
        $this->custom_field5_included = 1;
    }
    if ( isset( $_POST['custom-field5-required'] ) ) {
        $this->custom_field5_required = 1;
    }
    if ( isset( $_POST['custom-field5-label'] ) ) {
        $this->custom_field5_label = sanitize_text_field( $_POST['custom-field5-label'] );
    }
    if ( isset( $_POST['custom-field5-values'] ) ) {
        $this->custom_field5_values = sanitize_text_field( $_POST['custom-field5-values'] );
    }
    if ( isset( $_POST['custom-field5-location'] ) ) {
        $this->custom_field5_location = sanitize_text_field( $_POST['custom-field5-location'] );
    }
    if ( isset( $_POST['displays-custom-field6'] ) ) {
        $this->displays_custom_field6 = 1;
    }
    if ( isset( $_POST['custom-field6-label'] ) ) {
        $this->custom_field6_label = sanitize_text_field( $_POST['custom-field6-label'] );
    }
    if ( isset( $_POST['custom-field6-location'] ) ) {
        $this->custom_field6_location = sanitize_text_field( $_POST['custom-field6-location'] );
    }
    if ( isset( $_POST['displays-custom-field7'] ) ) {
        $this->displays_custom_field7 = 1;
    }
    if ( isset( $_POST['custom-field7-label'] ) ) {
        $this->custom_field7_label = sanitize_text_field( $_POST['custom-field7-label'] );
    }
    if ( isset( $_POST['custom-field7-location'] ) ) {
        $this->custom_field7_location = sanitize_text_field( $_POST['custom-field7-location'] );
    }
    if ( isset( $_POST['open-message-button'] ) ) {
        $this->open_message_button = sanitize_text_field( $_POST['open-message-button'] );
    }
    if ( isset( $_POST['open-editable-message-button'] ) ) {
        $this->open_editable_message_button = sanitize_text_field( $_POST['open-editable-message-button'] );
    }
    if ( isset( $_POST['displays-custom-message'] ) ) {
        $this->displays_custom_message = 1;
    }
    if ( isset( $_POST['custom-message-label'] ) ) {
        $this->custom_message_label = sanitize_text_field( $_POST['custom-message-label'] );
    }
    if ( isset( $_POST['displays-optin'] ) ) {
        $this->displays_optin = 1;
    }
    if ( isset( $_POST['optin-label'] ) ) {
        $this->optin_label = sanitize_text_field( $_POST['optin-label'] );
    }
    if ( isset( $_POST['bcc'] ) ) {
        $this->bcc = sanitize_text_field( $_POST['bcc'] );
    }
    if ( isset( $_POST['mailchimp-enable'] ) ) {
        $this->mailchimp_enable = 1;
    }
    if ( isset( $_POST['mailchimp-api-key'] ) ) {
        $this->mailchimp_api_key = sanitize_text_field( $_POST['mailchimp-api-key'] );
    }
    if ( isset( $_POST['mailchimp-server'] ) ) {
        $this->mailchimp_server = sanitize_text_field( $_POST['mailchimp-server'] );
    }
    if ( isset( $_POST['mailchimp-list-id'] ) ) {
        $this->mailchimp_list_id = sanitize_text_field( $_POST['mailchimp-list-id'] );
    }
     if ( isset( $_POST['mailerlite-enable'] ) ) {
        $this->mailerlite_enable = 1;
    }
    if ( isset( $_POST['mailerlite-api-key'] ) ) {
        $this->mailerlite_api_key = sanitize_text_field( $_POST['mailerlite-api-key'] );
    }
    if ( isset( $_POST['mailerlite-group-id'] ) ) {
        $this->mailerlite_group_id = sanitize_text_field( $_POST['mailerlite-group-id'] );
    }   
    if ( isset( $_POST['activecampaign-enable'] ) ) {
        $this->activecampaign_enable = 1;
    }
    if ( isset( $_POST['activecampaign-api-key'] ) ) {
        $this->activecampaign_api_key = sanitize_text_field( $_POST['activecampaign-api-key'] );
    }

    if ( isset( $_POST['activecampaign-server'] ) ) {
        $this->activecampaign_server = sanitize_text_field( $_POST['activecampaign-server'] );
    }
    if ( isset( $_POST['activecampaign-list-id'] ) ) {
        $this->activecampaign_list_id = sanitize_text_field( $_POST['activecampaign-list-id'] );
    }
 
    if ( isset( $_POST['activecampaign-map1value'] ) ) {
        $this->activecampaign_map1field = sanitize_text_field( $_POST["activecampaign-map1value"] );
    }
    if ( isset( $_POST['activecampaign-map2value'] ) ) {
        $this->activecampaign_map2field = "FIRSTNAME";
    }
    if ( isset( $_POST['activecampaign-map3value'] ) ) {
        $this->activecampaign_map3field = "LASTNAME";
    }
    if ( isset( $_POST['activecampaign-map4value'] ) ) {
        $this->activecampaign_map4field = "EMAIL";
    }
    if ( isset( $_POST['activecampaign-map5value'] ) ) {
        $this->activecampaign_map5field = sanitize_text_field( $_POST["activecampaign-map5value"] );
    }
    if ( isset( $_POST['activecampaign-map6value'] ) ) {
        $this->activecampaign_map6field = sanitize_text_field( $_POST["activecampaign-map6value"] );
    }
    if ( isset( $_POST['activecampaign-map7value'] ) ) {
        $this->activecampaign_map7field = sanitize_text_field( $_POST["activecampaign-map7value"] );
    }
    if ( isset( $_POST['activecampaign-map8value'] ) ) {
        $this->activecampaign_map8field = sanitize_text_field( $_POST["activecampaign-map8value"] );
    }
    if ( isset( $_POST['activecampaign-map9value'] ) ) {
        $this->activecampaign_map9field = sanitize_text_field( $_POST["activecampaign-map9value"] );
    }
    if ( isset( $_POST['activecampaign-map10value'] ) ) {
        $this->activecampaign_map10field = sanitize_text_field( $_POST["activecampaign-map10value"] );
    }
    if ( isset( $_POST['activecampaign-map11value'] ) ) {
        $this->activecampaign_map11field = sanitize_text_field( $_POST["activecampaign-map11value"] );
    }
    if ( isset( $_POST['activecampaign-map12value'] ) ) {
        $this->activecampaign_map12field = sanitize_text_field( $_POST["activecampaign-map12value"] );
    }
    if ( isset( $_POST['activecampaign-map13value'] ) ) {
        $this->activecampaign_map13field = sanitize_text_field( $_POST["activecampaign-map13value"] );
    }
    if ( isset( $_POST['activecampaign-map14value'] ) ) {
        $this->activecampaign_map14field = sanitize_text_field( $_POST["activecampaign-map14value"] );
    }
    if ( isset( $_POST['sendy-enable'] ) ) {
        $this->sendy_enable = 1;
    }
    if ( isset( $_POST['sendy-api-key'] ) ) {
        $this->sendy_api_key = sanitize_text_field( $_POST['sendy-api-key'] );
    }
    if ( isset( $_POST['sendy-server'] ) ) {
        $this->sendy_server = sanitize_text_field( $_POST['sendy-server'] );
    }
    if ( isset( $_POST['sendy-list-id'] ) ) {
        $this->sendy_list_id = sanitize_text_field( $_POST['sendy-list-id'] );
    }
}

/**
 * Retrieves a list of petitions to populate select box navigation
 * Only queries the info needed to populate select box at head of Signatures view
 *
 * @return (object) query results
 */
public function quicklist()
{
    global $wpdb, $db_petitions;

    $sql = "
        SELECT id, title
        FROM `$db_petitions`
    ";
    $query_results = $wpdb->get_results( $sql );

    return $query_results;
}

/**
 * Reads a petition record and it's signature count from the database
 * 
 * @param (int) $id value of the petition's 'id' field in the database
 * @return (bool) true if query returns a result, false if no results found
 */
public function retrieve( $id )
{
    if( !is_numeric( $id )){ return; }
    global $wpdb, $db_petitions, $db_signatures;

      $sql = "
            SELECT $db_petitions.*, COUNT( $db_signatures.id ) AS 'signatures'
            FROM $db_petitions
            LEFT JOIN $db_signatures
                ON $db_petitions.id = $db_signatures.petitions_id
            WHERE $db_petitions.id = $id
            GROUP BY $db_petitions.id
            ";  
        
    $petition = $wpdb->get_row( $sql );
    $rowcount = $wpdb->get_var( $sql ); 
    
    if(isset( $petition->requires_confirmation ) && $petition->requires_confirmation != 0){
      $sql = "
        SELECT $db_petitions.*, COUNT( $db_signatures.id ) AS 'signatures'
        FROM $db_petitions
        LEFT JOIN $db_signatures
            ON $db_petitions.id = $db_signatures.petitions_id
            AND ( $db_signatures.is_confirmed != '0' )
        WHERE $db_petitions.id = $id
        GROUP BY $db_petitions.id
    ";
    $petition = $wpdb->get_row( $sql );
    $rowcount = $wpdb->get_var( $sql );    
    }
    
    if (  $rowcount > 0 ) {
        $this->_populate_from_query( $petition );
        return true;
    }
    else {
        return false;
    }
}

/**
 * Counts total of all signatures in database 
 *  
 * @param (int) $id value of the petition's 'id' field in the database
 * @return (bool) true if query returns a result, false if no results found
 */
public function retrieveall(  )
{
    global $wpdb, $db_petitions, $db_signatures;

      $sql = "
            SELECT COUNT( $db_signatures.id ) AS 'signatures'
            FROM $db_signatures";  
    $rowcount = $wpdb->get_var( $sql ); 
    
    if(isset( $petition->requires_confirmation ) && $petition->requires_confirmation != 0){
      $sql = "SELECT COUNT( $db_signatures.id ) AS 'signatures'
        FROM $db_signatures
        WHERE $db_signatures.is_confirmed != '0'";
    $rowcount = $wpdb->get_var( $sql );    
    }
    
    if (  $rowcount > 0 ) {
        $this->_populate_from_query( $petition );
        return true;
    }
    else {
        return false;
    }
}
    
	//********************************************************************************
	//* Private
	//********************************************************************************

	/**
	 * Populates the parameters of this object with values from the database 
	 * 
	 * @param (object) $petition database query results
	 */
	private function _populate_from_query( $petition )
	{
		$this->id                    = $petition->id;
		$this->title                 = $petition->title;
		$this->target_email          = $petition->target_email;
		$this->target_email_CC       = $petition->target_email_CC;
		$this->email_subject         = $petition->email_subject;
		$this->greeting              = $petition->greeting;
		$this->petition_message      = $petition->petition_message;
		$this->petition_footer       = $petition->petition_footer;
		$this->address_fields        = unserialize( $petition->address_fields );
		$this->street_required       = $petition->street_required;
		$this->city_required      	 = $petition->city_required;
		$this->state_required      	 = $petition->state_required;
		$this->postcode_required      = $petition->postcode_required;
		$this->country_required      = $petition->country_required;
		$this->expires               = $petition->expires;
		$this->expiration_date       = $petition->expiration_date;
		$this->created_date          = $petition->created_date;
		$this->goal                  = $petition->goal;
		$this->increase_goal         = $petition->increase_goal;
		$this->goal_bump             = $petition->goal_bump;
		$this->goal_trigger          = $petition->goal_trigger;
		$this->sends_email           = $petition->sends_email;
		$this->x_message       = $petition->x_message;
		$this->display_petition_message       = $petition->display_petition_message;
		$this->allow_anonymous        = $petition->allow_anonymous;
		$this->requires_confirmation = $petition->requires_confirmation;
		$this->return_url            = $petition->return_url;
		$this->hide_email_field     = $petition->hide_email_field;
		$this->displays_custom_field = $petition->displays_custom_field;
		$this->custom_field_included = $petition->custom_field_included;
		$this->custom_field_required = $petition->custom_field_required;
		$this->custom_field_label    = $petition->custom_field_label;
		$this->custom_field_location    = $petition->custom_field_location;
        $this->displays_custom_field2 = $petition->displays_custom_field2;
		$this->custom_field2_included = $petition->custom_field2_included;
		$this->custom_field2_required = $petition->custom_field2_required;
		$this->custom_field2_label    = $petition->custom_field2_label;
		$this->custom_field2_location    = $petition->custom_field2_location;
        $this->displays_custom_field3 = $petition->displays_custom_field3;
		$this->custom_field3_included = $petition->custom_field3_included;
		$this->custom_field3_required = $petition->custom_field3_required;
		$this->custom_field3_label    = $petition->custom_field3_label;
		$this->custom_field3_location    = $petition->custom_field3_location;
        $this->displays_custom_field4 = $petition->displays_custom_field4;
		$this->custom_field4_included = $petition->custom_field4_included;
		$this->custom_field4_required = $petition->custom_field4_required;
		$this->custom_field4_label    = $petition->custom_field4_label;
		$this->custom_field4_location    = $petition->custom_field4_location;
        $this->displays_custom_field5 = $petition->displays_custom_field5;
		$this->custom_field5_included = $petition->custom_field5_included;
		$this->custom_field5_required = $petition->custom_field5_required;
		$this->custom_field5_label    = $petition->custom_field5_label;
        $this->custom_field5_values    = $petition->custom_field5_values;
		$this->custom_field5_location    = $petition->custom_field5_location;
		$this->displays_custom_field6 = $petition->displays_custom_field6;
		$this->custom_field6_label    = $petition->custom_field6_label;
		$this->custom_field6_location    = $petition->custom_field6_location;
		$this->displays_custom_field7 = $petition->displays_custom_field7;
		$this->custom_field7_label    = $petition->custom_field7_label;
		$this->custom_field7_location    = $petition->custom_field7_location;
		$this->displays_optin        = $petition->displays_optin;
		$this->open_message_button   = $petition->open_message_button;
		$this->open_editable_message_button   = $petition->open_editable_message_button;
		$this->displays_custom_message = $petition->displays_custom_message;
		$this->custom_message_label    = $petition->custom_message_label;
		$this->optin_label           = $petition->optin_label;
		$this->signatures            = $petition->signatures;
		$this->is_editable           = $petition->is_editable;
		$this->redirect_url_option   = $petition->redirect_url_option;
		$this->redirect_url          = $petition->redirect_url;
		$this->redirect_delay       = $petition->redirect_delay;
		$this->url_target           = $petition->url_target;
        $this->mailchimp_enable     = $petition->mailchimp_enable;
        $this->mailchimp_api_key    = $petition->mailchimp_api_key;
        $this->mailchimp_server     = $petition->mailchimp_server;
        $this->mailchimp_list_id    = $petition->mailchimp_list_id;
        $this->mailerlite_enable     = $petition->mailerlite_enable;
        $this->mailerlite_api_key    = $petition->mailerlite_api_key;
        $this->mailerlite_group_id    = $petition->mailerlite_group_id;
        $this->activecampaign_enable     = $petition->activecampaign_enable;
        $this->activecampaign_api_key    = $petition->activecampaign_api_key;
        $this->activecampaign_server     = $petition->activecampaign_server;
        $this->activecampaign_list_id    = $petition->activecampaign_list_id;
        $this->activecampaign_map1field    = $petition->activecampaign_map1field;
        $this->activecampaign_map2field    = $petition->activecampaign_map2field;
        $this->activecampaign_map3field    = $petition->activecampaign_map3field;
        $this->activecampaign_map4field    = $petition->activecampaign_map4field;
        $this->activecampaign_map5field    = $petition->activecampaign_map5field;
        $this->activecampaign_map6field    = $petition->activecampaign_map6field;
        $this->activecampaign_map7field    = $petition->activecampaign_map7field;
        $this->activecampaign_map8field    = $petition->activecampaign_map8field;
        $this->activecampaign_map9field    = $petition->activecampaign_map9field;
        $this->activecampaign_map10field    = $petition->activecampaign_map10field;
        $this->activecampaign_map11field    = $petition->activecampaign_map11field;
        $this->activecampaign_map12field    = $petition->activecampaign_map12field;
        $this->activecampaign_map13field    = $petition->activecampaign_map13field;
        $this->activecampaign_map14field    = $petition->activecampaign_map14field;
        $this->sendy_enable     = $petition->sendy_enable;
        $this->sendy_api_key    = $petition->sendy_api_key;
        $this->sendy_server     = $petition->sendy_server;
        $this->sendy_list_id    = $petition->sendy_list_id;
	}

	/**
	 * Creates MySQL-formatted date string from submitted year, month, day, hour, and minute form values
	 * And assigns it to this object's $expiration date parameter
	 */
	private function _set_expiration_date()
	{
		// clean post data
		if ( isset( $_POST['year'] ) ) {
			$year = absint( $_POST['year'] );
		}
		if ( isset( $_POST['month'] ) ) {
			$month = absint( $_POST['month'] );
		}
		if ( isset( $_POST['day'] ) ) {
			$day = absint( $_POST['day'] );
		}
		if ( isset( $_POST['hour'] ) ) {
			$hour = absint( $_POST['hour'] );
		}
		if ( isset( $_POST['minutes'] ) ) {
			$minutes = absint( $_POST['minutes'] );
		}

		// force dates to be rational (ie: converts Jan 45 to Feb 14)
		$timestamp = mktime( $hour, $minutes, 0, $month, $day, $year );
		$year      = date( 'Y', $timestamp );
		$month     = date( 'm', $timestamp );
		$day       = date( 'd', $timestamp );
		$hour      = date( 'H', $timestamp );
		$minutes   = date( 'i', $timestamp );
		$this->expiration_date = $year . '-' . $month . '-' . $day . ' ' . $hour . ':' . $minutes;
	}

}

	/**
	 * Constructs an array of user-allowed HTML tags for use with wp_kses()
	 */

		$allowed_tags = array(
			'a'      => array( 'href' => array(),'title' => array() ),
			'em'     => array(),
			'strong' => array(),
			'span' => array(
			    'class' => array()
			    )
			,
			'p'      => array()
		);

?>