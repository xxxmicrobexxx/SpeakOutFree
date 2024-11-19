<?php
/* 
Plugin Name: SpeakOut! Email Petitions
Plugin URI: https://speakoutpetitions.com/
Description: Create custom email petition forms and include them on your site via shortcode or a widget. When signed, the petition is emailed to your target and signatures are saved in the database.  There are many customisable options to make this plugin very flexible.  The free version is limited to one petition, the Pro version has all features enabled.   

Author: Steve D
Author URI: https://SpeakOutPetitions.com

Text Domain: speakout
Domain Path: /languages
Requires at least: 5.0
Tested up to: 6.7
Requires PHP: 7.4
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Version: 4.4.3

{Plugin Name} is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version. 
 
{Plugin Name} is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of 
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details. 
 
For the full text of the GNU General Public License see {License URI}.
*/

global $wpdb, $db_petitions, $db_signatures, $dk_speakout_version;
$dk_speakout_version = '4.4.3';
$db_petitions  = $wpdb->prefix . 'dk_speakout_petitions';
$db_signatures = $wpdb->prefix . 'dk_speakout_signatures';


// enable localizations
add_action( 'init', 'dk_speakout_translate' );
function dk_speakout_translate() {
	load_plugin_textdomain( 'speakout', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

// load admin functions only on admin pages
if ( is_admin() ) {
	include_once( dirname( __FILE__ ) . '/includes/install.php' );
	include_once( dirname( __FILE__ ) . '/includes/admin.php' );
	include_once( dirname( __FILE__ ) . '/includes/petitions.php' );
	include_once( dirname( __FILE__ ) . '/includes/addnew.php' );
	include_once( dirname( __FILE__ ) . '/includes/signatures.php' );
	include_once( dirname( __FILE__ ) . '/includes/settings.php' );
    include_once( dirname( __FILE__ ) . '/includes/upgrade.php' );
	include_once( dirname( __FILE__ ) . '/includes/csv.php' );
	include_once( dirname( __FILE__ ) . '/includes/ajax.php' );
	include_once( dirname( __FILE__ ) . '/includes/help.php' );

	// enable plugin activation
	register_activation_hook( __FILE__, 'dk_speakout_install' );
}
// public pages
else {
	include_once( dirname( __FILE__ ) . '/includes/emailpetition.php' );
	include_once( dirname( __FILE__ ) . '/includes/signaturelist.php' );
	include_once( dirname( __FILE__ ) . '/includes/confirmations.php' );
}

// load the widget (admin and public)
include_once( dirname( __FILE__ ) . '/includes/widget.php' );

// add Support and Upgrade links to the Plugins page - note: they only show when activated
add_filter( 'plugin_row_meta', 'dk_speakout_meta_links', 10, 2 );
function dk_speakout_meta_links( $links, $file ) {
	$plugin = plugin_basename( __FILE__ );

	// create link
	if ( $file == $plugin ) {

        $createLink = '<a href="https://speakoutpetitions.com/faqconc/why-a-premium-version/" target="_blank" >%s</a>';

		return array_merge(
			$links,
			array(
				sprintf( '<a href="https://wordpress.org/support/plugin/speakout/" target="_blank">%s</a>', __( 'Support', 'speakout' ) ),
				sprintf(  $createLink , __( 'Upgrade to Pro', 'speakout' ) )
			)
		);
	}

	return $links;
}

// eliminate warning - ob_end_flush(): failed to send buffer of zlib output compression  
remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );

//currently disabled until I have time to fix
/* $current_user = get_current_user_id();

function SpeakOut_plugin_notice() {
    $user_id = $current_user->ID;
		if (!get_user_meta($user_id, 'SpeakOut_plugin_notice_ignore')) {
		echo '<div class="updated notice"><p>The SpeakOut! petition plugin is now also integrated with Mailerlite. Please help keep SpeakOut! 100% free - <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4PPYZ8K2KLXUJ" target=_blank">Donate</a> :: <a href="?' . $_SERVER['QUERY_STRING'] . '&SpeakOut-ignore-notice">Dismiss</a></p></div>';
	}
}
add_action('admin_notices', 'SpeakOut_plugin_notice');
	
function SpeakOut_plugin_notice_ignore() {
	$user_id = $current_user->ID;
	if (isset($_GET['SpeakOut-ignore-notice'])) {
		add_user_meta($user_id, 'SpeakOut_plugin_notice_ignore', 'true', true);
	}
}
add_action('admin_init', 'SpeakOut_plugin_notice_ignore');
*/
?>