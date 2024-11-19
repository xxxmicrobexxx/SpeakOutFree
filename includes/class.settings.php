<?php

/**
 * Class for accessing and manipulating settings array in SpeakOut! Email Petitions plugin for WordPress
 */
class dk_speakout_Settings
{
	public $petitions_rows;
	public $signatures_rows;
	public $petition_theme;
	public $widget_theme;
	public $button_text = "Sign Now";
	public $expiration_message;
	public $success_message;
	public $already_signed_message;
	public $share_message;
	public $confirm_subject;
	public $confirm_message;
	public $confirm_email;
	public $optin_default;
	public $display_count;
	public $csv_signatures;
	public $signaturelist_theme;
	public $signaturelist_header;
	public $signaturelist_rows;
	public $signaturelist_columns;
	public $signaturelist_privacy;
	public $signaturelist_display;
	public $sig_email = 0;
	public $sig_city = 0;
	public $sig_state = 0;
	public $sig_postcode = 0;
	public $sig_country = 0;
	public $sig_custom = 0;
    public $sig_custom2 = 0;
    public $sig_custom3 = 0;
    public $sig_custom4 = 0;
    public $sig_custom5 = 0;
    public $sig_custom6 = 0;
    public $sig_custom7 = 0;
	public $sig_message = 0;
	public $sig_date = 0;
	public $display_bcc;
	public $display_privacypolicy;
	public $privacypolicy_url;
    public $g_recaptcha_version = "";
	public $g_recaptcha_status = "off";
	public $g_recaptcha_site_key = "";
	public $g_recaptcha_private_key = "";
	public $hcaptcha_status = "off" ;
	public $hcaptcha_site_key = "";
	public $hcaptcha_private_key = "";
	public $display_anedot = "off";
	public $display_sharing = "on";
	public $display_honorific;
	public $anedot_page_id;
	public $anedot_embed_pref = "true";
	public $anedot_iframe_width = "100%";
	public $anedot_iframe_height = "1570";
    public $petition_fade = "false";
    public $eu_postalcode;
    public $thousands_separator = ",";
    public $decimal_separator = ".";
    public $display_flags;
    public $sigtab_email;
    public $sigtab_petition_info;
    public $sigtab_street_address;
    public $sigtab_city;
    public $sigtab_state;
    public $sigtab_postalcode;
    public $sigtab_country;
    public $sigtab_custom_field1;
    public $sigtab_custom_field2;
    public $sigtab_custom_field3;
    public $sigtab_custom_field4;
    public $sigtab_custom_field5;
    public $sigtab_custom_field6;
    public $sigtab_custom_field7;
    public $sigtab_confirmed_status;
    public $sigtab_date_signed;
    public $sigtab_display_time;
    public $sigtab_IP_address;
    public $webhooks = "off";

	/**
	 * Retrieves the plugin options and populates this object
	 */
	public function retrieve()
	{
		$options  = get_option( 'dk_speakout_options' );

		$this->petitions_rows         		= $options['petitions_rows'];
		$this->signatures_rows        		= $options['signatures_rows'];
		$this->petition_theme         		= $options['petition_theme'];
		$this->widget_theme           		= $options['widget_theme'];
		$this->button_text            		= $options['button_text'];
		$this->expiration_message     		= $options['expiration_message'];
		$this->success_message        		= $options['success_message'];
		$this->already_signed_message 		= $options['already_signed_message'];
		$this->share_message          		= $options['share_message'];
		$this->confirm_subject        		= $options['confirm_subject'];
		$this->confirm_message        		= $options['confirm_message'];
		$this->confirm_email          		= $options['confirm_email'];
		$this->optin_default          		= $options['optin_default'];
		$this->display_count          		= $options['display_count'];
		$this->csv_signatures         		= $options['csv_signatures'];
		$this->signaturelist_theme    		= $options['signaturelist_theme'];
		$this->signaturelist_header   		= $options['signaturelist_header'];
		$this->signaturelist_rows     		= $options['signaturelist_rows'];
		$this->signaturelist_privacy  		= $options['signaturelist_privacy'];
		$this->signaturelist_display  		= $options['signaturelist_display'];
		$this->signaturelist_columns  		= $options['signaturelist_columns'];
		$this->display_bcc            		= $options['display_bcc'];
		$this->display_privacypolicy    	= $options['display_privacypolicy'];
		$this->privacypolicy_url        	= $options['privacypolicy_url'];
        $this->g_recaptcha_version          = $options['g_recaptcha_version'];
		$this->g_recaptcha_status           = $options['g_recaptcha_status'];
		$this->g_recaptcha_site_key         = $options['g_recaptcha_site_key'];
		$this->g_recaptcha_secret_key       = $options['g_recaptcha_secret_key'];
		$this->hcaptcha_status           	= $options['hcaptcha_status'];
		$this->hcaptcha_site_key            = $options['hcaptcha_site_key'];
		$this->hcaptcha_secret_key          = $options['hcaptcha_secret_key'];
		$this->display_anedot               = $options['display_anedot'];
		$this->display_sharing              = $options['display_sharing'];
		$this->display_honorific      		= $options['display_honorific'];
        $this->anedot_page_id				= $options['anedot_page_id'];
		$this->anedot_embed_pref		    = $options['anedot_embed_pref'];
		$this->anedot_iframe_width		    = $options['anedot_iframe_width'];
		$this->anedot_iframe_height		    = $options['anedot_iframe_height'];
        $this->petition_fade                = $options['petition_fade'];
        $this->eu_postalcode                = $options['eu_postalcode'];
        $this->thousands_separator          = $options['thousands_separator'];
        $this->decimal_separator            = $options['decimal_separator'];
        $this->display_flags                = $options['display_flags'];
        $this->sigtab_email                 = $options['sigtab_email'];
        $this->sigtab_petition_info         = $options['sigtab_petition_info'];
        $this->sigtab_street_address        = $options['sigtab_street_address'];
        $this->sigtab_city                  = $options['sigtab_city'];
        $this->sigtab_state                 = $options['sigtab_state'];
        $this->sigtab_postalcode            = $options['sigtab_postalcode'];
        $this->sigtab_country               = $options['sigtab_country'];
        $this->sigtab_custom_field1         = $options['sigtab_custom_field1'];
        $this->sigtab_custom_field2         = $options['sigtab_custom_field2'];
        $this->sigtab_custom_field3         = $options['sigtab_custom_field3'];
        $this->sigtab_custom_field4         = $options['sigtab_custom_field4'];
        $this->sigtab_custom_field5         = $options['sigtab_custom_field5'];
        $this->sigtab_custom_field6         = $options['sigtab_custom_field6'];
        $this->sigtab_custom_field7         = $options['sigtab_custom_field7'];
        $this->sigtab_confirmed_status      = $options['sigtab_confirmed_status'];
        $this->sigtab_optin                 = $options['sigtab_optin'];
        $this->sigtab_date_signed           = $options['sigtab_date_signed'];
        $this->sigtab_display_time          = $options['sigtab_display_time'];
        $this->sigtab_IP_address            = $options['sigtab_IP_address'];
        $this->webhooks                     = $options['webhooks'];
    
		$this->_read_signaturelist_columns();
	}

	/**
	 * Updates the plugin options
	 */
	public function update()
	{
		$this->_clean_post_data();

		$options = array(
			'petitions_rows'			=> $this->petitions_rows,
			'signatures_rows'			=> $this->signatures_rows,
			'petition_theme'			=> $this->petition_theme,
			'widget_theme'				=> $this->widget_theme,
			'button_text'				=> $this->button_text,
			'expiration_message'		=> $this->expiration_message,
			'success_message'			=> $this->success_message,
			'already_signed_message'	=> $this->already_signed_message,
			'share_message'				=> $this->share_message,
			'confirm_subject'			=> $this->confirm_subject,
			'confirm_message'			=> $this->confirm_message,
			'confirm_email'				=> $this->confirm_email,
			'optin_default'				=> $this->optin_default,
			'display_count'				=> $this->display_count,
			'csv_signatures'			=> $this->csv_signatures,
			'signaturelist_theme'		=> $this->signaturelist_theme,
			'signaturelist_header'		=> $this->signaturelist_header,
			'signaturelist_rows'		=> $this->signaturelist_rows,
			'signaturelist_columns'		=> $this->signaturelist_columns,
			'signaturelist_privacy'		=> $this->signaturelist_privacy,
			'signaturelist_display'		=> $this->signaturelist_display,
			'display_bcc'				=> $this->display_bcc,
			'display_privacypolicy'		=> $this->display_privacypolicy,
            'g_recaptcha_version'       => $this->g_recaptcha_version,
			'g_recaptcha_status'        => $this->g_recaptcha_status,
		    'g_recaptcha_site_key'      => $this->g_recaptcha_site_key,
		    'g_recaptcha_secret_key'    => $this->g_recaptcha_secret_key,
			'hcaptcha_status'           => $this->hcaptcha_status,
		    'hcaptcha_site_key'         => $this->hcaptcha_site_key,
		    'hcaptcha_secret_key'       => $this->hcaptcha_secret_key,
			'privacypolicy_url'			=> $this->privacypolicy_url,
			'display_anedot'            => $this->display_anedot,
			'display_sharing'           => $this->display_sharing,
			'display_honorific'			=> $this->display_honorific,
			'anedot_page_id'			=> $this->anedot_page_id,
			'anedot_embed_pref'			=> $this->anedot_embed_pref,
			'anedot_iframe_width'		=> $this->anedot_iframe_width,
			'anedot_iframe_height'		=> $this->anedot_iframe_height,
            'petition_fade'             => $this->petition_fade,
            'eu_postalcode'             => $this->eu_postalcode,
            'thousands_separator'       => $this->thousands_separator,
            'decimal_separator'         => $this->decimal_separator,
            'thousands_separator'       => $this->thousands_separator,
            'decimal_separator'         => $this->decimal_separator,
            'display_flags'             => $this->display_flags,
            'sigtab_email'              => $this->sigtab_email,                
            'sigtab_petition_info'      => $this->sigtab_petition_info,        
            'sigtab_street_address'     => $this->sigtab_street_address,       
            'sigtab_city'               => $this->sigtab_city,                 
            'sigtab_state'              => $this->sigtab_state,               
            'sigtab_postalcode'         => $this->sigtab_postalcode,           
            'sigtab_country'            => $this->sigtab_country,          
            'sigtab_custom_field1'      => $this->sigtab_custom_field1, 
            'sigtab_custom_field2'      => $this->sigtab_custom_field2,
            'sigtab_custom_field3'      => $this->sigtab_custom_field3,
            'sigtab_custom_field4'      => $this->sigtab_custom_field4,
            'sigtab_custom_field5'      => $this->sigtab_custom_field5,
            'sigtab_custom_field6'      => $this->sigtab_custom_field6,
            'sigtab_custom_field7'      => $this->sigtab_custom_field7,
            'sigtab_confirmed_status'   => $this->sigtab_confirmed_status,     
            'sigtab_optin'              => $this->sigtab_optin,               
            'sigtab_date_signed'        => $this->sigtab_date_signed,
            'sigtab_display_time'       => $this->sigtab_display_time,
            'sigtab_IP_address'         => $this->sigtab_IP_address,
            'webhooks'                  => $this->webhooks,
		);

		update_option( 'dk_speakout_options', $options );
	}

	/**
	 * Constructs an array of user-allowed HTML tags for use with wp_kses()
	 */
	private function _allowed_html_tags()
	{
		$allowed_tags = array(
			'a'      => array( 'href' => array(),'title' => array() ),
			'em'     => array(),
			'strong' => array(),
			'p'      => array()
		);

		return $allowed_tags;
	}

	/**
	 * Prepares user-submitted form values for placing in the database
	 */
	private function _clean_post_data()
	{
        
                
        // sanitize name and email
        $input = $_POST['confirm_email'];
        preg_match( '/([^<]+)<([^>]+)>/i', $input, $matches, PREG_UNMATCHED_AS_NULL );
        $name = sanitize_text_field( $matches[ 1 ] );
        $email = sanitize_email( $matches[ 2 ] );
        $this->confirm_email    =   $name . " <" . $email . ">" ;
        
        
		$allowed_tags = $this->_allowed_html_tags();
		$signaturelist_columns          = $this->_write_signaturelist_columns();
		//prevent 0 or blank value
        $petition_rows                   = sanitize_text_field( $_POST['petitions_rows'] < 1 ? 1 : absint( $_POST['petitions_rows'] ) );
		$this->petitions_rows         	= $petition_rows;
        $signatures_rows                 = sanitize_text_field( $_POST['signatures_rows'] < 1 ? 1 : absint( $_POST['signatures_rows'] ) );
		$this->signatures_rows        	= $signatures_rows;
		$this->petition_theme         	 = sanitize_text_field( $_POST['petition_theme'] );
		$this->widget_theme           	 = sanitize_text_field( $_POST['widget_theme'] );
		if(!isset($_POST['button_text'])){$_POST['button_text'] = "Sign Now";}
		    $this->button_text            	= sanitize_text_field( $_POST['button_text'] ) ;
		if(!isset($_POST['expiration_message'])){$_POST['expiration_message'] = "This petition is now closed.";}
		    $this->expiration_message     	= sanitize_text_field( $_POST['expiration_message'] );
		$this->success_message        	= wp_kses( stripslashes( $_POST['success_message'] ), $allowed_tags );
		if(!isset($_POST['already_signed_message'])){$_POST['already_signed_message'] = "This petition has already been signed using your email address.";}
		    $this->already_signed_message 	= sanitize_text_field(  $_POST['already_signed_message'] );
		if(!isset($_POST['share_message'])){ $_POST['share_message'] = "Share this with your friends:";}
		    $this->share_message          	= sanitize_text_field(  $_POST['share_message'] );
		$this->confirm_subject        	= sanitize_text_field(  $_POST['confirm_subject'] );
		$this->confirm_message        	= strip_tags( nl2br( stripslashes( $_POST['confirm_message'] )),'<em><strong><hr><span><br /><table><tr><td>'  );
		$this->optin_default          	 = sanitize_text_field( $_POST['optin_default'] );
		$this->display_count          	 = sanitize_text_field( $_POST['display_count'] );
		$this->csv_signatures         	 = sanitize_text_field( $_POST['csv_signatures'] );
		$this->signaturelist_theme    	 = sanitize_text_field( $_POST['signaturelist_theme'] );
		$this->signaturelist_header   	= sanitize_text_field(  $_POST['signaturelist_header'] );
		$this->signaturelist_rows     	= absint( sanitize_text_field( $_POST['signaturelist_rows'] ) );
		$this->signaturelist_columns  	= $signaturelist_columns;
		$this->signaturelist_privacy     = sanitize_text_field( $_POST['signaturelist_privacy'] );
		$this->signaturelist_display     = sanitize_text_field( $_POST['signaturelist_display'] );
		$this->display_bcc               = sanitize_text_field( $_POST['display_bcc'] );
		$this->display_privacypolicy     = sanitize_text_field( $_POST['display_privacypolicy'] );
		$this->privacypolicy_url         = sanitize_text_field( $_POST['privacypolicy_url'] );
        if(isset($_POST['g_recaptcha_status'])){
            $this->g_recaptcha_version       = sanitize_text_field( $_POST['g_recaptcha_version'] );
            $this->g_recaptcha_status        = sanitize_text_field( $_POST['g_recaptcha_status'] );
            $this->g_recaptcha_site_key      = sanitize_text_field( $_POST['g_recaptcha_site_key'] );
            $this->g_recaptcha_secret_key    = sanitize_text_field( $_POST['g_recaptcha_secret_key'] );
        }
        if(isset($_POST['hcaptcha_status'])){
            $this->hcaptcha_status           = sanitize_text_field( $_POST['hcaptcha_status'] );
            $this->hcaptcha_site_key         = sanitize_text_field( $_POST['hcaptcha_site_key'] );
            $this->hcaptcha_secret_key       = sanitize_text_field( $_POST['hcaptcha_secret_key'] );
        }
		$this->display_anedot            = sanitize_text_field( $_POST['display_anedot'] );
		$this->display_sharing           = sanitize_text_field( $_POST['display_sharing'] );
		$this->display_honorific		 = sanitize_text_field( $_POST['display_honorific'] );
		$this->anedot_page_id            = sanitize_text_field( $_POST['anedot_page_id'] );
		$this->anedot_embed_pref         = sanitize_text_field( $_POST['anedot_embed_pref'] );
		$this->anedot_iframe_width       = sanitize_text_field( $_POST['anedot_iframe_width'] );
		$this->anedot_iframe_height      = sanitize_text_field( $_POST['anedot_iframe_height'] );
        if(isset($_POST['petition_fade'])){
            $this->petition_fade             = sanitize_text_field( $_POST['petition_fade'] );
        }
        $this->eu_postalcode             = sanitize_text_field( $_POST['eu_postalcode'] );
        $this->thousands_separator       = sanitize_text_field( $_POST['thousands_separator'] );
        $this->decimal_separator         = sanitize_text_field( $_POST['decimal_separator'] );
        $this->display_flags             = sanitize_text_field( $_POST['display_flags'] );
        $this->sigtab_email              = sanitize_text_field( $_POST['sigtab_email'] ); 
        $this->sigtab_petition_info      = sanitize_text_field( $_POST['sigtab_petition_info'] );    
        $this->sigtab_street_address     = sanitize_text_field( $_POST['sigtab_street_address'] );   
        $this->sigtab_city               = sanitize_text_field( $_POST['sigtab_city'] );             
        $this->sigtab_state              = sanitize_text_field( $_POST['sigtab_state'] );               
        $this->sigtab_postalcode         = sanitize_text_field( $_POST['sigtab_postalcode'] );       
        $this->sigtab_country            = sanitize_text_field( $_POST['sigtab_country'] );          
        $this->sigtab_custom_field1      = sanitize_text_field( $_POST['sigtab_custom_field1'] );
        $this->sigtab_custom_field2      = sanitize_text_field( $_POST['sigtab_custom_field2'] );
        $this->sigtab_custom_field3      = sanitize_text_field( $_POST['sigtab_custom_field3'] );
        $this->sigtab_custom_field4      = sanitize_text_field( $_POST['sigtab_custom_field4'] );
        $this->sigtab_custom_field5      = sanitize_text_field( $_POST['sigtab_custom_field5'] );
        $this->sigtab_custom_field6      = sanitize_text_field( $_POST['sigtab_custom_field6'] );
        $this->sigtab_custom_field7      = sanitize_text_field( $_POST['sigtab_custom_field7'] );
        $this->sigtab_confirmed_status   = sanitize_text_field( $_POST['sigtab_confirmed_status'] ); 
        $this->sigtab_optin              = sanitize_text_field( $_POST['sigtab_optin'] );              
        $this->sigtab_date_signed        =  $_POST['sigtab_date_signed'] ;
        $this->sigtab_display_time       = sanitize_text_field( $_POST['sigtab_display_time'] );
        $this->sigtab_IP_address         = sanitize_text_field( $_POST['sigtab_IP_address'] );
        $this->webhooks                  = $_POST['webhooks'] ;
        	}
	
	/**
	 * Unserializes signaturelist_columns array and assigns values to this object
	 */
	private function _read_signaturelist_columns()
	{
		$signature_columns = unserialize( $this->signaturelist_columns );

		if ( in_array( 'sig_email', $signature_columns ) ) {
			$this->sig_email = 1;
		}
		if ( in_array( 'sig_city', $signature_columns ) ) {
			$this->sig_city = 1;
		}
		if ( in_array( 'sig_state', $signature_columns ) ) {
			$this->sig_state = 1;
		}
		if ( in_array( 'sig_postcode', $signature_columns ) ) {
			$this->sig_postcode = 1;
		}
		if ( in_array( 'sig_country', $signature_columns ) ) {
			$this->sig_country = 1;
		}
		if ( in_array( 'sig_custom', $signature_columns ) ) {
			$this->sig_custom = 1;
		}
        if ( in_array( 'sig_custom2', $signature_columns ) ) {
			$this->sig_custom2 = 1;
		}
        if ( in_array( 'sig_custom3', $signature_columns ) ) {
			$this->sig_custom3 = 1;
		}
        if ( in_array( 'sig_custom4', $signature_columns ) ) {
			$this->sig_custom4 = 1;
		}
        if ( in_array( 'sig_custom5', $signature_columns ) ) {
			$this->sig_custom5 = 1;
		}
		if ( in_array( 'sig_custom6', $signature_columns ) ) {
			$this->sig_custom6 = 1;
		}
		if ( in_array( 'sig_custom7', $signature_columns ) ) {
			$this->sig_custom7 = 1;
		}
		if ( in_array( 'sig_message', $signature_columns ) ) {
			$this->sig_message = 1;
		}
		if ( in_array( 'sig_date', $signature_columns ) ) {
			$this->sig_date = 1;
		}
	}

	/**
	 * Creates array from signaturelist columns values
	 * and serializes the array for placement in the database
	 */
	private function _write_signaturelist_columns()
	{
		$columns = array();
		
        array_push($columns, "sig_count");
        array_push($columns, "sig_name");
        if ( isset( $_POST['sig_email'] ) ) {
			array_push( $columns, 'sig_email' );
		}
		if ( isset( $_POST['sig_city'] ) ) {
			array_push( $columns, 'sig_city' );
		}
		if ( isset( $_POST['sig_state'] ) ) {
			array_push( $columns, 'sig_state' );
		}
		if ( isset( $_POST['sig_postcode'] ) ) {
			array_push( $columns, 'sig_postcode' );
		}
		if ( isset( $_POST['sig_country'] ) ) {
			array_push( $columns, 'sig_country' );
		}
		if ( isset( $_POST['sig_custom'] ) ) {
			array_push( $columns, 'sig_custom' );
		}
        if ( isset( $_POST['sig_custom2'] ) ) {
			array_push( $columns, 'sig_custom2' );
		}
        if ( isset( $_POST['sig_custom3'] ) ) {
			array_push( $columns, 'sig_custom3' );
		}
        if ( isset( $_POST['sig_custom4'] ) ) {
			array_push( $columns, 'sig_custom4' );
		}
        if ( isset( $_POST['sig_custom5'] ) ) {
			array_push( $columns, 'sig_custom5' );
		}
		if ( isset( $_POST['sig_custom6'] ) ) {
			array_push( $columns, 'sig_custom6' );
		}
		if ( isset( $_POST['sig_custom7'] ) ) {
			array_push( $columns, 'sig_custom7' );
		}
		if ( isset( $_POST['sig_message'] ) ) {
			array_push( $columns, 'sig_message' );
		}
		if ( isset( $_POST['sig_date'] ) ) {
			array_push( $columns, 'sig_date' );
		}

		return serialize( $columns );
	}
	
}

?>