<?php

/**
 * Class for displaying signatures via [signaturelist] shortcode
 */
class dk_speakout_Signaturelist
{

	/**
	 * generates list of signatures for a single petition
	 *
	 * @param int $id the ID petition for which we are displaying signatures
	 * @param int $start the first signature to be retrieved
	 * @param int $limit number of signatures to be retrieved
	 * @param string $context either 'shortcode' or 'ajax' to distinguish between calls from the initia page load (shortcode) and calls from pagination buttons (ajax)
	 * @param string $dateformat PHP date format provided by shortcode attribute - also relayed in ajax requests
	 * @param string $nextbuttontext provided by shortcode attribute
	 * @param string $prevtbuttontext provided by shortcode attribute
	 *
	 * @return string HTML  containing signatures 
	 */
	public static function table( $id, $start, $limit, $context = 'shortcode', $dateformat = 'M d, Y', $firstbuttontext = '&lt;&lt;', $nextbuttontext = '&gt;', $prevbuttontext = '&lt;', $lastbuttontext = '&gt;&gt;') {

		// Check if we have a form preslected
		if (array_key_exists('petition', $_GET)){
			$id = isset( $_GET['petition'] ) ? abs( $_GET['petition'] ) : 1 ;
		}
		include_once( 'class.signature.php' );
		$the_signatures = new dk_speakout_Signature();
        
        include_once( 'class.petition.php' );
        $petition = new dk_speakout_Petition();
        $petition->retrieve( $id );
        
        $hideUnconfirmed = $petition->requires_confirmation == 1 ? true : false ;
        
		$options = get_option( 'dk_speakout_options' );

		// get the signatures
		$signatures = $the_signatures->all( $id, $start, $limit, 'signaturelist', $hideUnconfirmed );

		$total = $the_signatures->count( $id, 'signaturelist', $hideUnconfirmed);
		$current_signature_number = $total - $start;
		$signatures_list = '';
		
		// get list of columns to display - as defined in settings
		$columns = unserialize( $options['signaturelist_columns'] );
		
    	// only show signature list if there are signatures
    	if ( $total > 0 ) {
    		// determine which columns to display but only if we have selected at least one
    		// this avoids a PHP warning if none are displayed
    		if(count($columns) > 0 ){
        		$display_city     = ( in_array( 'sig_city', $columns ) ) ? 1 : 0;
        		$display_state    = ( in_array( 'sig_state', $columns ) ) ? 1 : 0;
        		$display_postcode = ( in_array( 'sig_postcode', $columns ) ) ? 1 : 0;
        		$display_country  = ( in_array( 'sig_country', $columns ) ) ? 1 : 0;
        		$display_custom   = ( in_array( 'sig_custom', $columns ) ) ? 1 : 0;
        		$display_date     = ( in_array( 'sig_date', $columns ) ) ? 1 : 0;
        		$display_message  = ( in_array( 'sig_message', $columns ) ) ? 1 : 0;
            }   
            
                //pass value for setting pagination link
                $linkOption = 3;

                if ( $context !== 'ajax' ) { // only include on initial page load (not when paging)
                    $signatures_list = '
                    <!-- signaturelist table -->
                    <div class="dk-speakout-signaturelistWrapper">
                      <div class="dk-speakout-signaturelist-header">' . $options['signaturelist_header'] . '</div>
                        <table class="dk-speakout-signaturelist dk-speakout-signaturelist-' . $id . '">';
    			}
    
    			$row_count = 0;
    			foreach ( $signatures as $signature ) {
    				if ( $row_count % 2 ) {
    					$signatures_list .= '<tr class="dk-speakout-even">'.PHP_EOL;
    				}
    				else {
    					$signatures_list .= '<tr class="dk-speakout-odd">'.PHP_EOL;
    				}
    				$signatures_list .= "\t" .'<td class="dk-speakout-signaturelist-count">' . number_format( $current_signature_number, 0, '.', ',' ) . '</td>'.PHP_EOL;
    				
        			if($signature->anonymise != 1){ // if signer has chosen anonymity hide name from public
        				$display_lastname =	$signature->last_name;
        				// if we have enabled privacy, only show forst letter of surname
        				if($options['signaturelist_privacy']=='enabled'){
        				 $display_lastname = mb_substr($signature->last_name, 0, 1) . ".";
        				}
        				$signatures_list .= "\t" .'<td class="dk-speakout-signaturelist-name" >';
                                // if we aren't collecting honorific, don't display them.  This works around if they were collected for a while and then no longer.
                                if ($options['display_honorific']== 'enabled'){
                                    $signatures_list .= stripslashes($signature->honorific . " ");
                                }
                       $signatures_list .= stripslashes($signature->first_name . ' ' . $display_lastname ) . '</td>'.PHP_EOL ;
        			}
                   else{
                       $signatures_list .= '<td>' . __( 'Anonymous', 'speakout' )  . '</td>'.PHP_EOL ;
                   }
    
    				// if we display both city and state, combine them into one column
    				$city  = ( $display_city )  ? $signature->city : '';
    				$state = ( $display_state ) ? $signature->state : '';
    				if ( $display_city && $display_state ) {
    					// should we separate with a comma?
    					$delimiter = ( $city !='' && $state != '' ) ? ', ' : '';
    					$signatures_list .= "\t" .'<td class="dk-speakout-signaturelist-city">' . stripslashes( $city . $delimiter . $state ) . '</td>'.PHP_EOL;
    				}
    				// else keep city or state values in their own column
    				else {
    					if ( $display_city ) $signatures_list  .= "\t" .'<td class="dk-speakout-signaturelist-city">' . stripslashes( $city ) . '</td>'.PHP_EOL;
    					if ( $display_state ) $signatures_list .= "\t" .'<td class="dk-speakout-signaturelist-state">' . stripslashes( $state ) . '</td>'.PHP_EOL;
    				}
    
    				if ( $display_postcode ) $signatures_list .= "\t" .'<td class="dk-speakout-signaturelist-postcode">' . stripslashes( $signature->postcode ) . '</td>'.PHP_EOL;
    				
//if country exists and we are displaying flag, show flag or show generic globe
                    if($options['display_flags']== 'on'){
                        $flag = strtolower(getCountrycode(stripslashes( $signature->country ))) > "" ?  strtolower(getCountrycode(stripslashes( $signature->country ))) . ".svg" : "Earth.svg";
                        $flag_image = '<img src="' . content_url()  . '/plugins/speakout/images/flags/' . $flag . '" class="dk-speakout-signaturelist-flag-icon"> ';
                    }
                    else{
                        $flag_image = "";
                    }
    				
                    $country = stripslashes( $signature->country ) > "" ? stripslashes( $signature->country ) : "??";

    				if ( $display_country ) $signatures_list  .= "\t" .'<td class="dk-speakout-signaturelist-country"><span class="dk-speakout-signaturelist-country-flag">' . $flag_image . '</span> '. $country .'</td>'.PHP_EOL;
                    
    				if ( $display_custom ) $signatures_list   .= "\t" .'<td class="dk-speakout-signaturelist-custom">' . stripslashes( $signature->custom_field ) . '</td>'.PHP_EOL;
                    
    				if ( $display_message ) $signatures_list     .= "\t" .'<td class="dk-speakout-signaturelist-message">' . mb_strimwidth(stripslashes( $signature->custom_message ),0,100,"...") . '</td>'.PHP_EOL;
                    
    				if ( $display_date ) $signatures_list     .= "\t" .'<td class="dk-speakout-signaturelist-date">' . date_i18n( $dateformat, strtotime( $signature->date ) ) . '</td>'.PHP_EOL;
    				$signatures_list .= '</tr>'.PHP_EOL;
     
    				$current_signature_number --;
    				$row_count ++;
    			}
    
    			if ( $context !== 'ajax' ) { // only include on initial page load
    
    				if ( $limit != 0 && $start + $limit < $total  ) {
    					$colspan = ( count( $columns ) + 2 );
    					$signatures_list .= '
    					<tr class="dk-speakout-signaturelist-pagelinks">
    						<td colspan="' . $colspan . '">
    						    <a class="dk-speakout-signaturelist-first dk-speakout-signaturelist-disabled" rel="' . $id .  ',' . $total . ',' . $limit . ',' . $total . ',0,' . $linkOption . '">' . $firstbuttontext . '</a>
    							<a class="dk-speakout-signaturelist-prev dk-speakout-signaturelist-disabled" rel="' . $id .  ',' . $total . ',' . $limit . ',' . $total . ',0,' . $linkOption . '">' . $prevbuttontext . '</a>
    							<a class="dk-speakout-signaturelist-next" rel="' . $id .  ',' . ( $start + $limit ) . ',' . $limit . ',' . $total . ',1,' . $linkOption . ',">' . $nextbuttontext . '</a>
    							<a class="dk-speakout-signaturelist-last" rel="' . $id .  ',' . ( $total - $limit ) . ',' . $limit . ',' . $total . ',1,' . $linkOption . ',">' . $lastbuttontext . '</a>
    						</td>
    					</tr>
    					';
    				}
    				$signatures_list .= '</table>
                    </div>';
    			}

		//common return
		return $signatures_list;
	 
        }// end of list > 0 display
    } // end table function

 
    public static function searchsigs( $id, $start, $limit, $context = 'shortcode', $dateformat = 'M d, Y', $firstbuttontext = '&lt;&lt;', $nextbuttontext = '&gt;', $prevbuttontext = '&lt;', $lastbuttontext = '&gt;&gt;' ) {
		
		// Check if we have a form preslected
		if (array_key_exists('petition', $_GET)){
			$id = isset( $_GET['petition'] ) ? abs( $_GET['petition'] ) : 1 ;
		}
		include_once( 'class.signature.php' );
		$the_signatures = new dk_speakout_Signature();
		$options = get_option( 'dk_speakout_options' );

		// get the signatures
        $sql = "
			SELECT $db_signatures.*, $db_petitions.title, $db_petitions.custom_field_label, $db_petitions.displays_custom_field
			FROM `$db_signatures`, `$db_petitions`
			WHERE $db_signatures.petitions_id = $db_petitions.id
			AND ($db_signatures.email LIKE '%" . $searchString . "%' OR $db_signatures.honorific LIKE '%" . $searchString . "%' OR $db_signatures.first_name LIKE '%" . $searchString . "%' OR $db_signatures.street_address LIKE '%" . $searchString . "%' OR $db_signatures.city LIKE '%" . $searchString . "%'  OR $db_signatures.state LIKE '%" . $searchString . "%' OR $db_signatures.country LIKE '%" . $searchString . "%' OR $db_signatures.custom_field LIKE '%" . $searchString . "%' OR $db_signatures.postcode LIKE '%" . $searchString . "%')
			$sql_petition_filter
			$sql_context_filter
			ORDER BY $db_signatures.id DESC $limit
		";

		$$the_signatures = $wpdb->get_results( $sql );

		$total = $the_signatures->count( $id, 'signaturelist',"" );
		$current_signature_number = $total - $start;
		$signatures_list = '';
		
		// get list of columns to display - as defined in settings
		$columns = unserialize( $options['signaturelist_columns'] );
		
    	// only show signature list if there are signatures
    	if ( $total > 0 ) {
    		// determine which columns to display but only if we have selected at least one
    		// this avoids a PHP warning if none are displayed
    		if(count($columns) > 0 ){
        		$display_city     = ( in_array( 'sig_city', $columns ) ) ? 1 : 0;
        		$display_state    = ( in_array( 'sig_state', $columns ) ) ? 1 : 0;
        		$display_postcode = ( in_array( 'sig_postcode', $columns ) ) ? 1 : 0;
        		$display_country  = ( in_array( 'sig_country', $columns ) ) ? 1 : 0;
        		$display_custom   = ( in_array( 'sig_custom', $columns ) ) ? 1 : 0;
        		$display_date     = ( in_array( 'sig_date', $columns ) ) ? 1 : 0;
        		$display_message  = ( in_array( 'sig_message', $columns ) ) ? 1 : 0;
            }    
            
            	    //pass value for setting pagination link
                    $linkOption = 3;
            	    
                if ( $context !== 'ajax' ) { // only include on initial page load (not when paging)
    				$signatures_list = '
    				<!-- signaturelist table -->
    				<div class="dk-speakout-signaturelistWrapper">
                      <div class="dk-speakout-signaturelist-header">' . $options['signaturelist_header'] . '</div>
                        <table class="dk-speakout-signaturelist dk-speakout-signaturelist-' . $id . '">';
    			}
    
    			$row_count = 0;
    			foreach ( $signatures as $signature ) {
    				if ( $row_count % 2 ) {
    					$signatures_list .= '<tr class="dk-speakout-even">'.PHP_EOL;
    				}
    				else {
    					$signatures_list .= '<tr class="dk-speakout-odd">'.PHP_EOL;
    				}
    				$signatures_list .= "\t" .'<td class="dk-speakout-signaturelist-count">' . number_format( $current_signature_number, 0, '.', ',' ) . '</td>'.PHP_EOL;
    				
        			if($signature->anonymise != 1){ // if signer has chosen anonymity hide name from public
        				$display_lastname =	$signature->last_name;
        				// if we have enabled privacy, only show forst letter of surname
        				if($options['signaturelist_privacy']=='enabled'){
        				 $display_lastname =	substr($signature->last_name, 0, 1) . ".";
        				}
        				$signatures_list .= "\t" .'<td class="dk-speakout-signaturelist-name" >';
                                // if we aren't collecting honorific, don't display them.  This works around if they were collected for a while and then no longer.
                                if ($options['display_honorific']== 'enabled'){
                                    $signatures_list .= stripslashes($signature->honorific . " ");
                                }
                       $signatures_list .= stripslashes($signature->first_name . ' ' . $display_lastname ) . '</td>'.PHP_EOL ;
        			}
                   else{
                       $signatures_list .= '<td>' . __( 'Anonymous', 'speakout' ). '</td>'.PHP_EOL ;
                   }
    
    				// if we display both city and state, combine them into one column
    				$city  = ( $display_city )  ? $signature->city : '';
    				$state = ( $display_state ) ? $signature->state : '';
    				if ( $display_city && $display_state ) {
    					// should we separate with a comma?
    					$delimiter = ( $city !='' && $state != '' ) ? ', ' : '';
    					$signatures_list .= "\t" .'<td class="dk-speakout-signaturelist-city">' . stripslashes( $city . $delimiter . $state ) . '</td>'.PHP_EOL;
    				}
    				// else keep city or state values in their own column
    				else {
    					if ( $display_city ) $signatures_list  .= "\t" .'<td class="dk-speakout-signaturelist-city">' . stripslashes( $city ) . '</td>'.PHP_EOL;
    					if ( $display_state ) $signatures_list .= "\t" .'<td class="dk-speakout-signaturelist-state">' . stripslashes( $state ) . '</td>'.PHP_EOL;
    				}
    
    				if ( $display_postcode ) $signatures_list .= "\t" .'<td class="dk-speakout-signaturelist-postcode">' . stripslashes( $signature->postcode ) . '</td>'.PHP_EOL;
    				
//if country exists and we are displaying flag, show flag or show generic globe
                    if($options['display_flags']== 'on'){
                        $flag = strtolower(getCountrycode(stripslashes( $signature->country ))) > "" ?  strtolower(getCountrycode(stripslashes( $signature->country ))) . ".svg" : "Earth.svg";
                        $flag_image = '<img src="' .  content_url() . '/plugins/speakout/images/flags/' . $flag . '" class="dk-speakout-signaturelist-flag-icon"> ';
                    }
                    else{
                        $flag_image = "";
                    }
    				
                    $country = stripslashes( $signature->country ) > "" ? stripslashes( $signature->country ) : "??";

    				if ( $display_country ) $signatures_list  .= "\t" .'<td class="dk-speakout-signaturelist-country"><span class="dk-speakout-signaturelist-country-flag">' . $flag_image . '</span> '. $country .'</td>'.PHP_EOL;
                    
    				if ( $display_custom ) $signatures_list   .= "\t" .'<td class="dk-speakout-signaturelist-custom">' . stripslashes( $signature->custom_field ) . '</td>'.PHP_EOL;
                    
    				if ( $display_message ) $signatures_list     .= "\t" .'<td class="dk-speakout-signaturelist-message">' . mb_strimwidth(stripslashes( $signature->custom_message ),0,100,"...") . '</td>'.PHP_EOL;
                    
    				if ( $display_date ) $signatures_list     .= "\t" .'<td class="dk-speakout-signaturelist-date">' . date_i18n( $dateformat, strtotime( $signature->date ) ) . '</td>'.PHP_EOL;
    				$signatures_list .= '</tr>'.PHP_EOL;
     
    				$current_signature_number --;
    				$row_count ++;
    			}
    
    			if ( $context !== 'ajax' ) { // only include on initial page load
    
    				if ( $limit != 0 && $start + $limit < $total  ) {
    					$colspan = ( count( $columns ) + 2 );
    					$signatures_list .= '
    					<tr class="dk-speakout-signaturelist-pagelinks">
    						<td colspan="' . $colspan . '">
    						    <a class="dk-speakout-signaturelist-first dk-speakout-signaturelist-disabled" rel="' . $id .  ',' . $total . ',' . $limit . ',' . $total . ',0,' . $linkOption . '">' . $firstbuttontext . '</a>
    							<a class="dk-speakout-signaturelist-prev dk-speakout-signaturelist-disabled" rel="' . $id .  ',' . $total . ',' . $limit . ',' . $total . ',0,' . $linkOption . '">' . $prevbuttontext . '</a>
    							<a class="dk-speakout-signaturelist-next" rel="' . $id .  ',' . ( $start + $limit ) . ',' . $limit . ',' . $total . ',1,' . $linkOption . '">' . $nextbuttontext . '</a>
    							<a class="dk-speakout-signaturelist-last" rel="' . $id .  ',' . ( $total - $limit ) . ',' . $limit . ',' . $total . ',1,' . $linkOption . '">' . $lastbuttontext . '</a>
    						</td>
    					</tr>
    					';
    				}
    				$signatures_list .= '</table>
                    </div>';
    			}
    
    		}// end total > 0
		//common return
		return $signatures_list;
	 
        }// end searchsigs function
} // end class 


// function to display country flag in signaure list
function getCountryCode($countryName){
    $countries = array(
        'Afghanistan' => 'AF',
        'Aland Islands' => 'AX',
        'Albania' => 'AL',
        'Algeria' => 'DZ',
        'American Samoa' => 'AS',
        'Andorra' => 'AD',
        'Angola' => 'AO',
        'Anguilla' => 'AI',
        'Antarctica' => 'AQ',
        'Antigua and Barbuda' => 'AG',
        'Argentina' => 'AR',
        'Armenia' => 'AM',
        'Aruba' => 'AW',
        'Australia' => 'AU',
        'Austria' => 'AT',
        'Azerbaijan' => 'AZ',
        'Bahamas' => 'BS',
        'Bahrain' => 'BH',
        'Bangladesh' => 'BD',
        'Barbados' => 'BB',
        'Belarus' => 'BY',
        'Belgium' => 'BE',
        'Belize' => 'BZ',
        'Benin' => 'BJ',
        'Bermuda' => 'BM',
        'Bhutan' => 'BT',
        'Bolivia' => 'BO',
        'Bonaire, Saint Eustatius and Saba' => 'BQ',
        'Bosnia and Herzegovina' => 'BA',
        'Botswana' => 'BW',
        'Bouvet Island' => 'BV',
        'Brazil' => 'BR',
        'British Indian Ocean Territory' => 'IO',
        'British Virgin Islands' => 'VG',
        'Brunei' => 'BN',
        'Bulgaria' => 'BG',
        'Burkina Faso' => 'BF',
        'Burundi' => 'BI',
        'Cambodia' => 'KH',
        'Cameroon' => 'CM',
        'Canada' => 'CA',
        'Cape Verde' => 'CV',
        'Cayman Islands' => 'KY',
        'Central African Republic' => 'CF',
        'Chad' => 'TD',
        'Chile' => 'CL',
        'China' => 'CN',
        'Christmas Island' => 'CX',
        'Cocos Islands' => 'CC',
        'Colombia' => 'CO',
        'Comoros' => 'KM',
        'Cook Islands' => 'CK',
        'Costa Rica' => 'CR',
        'Croatia' => 'HR',
        'Cuba' => 'CU',
        'Curacao' => 'CW',
        'Cyprus' => 'CY',
        'Czech Republic' => 'CZ',
        'Democratic Republic of the Congo' => 'CD',
        'Denmark' => 'DK',
        'Djibouti' => 'DJ',
        'Dominica' => 'DM',
        'Dominican Republic' => 'DO',
        'East Timor' => 'TL',
        'Ecuador' => 'EC',
        'Egypt' => 'EG',
        'El Salvador' => 'SV',
        'Equatorial Guinea' => 'GQ',
        'Eritrea' => 'ER',
        'Estonia' => 'EE',
        'Ethiopia' => 'ET',
        'Falkland Islands' => 'FK',
        'Faroe Islands' => 'FO',
        'Fiji' => 'FJ',
        'Finland' => 'FI',
        'France' => 'FR',
        'French Guiana' => 'GF',
        'French Polynesia' => 'PF',
        'French Southern Territories' => 'TF',
        'Gabon' => 'GA',
        'Gambia' => 'GM',
        'Georgia' => 'GE',
        'Germany' => 'DE',
        'Ghana' => 'GH',
        'Gibraltar' => 'GI',
        'Greece' => 'GR',
        'Greenland' => 'GL',
        'Grenada' => 'GD',
        'Guadeloupe' => 'GP',
        'Guam' => 'GU',
        'Guatemala' => 'GT',
        'Guernsey' => 'GG',
        'Guinea' => 'GN',
        'Guinea-Bissau' => 'GW',
        'Guyana' => 'GY',
        'Haití' => 'HT',
        'Heard Island and McDonald Islands' => 'HM',
        'Honduras' => 'HN',
        'Hong Kong' => 'HK',
        'Hungary' => 'HU',
        'Iceland' => 'IS',
        'India' => 'IN',
        'Indonesia' => 'ID',
        'Iran' => 'IR',
        'Iraq' => 'IQ',
        'Ireland' => 'IE',
        'Isle of Man' => 'IM',
        'Israel' => 'IL',
        'Italy' => 'IT',
        'Ivory Coast' => 'CI',
        'Jamaica' => 'JM',
        'Japan' => 'JP',
        'Jersey' => 'JE',
        'Jordan' => 'JO',
        'Kazakhstan' => 'KZ',
        'Kenya' => 'KE',
        'Kiribati' => 'KI',
        'Kosovo' => 'XK',
        'Kuwait' => 'KW',
        'Kyrgyzstan' => 'KG',
        'Laos' => 'LA',
        'Latvia' => 'LV',
        'Lebanon' => 'LB',
        'Lesotho' => 'LS',
        'Liberia' => 'LR',
        'Libya' => 'LY',
        'Liechtenstein' => 'LI',
        'Lithuania' => 'LT',
        'Luxembourg' => 'LU',
        'Macao' => 'MO',
        'Macedonia' => 'MK',
        'Madagascar' => 'MG',
        'Malawi' => 'MW',
        'Malaysia' => 'MY',
        'Maldives' => 'MV',
        'Mali' => 'ML',
        'Malta' => 'MT',
        'Marshall Islands' => 'MH',
        'Martinique' => 'MQ',
        'Mauritania' => 'MR',
        'Mauritius' => 'MU',
        'Mayotte' => 'YT',
        'Mexico' => 'MX',
        'Micronesia' => 'FM',
        'Moldova' => 'MD',
        'Monaco' => 'MC',
        'Mongolia' => 'MN',
        'Montenegro' => 'ME',
        'Montserrat' => 'MS',
        'Morocco' => 'MA',
        'Mozambique' => 'MZ',
        'Myanmar' => 'MM',
        'Namibia' => 'NA',
        'Nauru' => 'NR',
        'Nepal' => 'NP',
        'Netherlands' => 'NL',
        'New Caledonia' => 'NC',
        'New Zealand' => 'NZ',
        'Nicaragua' => 'NI',
        'Niger' => 'NE',
        'Nigeria' => 'NG',
        'Niue' => 'NU',
        'Norfolk Island' => 'NF',
        'North Korea' => 'KP',
        'Northern Mariana Islands' => 'MP',
        'Norway' => 'NO',
        'Oman' => 'OM',
        'Pakistan' => 'PK',
        'Palau' => 'PW',
        'Palestinian Territory' => 'PS',
        'Panamá' => 'PA',
        'Papua New Guinea' => 'PG',
        'Paraguay' => 'PY',
        'Peru' => 'PE',
        'Philippines' => 'PH',
        'Pitcairn' => 'PN',
        'Poland' => 'PL',
        'Portugal' => 'PT',
        'Puerto Rico' => 'PR',
        'Qatar' => 'QA',
        'Republic of the Congo' => 'CG',
        'Reunion' => 'RE',
        'Romania' => 'RO',
        'Russia' => 'RU',
        'Rwanda' => 'RW',
        'Saint Barthelemy' => 'BL',
        'Saint Helena' => 'SH',
        'Saint Kitts and Nevis' => 'KN',
        'Saint Lucia' => 'LC',
        'Saint Martin' => 'MF',
        'Saint Pierre and Miquelon' => 'PM',
        'Saint Vincent and the Grenadines' => 'VC',
        'Samoa' => 'WS',
        'San Marino' => 'SM',
        'Sao Tome and Principe' => 'ST',
        'Saudi Arabia' => 'SA',
        'Senegal' => 'SN',
        'Serbia' => 'RS',
        'Seychelles' => 'SC',
        'Sierra Leone' => 'SL',
        'Singapore' => 'SG',
        'Saint Maarten' => 'SX',
        'Slovakia' => 'SK',
        'Slovenia' => 'SI',
        'Solomon Islands' => 'SB',
        'Somalia' => 'SO',
        'South Africa' => 'ZA',
        'South Georgia and the South Sandwich Islands' => 'GS',
        'South Korea' => 'KR',
        'South Sudan' => 'SS',
        'Spain' => 'ES',
        'Sri Lanka' => 'LK',
        'Sudan' => 'SD',
        'Suriname' => 'SR',
        'Svalbard and Jan Mayen' => 'SJ',
        'Swaziland' => 'SZ',
        'Sweden' => 'SE',
        'Switzerland' => 'CH',
        'Syria' => 'SY',
        'Taiwan' => 'TW',
        'Tajikistan' => 'TJ',
        'Tanzania' => 'TZ',
        'Thailand' => 'TH',
        'Togo' => 'TG',
        'Tokelau' => 'TK',
        'Tonga' => 'TO',
        'Trinidad and Tobago' => 'TT',
        'Tunisia' => 'TN',
        'Turkey' => 'TR',
        'Turkmenistan' => 'TM',
        'Turks and Caicos Islands' => 'TC',
        'Tuvalu' => 'TV',
        'U.S. Virgin Islands' => 'VI',
        'British Virgin Islands' => 'VI',
        'Uganda' => 'UG',
        'Ukraine' => 'UA',
        'United Arab Emirates' => 'AE',
        'United Kingdom' => 'GB',
        'United States Minor Outlying Islands' => 'UM',
        'United States' => 'US',
        'Uruguay' => 'UY',

        'Uzbekistan' => 'UZ',
        'Vanuatu' => 'VU',
        'Vatican' => 'VA',
        'Venezuela' => 'VE',
        'Viet Nam' => 'VN',
        'Vietnam' => 'VN',
        'Wallis and Futuna' => 'WF',
        'Western Sahara' => 'EH',
        'Yemen' => 'YE',
        'Zambia' => 'ZM',
        'Zimbabwe' => 'ZW',
    );
    if(isset($countries[$countryName])) return $countries[$countryName];
        return null;
  }
  
  

?>