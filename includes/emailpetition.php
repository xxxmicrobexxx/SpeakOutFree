<?php

// register shortcode to display signatures count 
add_shortcode( 'signaturecount', 'dk_speakout_signaturescount_shortcode' );

function dk_speakout_signaturescount_shortcode( $attr ) {
    include_once( 'class.petition.php' );
    $petition = new dk_speakout_Petition();
    $id = 1; // default
    if ( isset( $attr[ 'id' ] ) && is_numeric( $attr[ 'id' ] ) ) {
        $id = $attr[ 'id' ];
    }

    $petition_exists = $petition->retrieve( $id );
    if ( $petition_exists ) {
        return "<span class='signatureCount'>" . $petition->signatures . "</span>";
    } else {
        return '';
    }
}

// register shortcode to display signatures goal
add_shortcode( 'signaturegoal', 'dk_speakout_signaturesgoal_shortcode' );

function dk_speakout_signaturesgoal_shortcode( $attr ) {
    include_once( 'class.petition.php' );
    $petition = new dk_speakout_Petition();

    $id = 1; // default
    if ( isset( $attr[ 'id' ] ) && is_numeric( $attr[ 'id' ] ) ) {
        $id = $attr[ 'id' ];
    }

    $petition_exists = $petition->retrieve( $id );
    if ( $petition_exists ) {
        return "<span class='signatureGoal'>" . $petition->goal . "</span>";
    } else {
        return '';
    }
}

// register shortcode to display petition title
add_shortcode( 'petitiontitle', 'dk_speakout_petitiontitle_shortcode' );

function dk_speakout_petitiontitle_shortcode( $attr ) {

    // Check if we have a form preslected
    if ( array_key_exists( 'petition', $_GET ) ) {
        $attr[ 'id' ] = $_GET[ 'petition' ];
    }


    include_once( 'class.petition.php' );
    $petition = new dk_speakout_Petition();

    $id = 1; // default
    if ( isset( $attr[ 'id' ] ) && is_numeric( $attr[ 'id' ] ) ) {
        $id = $attr[ 'id' ];
    }

    $petition_exists = $petition->retrieve( $id );
    if ( $petition_exists ) {
        return "<span class='petitionTitle'>" . $petition->title . "</span>";
    } else {
        return '';
    }
}

// register shortcode to display petition message
add_shortcode( 'petitionmessage', 'dk_speakout_petitionmessage_shortcode' );

function dk_speakout_petitionmessage_shortcode( $attr ) {

    // Check if we have a form preslected
    if ( array_key_exists( 'petition', $_GET ) ) {
        $attr[ 'id' ] = $_GET[ 'petition' ];
    }


    include_once( 'class.petition.php' );
    $petition = new dk_speakout_Petition();

    $id = 1; // default
    if ( isset( $attr[ 'id' ] ) && is_numeric( $attr[ 'id' ] ) ) {
        $id = $attr[ 'id' ];
    }

    $petition_exists = $petition->retrieve( $id );
    if ( $petition_exists ) {
         //to avoid clashing with other resources using parsedown
        if ( !class_exists( 'Parsedown' ) ) {
            include_once( 'parsedown.php' );
        }
        $Parsedown = new Parsedown();
        return "<span class='petitionMessage'>" . $Parsedown->text($petition->petition_message) . "</span>";
    } else {
        return '';
    }
}

// register shortcode to display petition form
add_shortcode( 'emailpetition', 'dk_speakout_emailpetition_shortcode' );

function dk_speakout_emailpetition_shortcode( $attr ) {

    // Check if we have a form preslected
    if ( array_key_exists( 'petition', $_GET ) ) {
        $attr[ 'id' ] = $_GET[ 'petition' ];
    }

    // only query a petition if the "id" attribute has been set
    if ( isset( $attr[ 'id' ] ) && is_numeric( $attr[ 'id' ] ) ) {

        global $dk_speakout_version;
        include_once( 'class.speakout.php' );
        include_once( 'class.petition.php' );
        include_once( 'class.wpml.php' );
        $petition = new dk_speakout_Petition();
        $wpml = new dk_speakout_WPML();
        $options = get_option( 'dk_speakout_options' );
        
        // get petition data from database
        $id = absint( $attr[ 'id' ] );
        $petition_exists = $petition->retrieve( $id );

        // attempt to translate with WPML
        $wpml->translate_petition( $petition );
        $options = $wpml->translate_options( $options );
        $wpml_lang = defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : '';

        if ( $petition_exists ) {

            $expired = ( $petition->expires == 1 && current_time( 'timestamp' ) >= strtotime( $petition->expiration_date ) ) ? 1 : 0;

            // shortcode attributes
            $width = isset( $attr[ 'width' ] ) ? 'style="width: ' . $attr[ 'width' ] . ';"': '';
            $height = isset( $attr[ 'height' ] ) ? 'style="height: ' . $attr[ 'height' ] . ' !important;"': '';
            $css_classes = isset( $attr[ 'class' ] ) ? $css_classes = $attr[ 'class' ] : '';
            $progress_width = ( $options[ 'petition_theme' ] == 'basic' ) ? 300 : 200; // defaults
            $progress_width = isset( $attr[ 'progresswidth' ] ) ? $attr[ 'progresswidth' ] : $progress_width;
            
            // Function to detect potential JavaScript in shorcode
            function contains_js($input) {
                $pattern = '/(javascript|on\w+|<script|<\/script|<\?|<\w+[^>]*\s*on\w+\s*=\s*["\']?[^>"\']+["\']?)/i';
                return preg_match($pattern, $input);
            }

            // Validate and sanitize shortcode attributes
            if (isset( $attr['width']) && contains_js($attr['width']) || 
                isset( $attr['height']) && contains_js($attr['height']) || 
                isset( $attr['css_classes']) && contains_js($attr['css_classes']  ) || 
                isset( $attr['progresswidth']) && contains_js($attr['progresswidth'])) {
                // Handle potential injection attempt
                die('Invalid javascript detected in shortcode');
            }

            if ( !$expired ) {
                $userdata = dk_speakout_SpeakOut::userinfo();

                // compose the petition form
                $petitionReadTitle = $petition->is_editable ? $petition->open_editable_message_button : $petition->open_message_button;

                $petition_form = "";
                //if we are displaying recaptcha include javascript			
                if ( isset( $options[ 'g_recaptcha_status' ] ) && $options[ 'g_recaptcha_status' ] == "on" ) {
                    //render based on version 2 or 3
                    if ( $options[ 'g_recaptcha_version' ] == 2 || $options[ 'g_recaptcha_version' ] == 0 ) {
                        $petition_form = '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
                    } elseif ( $options[ 'g_recaptcha_version' ] == 3 ) {
                        $petition_form = '<script src="https://www.google.com/recaptcha/api.js?render=' . $options[ 'g_recaptcha_site_key' ] . '"></script>';
                        $petition_form .= "<input type='hidden' id='dk-speakout-recaptcha-key' value='" . $options[ 'g_recaptcha_site_key' ] . "'>";
                    }
                }
                
                //if we are displaying hCaptcha include javascript			
                if ( isset( $options[ 'hcaptcha_status' ] ) && $options[ 'hcaptcha_status' ] == "on" ) {
                        $petition_form = '<script src="https://js.hcaptcha.com/1/api.js" async defer></script>';
                }

                //get the language
                list( $lang ) = explode( '-', get_bloginfo( 'language' ) );
                $showProw = get_option( "dk_speakout_license_key_verified") == 1 ? " Pro " : "";

                $petition_form .= '<!-- SpeakOut! Email Petitions ' . $dk_speakout_version . $showProw .' : ' . ucfirst( $lang ) . ' -->' . "\n";

                //prepare for required custom fields
                if ( $petition->custom_field_required == 1 ) {
                    $custom_field_required = " REQUIRED ";
                    $petition_form .= '<p id="dk-speakout-custom-required" style="display:none;" />';
                } else {
                    $custom_field_required = "";
                }
                
                if ( $petition->custom_field2_required == 1 ) {
                    $custom_field2_required = " REQUIRED ";
                    $petition_form .= '<p id="dk-speakout-custom2-required" style="display:none;" />';
                } else {
                    $custom_field2_required = "";
                }
                if ( $petition->custom_field3_required == 1 ) {
                    $custom_field3_required = " REQUIRED ";
                    $petition_form .= '<p id="dk-speakout-custom3-required" style="display:none;" />';
                } else {
                    $custom_field3_required = "";
                }
                if ( $petition->custom_field4_required == 1 ) {
                    $custom_field4_required = " REQUIRED ";
                    $petition_form .= '<p id="dk-speakout-custom4-required" style="display:none;" />';
                } else {
                    $custom_field4_required = "";
                }
                if ( $petition->custom_field5_required == 1 ) {
                    $custom_field5_required = ' required="required" ';
                    $petition_form .= '<p id="dk-speakout-custom5-required" style="display:none;" />';
                } else {
                    $custom_field5_required = "";
                }
                if ( $petition->custom_field6_required == 1 ) {
                    $custom_field6_required = " REQUIRED ";
                    $petition_form .= '<p id="dk-speakout-custom6-required" style="display:none;" />';
                } else {
                    $custom_field6_required = "";
                }
                if ( $petition->custom_field7_required == 1 ) {
                    $custom_field7_required = " REQUIRED ";
                    $petition_form .= '<p id="dk-speakout-custom7-required" style="display:none;" />';
                } else {
                    $custom_field7_required = "";
                }
                


                $petition_form .= '<div id="dk-speakout-windowshade"></div>
					<div class="dk-speakout-petition-wrap ' . $css_classes . '" id="dk-speakout-petition-' . $petition->id . '" ' . $width . '>
						<h3>' . stripslashes( esc_html( $petition->title ) ) . '</h3>';

                //display petition message (or not)
                if ( $petition->display_petition_message == 1 ) {
                    $petition_form .= '<a id="dk-speakout-readme-' . $petition->id . '" class="dk-speakout-readme" rel="' . $petition->id . '" style="display: none;"><span>' . __( $petitionReadTitle, 'speakout' ) . '</span></a>';
                }

                $petition_form .= '<div id="dk-speakout-form-wrap">
    				            <form class="dk-speakout-petition">';
                $petition_form .= '<input type="hidden" id="dk-speakout-posttitle-' . $petition->id . '" value="' . esc_attr( urlencode( stripslashes( $petition->title ) ) ) . '" />' . "\n";
                $petition_form .= '<input type="hidden" id="dk-speakout-tweet-' . $petition->id . '" value="' . dk_speakout_SpeakOut::x_encode( $petition->x_message ) . '" />' . "\n";
                // show language in HTML comment for support
                $petition_form .= '<input type="hidden" id="dk-speakout-lang-' . $petition->id . '" value="' . $wpml_lang . '" />' . "\n";
                $petition_form .= '<input type="hidden" id="dk-speakout-textval-' . $petition->id . '" value="val" />' . "\n";
                // Is petition fading out?
                $petition_form .= '<input type="hidden" id="dk-speakout-petition-fade-' . $petition->id . '" value="' . $options[ "petition_fade" ] . '" />' . "\n";
                // Are we confirming signatures?	
                $petition_form .= '<input type="hidden" id="dk-speakout-requires_confirmation-' . $petition->id . '" value="' . $petition->requires_confirmation . '" />' . "\n";
                // Are we collecting email address?
                $petition_form .= '<input type="hidden" id="dk-speakout-hide-email-field-' . $petition->id . '" value="' . $petition->hide_email_field . '" />' . "\n";

                //prepare for redirect URL
                if ( $petition->redirect_url_option == 1 && $petition->redirect_url > "" ) {
                    $petition_form .= '<input type="hidden" id="dk-speakout-url-target-' . $petition->id . '" value="' . $petition->url_target . '" />' . "\n";
                    $petition_form .= '<input type="hidden" id="dk-speakout-redirect-url-' . $petition->id . '" value="' . $petition->redirect_url . '" />' . "\n";
                    $petition_form .= '<input type="hidden" id="dk-speakout-redirect-delay-' . $petition->id . '" value="' . $petition->redirect_delay . '" />' . "\n";
                }

                //display custom fields at top
                if ( $petition->displays_custom_field == 1 && $petition->custom_field_location < 2 ) {
                    $petition_form .= '
    						<div class="dk-speakout-full">
    								<input name="dk-speakout-custom-field" id="dk-speakout-custom-field-' . $petition->id . '" maxlength="400" type="text"  placeholder="' . stripslashes( esc_html( $petition->custom_field_label ) ) . '"' . $custom_field_required . ' />
    							</div>';
                }
                if ( $petition->displays_custom_field2 == 1 && $petition->custom_field2_location < 2 ) {
                    $petition_form .= '
    						<div class="dk-speakout-full">
    								<input name="dk-speakout-custom-field2" id="dk-speakout-custom-field2-' . $petition->id . '" maxlength="400" type="text"  placeholder="' . stripslashes( esc_html( $petition->custom_field2_label ) ) . '"' . $custom_field2_required . ' />
    							</div>';
                }
                if ( $petition->displays_custom_field3 == 1 && $petition->custom_field3_location < 2 ) {
                    $petition_form .= '
    						<div class="dk-speakout-full">
    								<input name="dk-speakout-custom-field3" id="dk-speakout-custom-field3-' . $petition->id . '" maxlength="400" type="text"  placeholder="' . stripslashes( esc_html( $petition->custom_field3_label ) ) . '"' . $custom_field3_required . ' />
    							</div>';
                }
                if ( $petition->displays_custom_field4 == 1 && $petition->custom_field4_location < 2 ) {
                    $petition_form .= '
    						<div class="dk-speakout-full">
    								<input name="dk-speakout-custom-field4" id="dk-speakout-custom-field4-' . $petition->id . '" maxlength="400" type="text"  placeholder="' . stripslashes( esc_html( $petition->custom_field4_label ) ) . '"' . $custom_field4_required . ' />
    							</div>';
                }
                if ( $petition->displays_custom_field5 == 1 && $petition->custom_field5_location < 2 ) {
                    $petition_form .= '
    						<div class="dk-speakout-full">
    						<select name="dk-speakout-custom-field5" id="dk-speakout-custom-field5-' . $petition->id . '"' . $custom_field5_required . ' >
    						<option value="">' . $petition->custom_field5_label . '</option>';
    						$arrFieldValues = explode("|",$petition->custom_field5_values);
    						foreach($arrFieldValues as $fieldValue ){
    						    $petition_form .= '<option value="' . $fieldValue . '">' . $fieldValue . '</option>';
    						}
    					
    								
    				$petition_form .= '</select>
    				</div>';
                }
                if ( $petition->displays_custom_field6 == 1 && $petition->custom_field6_location < 2 ) {
                    $petition_form .= '
    						<div class="dk-speakout-full">
    								<input name="dk-speakout-custom-field6" id="dk-speakout-custom-field6-' . $petition->id . '" type="checkbox" ' . $custom_field6_required . ' /> <label for "dk-speakout-custom-field6">' . stripslashes( $petition->custom_field6_label ) . '</label>
    							</div>';
                }
                
                if ( $petition->displays_custom_field7 == 1 && $petition->custom_field7_location < 2 ) {
                    $petition_form .= '
    						<div class="dk-speakout-full">
    								<input name="dk-speakout-custom-field7" id="dk-speakout-custom-field7-' . $petition->id . '" type="checkbox" ' . $custom_field7_required . ' /> <label for "dk-speakout-custom-field7">' .stripslashes( $petition->custom_field7_label ) . '</label>
    							</div>';
                }
                // do we display honorific?
                if ( $options[ 'display_honorific' ] == 'enabled' ) {
                    $honorifics = "";
                    

                    // if custom honorifics file exists use that else fall back to included honorifics list
                    $custom_file_name = file_exists( plugin_dir_path( __DIR__ ) . "custom/honorifics.txt") ? plugin_dir_path( __DIR__ ) . "custom/honorifics.txt" : plugin_dir_path( __DIR__ ) . "includes/honorifics.txt";
                    // open honorifics list
                    if ( $file = fopen( $custom_file_name, "r" ) ) {
                        //loop through the file
                        while ( !feof( $file ) ) {
                            // grab the custom title
                            $theName = fgets( $file );
                            //build our string but leave out blank lines
                            if($theName > ""){
                                $honorifics .= '<option value="' . $theName . '">' . $theName . '</option>' . PHP_EOL;
                            }
                        }

                        //close the file
                        fclose( $file );
                    }
                    $petition_form .= '    <div class="dk-speakout-full">
                                <select name="dk-speakout-honorific" id="dk-speakout-honorific-' . $petition->id . '">'
                    . $honorifics .
                    '</select>
                                </div>';
                }

                $petition_form .= '	<div class="dk-speakout-full">
    								<input autocomplete="given-name" name="dk-speakout-first-name" id="dk-speakout-first-name-' . $petition->id . '" value="' . $userdata[ 'firstname' ] . '" type="text" placeholder="' . __( 'First Name', 'speakout' ) . '" required="required"  />
    							</div>
    							
    							<div class="dk-speakout-full">
    								<input autocomplete="family-name" name="dk-speakout-last-name" id="dk-speakout-last-name-' . $petition->id . '" value="' . $userdata[ 'lastname' ] . '" type="text" placeholder="' . __( 'Last Name', 'speakout' ) . '" required="required"  />
    							</div>';

                //display custom fields in middle
                if ( $petition->displays_custom_field == 1 && $petition->custom_field_location == 2 ) {
                    $petition_form .= '
    							<div class="dk-speakout-full">
    								<input name="dk-speakout-custom-field" id="dk-speakout-custom-field-' . $petition->id . '" maxlength="400" type="text"  placeholder="' . stripslashes( esc_html( $petition->custom_field_label ) ) . '"' . $custom_field_required . ' />
    							</div>';
                }
                if ( $petition->displays_custom_field2 == 1 && $petition->custom_field2_location == 2 ) {
                    $petition_form .= '
    							<div class="dk-speakout-full">
    								<input name="dk-speakout-custom-field2" id="dk-speakout-custom-field2-' . $petition->id . '" maxlength="400" type="text"  placeholder="' . stripslashes( esc_html( $petition->custom_field2_label ) ) . '"' . $custom_field2_required . ' />
    							</div>';
                }
                if ( $petition->displays_custom_field3 == 1 && $petition->custom_field3_location == 2 ) {
                    $petition_form .= '
    							<div class="dk-speakout-full">
    								<input name="dk-speakout-custom-field3" id="dk-speakout-custom-field3-' . $petition->id . '" maxlength="400" type="text"  placeholder="' . stripslashes( esc_html( $petition->custom_field3_label ) ) . '"' . $custom_field3_required . ' />
    							</div>';
                }
                if ( $petition->displays_custom_field4 == 1 && $petition->custom_field4_location == 2 ) {
                    $petition_form .= '
    							<div class="dk-speakout-full">
    								<input name="dk-speakout-custom-field4" id="dk-speakout-custom-field4-' . $petition->id . '" maxlength="400" type="text"  placeholder="' . stripslashes( esc_html( $petition->custom_field4_label ) ) . '"' . $custom_field4_required . ' />
    							</div>';
                }
                if ( $petition->displays_custom_field5 == 1 && $petition->custom_field5_location == 2 ) {
                    $petition_form .= '
    						<div class="dk-speakout-full">
    						<select name="dk-speakout-custom-field5" id="dk-speakout-custom-field5-' . $petition->id . '"' . $custom_field5_required . ' >
    						<option value="">' . $petition->custom_field5_label . '</option>';
    						$arrFieldValues = explode("|",$petition->custom_field5_values);
    						foreach($arrFieldValues as $fieldValue ){
    						    $petition_form .= '<option value="' . $fieldValue . '">' . $fieldValue . '</option>';
    						}
    					
    								
    				$petition_form .= '</select>
    				</div>';
                }
                if ( $petition->displays_custom_field6 == 1 && $petition->custom_field6_location == 2 ) {
                    $petition_form .= '
    						<div class="dk-speakout-full">
    								<input name="dk-speakout-custom-field6" id="dk-speakout-custom-field6-' . $petition->id . '" type="checkbox" ' . $custom_field6_required . ' /> <label for "dk-speakout-custom-field6">' .stripslashes( $petition->custom_field6_label ) . '</label>
    							</div>';
                }
                
                if ( $petition->displays_custom_field7 == 1 && $petition->custom_field7_location == 2 ) {
                    $petition_form .= '
    						<div class="dk-speakout-full">
    								<input name="dk-speakout-custom-field7" id="dk-speakout-custom-field7-' . $petition->id . '" type="checkbox" ' . $custom_field7_required . ' /> <label for "dk-speakout-custom-field7">' .stripslashes( $petition->custom_field7_label ) . '</label>
    							</div>';
                }

                // if only collecting signatures it is possible to hide email field.
                if ( $petition->hide_email_field != 1 ) {
                    $petition_form .= '<div class="dk-speakout-full">
    								<input autocomplete="email" name="dk-speakout-email" id="dk-speakout-email-' . $petition->id . '" value="' . $userdata[ 'email' ] . '" type="email"  placeholder="' . __( 'Email', 'speakout' ) . '" required="required"  />
    							</div>';
                }

                if ( in_array( 'street', $petition->address_fields ) ) {
                    $required = $petition->street_required == 1 ? " required='required' " : "";
                    $petition_form .= '
    							<div class="dk-speakout-full">
    								<input  autocomplete="address-line1" name="dk-speakout-street" id="dk-speakout-street-' . $petition->id . '" maxlength="200" type="text"  placeholder="' . __( 'Street', 'speakout' ) . '" ' . $required . ' />
    							</div>';
                }
                $petition_form .= '<div>'; // need this div to give half-width fields a new parent - so we can style their margins differently by :nth-child

                // option allows for EU postal code position before city
                if ( in_array( 'postcode', $petition->address_fields ) && $options[ 'eu_postalcode' ] == 'enabled' ) {
                    $required = $petition->postcode_required == 1 ? ' required="required" ' : '';
                    $petition_form .= '
    							<div class="dk-speakout-half">
    								<input  autocomplete="postal-code" name="dk-speakout-postcode" id="dk-speakout-postcode-' . $petition->id . '" maxlength="200" type="text"  placeholder="' . __( 'Postal Code', 'speakout' ) . '" ' . $required . '/>
    							</div>';
                }
                if ( in_array( 'city', $petition->address_fields ) ) {
                    $required = $petition->city_required == 1 ? ' required="required" ' : '';
                    $petition_form .= '
    							<div class="dk-speakout-half">
    								<input  autocomplete="address-level2" name="dk-speakout-city" id="dk-speakout-city-' . $petition->id . '" maxlength="200" type="text" placeholder="' . __( 'City', 'speakout' ) . '" ' . $required . ' />
    							</div>';
                }
                if ( in_array( 'state', $petition->address_fields ) ) {
                    $required = $petition->state_required == 1 ? ' required="required" ' : '';
                    $petition_form .= '
    							<div class="dk-speakout-half">
    								<input  autocomplete="address-level1" name="dk-speakout-state" id="dk-speakout-state-' . $petition->id . '" maxlength="200" type="text" list="dk-speakout-states"  placeholder="' . __( 'State / Province', 'speakout' ) . '" ' . $required . ' />
    							
    							</div>';
                }
                // non EU postal code position
                if ( in_array( 'postcode', $petition->address_fields ) && $options[ 'eu_postalcode' ] != 'enabled' ) {
                    $required = $petition->postcode_required == 1 ? ' required="required" ' : '';
                    $petition_form .= '
    							<div class="dk-speakout-half">
    								<input  autocomplete="postal-code" name="dk-speakout-postcode" id="dk-speakout-postcode-' . $petition->id . '" maxlength="200" type="text"  placeholder="' . __( 'Postal Code', 'speakout' ) . '" ' . $required . '/>
    							</div>';
                }

                if ( in_array( 'country', $petition->address_fields ) ) {
                    $required = $petition->country_required == 1 ? ' required="required" ' : '';
                    $countries = "";
                    
                    // if custom country file exists use that else fall back to included country list
                    $custom_file_name = file_exists(plugin_dir_path( __DIR__ ) . "custom/countries.txt") ? plugin_dir_path( __DIR__ ) . "custom/countries.txt" : plugin_dir_path( __DIR__ ) . "includes/countries.txt";
                    
                    // open coutires list
                    if ( $file = fopen( $custom_file_name, "r" ) ) {
                        //loop through the file
                        while ( !feof( $file ) ) {
                            // grab the custom title
                            $theName = fgets( $file );

                            if($theName > ""){
                                //country has two components so split them
                                $arrCountry = explode( "|", $theName );
                                //build our string
                                $countries .= '<option value="' . $arrCountry[ 0 ] . '">' . $arrCountry[ 1 ] . '</option>' . PHP_EOL;
                            }
                        }

                        fclose( $file );
                    }

                    $petition_form .= '
    							<div class="dk-speakout-half">
    								<select name="dk-speakout-country"   id="dk-speakout-country-' . $petition->id . '" ' . $required . ' />
    								    <option value="">' . __( 'Country', 'speakout' ) . '</option>'
                    . $countries .

                    '</select>
    							</div>';
                }
                //display custom fields at bottom
                if ( $petition->displays_custom_field == 1 && $petition->custom_field_location == 3 ) {
                    $petition_form .= '
    							<div class="dk-speakout-full">
    								<input name="dk-speakout-custom-field" id="dk-speakout-custom-field-' . $petition->id . '" maxlength="400" type="text"  placeholder="' . stripslashes( esc_html( $petition->custom_field_label ) ) . '"' . $custom_field_required . ' />
    							</div>';
                }
                if ( $petition->displays_custom_field2 == 1 && $petition->custom_field2_location == 3 ) {
                    $petition_form .= '
    							<div class="dk-speakout-full">
    								<input name="dk-speakout-custom-field2" id="dk-speakout-custom-field2-' . $petition->id . '" maxlength="400" type="text"  placeholder="' . stripslashes( esc_html( $petition->custom_field2_label ) ) . '"' . $custom_field2_required . ' />
    							</div>';
                }
                if ( $petition->displays_custom_field3 == 1 && $petition->custom_field3_location == 3 ) {
                    $petition_form .= '
    							<div class="dk-speakout-full">
    								<input name="dk-speakout-custom-field3" id="dk-speakout-custom-field3-' . $petition->id . '" maxlength="400" type="text"  placeholder="' . stripslashes( esc_html( $petition->custom_field3_label ) ) . '"' . $custom_field3_required . ' />
    							</div>';
                }
                if ( $petition->displays_custom_field4 == 1 && $petition->custom_field4_location == 3 ) {
                    $petition_form .= '
    							<div class="dk-speakout-full">
    								<input name="dk-speakout-custom-field4" id="dk-speakout-custom-field4-' . $petition->id . '" maxlength="400" type="text"  placeholder="' . stripslashes( esc_html( $petition->custom_field4_label ) ) . '"' . $custom_field4_required . ' />
    							</div>';
                }
                if ( $petition->displays_custom_field5 == 1 && $petition->custom_field5_location == 3 ) {
                    $petition_form .= '
    						<div class="dk-speakout-full">
    						<select name="dk-speakout-custom-field5" id="dk-speakout-custom-field5-' . $petition->id . '"' . $custom_field5_required . ' >
    						<option value="">' . $petition->custom_field5_label . '</option>';
    						$arrFieldValues = explode("|",$petition->custom_field5_values);
    						foreach($arrFieldValues as $fieldValue ){
    						    $petition_form .= '<option value="' . $fieldValue . '">' . $fieldValue . '</option>';
    						}
    					
    								
    				$petition_form .= '</select>
    				</div>';
                }
                if ( $petition->displays_custom_field6 == 1 && $petition->custom_field6_location == 3 ) {
                    $petition_form .= '
    						<div class="dk-speakout-full">
    								<input name="dk-speakout-custom-field6" id="dk-speakout-custom-field6-' . $petition->id . '" type="checkbox" ' . $custom_field6_required . ' /> <label for "dk-speakout-custom-field6">' .stripslashes( $petition->custom_field6_label ) . '</label>
    							</div>';
                }
                
                if ( $petition->displays_custom_field7 == 1 && $petition->custom_field7_location == 3 ) {
                    $petition_form .= '
    						<div class="dk-speakout-full">
    								<input name="dk-speakout-custom-field7" id="dk-speakout-custom-field7-' . $petition->id . '" type="checkbox" ' . $custom_field7_required . ' /> <label for "dk-speakout-custom-field7">' .stripslashes( $petition->custom_field7_label ) . '</label>
    							</div>';
                }
                $petition_form .= '</div>';

                if ( $petition->is_editable == 1 && $petition->display_petition_message == 1 ) {
                    $petition_form .= '
    							<div class="dk-speakout-full dk-speakout-message-editable" id="dk-speakout-message-editable-' . $petition->id . '">
    								<p class="dk-speakout-greeting">' . $petition->greeting . '</p>
    								<textarea name="dk-speakout-message" class="dk-speakout-message-' . $petition->id . '" ' . $height . ' rows="8">' . stripslashes( esc_textarea( $petition->petition_message ) ) . '</textarea>';
                    $petition_form .= "<div id='dk_speakout_markdown'>" . __( 'You can add formatting using markdown syntax' ) . " - <a href='https://www.markdownguide.org/basic-syntax/' target='_blank'>" . __( "read more" ) . "</a></div>";


                    //if there is a petition footer, show it as part of petition view
                    if ( $petition->petition_footer != '' ) {
                        $petition_form .= '<br><br>' . $petition->petition_footer;
                    }
                    $petition_form .= '</div>';
                } elseif ( $petition->display_petition_message == 1 ) {
                        //to avoid clashing with other resources using parsedown
                        if ( !class_exists( 'Parsedown' ) ) {
                            include_once( 'parsedown.php' );
                        }
                        $Parsedown = new Parsedown();
                        $petition_form .= '
    							<div class="dk-speakout-full dk-speakout-message" ' . $height . ' id="dk-speakout-message-' . $petition->id . '">
    								<p class="dk-speakout-greeting">' . $petition->greeting . '</p>
    								' . stripslashes( wpautop( $Parsedown->text( $petition->petition_message ) ) ) . '
    								<p class="dk-speakout-caps">%%' . __( 'your signature', 'speakout' ) . '%%</p>';


                        //if there is a petition footer, show it as part of petition view
                        if ( $petition->petition_footer != '' ) {
                            $petition_form .= '<br><br>' . $petition->petition_footer;
                        }
                        $petition_form .= '</div>';
                    }
                    // if not collecting email address, there is nothing to opt into even if enabled
                if ( $petition->displays_optin == 1 && $petition->hide_email_field != 1 ) {
                    $optin_default = $options[ 'optin_default' ] == 'checked' ? ' checked="checked"' : '';
                    $petition_form .= '
    							<div class="dk-speakout-optin-wrap" >
    								<div class="dk-speakout-optin-checkbox">
    								    <input type="checkbox" name="dk-speakout-optin"  id="dk-speakout-optin-' . $petition->id . '"' . $optin_default . ' />
    								    <label for="dk-speakout-optin-' . $petition->id . '" class="dk-speakout-options">' . stripslashes( esc_html( $petition->optin_label ) ) . '</label>
    							    </div>
    							</div>';
                }

                // if not collecting email address, there is nowhere to BCC even if enabled
                if ( $options[ 'display_bcc' ] == 'enabled' && $petition->hide_email_field != 1 ) {

                    $petition_form .= '
    							<div class="dk-speakout-bcc-wrap">
    								<div class="dk-speakout-options-checkbox">
    								    <input type="checkbox" name="dk-speakout-bcc" id="dk-speakout-bcc-' . $petition->id . '" CHECKED />
    								    <label for="dk-speakout-bcc-' . $petition->id . '" class="dk-speakout-options">' . __( 'BCC yourself', 'speakout' ) . ' </label>
    								
    							    </div>
    							</div>';
                }


                // if publicly anonymous allowed, display option
                if ( $petition->allow_anonymous == 1 ) {
                    $petition_form .= '
    							<div class="dk-speakout-anonymise-wrap">
    								<div class="dk-speakout-options-checkbox">
    								    <input type="checkbox" name="dk-speakout-anonymise" id="dk-speakout-anonymise-' . $petition->id . '" value="1" />
    								    <label for="dk-speakout-anonymise-' . $petition->id . '" class="dk-speakout-options">' . __( 'Hide name from public', 'speakout' ) . ' </label>
    								
    							    </div>
    							</div>';
                }

                if ( $options[ 'display_privacypolicy' ] == 'enabled' ) {
                    $petition_form .= '
    							<div class="dk-speakout-privacypolicy-wrap">
    								<div class="dk-speakout-options-checkbox">
    								    <input type="checkbox" name="dk-speakout-privacypolicy" id="dk-speakout-privacypolicy-' . $petition->id . '" class="required" />
    								    <label for="dk-speakout-privacypolicy-' . $petition->id . '" class="required dk-speakout-options">' . __( 'Yes, I accept your ', 'speakout' ) . '<a href="' . $options[ 'privacypolicy_url' ] . '" target="_blank">' . __( 'privacy policy', 'speakout' ) . '</a></label>
    								</div>
    							</div>';
                }

                //recaptcha but not for version 3
                if ( isset( $options[ 'g_recaptcha_status' ] ) && $options[ 'g_recaptcha_status' ] == 'on' && $options[ 'g_recaptcha_version' ] != 3 ) {
                    $petition_form .= '<div class="dk-speakout-recaptcha">
                            <div class="g-recaptcha" data-sitekey="' . $options[ "g_recaptcha_site_key" ] . '"></div>
                                <br/></div>';
                }
                
                //hCaptcha
                if ( isset( $options[ 'hcaptcha_status' ] ) && $options[ 'hcaptcha_status' ] == 'on' ) {
                    $petition_form .= '<div class="dk-speakout-hcaptcha">
                            <div class="h-captcha" data-sitekey="' . $options[ "hcaptcha_site_key" ] . '"></div>
                                <br/></div>';
                }
                
                $petition_form .= '
                            <div class="dk-speakout-submit-wrap">
                                <div id="dk-speakout-ajaxloader-' . $petition->id . '" class="dk-speakout-ajaxloader" style="visibility: hidden;">&nbsp;</div>
                                <button name="' . $petition->id . '" class="dk-speakout-submit">' . stripslashes( esc_html( $options[ 'button_text' ] ) ) . '</button>
                            </div>';

                $petition_form .= ' </form>';
                $petition_form .= '</div>';
                $petition_form .= '<div class="dk-speakout-response"></div>';

                if ( $options[ 'display_count' ] == 1 ) {
                    $goal_text = ( $petition->goal != 0 ) ? ' = ' . round( ( $petition->signatures / $petition->goal ) * 100 ) . '% ' . __( 'of goal', 'speakout' ): '';

                    $petition_form .= '
						<div class="dk-speakout-progress-wrap">
    							<div class="dk-speakout-signature-count">';
                    $petition_form .= '<span>' . number_format(  $petition->signatures, 0 , $options[ 'decimal_separator' ] , $options[ 'thousands_separator' ] ) . '</span> ' . __( 'signatures', 'speakout' ) . $goal_text;
                    $petition_form .= '</div>';

                    // if our goal is greater than 0 show the progress bar
                    if ( $petition->goal != 0 ) {
                        $petition_form .= '
                            <div class="dk-speakout-count">0' . dk_speakout_SpeakOut::progress_bar( $petition->goal, $petition->signatures, $progress_width ) . ' ' . number_format( $petition->goal ) . '</div>';
                    }

                    $petition_form .= '
						</div>';
                }
                //display or hide sharing icons
                if ( $options[ 'display_sharing' ] == "enabled" || $options[ 'display_sharing' ] == "on") {
                    $petition_form .= '
						<div class="dk-speakout-share">
							<div>
                                 <p>' . stripslashes( esc_html( $options[ 'share_message' ] ) ) . '</p>
							     <p>
                                    <a class="dk-speakout-facebook" href="#" title="Facebook" rel="' . $petition->id . '"><span>&nbsp;</span></a>
                                    <a class="dk-speakout-email"  target="_blank" href="mailto:?subject=Petition: ' . esc_html( $petition->title ) .'&amp;body=Hi there, I want to share this petition titled %22' .esc_html( $petition->title )  . '%22 with you: https://'  .  $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] .  '" title="Share by Email"><span>&nbsp;</span></a>

                                    <a class="dk-speakout-x" href="#" title="x" rel="' . $petition->id . '"><span>&nbsp;</span></a>
							     </p>
						    </div>
							<div class="dk-speakout-clear"></div>
						</div>';
                }
                $petition_form .= '</div>';
            }
            // petition has expired
            else {
                $goal_text = ( $petition->goal != 0 ) ? '<p><strong>' . __( 'Signature goal', 'speakout' ) . ':</strong> ' . $petition->goal . '</p>': '';
                $petition_form = '
					<div class="dk-speakout-petition-wrap dk-speakout-expired" id="dk-speakout-petition-' . $petition->id . '">
						<h3>' . stripslashes( esc_html( $petition->title ) ) . '</h3>
						<p>' . stripslashes( esc_html( $options[ 'expiration_message' ] ) ) . '</p>
						<p><strong>' . __( 'End date', 'speakout' ) . ':</strong> ' . date( 'M d, Y', strtotime( $petition->expiration_date ) ) . '</p>
						<p><strong>' . __( 'Signatures collected', 'speakout' ) . ':</strong> ' . number_format(  $petition->signatures, 0 , $options[ 'decimal_separator' ] , $options[ 'thousands_separator' ] )  . '</p>
						' . $goal_text . '
						<div class="dk-speakout-progress-wrap">
							<div class="dk-speakout-signature-count">';
                $petition_form .= '<span>' . number_format(  $petition->signatures, 0 , $options[ 'decimal_separator' ] , $options[ 'thousands_separator' ] )  . '</span> ';
                $petition_form .= _n( 'signature', 'signatures', number_format(  $petition->signatures, 0 , $options[ 'decimal_separator' ] , $options[ 'thousands_separator' ] ) , 'speakout' ) . $goal_text;
                $petition_form .= '</div>' .
                dk_speakout_SpeakOut::progress_bar( $petition->goal, $petition->signatures, $progress_width ) . '
                        </div>
					</div>';
            }

        }
        // petition doesn't exist
        else {
            $petition_form = '';
        }
    }

    // id attribute was left out, as in [emailpetition]
    else {
        $petition_form = '
			<div class="dk-speakout-petition-wrap dk-speakout-expired">
				<h3>' . __( 'Petition', 'speakout' ) . '</h3>
				<div class="dk-speakout-notice">
					<p>' . __( 'Error: The site administrator must include a valid petition id  in the shortcode.', 'speakout' ) . '</p>
				</div>
			</div>';
    }

    return $petition_form;
}

// load public CSS on pages/posts that contain the [emailpetition] shortcode
add_filter( 'the_posts', 'dk_speakout_public_css_js' );

function dk_speakout_public_css_js( $posts ) {
    global $dk_speakout_version;
    if ( empty( $posts ) ) return $posts;

    $options = get_option( 'dk_speakout_options' );
    $shortcode_found = false;

    foreach ( $posts as $post ) {
        if ( strstr( $post->post_content, '[emailpetition' ) ) {
            $shortcode_found = true;
            break;
        }
    }

    // load the CSS and JavaScript
    if ( $shortcode_found ) {
        $theme = $options[ 'petition_theme' ];

        switch ( $theme ) {
            case 'default':
                wp_enqueue_style( 'dk_speakout_css', plugins_url( 'speakout/css/theme-default.css' ), array(), $dk_speakout_version );
                break;
            case 'basic':
                wp_enqueue_style( 'dk_speakout_css', plugins_url( 'speakout/css/theme-basic.css' ), array(), $dk_speakout_version );
                break;
            case 'none':
                $parent_dir = get_template_directory_uri();
                $parent_petition_theme_url = $parent_dir . '/petition.css';

                // if a child theme is in use
                // attempt to load petition.css from child theme folder
                if ( is_child_theme() ) {
                    $child_dir = get_stylesheet_directory_uri();
                    $child_petition_theme_url = $child_dir . '/petition.css';
                    $child_petition_theme_path = STYLESHEETPATH . '/petition.css';

                    // use child theme if it exists
                    if ( file_exists( $child_petition_theme_path ) ) {
                        wp_enqueue_style( 'dk_speakout_css', $child_petition_theme_url, array(), $dk_speakout_version );
                    }
                    // else try to load style from parent theme folder
                    else {
                        wp_enqueue_style( 'dk_speakout_css', $parent_petition_theme_url, array(), $dk_speakout_version );
                    }
                }
                // try to load style from active theme folder
                else {
                    wp_enqueue_style( 'dk_speakout_css', $parent_petition_theme_url, array(), $dk_speakout_version );
                }
                break;
        }

        // ensure ajax callback url works on both https and http
        $protocol = isset( $_SERVER[ 'HTTPS' ] ) ? 'https://' : 'http://';
        $params = array( 'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ) );
        if ( isset( $options[ 'g_recaptcha_status' ] ) && $options[ 'g_recaptcha_status' ] == "on" ) {
            wp_enqueue_script( 'dk_speakout_js', plugins_url( 'speakout/js/public-gr.js' ), array( 'jquery' ), $dk_speakout_version );
        } 
        elseif ( isset( $options[ 'hcaptcha_status' ] ) && $options[ 'hcaptcha_status' ] == "on" ) {
            wp_enqueue_script( 'dk_speakout_js', plugins_url( 'speakout/js/public-h.js' ), array( 'jquery' ), $dk_speakout_version );
        }
        else {
            wp_enqueue_script( 'dk_speakout_js', plugins_url( 'speakout/js/public.js' ), array( 'jquery' ), $dk_speakout_version );
        }
        wp_enqueue_script( 'jquery-effects-highlight' );
        wp_localize_script( 'dk_speakout_js', 'dk_speakout_js', $params );
    }

    return $posts;
}

?>