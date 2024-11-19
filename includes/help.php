<?php

// contextual help to Add New page
function dk_speakout_help_addnew() {
	$tab_petitions = '
		<p><strong>' . __( "Title", "speakout" ) . '</strong>&mdash;' . __( "Enter the title of your petition, which will appear at the top of the petition form.", "speakout" ) . '</p>
		<p><strong>' . __( "Do not send email (only collect signatures)", "speakout" ) . '</strong>&mdash;' . __( "Use this option if do not wish to send petition emails to a target address.", "speakout" ) . '</p>
		<p><strong>' . __( "Target Email", "speakout" ) . '</strong>&mdash;' . __( "Enter the email address to which the petition will be sent. You may enter multiple email addresses, separated by commas.", "speakout" ) . '</p>
		<p><strong>' . __( "Email Subject", "speakout" ) . '</strong>&mdash;' . __( "Enter the subject of your petition email.", "speakout" ) . '</p>
		<p><strong>' . __( "Greeting", "speakout" ) . '</strong>&mdash;' . __( "Include a greeting to the recipient of your petition, such as \"Dear Mayor,\" which will appear as the first line of the email.", "speakout" ) . '</p>
        <p><strong>' . __( "Petition Message", "speakout" ) . '</strong>&mdash;' . __( "Enter the content of your petition email.", "speakout" ) . '</p>
		<p><strong>' . __( "Petition Footer", "speakout" ) . '</strong>&mdash;' . __( "This is included at the bottom of each petition that is sent.", "speakout" ) . '</p>
        <p><strong>' . __( "x Message", "speakout" ) . '</strong>&mdash;' . __( "Enter a prepared tweet that will be presented to users when the x button is clicked.", "speakout" ) . '</p>
	';
		
	$tab_petition_options = '
		<p><strong>' . __( "Allow public anonymous", "speakout" ) . '</strong>&mdash;' . __( "Gives option for person to be displayed as \"Anonymous\" if they choose it when signing", "speakout" ) . '</p>
        <p><strong>' . __( "Confirm signatures", "speakout" ) . '</strong>&mdash;' . __( "Use this option to cause an email to be sent to the signers of your petition. This email contains a special link must be clicked to confirm the signer's email address. Petition emails will not be sent until the signature is confirmed.", "speakout" ) . '</p>
		<p><strong>' . __( "Allow messages to be edited", "speakout" ) . '</strong>&mdash;' . __( "Check this option to allow signatories to customize the text of their petition email - this should be used with caution.", "speakout" ) . '</p>
		<p><strong>' . __( "Set signature goal", "speakout" ) . '</strong>&mdash;' . __( "Enter the number of signatures you hope to collect. This number is used to calculate the progress bar display.", "speakout" ) . '</p>
        <p><strong>' . __( "Auto increase signature goal", "speakout" ) . '</strong>&mdash;' . __( "If set goal is enabled, this is a further option - click the ? for more information.", "speakout" ) . '</p>
		<p><strong>' . __( "Set expiration date", "speakout" ) . '</strong>&mdash;' . __( "Use this option to stop collecting signatures on a specific date.", "speakout" ) . '</p>
        <p><strong>' . __( "Redirect after signing", "speakout" ) . '</strong>&mdash;' . __( "Take your users to a different page or site after they sign.  Make sure you use the full URL including \"https://\" or whatever. You can specify the delay in milliseconds.  One second = 1000 milliseconds.", "speakout" ) . '</p>
	';
	$tab_display_options = '
		<p>' .  __( "These are pretty obvious.", "speakout" ) . '</p>
        <p><strong>' . __( "Display opt-in checkbox", "speakout" ) . '</strong>&mdash;' . __( "Include a checkbox that allows users to consent to receiving further email.", "speakout" ) . '</p>
        <p><strong>' . __( "Display address fields", "speakout" ) . '</strong>&mdash;' . __( "Select the address fields to display in the petition form. Note that you can make them required or not.", "speakout" ) . '</p>
		<p><strong>' . __( "Display custom fields", "speakout" ) . '</strong>&mdash;' . __( "There is a variety of custom fields that can be added to the petition form for collecting additional data.  You can position each field at the top, above the email address or at the bottom of the petition form", "speakout" ) . '</p>
        <p><strong>' . __( "Display custom message", "speakout" ) . '</strong>&mdash;' . __( "This is displayed after the petition is signed.", "speakout" ) . '</p>
		
	';
    $tab_3rd_party = '
		<p>' .  __( "If you use any of these 3rd party email services, in the Pro version you can allow users to subscribe as long as you have  \"opt in\" enabled.", "speakout" ) . '</p>		
	';

	// create the tabs
	$screen = get_current_screen();

	$screen->add_help_tab( array (
		'id'      => 'dk_speakout_help_petition',
		'title'   => __( "Petition Content", "speakout" ),
		'content' => $tab_petitions
	));
	$screen->add_help_tab( array (
		'id'      => 'dk_speakout_help_petition_options',
		'title'   => __( "Petition Options", "speakout" ),
		'content' => $tab_petition_options
	));
	$screen->add_help_tab( array (
		'id'      => 'dk_speakout_help_display_options',
		'title'   => __( "Display Options", "speakout" ),
		'content' => $tab_display_options 
	));
    $screen->add_help_tab( array (
		'id'      => 'dk_speakout_help_3rd_party',
		'title'   => __( "3rd Party Integrations", "speakout" ),
		'content' => $tab_3rd_party
	));
	
}

// contextual help for Settings page
function dk_speakout_help_settings() {
	$tab_petition_form = '
		<p>' . __( "These settings control the display of the [emailpetition] shortcode and sidebar widget.", "speakout" ) . '</p>
		<p><strong>' . __( "Petition Theme", "speakout" ) . '</strong>&mdash;' . __( "Select a CSS theme that will control the appearance of petition forms.", "speakout" ) . '</p>
		<p><strong>' . __( "Widget Theme", "speakout" ) . '</strong>&mdash;' . __( "Select a CSS theme that will control the appearance of petition widgets.", "speakout" ) . '</p>
		<p><strong>' . __( "Submit Button Text", "speakout" ) . '</strong>&mdash;' . __( "Enter the text that displays in the orange submit button on petition forms.", "speakout" ) . '</p>
		<p><strong>' . __( "Success Message", "speakout" ) . '</strong>&mdash;' . __( "Enter the text that appears when a user successfully signs your petition with a unique email address.", "speakout" ) . '</p>
		<p><strong>' . __( "Share Message", "speakout" ) . '</strong>&mdash;' . __( "Enter the text that appears above the X and Facebook buttons after the petition form has been submitted.", "speakout" ) . '</p>
		<p><strong>' . __( "Expiration Message", "speakout" ) . '</strong>&mdash;' . __( "Enter the text to display in place of the petition form when a petition is past its expiration date.", "speakout" ) . '</p>
		<p><strong>' . __( "Already Signed Message", "speakout" ) . '</strong>&mdash;' . __( "Enter the text to display when a petition is signed using an email address that has already been submitted.", "speakout" ) . '</p>
		<p><strong>' . __( "Opt-in Default", "speakout" ) . '</strong>&mdash;' . __( "Choose whether the opt-in checkbox is checked or unchecked by default.", "speakout" ) . '</p>
		<p><strong>' . __( "Display signature count", "speakout" ) . '</strong>&mdash;' . __( "Choose whether you wish to display the number of signatures that have been collected.", "speakout" ) . '</p>
	';
	$tab_confirmation_emails = '
		<p>' . __( "These settings control the content of the confirmation emails.", "speakout" ) . '</p>
		<p><strong>' . __( "Email From", "speakout" ) . '</strong>&mdash;' . __( "Enter the email address associated with your website. Confirmation emails will be sent from this address.", "speakout" ) . '</p>
		<p><strong>' . __( "Email Subject", "speakout" ) . '</strong>&mdash;' . __( "Enter the subject of the confirmation email.", "speakout" ) . '</p>
		<p><strong>' . __( "Email Message", "speakout" ) . '</strong>&mdash;' . __( "Enter the content of the confirmation email.", "speakout" ) . '</p>
	';
	$tab_public_signature_list = '
		<p>' . __( "These settings control the display of the [signaturelist] shortcode.", "speakout" ) . '</p>
		<p><strong>' . __( "Title", "speakout" ) . '</strong>&mdash;' . __( "Enter the text that appears above the signature list.", "speakout" ) . '</p>
		<p><strong>' . __( "Theme", "speakout" ) . '</strong>&mdash;' . __( "Select a CSS theme that will control the appearance of signature lists.", "speakout" ) . '</p>
		<p><strong>' . __( "Rows", "speakout" ) . '</strong>&mdash;' . __( "Enter the number of signatures that will be displayed in the signature list.", "speakout" ) . '</p>
		<p><strong>' . __( "Display", "speakout" ) . '</strong>&mdash;' . __( "Choose the signature list styling, click the ? for more information.", "speakout" ) . '</p>
        <p><strong>' . __( "Columns", "speakout" ) . '</strong>&mdash;' . __( "Select the columns that will appear in the public signature list.", "speakout" ) . '</p>
        <p><strong>' . __( "Flags", "speakout" ) . '</strong>&mdash;' . __( "You can display the flag of the country the signer has chosen, if you have Country enabled.", "speakout" ) . '</p>
        <p><strong>' . __( "Privacy", "speakout" ) . '</strong>&mdash;' . __( "Hide all but the first letter of the last name..", "speakout" ) . '</p>
	';
    $tab_admin_signature_list = '
		<p>' . __( "These settings control the display of the signatures in the dashboard.", "speakout" ) . '</p>
		<p><strong>' . __( "Columns", "speakout" ) . '</strong>&mdash;' . __( "Select the columns that will appear in the admin signature list.", "speakout" ) . '</p>
        <p><strong>' . __( "CSV", "speakout" ) . '</strong>&mdash;' . __( "When outputting the CSV file, what should be included?", "speakout" ) . '</p>
	';
	$tab_admin_display = '
		<p>' . __( "These settings control the look of the plugin's options pages within the WordPress administrator.", "speakout" ) . '</p>
		<p><strong>' . __( "Petitions table shows", "speakout" ) . '</strong>&mdash;' . __( "Enter the number of rows to display in the \"Email Petitions\" table (default=20)", "speakout" ) . '</p>
		<p><strong>' . __( "Signatures table shows", "speakout" ) . '</strong>&mdash;' . __( "Enter the number of rows to display in the \"Signatures\" table (default=50)", "speakout" ) . '</p>
	';
    $tab_security = '
		<p>' . __( "Security settings", "speakout" ) . '</p>
		<p><strong>' . __( "Webhooks", "speakout" ) . '</strong>&mdash;' . __( "You can extend SpeakOut! by adding callbacks to various webhooks.  Click the ? link on the security page for more information.", "speakout" ) . '</p>
	';

	// create the tabs
	$screen = get_current_screen();

	$screen->add_help_tab( array (
		'id'      => 'dk_speakout_help_petition_form',
		'title'   => __( "Petition Form", "speakout" ),
		'content' => $tab_petition_form
	));	
    $screen->add_help_tab( array (
		'id'      => 'dk_speakout_help_confirmation_emails',
		'title'   => __( "Confirmation Emails", "speakout" ),
		'content' => $tab_confirmation_emails
	));
	$screen->add_help_tab( array (
		'id'      => 'dk_speakout_help_public_signature_list',
		'title'   => __( "Public Signature List", "speakout" ),
		'content' => $tab_public_signature_list
	));
	$screen->add_help_tab( array (
		'id'      => 'dk_speakout_help_admin_signature_list',
		'title'   => __( "Admin Signature List", "speakout" ),
		'content' => $tab_admin_signature_list
	));
	$screen->add_help_tab( array (
		'id'      => 'dk_speakout_help_admin_display',
		'title'   => __( "Admin Display", "speakout" ),
		'content' => $tab_admin_display
	));
    $screen->add_help_tab( array (
		'id'      => 'dk_speakout_security_display',
		'title'   => __( "Security", "speakout" ),
		'content' => $tab_security
	));
}
?>