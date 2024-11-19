<?php

/**
 * Class for accessing and manipulating signature data in SpeakOut! Email Petitions plugin for WordPress
 */
class dk_speakout_Signature
{
	public $id;
	public $petitions_id;
	public $honorific='';
	public $first_name = '';
	public $last_name = '';
	public $email = '';
	public $street_address = '';
	public $city = '';
	public $state = '';
	public $postcode = '';
	public $country = '';
	public $custom_field = '';
    public $custom_field2 = '';
    public $custom_field3 = '';
    public $custom_field4 = '';
    public $custom_field5 = '';
    public $custom_field6 = 0;
    public $custom_field7 = 0;
	public $optin = '';
	public $date = '';
	public $confirmation_code = '';
	public $is_confirmed = '';
	public $custom_message = '';
	public $submitted_message = '';
	public $language = '';
    public $anonymise = '';

	/**
	 * Retrieves a selection of signature records from the database
	 * 
	 * @param $petition_id (int) optional: the id of the petition whose signature should be retrieved
	 * @param $start (int) optional: the first record to be retrieved
	 * @param $limit (int) optional: the maximum number of records to be retrieved
	 * @param $context (string) optional: context the method is being called from ('csv' or 'signaturelist')
	 * @return (object) query results
	 */
	public function all( $petition_id, $start = 0, $limit = 0, $context = '', $hideUnconfirmed = true )
	{
		global $wpdb, $db_petitions, $db_signatures;

		// restrict query results to signatures from a single petition?
		$sql_petition_filter = '';
		if ( $petition_id ) {
			$sql_petition_filter = "AND $db_signatures.petitions_id = '$petition_id'";
		}

		// limit query results returned if $limit filter is provided
		$sql_limit = '';
		if ( $limit != 0 ) {
			$sql_limit = 'LIMIT ' . $start . ', ' . $limit;
		}

		$sql_context_filter = '';
		// restrict results to either single or double opt-in signatures
		if ( $context == 'csv' ) {
			$options = get_option( 'dk_speakout_options' );

			if ( $options['csv_signatures'] == 'single_optin' ) {
				$sql_context_filter = "AND $db_signatures.optin = 1";
			}
			elseif ( $options['csv_signatures'] == 'double_optin' ) {
				$sql_context_filter = "AND $db_signatures.optin = 1 AND $db_signatures.is_confirmed = 1" ;
			}
            elseif ( $options['csv_signatures'] == 'confirmed' ) {
				$sql_context_filter = "AND $db_signatures.is_confirmed = 1" ;
			}
		}
		// include unconfirmed signatures
		elseif ( $context == 'signaturelist' && $hideUnconfirmed == true) {
			$sql_context_filter = "AND ( $db_signatures.is_confirmed = 1 )";
		}
				// exclude unconfirmed signatures
		elseif ( $context == 'signaturelist' && $hideUnconfirmed == false) {
			$sql_context_filter = "";
		}

		$sql = "
			SELECT $db_signatures.*, $db_petitions.title, $db_petitions.custom_field_label, $db_petitions.displays_custom_field, 
			    $db_petitions.custom_field2_label, $db_petitions.displays_custom_field2, 
			    $db_petitions.custom_field3_label, $db_petitions.displays_custom_field3, 
			    $db_petitions.custom_field4_label, $db_petitions.displays_custom_field4, 
			    $db_petitions.custom_field5_label, $db_petitions.displays_custom_field5, $db_petitions.custom_field5_values,
			    $db_petitions.custom_field6_label, $db_petitions.displays_custom_field6, $db_petitions.custom_field6_value, 
			    $db_petitions.custom_field7_label, $db_petitions.displays_custom_field7, $db_petitions.custom_field7_value  
			FROM `$db_signatures`, `$db_petitions`
			WHERE $db_signatures.petitions_id = $db_petitions.id
			$sql_petition_filter
			$sql_context_filter
			ORDER BY $db_signatures.id DESC $sql_limit
		";
        
		$query_results = $wpdb->get_results( $sql );

		return $query_results; 
	}
	
	
/////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Retrieves a selection of signature records from the database from a search query
	 * 
	 * @param $petition_id (int) optional: the id of the petition whose signature should be retrieved
	 * @param $searchString (string) required: the string to search for
	 * @param $start (int) optional: the first record to be retrieved
	 * @param $limit (int) optional: the maximum number of records to be retrieved
	 * @param $context (string) optional: context the method is being called from ('csv' or 'signaturelist')
	 * @return (object) query results
	 */
	public function search( $petition_id, $searchString, $start = 0, $limit = 0, $context = '' )
	{
		global $wpdb, $db_petitions, $db_signatures;
		// restrict query results to signatures from a single petition?
		$sql_petition_filter = '';
		if ( $petition_id ) {
			$sql_petition_filter = "AND $db_signatures.petitions_id = '$petition_id'";
		}

		// limit query results returned if $limit filter is provided
		$sql_limit = '';
		if ( $limit != 0 ) {
			$sql_limit = 'LIMIT ' . $start . ', ' . $limit;
		}

		$sql_context_filter = '';
		// restrict results to either single or double opt-in signatures
		if ( $context == 'csv' ) {
			$options = get_option( 'dk_speakout_options' );

			if ( $options['csv_signatures'] == 'single_optin' ) {
				$sql_context_filter = "AND $db_signatures.optin = 1";
			}
			elseif ( $options['csv_signatures'] == 'double_optin' ) {
				$sql_context_filter = "AND $db_signatures.optin = 1 AND $db_signatures.is_confirmed = 1" ;
			}
		}
		// exclude unconfirmed signatures
		elseif ( $context == 'signaturelist' ) {
			$sql_context_filter = "AND ( $db_signatures.is_confirmed = '' OR $db_signatures.is_confirmed = 1 )";
		}

		$sql = "
			SELECT $db_signatures.*, $db_petitions.title, $db_petitions.custom_field_label, $db_petitions.displays_custom_field
			FROM `$db_signatures`, `$db_petitions`
			WHERE $db_signatures.petitions_id = $db_petitions.id
			AND ($db_signatures.email LIKE '%" . $searchString . "%' 
            OR $db_signatures.honorific LIKE '%" . $searchString . "%' 
            OR $db_signatures.first_name LIKE '%" . $searchString . "%' 
            OR $db_signatures.street_address LIKE '%" . $searchString . "%' 
            OR $db_signatures.city LIKE '%" . $searchString . "%'  
            OR $db_signatures.state LIKE '%" . $searchString . "%' 
            OR $db_signatures.country LIKE '%" . $searchString . "%' 
            OR $db_signatures.custom_field LIKE '%" . $searchString . "%' 
            OR $db_signatures.custom_field2 LIKE '%" . $searchString . "%' 
            OR $db_signatures.custom_field3 LIKE '%" . $searchString . "%'
            OR $db_signatures.custom_field4 LIKE '%" . $searchString . "%' 
            OR $db_signatures.custom_field5 LIKE '%" . $searchString . "%' 
            OR $db_signatures.postcode LIKE '%" . $searchString . "%')
			$sql_petition_filter
			$sql_context_filter
			ORDER BY $db_signatures.id DESC $sql_limit
		";

		$query_results = $wpdb->get_results( $sql );
		
		return $query_results;
	}

/////////////////////////////////////////////////////////////////////////////////////////
	/**
	 * Checks if a signature has been confirmed by matching a provided confirmation code with one in the database
	 * 
	 * @param $confirmation_code (string) the confirmation code to check
	 * @return (bool) true if match is found, false if no match is found
	 */
	public function check_confirmation( $confirmation_code )
	{
		global $wpdb, $db_signatures;

		$sql = "
			SELECT id
			FROM $db_signatures
			WHERE `confirmation_code` = '$confirmation_code' AND `is_confirmed` = 1
		";
		$query_results = $wpdb->get_row( $sql );

		if ( $wpdb->num_rows > 0 ) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Attempts to confirm an email address by matching the confirmation code provided with one in the database
	 * 
	 * @param $confirmation_code (string) variable sent through link in confirmation email
	 * @return (bool) true if confirmation status was updated, false if confirmation code was not found or the signature was already confirmed
	 */
	public function confirm( $confirmation_code )
	{
		global $wpdb, $db_signatures;

		$data  = array( 'is_confirmed' => 1 );
		$where = array( 'confirmation_code' => $confirmation_code );

		$rows_affected = $wpdb->update( $db_signatures, $data, $where );

		if ( $rows_affected > 0 ) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Counts the number of signatures in the database
	 * 
	 * @param $petition_id (int) optional: unique 'id' of a petition, used to limit results to a single petition
	 * @return (int) the number of signatures found in the database
	 */
	public function count( $petition_id, $context = '', $hideUnconfirmed = false )
	{
		global $wpdb, $db_signatures;

		// count number of signatures in db
		// add WHERE clause if counting signatures from a single petition
		$sql_where = '';
		if ( $petition_id ) {
			$sql_where = " WHERE `petitions_id` = '$petition_id' ";
		}

		// exclude unconfirmed signatures
		$sql_context_filter = '';
		if ( $context == 'signaturelist'&& $hideUnconfirmed == true ) {
            $sql_context_filter = $petition_id ? " AND " : " WHERE "; 
			$sql_context_filter .= " (  $db_signatures.is_confirmed = 1 )";
		}            

		$sql = "
			SELECT `id`
			FROM `$db_signatures`
			$sql_where
			$sql_context_filter
		";
		$query_results = $wpdb->get_results( $sql );
		return $wpdb->num_rows;
	}

	/**
	 * Counts the number of signatures in the database
	 * 
	 * @param $petition_id (int) optional: unique 'id' of a petition, used to limit results to a single petition
	 * @return (int) the number of signatures found in the database
	 */
	public function countunconfirmed( $petition_id )
	{
		global $wpdb, $db_signatures;


		$sql = "
			SELECT `id`
			FROM `$db_signatures`
			WHERE `petitions_id` = '$petition_id'
			AND `is_confirmed` != 1 
		";
		$query_results = $wpdb->get_results( $sql );

		return $wpdb->num_rows;
	}
	
	/**
	 * Creates a new signature record in the database and updates the goal if needed
	 * 
	 * @param $petition_id (int) the unique id of the petition we are signing
	 */
	public function create( $petition_id, $increase_goal)
	{
		global $wpdb, $db_signatures;

		$data = array(
			'petitions_id'      => $petition_id,
			'honorific'			=> $this->honorific,
			'first_name'        => $this->first_name,
			'last_name'         => $this->last_name,
			'email'             => $this->email,
			'date'              => $this->date,
			'confirmation_code' => $this->confirmation_code,
			'is_confirmed'      => $this->is_confirmed,
			'optin'             => $this->optin,
			'street_address'    => $this->street_address,
			'city'              => $this->city,
			'state'             => $this->state,
			'postcode'          => $this->postcode,
			'country'           => $this->country,
			'custom_field'      => $this->custom_field,
            'custom_field2'      => $this->custom_field2,
            'custom_field3'      => $this->custom_field3,
            'custom_field4'      => $this->custom_field4,
            'custom_field5'      => $this->custom_field5,
            'custom_field6'      => $this->custom_field6,
            'custom_field7'      => $this->custom_field7,
			'custom_message'    => $this->custom_message,
			'language'          => $this->language,
			'IP_address'		=> sanitize_text_field( $_SERVER['REMOTE_ADDR'] ),
            'anonymise'         => $this->anonymise
		);

		$wpdb->insert( $db_signatures, $data );

		// grab the id of the record we just added to the database
		$this->id = $wpdb->insert_id;
		
		//update goal
        if($increase_goal == 1){
          $this->update_goal($petition_id );
        }

        if ( $options['webhooks'] == 'on' ) {
            do_action( 'speakout_after_petition_signed', $data ); 
        }
	}

    /**
    * Update the goal if that option is enabled and 
    * trigger point reached
    */
    
    private function update_goal($petition_id )
    {

            global $wpdb, $db_petitions;
            
            $sql = "SELECT `goal`, `increase_goal`, `goal_bump`, `goal_trigger` FROM `$db_petitions` WHERE `id` = $petition_id ";

            // get our goal
            $query_results = $wpdb->get_row( $sql );

            //count signatures
            $totalSignatures = $this->count( $petition_id );

            //calculate the value of the trigger
            $trigger = round( ( $query_results->goal * $query_results->goal_trigger) / 100 );

            // if we have reached the trigger
            if($totalSignatures >= $trigger){
                if( intval($query_results->goal_trigger) == 0 ){
                    //  if no bump amount, calculate a pretty value
                    $plusHalf = round($query_results->goal * 1.5);
                    if ($plusHalf > 99){
                            $bumpBy = ceil($plusHalf / 100) * 100;
                    }
                    elseif($plusHalf > 999){
                            $bumpBy = ceil($plusHalf / 1000) * 1000;
                    }                           
                    elseif($plusHalf > 9999){
                            $bumpBy = ceil($plusHalf / 10000) * 10000;
                    }
                    else{
                        $bumpby = 50;
                    }
                }
                else{ //otherwise if bump amount is set use it
                  $bumpBy = $query_results->goal_bump;
                }
            }

        if ( $options['webhooks'] == 'on' ) {
            $oldGoal = $query_results->goal;
		    $bumpBy = $bumpBy;
		    do_action( 'speakout_goal_updated', $petition_id, $oldGoal, $bumpBy  );
		}
        
        // update our new goal
        $sql = "UPDATE " .  $db_petitions . " SET `goal` = " . ($query_results->goal + $bumpBy) . " WHERE id=".  $petition_id ;
		$query_results = $wpdb->get_results( $wpdb->prepare( $sql ) );
        
        
        
    }
    
    
	/**
	 * Generates a confirmation code and assigns it to this object
	 */
	public function create_confirmation_code()
	{
		$this->confirmation_code = substr( md5( uniqid() ), 0, 16 );
	}

	
	/**
	 * Determines whether an email address has previously been used to sign the petition
	 * 
	 * @param string $email email address
	 * @param int $petition_id the petition whose signatures we are searching
	 * @return true if signature is unique, false if signature has been used
	 */
	public function has_unique_email( $email, $petition_id, $hide_email )
	{
		// if we aren't collecting emails, no need to check for unique
		if($hide_email == 1){
		    return true;
		}
		global $wpdb, $db_signatures;

		$sql = "
			SELECT `id`
			FROM $db_signatures
			WHERE `email` = %s AND `petitions_id` = %d
		";
		$query_results = $wpdb->get_row( $wpdb->prepare( $sql, $email, $petition_id ) );

		if ($wpdb->num_rows < 1 ) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * populates the parameters of this object with posted form values 
	 */
	public function populate_from_post()
	{
		$this->petitions_id = sanitize_text_field( $_POST['id'] );
		$this->honorific   = sanitize_text_field( $_POST['honorific'] );
		$this->first_name   = sanitize_text_field( $_POST['first_name'] );
		$this->last_name    = sanitize_text_field( $_POST['last_name'] );
		$this->email        = sanitize_email( $_POST['email'] );
		$this->date         = current_time( 'mysql', 0 );

		if ( isset( $_POST['custom_message'] ) ) {
			$this->submitted_message = sanitize_textarea_field( $_POST['custom_message'] );
		}
		if ( isset( $_POST['street'] ) ) {
			$this->street_address = sanitize_text_field( $_POST['street'] );
		}
		if ( isset( $_POST['city'] ) ) {
			$this->city = sanitize_text_field( $_POST['city'] );

		}
		if ( isset( $_POST['state'] ) ) {
			$this->state = sanitize_text_field( $_POST['state'] );
		}
		if ( isset( $_POST['postcode'] ) ) {
			$this->postcode = sanitize_text_field( $_POST['postcode'] );
		}
		if ( isset( $_POST['country'] ) ) {
			$this->country = sanitize_text_field( $_POST['country'] );
		}
		if ( isset( $_POST['custom_field'] ) ) {
			$this->custom_field = sanitize_text_field( $_POST['custom_field'] );
		}
        if ( isset( $_POST['custom_field2'] ) ) {
			$this->custom_field2 = sanitize_text_field( $_POST['custom_field2'] );
		}
        if ( isset( $_POST['custom_field3'] ) ) {
			$this->custom_field3 = sanitize_text_field( $_POST['custom_field3'] );
		}
        if ( isset( $_POST['custom_field4'] ) ) {
			$this->custom_field4 = sanitize_text_field( $_POST['custom_field4'] );
		}
        if ( isset( $_POST['custom_field5'] ) ) {
			$this->custom_field5 = sanitize_text_field( $_POST['custom_field5'] );
		}
		if ( isset( $_POST['custom_field6'] ) ) {
			$this->custom_field6 = 1;
		}
		if ( isset( $_POST['custom_field7'] ) ) {
			$this->custom_field7 = 1;
		}
		if ( isset( $_POST['optin'] ) && $_POST['optin'] == 'on' ) {
			$this->optin = 1;
		}
		if ( isset( $_POST['anonymise'] ) && $_POST['anonymise'] == 'on' ) {
			$this->anonymise = 1;
		}
		if ( isset( $_POST['lang'] ) ) {
			$this->language = sanitize_text_field( $_POST['lang'] );
		}
	}

	/**
	 * Reads a signature record from the database and populates the object with it's results
	 * 
	 * @param int $signature_id value of the signature's 'id' field in the database
	 */
	public function retrieve( $signature_id )
	{
		global $wpdb, $db_signatures;

		$sql = "
			SELECT *
			FROM `$db_signatures`
			WHERE `id` = %d
		";
		$query_results = $wpdb->get_row( $wpdb->prepare( $sql, $signature_id ) );

		$this->_populate_from_query( $query_results );
	}

	/**
	 * Retrieves a confirmed signature via its confirmation_code
	 * and populates this object with the result
	 * 
	 * @param $confirmation_code (string) the signature's confirmation_code
	 */
	public function retrieve_confirmed( $confirmation_code )
	{
		global $wpdb, $db_signatures;

		$sql = "
			SELECT *
			FROM $db_signatures
			WHERE `confirmation_code` = '%s' AND `is_confirmed` = 1
		";
		$query_results = $wpdb->get_row( $wpdb->prepare( $sql, $confirmation_code ) );

		$this->_populate_from_query( $query_results );
	}

	/**
	 * Retrieves unconfirmed signatures from the database
	 * Used to display only unconfirmed from signatures admin screen
	 * 
	 * @param $petition_id (int) unique 'id' of the petition whose signatures we are searching
	 * 
	 * @return (object) query results
	 */
	public function retrieve_unconfirmed( $petition_id )
	{
		global $wpdb, $db_signatures;
		
		$sql = "
			SELECT * 
			FROM $db_signatures
			WHERE `petitions_id` = '%d' AND `is_confirmed` != 1
		";
		$query_results = $wpdb->get_results( $wpdb->prepare( $sql, $petition_id ) );

		return $query_results;
	}
	
	/**
	 * Retrieves unconfirmed signatures from the database
	 * Used to re-send confirmation emails from signatures admin screen
	 * 
	 * @param $petition_id (int) unique 'id' of the petition whose signatures we are searching
	 * @param $siglist (string) delimited string of selected signature ID
	 * 
	 * @return (object) query results
	 */
		public function unconfirmed( $petition_id, $siglist )
	{
		global $wpdb, $db_signatures;
		
         if ($siglist > ""){
             $allsigs = explode( "|", $siglist);
             
                $siglist = " AND id IN ( ";
                foreach ($allsigs as $sigID){
                    $siglist .=  $sigID . ",";
                }
                $siglist = substr($siglist, 0, -2) . " )";
         }
         
		$sql = "
			SELECT *
			FROM $db_signatures
			WHERE `petitions_id` = %1d %2s AND `is_confirmed` != 1
		" ;
		
		$query_results = $wpdb->get_results( $wpdb->prepare( $sql, $petition_id,  $siglist) );

		return $query_results;
	}
	

	/**
	 * Forces selected signatures to be confirmed 
	 * 
	 * @param $siglist (string) delimited string of selected signature IDs
	 * @return (object) query results
	 */
	public function confirmSelected( $siglist )
	{
		global $wpdb, $db_signatures;
		
         //create SQL statement from passed sig IDs
         if ($siglist > ""){
             $allsigs = explode( "|", $siglist);
             
                $siglist = " id IN ( ";
                foreach ($allsigs as $sigID){
                    $siglist .=  $sigID . ",";
                }
                $siglist = substr($siglist, 0, -2) . " )";
         }
         
		$sql = "UPDATE " .  $db_signatures . " SET `is_confirmed` = 1	WHERE ".  $siglist ;

		$wpdb->query($wpdb->prepare($sql));
	
	}
	
	/**
	 * deletes selected signatures 
	 * 
	 * @param $siglist (string) delimited string of selected signature IDs
	 * @return (object) query results
	 */
	public function deleteSelected( $siglist )
	{
		global $wpdb, $db_signatures;
		
         //create SQL statement from passed sig IDs
         if ($siglist > ""){
             $allsigs = explode( "|", $siglist);
             
                $siglist = " id IN ( ";
                foreach ($allsigs as $sigID){
                    $siglist .=  $sigID . ",";
                }
                $siglist = substr($siglist, 0, -2) . " )";
         }
         
		$sql = "DELETE FROM  " .  $db_signatures . " WHERE ".  $siglist ;

		$query_results = $wpdb->get_results( $wpdb->prepare( $sql ) );

		return $query_results;
	
	}	
	/**
	 * Deletes a signature from the database
	 * 
	 * @param int $signature_id value of the signature's 'id' field in the database
	 */
	public function delete( $signature_id )
	{
		global $wpdb, $db_signatures;

		$sql = "
			DELETE FROM `$db_signatures`
			WHERE `id` = %d
		";
		$wpdb->query( $wpdb->prepare( $sql, $signature_id ) );
	}

	
	//********************************************************************************
	//* Private
	//********************************************************************************

	/**
	 * populates the parameters of this object with values from the database
	 * 
	 * @param $signature (object) database query results
	 */
	private function _populate_from_query( $signature )
	{
		$this->id                = $signature->id;
		$this->petitions_id      = $signature->petitions_id;
		$this->honorific		 = $signature->honorific;
		$this->first_name        = $signature->first_name;
		$this->last_name         = $signature->last_name;
		$this->email             = $signature->email;
		$this->street_address    = $signature->street_address;
		$this->city              = $signature->city;
		$this->state             = $signature->state;
		$this->postcode          = $signature->postcode;
		$this->country           = $signature->country;
		$this->custom_field      = $signature->custom_field;
        $this->custom_field2      = $signature->custom_field2;
        $this->custom_field3      = $signature->custom_field3;
        $this->custom_field4      = $signature->custom_field4;
        $this->custom_field5      = $signature->custom_field5;
        $this->custom_field6      = $signature->custom_field6;
        $this->custom_field7      = $signature->custom_field7;
		$this->optin             = $signature->optin;
		$this->date              = $signature->date;
		$this->confirmation_code = $signature->confirmation_code;
		$this->is_confirmed      = $signature->is_confirmed;
		$this->custom_message    = $signature->custom_message;
        $this->anonymise         = $signature->anonymise;
	}

}

?>