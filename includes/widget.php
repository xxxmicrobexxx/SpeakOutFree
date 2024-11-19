<?php
// register widget
add_action( 'widgets_init', 'dk_speakout_register_widgets' );
function dk_speakout_register_widgets() {
	register_widget( 'dk_speakout_petition_widget' );
}

class dk_speakout_petition_widget extends WP_Widget {

	function __construct() {
    global $dk_speakout_version;
		$widget_ops = array(
			'classname'   => 'dk_speakout_widget',
			'description' => __( 'Display a petition form.', 'speakout' )
		);
		parent::__construct( 'dk_speakout_petition_widget', 'SpeakOut! Email Petitions', $widget_ops );

		// load widget scripts
		if ( ! is_admin() && is_active_widget( false, false, $this->id_base, true ) ) {

			// load the JavaScript
			wp_enqueue_script( 'dk_speakout_widget_js', plugins_url( 'speakout/js/widget.js' ), array( 'jquery' ),  $dk_speakout_version );

			// load the CSS theme
			$options = get_option( 'dk_speakout_options' );
			$theme   = $options['widget_theme'];

			 // load default theme
			if ( $theme === 'default' ) {
				wp_enqueue_style( 'dk_speakout_widget_css', plugins_url( 'speakout/css/widget.css' ), array(), $dk_speakout_version );
			}
			// attempt to load cusom theme (petition-widget.css)
			else {
				$parent_dir       = get_template_directory_uri();
				$parent_theme_url = $parent_dir . '/petition-widget.css';

				// if a child theme is in use
				// try to load style from child theme folder
				if ( is_child_theme() ) {
					$child_dir        = get_stylesheet_directory_uri();
					$child_theme_url  = $child_dir . '/petition-widget.css';
					$child_theme_path = STYLESHEETPATH . '/petition-widget.css';

					// use child theme if it exists
					if ( file_exists( $child_theme_path ) ) {
						wp_enqueue_style( 'dk_speakout_widget_css', $child_theme_url, array(), $dk_speakout_version );
					}
					// else try to load style from parent theme folder
					else {
						wp_enqueue_style( 'dk_speakout_widget_css', $parent_theme_url, array(), $dk_speakout_version );
					}
				}
				// if not using a child theme, just try to load style from active theme folder
				else {
					wp_enqueue_style( 'dk_speakout_widget_css', $parent_theme_url, array(), $dk_speakout_version );
				}
			}

			// set up AJAX callback script
			$protocol = isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://';
			$params   = array( 'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ) );
			wp_localize_script( 'dk_speakout_widget_js', 'dk_speakout_widget_js', $params );
		}
		
		
	}
	public function dk_speakout_petition_widget(){
			self :: __contruct();
	}

	// create widget form (admin)
	function form( $instance ) {
		include_once( 'class.petition.php' );
		$the_petition   = new dk_speakout_Petition();
		$options        = get_option( 'dk_speakout_options' );
		$defaults       = array( 'title' => __( 'Sign the Petition', 'speakout' ), 'call_to_action' => '', 'sharing_url' => '', 'petition_id' => 1 );
		$instance       = wp_parse_args( ( array ) $instance, $defaults );
		$title          = $instance['title'];
		$call_to_action = $instance['call_to_action'];
		$sharing_url    = $instance['sharing_url'];
		$petition_id    = $instance['petition_id'];

		// get petitions list to fill out select box
		$petitions = $the_petition->quicklist();
        

		// display the form (admin)
		echo '<p><label>' . __( 'Title', 'speakout' ) . ':</label><br /><input class="widefat" type="text" name="' . $this->get_field_name( 'title' ) . '" value="' . stripslashes( $instance['title'] ) . '"></p>';
		echo '<p><label>' . __( 'Sharing URL', 'speakout' ) . ':</label><br /><input class="widefat" type="text" name="' . $this->get_field_name( 'sharing_url' ) . '" value="' . stripslashes( $instance['sharing_url'] ) . '"></p>';
		echo '<p><label>' . __( 'Call to Action', 'speakout' ) . ':</label><br /><textarea maxlength="140" class="widefat" name="' . $this->get_field_name( 'call_to_action' ) . '">' . $instance['call_to_action'] . '</textarea></p>';
		echo '<p><label>' . __( 'Petition', 'speakout' ) . ':</label><br /><select class="widefat" name="' . $this->get_field_name( 'petition_id' ) . '">';
		foreach ( $petitions as $petition ) {
			$selected = ( $petition_id == $petition->id ) ? ' selected="selected"' : '';
			echo '<option value="' . $petition->id . '" ' . $selected . '>' . esc_html( $petition->title ) . '</option>';
		}
		echo '</select></p>';
	}

	// save the widget settings (admin)
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']          = strip_tags( $new_instance['title'] );
		$instance['sharing_url']    = strip_tags( $new_instance['sharing_url'] );
		$instance['call_to_action'] = strip_tags( $new_instance['call_to_action'] );
		$instance['petition_id']    = $new_instance['petition_id'];

		// register widget strings in WPML
		include_once( 'class.wpml.php' );
		$wpml = new dk_speakout_WPML();
		$wpml->register_widget( $instance );

		return $instance;
	}

	// display widget (public)
	function widget( $args, $instance ) {

		global $dk_speakout_version;

		include_once( 'class.speakout.php' );
		include_once( 'class.petition.php' );
		include_once( 'class.wpml.php' );
		$options  = get_option( 'dk_speakout_options' );
		$petition = new dk_speakout_Petition();
		$wpml     = new dk_speakout_WPML();
		extract( $args );

		// get widget data
		$instance       = $wpml->translate_widget( $instance );
		$title          = apply_filters( 'widget_title', $instance['title'] );
		$call_to_action = empty( $instance['call_to_action'] ) ? '&nbsp;' : $instance['call_to_action'];
		$petition->id   = empty( $instance['petition_id'] ) ? 1 : absint( $instance['petition_id'] );
		
		// Check if we have a form preslected
		if (array_key_exists('petition', $_GET)){
			$petition->id = isset( $_GET['petition'] ) ? abs( $_GET['petition'] ) : 1 ;
		}
		$get_petition   = $petition->retrieve( $petition->id );
		$wpml->translate_petition( $petition );
		$options = $wpml->translate_options( $options );

		// set up variables for widget display
		$userdata      = dk_speakout_SpeakOut::userinfo();
		$expired       = ( $petition->expires == '1' && current_time( 'timestamp' ) >= strtotime( $petition->expiration_date ) ) ? 1 : 0;
		$greeting      = ( $petition->greeting != '' && $petition->sends_email == 1 ) ? '<p><span class="dk-speakout-widget-greeting">' . $petition->greeting . '</span></p>' : '';
		$optin_default = ( $options['optin_default'] == 'checked' ) ? 'checked' : '';

		// get language value from URL if available (for WPML)
		$wpml_lang = '';
		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			$wpml_lang = ICL_LANGUAGE_CODE;
		}

		// check if petition exists...
		// if a petition has been deleted, but its widget still exists, don't try to display the form
		if ( $get_petition ) {
            
            //get the language
                list ( $lang ) = explode( '-', get_bloginfo( 'language' ) );

			// compose the petition widget and pop-up form
			$petition_widget = '<!-- SpeakOut! Email Petitions ' . $dk_speakout_version . ' : ' . ucfirst($lang) . ' -->';
			
			//prepare for required custom field
			if($petition->custom_field_required == 1){
                $custom_field_required = " REQUIRED ";
                $petition_widget .= '<p id="dk-speakout-widget-custom-required" style="display:none;" />';
    		}
    		else{
    		    $custom_field_required = "";
    		}
            if ( $petition->custom_field2_required == 1 ) {
                $custom_field2_required = " REQUIRED ";
                $petition_widget .= '<p id="dk-speakout-widget-custom2-required" style="display:none;" />';
            } else {
                $custom_field2_required = "";
            }
            if ( $petition->custom_field3_required == 1 ) {
                $custom_field3_required = " REQUIRED ";
                $petition_widget .= '<p id="dk-speakout-widget-custom3-required" style="display:none;" />';
            } else {
                $custom_field3_required = "";
            }
            if ( $petition->custom_field4_required == 1 ) {
                $custom_field4_required = " REQUIRED ";
                $petition_widget .= '<p id="dk-speakout-widget-custom4-required" style="display:none;" />';
            } else {
                $custom_field4_required = "";
            }
            if ( $petition->custom_field5_required == 1 ) {
                $custom_field5_required = " REQUIRED ";
                $petition_widget .= '<p id="dk-speakout-widget-custom5-required" style="display:none;" />';
            } else {
                $custom_field5_required = "";
            }
            
            
    			
			$petition_widget .='<div class="dk-speakout-widget-wrap">
					<h3>' . esc_html( $title ) . '</h3>
					<p>' . esc_html( $call_to_action ) . '</p>
					<div class="dk-speakout-widget-button-wrap">
						<a rel="dk-speakout-widget-popup-wrap-' . $petition->id . '" class="dk-speakout-widget-button"><span>' . $options['button_text'] . '</span></a>
					</div>';
			if ( $options['display_count'] == 1 ) {
			    //set goal text
			    $goal_text = ( $petition->goal != 0 ) ? ' = ' . round(( $petition->signatures  /  $petition->goal )* 100) . '% '. __( 'of goal', 'speakout' ):'';
			    
				$petition_widget .= '
					<div class="dk-speakout-widget-progress-wrap">
                        <div class="dk-speakout-widget-signature-count">';
                            $petition_widget .= '<span>' . number_format(  $petition->signatures, 0 , $options[ 'decimal_separator' ] , $options[ 'thousands_separator' ] ) . '</span> ' . __( 'signatures', 'speakout' ) . $goal_text;
                            $petition_widget .= '</div>';
						
    			// if our goal is greater than 0 show the progress bar			
    			if($petition->goal != 0){
    				$petition_widget .= 
    						'<br />0' .  dk_speakout_SpeakOut::progress_bar( $petition->goal, $petition->signatures, 150 ) . ' ' . $petition->goal;
    			}		 
				$petition_widget .= 
						 '</div>';
			}
			$petition_widget .= '
				</div>

				<div id="dk-speakout-widget-windowshade"></div>
				<div id="dk-speakout-widget-popup-wrap-' . $petition->id . '" class="dk-speakout-widget-popup-wrap">
					<h3>' . esc_html( $petition->title ) . '</h3>
					<div class="dk-speakout-widget-close"></div>';
            if($petition->display_petition_message == 1){ // are we displaying the message?
                 //to avoid clashing with other resources using parsedown
                if ( ! class_exists( 'Parsedown' ) ) {
                    include_once( 'parsedown.php' );
                }
                $Parsedown = new Parsedown();
                
                if ( $petition->is_editable == 1 ) {
                    $petition_widget .= '
                    <div class="dk-speakout-widget-message-wrap">
                    <p class="dk-speakout-greeting">' . $petition->greeting . '</p>
                    <textarea name="dk-speakout-widget-message" id="dk-speakout-widget-message-' . $petition->id . '" class="dk-speakout-widget-message">' .  wpautop( $Parsedown->text( esc_html( $petition->petition_message ) ) ) . '</textarea>
                    <p class="dk-speakout-caps">**' . __( 'your signature', 'speakout' ) . '**</p>';

                    //if there is a petition footer, show it as part of petition view
                    if( $petition->petition_footer != ''){
                        $petition_widget .= '<br><br>' .  esc_html( $petition->petition_footer ); 
                    }
                    $petition_widget .= '</div>';
                }
                else {
                    $petition_widget .= '
                    <div class="dk-speakout-widget-message-wrap">
                    <div class="dk-speakout-widget-message">
                    <p class="dk-speakout-greeting">' .  esc_html( $petition->greeting ) . '</p>
                    ' . wpautop( $Parsedown->text(  esc_html( $petition->petition_message ) ) ) . '
                    <p class="dk-speakout-caps">**' . __( 'your signature', 'speakout' ) . '**</p>';

                    //if there is a petition footer, show it as part of petition view
                    if( $petition->petition_footer != ''){
                        $petition_widget .= '<br><br>' .  esc_html( $petition->petition_footer ); 
                    }
                    $petition_widget .= '</div>
                    </div>';
                }
            }
			$petition_widget .= '
					<div class="dk-speakout-widget-form-wrap">
						<div class="dk-speakout-widget-response"></div>
						<form class="dk-speakout-widget-form">
							<input type="hidden" id="dk-speakout-widget-posttitle-' . $petition->id . '" value="' .  esc_html(  $petition->title ) .'" />
							<input type="hidden" id="dk-speakout-widget-shareurl-' . $petition->id . '" value="' .  esc_html(  $instance['sharing_url']  ) .'" />
							<input type="hidden" id="dk-speakout-widget-tweet-' . $petition->id . '" value="' . dk_speakout_SpeakOut::x_encode(  esc_html( $petition->x_message ) ) .'" />
							<input type="hidden" id="dk-speakout-widget-lang-' . $petition->id . '" value="' .  esc_html( $wpml_lang ) .'" />';

			if ( $expired ) {
				$petition_widget .= '
					<p><strong>' . $options['expiration_message'] . '</strong></p>
					<p>' . __( 'End date', 'speakout' ) . ': ' . date( 'M d, Y', strtotime( $petition->expiration_date ) ) . '</p>
					<p>' . __( 'Signatures collected', 'speakout' ) . ': ' . number_format(  $petition->signatures, 0 , $options[ 'decimal_separator' ] , $options[ 'thousands_separator' ] ) . '</p>';
				if ( $petition->goal != 0 ) {
					$petition_widget .= '<p><div class="dk-speakout-expired-goal"><span>' . __( 'Signature goal', 'speakout' ) . ':</span> ' .  esc_html( $petition->goal ) . '</p></div></form></div></div>';
				} 
				else {
					$petition_widget .= '</form></div></div>';
				}
			}
			else {
			    
			//display custom field at top
			if ( $petition->displays_custom_field == 1 && $petition->custom_field_location < 2 ) {
					$petition_widget .= '
						<div class="dk-speakout-widget-full">
								<input name="dk-speakout-widget-custom-field" id="dk-speakout-widget-custom-field-' . $petition->id . '" maxlength="400" type="text"  placeholder="' . esc_html( $petition->custom_field_label )   . '"' .  esc_html( $custom_field_required ) .' />
							</div>';
				}
                if ( $petition->displays_custom_field2 == 1 && $petition->custom_field2_location < 2 ) {
                    $petition_widget .= '
    						<div class="dk-speakout-widget-full">
    								<input name="dk-speakout-widget-custom-field2" id="dk-speakout-widget-custom-field2-' . $petition->id . '" maxlength="400" type="text"  placeholder="' . esc_html( $petition->custom_field2_label  ) . '"' .  esc_html( $custom_field2_required ) . ' />
    							</div>';
                }
                if ( $petition->displays_custom_field3 == 1 && $petition->custom_field3_location < 2 ) {
                    $petition_widget .= '
    						<div class="dk-speakout-widget-full">
    								<input name="dk-speakout-widget-custom-field3" id="dk-speakout-widget-custom3-field-' . $petition->id . '" maxlength="400" type="text"  placeholder="' . esc_html( $petition->custom_field3_label ) . '"' .  esc_html( $custom_field3_required ) . ' />
    							</div>';
                }
                if ( $petition->displays_custom_field4 == 1 && $petition->custom_field4_location < 2 ) {
                    $petition_widget .= '
    						<div class="dk-speakout-widget-full">
    								<input name="dk-speakout-widget-custom-field4" id="dk-speakout-widget-custom-field4-' . $petition->id . '" maxlength="400" type="text"  placeholder="' . esc_html( $petition->custom_field4_label ) . '"' .  esc_html( $custom_field4_required ). ' />
    							</div>';
                }
                if ( $petition->displays_custom_field5 == 1 && $petition->custom_field5_location < 2 ) {
                    $petition_widget .= '
    						<div class="dk-speakout-widget-full">
    						<select name="dk-speakout-widget-custom-field5" id="dk-speakout-widget-custom-widget-field5-' . $petition->id . '" >
    						<option value=">' .  esc_html( $petition->custom_field5_label ). '">' .  esc_html( $petition->custom_field5_label ) . '</option>';
    						$arrFieldValues = explode("|",$petition->custom_field5_values);
    						foreach($arrFieldValues as $fieldValue ){
    						    $petition_form .= '<option value="' .  esc_html( $fieldValue ) . '">' .  esc_html( $fieldValue ) . '</option>';
    						}
    					
    								
    				$petition_form .= '</select>
    				</div>';
                }

				
                        // do we display honorific?
                        if ( $options['display_honorific'] == 'enabled' ) {
                        $honorifics = "";
                            
                    // if custom honorifics file exists use that else fall back to included honorifics list
                    $custom_file_name = file_exists( plugin_dir_path( __DIR__ ) . "custom/honorifics.txt") ? plugin_dir_path( __DIR__ ) . "custom/honorifics.txt" : plugin_dir_path( __DIR__ ) . "includes/honorifics.txt";
                            // open honorifics list
                            if ( $file = fopen( $custom_file_name, "r" ) ) {
                                //loop through the file
                                while (!feof($file)) {
                                    // grab the custom title
                                    $theName = fgets($file);
                                    //build our string
                                    $honorifics .= '<option value="' .  esc_html( $theName ) . '">' .  esc_html( $theName ) . '</option>' . PHP_EOL;
                                }
                                
                                //close the file
                                fclose($file);
                            }
                            $petition_widget .='    <div class="dk-speakout-widget-full">
                                <select name="dk-speakout-honorific" id="dk-speakout-honorific-' . $petition->id . '">'
                                .  esc_html( $honorifics ) . 
                                '</select>
                                </div>';
                        }
				
				$petition_widget .= '
							<div class="dk-speakout-widget-full">
								<input name="dk-speakout-widget-first-name" id="dk-speakout-widget-first-name-' . $petition->id . '" value="' .  esc_html( $userdata['firstname'] ) . '" type="text" placeholder="'. __( 'First Name', 'speakout' ) .'" required   />
							</div>
							<div class="dk-speakout-widget-full">
				
								<input name="dk-speakout-widget-last-name" id="dk-speakout-widget-last-name-' . $petition->id . '" value="' .  esc_html( $userdata['lastname'] ) . '" type="text" placeholder="' . __( 'Last Name', 'speakout' ) . '" required />
							</div>';
							
				//display custom field in middle
				if ( $petition->displays_custom_field == 1 && $petition->custom_field_location == 2 ) {
					$petition_widget .= '
							<div class="dk-speakout-widget-full">
								<input name="dk-speakout-widget-custom-field" id="dk-speakout-widget-custom-field-' . $petition->id . '" maxlength="400" type="text"  placeholder="' . esc_html( $petition->custom_field_label )  . '"' .  esc_html( $custom_field_required ) .' />
							</div>';
				}
                
                if ( $petition->displays_custom_field2 == 1 && $petition->custom_field2_location == 2 ) {
                    $petition_widget .= '
    							<div class="dk-speakout-widget-full">
    								<input name="dk-speakout-widget-custom-field2" id="dk-speakout-widget-custom-field2-' . $petition->id . '" maxlength="400" type="text"  placeholder="' . esc_html( $petition->custom_field2_label ) . '"' .  esc_html( $custom_field2_required ) . ' />
    							</div>';
                }
                if ( $petition->displays_custom_field3 == 1 && $petition->custom_field3_location == 2 ) {
                    $petition_widget .= '
    							<div class="dk-speakout-widget-full">
    								<input name="dk-speakout-widget-custom-field3" id="dk-speakout-widget-custom-field3-' . $petition->id . '" maxlength="400" type="text"  placeholder="' . esc_html( $petition->custom_field3_label ) . '"' .  esc_html( $custom_field3_required ) . ' />
    							</div>';
                }
                if ( $petition->displays_custom_field4 == 1 && $petition->custom_field4_location == 2 ) {
                    $petition_widget .= '
    							<div class="dk-speakout-widget-full">
    								<input name="dk-speakout-widget-custom-field4" id="dk-speakout-widget-custom-field4-' . $petition->id . '" maxlength="400" type="text"  placeholder="' . esc_html( $petition->custom_field4_label ) . '"' .  esc_html( custom_field4_required ) . ' />
    							</div>';
                }
                if ( $petition->displays_custom_field5 == 1 && $petition->custom_field5_location == 2 ) {
                    $petition_widget .= '
    						<div class="dk-speakout-widget-full">
    						<select name="dk-speakout-widget-custom-field5" id="dk-speakout-widget-custom-field5-' . $petition->id . '" >
    						<option value=">' .  esc_html( $petition->custom_field5_label ) . '">' .  esc_html( $petition->custom_field5_label ) . '</option>';
    						$arrFieldValues = explode("|",$petition->custom_field5_values);
    						foreach($arrFieldValues as $fieldValue ){
    						    $petition_form .= '<option value="' .  esc_html( $fieldValue ) . '">' .  esc_html( $fieldValue ) . '</option>';
    						}
    					
    								
    				$petition_form .= '</select>
    				</div>';
                }
				// if only collecting signatures it is possible to hide email field.
                if ( $petition->hide_email_field != 1 ) {
                    $petition_widget .= '
							<div class="dk-speakout-widget-full">
								<input name="dk-speakout-widget-email" id="dk-speakout-widget-email-' . $petition->id . '" value="' .  esc_html( $userdata['email'] ). '" type="email"  placeholder="' . __( 'Email', 'speakout' ) . '" required />
							</div>';
                }
				if ( $petition->requires_confirmation ) {
					$petition_widget .= '
							<div class="dk-speakout-widget-full">
								<input name="dk-speakout-widget-email-confirm" id="dk-speakout-widget-email-confirm-' . $petition->id . '" value="" type="email"  placeholder="' . __( 'Confirm Email', 'speakout' ) . '" required  />
							</div>';
				}
				// set value of variable based on whether addresses are required
				$required="";
				if($petition_widget->address_required == 1){
				    $required = ' required '; 
				    $petition_widget .= '<input type="hidden" name="dk-address-required" id="dk-address-required" value="1" >';
				}
				
				if ( in_array( 'street', $petition->address_fields ) ) {
					$petition_widget .= '
							<div class="dk-speakout-widget-full">
								<input name="dk-speakout-widget-street" id="dk-speakout-widget-street-' . $petition->id . '" maxlength="200" type="text"   placeholder="' . __( 'Address', 'speakout' ) . '" ' .  esc_html( $required ) . '/>
							</div>';
				}
				if ( in_array( 'city', $petition->address_fields ) ) {
					$petition_widget .= '
							<div class="dk-speakout-widget-half">
								<input name="dk-speakout-widget-city" id="dk-speakout-widget-city-' . $petition->id . '" maxlength="200" type="text" placeholder="' . __( 'City', 'speakout' ) . '" ' . $required . ' />
							</div>';
				}
				if ( in_array( 'state', $petition->address_fields ) ) {
					$petition_widget .= '
							<div class="dk-speakout-widget-half">
								<input name="dk-speakout-widget-state" id="dk-speakout-widget-state-' . $petition->id . '" maxlength="200" type="text" list="dk-speakout-states"  placeholder="' . __( 'State / Province', 'speakout' ) . '" ' .  esc_html( $required ). ' />
							
							</div>';
				}
				if ( in_array( 'postcode', $petition->address_fields ) ) {
					$petition_widget .= '
							<div class="dk-speakout-widget-half">
								<input name="dk-speakout-widget-postcode" id="dk-speakout-widget-postcode-' . $petition->id . '" maxlength="200" type="text"  placeholder="' . __( 'Postal Code', 'speakout' ) . '" ' .  esc_html( $required ) . '/>
							</div>';
				}
					if ( in_array( 'country', $petition->address_fields ) ) {
    					$required = $petition->country_required == 1 ? " required='required'" : "";
                         $countries = "";
                        
                    // if custom country file exists use that else fall back to included country list
                    $custom_file_name = file_exists(plugin_dir_path( __DIR__ ) . "custom/countries.txt") ? plugin_dir_path( __DIR__ ) . "custom/countries.txt" : plugin_dir_path( __DIR__ ) . "includes/countries.txt";
                        
                            // open honorifics list
                            if ( $file = fopen($custom_file_name, "r")){
                                //loop through the file
                                while (!feof($file)) {
                                    // grab the custom title
                                    $theName = fgets($file);
                                    
                                    //country has two components so split them
                                    $arrCountry = explode("|", $theName);
                                    //build our string
                                    $countries .= '<option value="' .  esc_html( $arrCountry[0] ). '">' .  esc_html( $arrCountry[1] ). '</option>' . PHP_EOL;
                                }
                                
                                fclose($file);
                            }
                        
    					$petition_widget .= '
    							<div class="dk-speakout-half">
    								<select name="dk-speakout-country"   id="dk-speakout-country-' . $petition->id . '" ' .  esc_html( $required ). ' />
    								    <option value="">' . __( 'Country', 'speakout' ) . '</option>'
                                            .  $countries .
                            
  				                  '</select>
    							</div>';
    				}
				//display custom field at bottom
				if ( $petition->displays_custom_field == 1 && $petition->custom_field_location ==3 ) {
					$petition_widget .= '
							<div class="dk-speakout-widget-full">
								<input name="dk-speakout-widget-custom-field" id="dk-speakout-widget-custom-field-' . $petition->id . '" maxlength="400" type="text"  placeholder="' .  esc_html( $petition->custom_field_label )  . '"' .  esc_html( $custom_field_required ) .' />
							</div>';
				}
                if ( $petition->displays_custom_field2 == 1 && $petition->custom_field2_location == 3 ) {
                    $petition_form .= '
    							<div class="dk-speakout-widget-full">
    								<input name="dk-speakout-widget-custom-field2" id="dk-speakout-widget-custom-field2-' . $petition->id . '" maxlength="400" type="text"  placeholder="' .  esc_html( $petition->custom_field2_label ) . '"' .  esc_html( $custom_field2_required ) . ' />
    							</div>';
                }
                if ( $petition->displays_custom_field3 == 1 && $petition->custom_field3_location == 3 ) {
                    $petition_form .= '
    							<div class="dk-speakout-widget-full">
    								<input name="dk-speakout-widget-custom-field3" id="dk-speakout-widget-custom-field3-' . $petition->id . '" maxlength="400" type="text"  placeholder="' . esc_html( $petition->custom_field3_label ) . '"' .  esc_html( $custom_field3_required ) . ' />
    							</div>';
                }
                if ( $petition->displays_custom_field4 == 1 && $petition->custom_field4_location == 3 ) {
                    $petition_form .= '
    							<div class="dk-speakout-widget-full">
    								<input name="dk-speakout-widget-custom-field4" id="dk-speakout-widget-custom-field4-' . $petition->id . '" maxlength="400" type="text"  placeholder="' . esc_html( $petition->custom_field4_label ) . '"' .  esc_html( $custom_field4_required ) . ' />
    							</div>';
                }
                if ( $petition->displays_custom_field5 == 1 && $petition->custom_field5_location == 3 ) {
                    $petition_form .= '
    						<div class="dk-speakout-widget-full">
    						<select name="dk-speakout-widget-custom-field5" id="dk-speakout-widget-custom-field5-' . $petition->id . '" >
    						<option value=">' .  esc_html( $petition->custom_field5_label ) . '">' .  esc_html( $petition->custom_field5_label ) . '</option>';
    						$arrFieldValues = explode("|",$petition->custom_field5_values);
    						foreach($arrFieldValues as $fieldValue ){
    						    $petition_form .= '<option value="' .  esc_html( $fieldValue ). '">' .  esc_html( $fieldValue ) . '</option>';
    						}
    					
    								
    				$petition_form .= '</select>
    				</div>';
                }
                
				if( $petition->displays_optin == 1 ) {
					$optin_default = ( $options['optin_default'] == 'checked' ) ? ' checked="checked"' : '';
					$petition_widget .= '
							<div class="dk-speakout-widget-optin-wrap">
								<input type="checkbox" name="dk-speakout-widget-optin" id="dk-speakout-widget-optin-' . $petition->id . '"' . $optin_default . ' />
								<label for="dk-speakout-widget-optin-' . $petition->id . '">' . esc_html( $petition->optin_label ) . '</label>
							</div>';
				}
				
					if ( $options['display_bcc'] == 'enabled' ) {
					    $petition_widget .= '
								<div class="dk-speakout-widget-bcc-wrap">
								<input type="checkbox" name="dk-speakout-widget-bcc" id="dk-speakout-widget-bcc-' . $petition->id . '" CHECKED  />
								<label for="dk-speakout-widget--widgetbcc-' . $petition->id . '">' . __( 'BCC yourself', 'speakout' ) . '</label>
							</div>';
					}
                
                    				// if publicly anonymous allowed, display option
    				if ( $petition->allow_anonymous == 1) {
    				    $petition_widget .= '
    							<div class="dk-speakout-widget-anonymise-wrap">
    								<div class="dk-speakout-widget-options-checkbox">
    								    <input type="checkbox" name="dk-speakout-widget-anonymise" id="dk-speakout-widget-anonymise-' . $petition->id . '" value="1" />
    								    <label for="dk-speakout-widget-anonymise-' . $petition->id . '" class="dk-speakout-options">' . __( 'Hide name from public', 'speakout' ) . ' </label>
    								
    							    </div>
    							</div>';
    				}
                
					if ( $options['display_privacypolicy'] == 'enabled' ) {
				$petition_widget .= '
								<div class="dk-speakout-widget-privacypolicy-wrap">
								<div><input type="checkbox" name="dk-speakout-widget-privacypolicy" id="dk-speakout-widget-privacypolicy" class="required"  />
								<label for="dk-speakout-widget-privacypolicy-' . $petition->id . ' " class="required">' . __( 'Yes, I accept your ', 'speakout') . '<a href="' . esc_url( $options['privacypolicy_url'] ) . '" target="_blank">' . __( 'privacy policy', 'speakout') . '</a></label></div>
							</div>';
				}	
					
					
					
					
				$petition_widget .= '
							<div class="dk-speakout-widget-submit-wrap">
								<div id="dk-speakout-widget-ajaxloader-' . $petition->id . '" class="dk-speakout-widget-ajaxloader" style="visibility: hidden;">&nbsp;</div>
								<a name="' . $petition->id . '" class="dk-speakout-widget-submit"><span>' . esc_html( $options['button_text'] ) . '</span></a>
							</div>
						</form>
						<div class="dk-speakout-widget-share">
							<p><strong>' . esc_html( $options['share_message'] ) . '</strong></p>
							<p>
							<a class="dk-speakout-widget-facebook" href="#" title="Facebook"><span></span></a>
							<a class="dk-speakout-email"  target="_blank" href="mailto:?subject=Petition: ' . esc_html( $petition->title ) .'&amp;body=Hi there, I want to share this petition titled %22' .esc_html( $petition->title )  . '%22 with you: https://'  .  $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] .  '" title="Share by Email"><span>&nbsp;</span></a>
                            <a class="dk-speakout-widget-x" href="#" title="x"><span></span></a>
							</p>
							<div class="dk-speakout-clear"></div>
						</div>
					</div>
				</div>';
			}

			echo $petition_widget;
		}
	}

}

?>