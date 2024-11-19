<style>
    label{margin:10px 0 5px 0;}
</style><div class="wrap" id="dk-speakout">
	<div id="icon-dk-speakout" class="icon32"><br /></div>
	<h2><?php echo esc_html( $page_title ); ?></h2> 
        
  <div id="upgradeTopDisplay"><span class="upgrade-message">In the free version you are limited to this one petition and some features are unavailable. <a href="<?php echo site_url(); ?>/wp-admin/admin.php?page=dk_speakout_upgrade"><?php _e("Upgrade to Pro", "speakout"); ?></a></span></div><div id="requiredInfo"><span class="required"> </span> = required field</div>

	<?php if ( $message_update ) echo '<div id="message" class="updated"><p>' . esc_html( $message_update ) . '</p></div>'; ?>
	<div id="message" class="error dk-speakout-error-msg"><p><?php _e( 'Error: Please correct the highlighted fields.  It may be behind a closed tab', 'speakout' ); ?></p></div>

	<form name="dk-speakout-edit-petition" id="dk-speakout-edit-petition" method="post" action="">
		<?php wp_nonce_field( $nonce ); ?>
		<input type="hidden" name="action" value="<?php echo esc_html( $action ); ?>" />
		<?php if ( $petition->id ) echo '<input type="hidden" name="id" value="' . esc_attr( $petition->id ) . '" />'; ?>
		<input type="hidden" name="action" value="<?php echo esc_html( $action ); ?>" />
		<input type="hidden" name="tab" id="dk-petition-tab" value="<?php echo esc_html( $tab ); ?>" />
<ul id="dk-petition-tabbar">
	<li><a class="dk-petition-tab-01" rel="dk-petition-tab-01"><?php _e( 'Petition Content', 'speakout' ); ?></a></li>
	<li><a class="dk-petition-tab-02" rel="dk-petition-tab-02"><?php _e( 'Petition Options', 'speakout' ); ?></a></li>
	<li><a class="dk-petition-tab-03" rel="dk-petition-tab-03"><?php _e( 'Display Options', 'speakout' ); ?></a></li>
    <li><a class="dk-petition-tab-04" rel="dk-petition-tab-04"><?php _e( '3rd Party Integrations', 'speakout' ); ?></a></li>
</ul>
		
<div id="post-body-content">
    <div id="dk-petition-tab-01" class="dk-petition-tabcontent">
    	<div class="postbox">		
    	    <div id="titlediv">
				<div id="titlewrap">
					<label for="title" class="required"><?php _e( 'Title', 'speakout' ); ?></label>
					<input type="text" name="title" size="100" tabindex="1" value="<?php echo esc_html(  stripcslashes ($petition->title)  ); ?>" id="title" <?php if($petition->title == ""){ echo " placeholder='"; _e( 'Enter title here', 'speakout' ); echo "'"; }?>/>
				</div>
			</div>
  
				<div class="dk-speakout-checkbox sends_email">
					
					<?php 
					    if ( $petition->sends_email == '0' && $petition->hide_email_field == '1'){
					        echo '<input type="checkbox" name="sends_email_dummy" id="sends_email_dummy" checked="checked" disabled="disabled"/>';
					        echo '<input type="hidden" name="sends_email" id="sends_email" value="1" />';
					}
					else{ 
					     $isChecked = $petition->sends_email == '0' ? 'checked="checked"' : '';
					        echo '<input type="checkbox" name="sends_email" id="sends_email" ' . $isChecked . ' />';
					 } 
					 ?>
					<label for="sends_email" class="dk-speakout-inline"><?php _e( 'Do not send email (only collect signatures)', 'speakout' ); ?></label>
				</div>
				<div class="dk-speakout-petition-content">
					<div class="dk-speakout-email-headers">
						<label for="target_email" ><?php _e( 'Target Email', 'speakout' );?> <span  class="required"></span> <span class='normalText'><?php _e( 'you may enter multiple addresses separated by commas', 'speakout' ); ?></span> <a href="https://speakoutpetitions.com/faqconc/there-isnt-room-for-all-my-target-emails/" target = _blank">?</a></label> 
						<input name="target_email" id="target_email" value="<?php echo esc_attr( $petition->target_email ); ?>" size="40" maxlength="300" type="text" />
						
						<label for="target_email_CC"><?php _e( 'CC Email', 'speakout' ); ?> <span class='normalText'><?php _e( 'you may enter multiple addresses separated by commas', 'speakout' ); ?></span></label> 
						<input name="target_email_CC" id="target_email_CC" value="<?php echo esc_attr( $petition->target_email_CC ); ?>" size="40" maxlength="300" type="text" />

						<label for="email_subject" class="required"><?php _e( 'Email Subject', 'speakout' ); ?></label>
						<input name="email_subject" id="email_subject" value="<?php echo stripcslashes( $petition->email_subject ) ; ?>" size="40" maxlength="80" type="text" />

						<label for="greeting" class="required"><?php _e( 'Greeting', 'speakout' ); ?></label>
						<input name="greeting" id="greeting" value="<?php echo stripcslashes( $petition->greeting ); ?>" size="40" maxlength="80" type="text" />
					</div>
				</div>


				<label for="petition_message"  class="required"><?php _e( 'Petition Message', 'speakout' ); ?></label>
				<textarea name="petition_message" id="petition_message" rows="10" cols="80"><?php echo stripcslashes( $petition->petition_message ); ?></textarea>
                <div class="insert_tags"><?php _e('You can personalise the message by inserting tags'); ?>: %honorific%  %first_name%  %last_name% %petition_title%</div>
                <div class="markdown"><?php _e('You or the signer can format the message using Markdown syntax - see'); ?> <a href="https://speakoutpetitions.com/markdown-guide/" target="_new">https://speakoutpetitions.com/markdown-guide</a></div>
			
 				<label for="petition_footer"><?php _e( 'Petition Footer (below signature)', 'speakout' ); ?></label>
				<textarea name="petition_footer" id="petition_footer" rows="3" cols="80"><?php echo stripcslashes( $petition->petition_footer ); ?></textarea>
		</div>

		<div class="postbox">
			<label for="x_message"><?php _e( 'X (Twiter) Message', 'speakout' ); ?></label>
				<textarea name="x_message" id="x_message" rows="2" cols="80"><?php echo stripcslashes( $petition->x_message ); ?></textarea>
				<div id="x-counter"></div> 
		</div>

	</div> <!-- end tab 1 -->

    <div id="dk-petition-tab-02" class="dk-speakout-hidden dk-petition-tabcontent">
        <div class="postbox">
    		<div id="minor-publishing">
    
    			<div class="misc-pub-section  dk-speakout-hide-email-option" <?php if ( $petition->hide_email_field == '1' ){ echo 'style="display:block;"'; }else{ echo 'style="display:none;"';} ?> >
    			  <div class="dk-speakout-checkbox"> 
    			 <?php 
				    if ( $petition->hide_email_field == '1' ){
				        echo '<input type="checkbox" name="hide_email_field_dummy" id="hide_email_field_dummy" checked="checked" disabled="disabled"/>';
				        echo '<input type="hidden" name="hide_email_field" id="hide_email_field" value="1" />';
				    }
				else{ 
				?>
			        <input type="checkbox" name="hide_email_field" id="hide_email_field" />
					        
				<?php 
					} 
				?>
    			   
    					<label for="hide_email_field" id="hide-email-field-label" class="dk-speakout-inline"><?php _e( "Don't collect email address", 'speakout'); ?></label><br>
    					<span class="hide_email_warning"><?php _e( "This cannot be reversed", 'speakout'); ?> <a href='https://speakoutpetitions.com/faqconc/why-cant-no-email-address-be-reversed/' target="_blank">?</a></span>
    				</div>
    			</div>
    
    			<div class="misc-pub-section">
    			    <div class="dk-speakout-checkbox" <?php  if ( $petition->hide_email_field == '1' ){echo 'style="display:none;"'; } ?> >
                        <input type="checkbox" name="allow_anonymous" id="allow-anonymous" <?php if ( $petition->allow_anonymous == 1 ) echo 'checked="checked"'; ?> />
    					<label for="allow_anonymous" id="allow-anonymous-label" class="dk-speakout-inline"><?php _e( "Allow public anonymous", 'speakout'); ?></label> <a href="https://speakoutpetitions.com/faqconc/can-people-sign-anonymously/" target="_blank">?</a><br>
    				</div>
    			</div>
    			
    									
    			<!-- Email Confirmation -->
    			<div class="misc-pub-section">
    				<div class="dk-speakout-checkbox">
    					<input type="checkbox" name="requires_confirmation" id="requires_confirmation" <?php if ( $petition->requires_confirmation == 1 ) echo 'checked="checked"'; ?> />
    					<label for="requires_confirmation" id="requires_confirmation-label" class="dk-speakout-inline"><?php _e( 'Confirm signatures', 'speakout'); ?></label>
    				</div>
    				<div class="margin-20-left dk-speakout-returnurl dk-speakout-subsection <?php if ( $petition->requires_confirmation != 1 ) echo 'dk-speakout-hidden'; ?>">
    					<label for="return_url"><?php _e( 'Return URL', 'speakout'); ?>:</label>
    					<input id="return_url" name="return_url" value="<?php echo esc_attr( $petition->return_url ); ?>" size="30" maxlength="200" type="text" />
    				</div>
    			</div>
    
    			<!-- Editable -->
    			<div class="misc-pub-section">
    				<div class="dk-speakout-checkbox">
    					<span class="freeCheck"><a href="<?php site_url(); ?>/wp-admin/admin.php?page=dk_speakout_upgrade"><img src="<?php echo content_url(); ?>/plugins/speakout/images/lock.png"></a></span>
    					<label for="is_editable" class="dk-speakout-inline"><?php _e( 'Allow message to be edited', 'speakout'); ?></label>
    				</div>
    			</div>
    
    			<!-- Signature Goal -->
    			<div class="misc-pub-section">
    				<div class="dk-speakout-checkbox">
    					<input type="checkbox" name="has_goal" id="has_goal" <?php if ( $petition->goal > 0 ) echo 'checked="checked"'; ?> />
    					<label for="has_goal" class="dk-speakout-inline"><?php _e( 'Set signature goal', 'speakout'); ?></label>
    				</div>
    				<div class="margin-20-left dk-speakout-goal dk-speakout-subsection <?php if ( $petition->goal < 1 ) echo ' dk-speakout-hidden'; ?>">
    					<label for="goal"><?php _e( 'Goal', 'speakout'); ?>:</label>
    					<input id="goal" name="goal" value="<?php echo esc_attr( $petition->goal ); ?>" size="8" maxlength="8" type="text" /><br />
        					<input type="checkbox" name="increase_goal" id="increase_goal" <?php if( $petition->increase_goal == 1 ) echo 'checked="checked"'; ?> />
        					<?php _e( 'Auto increase goal', 'speakout'); ?> 
        					    <span class='goal-options <?php if( $petition->increase_goal < 1 ) echo "dk-speakout-hidden"; ?>'><?php _e( 'by', 'speakout'); ?> <input id="goal_bump" name="goal_bump" value="<?php echo esc_attr( $petition->goal_bump ); ?>" size="8" maxlength="8" type="text" /> 
        					    <?php _e( 'when count', 'speakout'); ?> = <input id="goal_trigger" name="goal_trigger" value="<?php echo esc_attr( $petition->goal_trigger ); ?>" size="3" maxlength="3" type="text" />% <?php _e( 'of goal', 'speakout'); ?></span> <a href="https://speakoutpetitions.com/faqconc/auto-update-the-goal/" target="_blank">?</a>
    				</div>
    			</div>
    
    			<!-- Expiration Date -->
    			<div class="misc-pub-section misc-pub-section-last">
    				<div class="dk-speakout-checkbox">
    					<span class="freeCheck"><a href="<?php site_url(); ?>/wp-admin/admin.php?page=dk_speakout_upgrade"><img src="<?php echo content_url(); ?>/plugins/speakout/images/lock.png"></a></span>
    					<label for="expires" class="dk-speakout-inline"><?php _e( 'Set expiration date', 'speakout'); ?></label>
    				</div>
    			</div>
    			
                <!-- Reirection URL -->
    			<div class="misc-pub-section">
    				<div class="dk-speakout-checkbox">
    					<span class="freeCheck"><a href="<?php site_url(); ?>/wp-admin/admin.php?page=dk_speakout_upgrade"><img src="<?php echo content_url(); ?>/plugins/speakout/images/lock.png"></a></span>
    					<label for="redirect_url_option" class="dk-speakout-inline"><?php _e( 'Redirect after successful sign', 'speakout'); ?></label>
    				</div>
    			</div>
    		</div>
        </div> <!-- end postbox -->
    </div> <!-- end tab 2 -->
    
    <div id="dk-petition-tab-03" class="dk-speakout-hidden dk-petition-tabcontent">
        <div class="postbox">
        	<div id="minor-publishing">
        	   <!-- read petition option -->
                <div class="misc-pub-section">
        		    <div class="dk-speakout-checkbox">
        				<label for="open_message_button" class="dk-speakout-inline"><?php _e( 'Text to open/close petition message', 'speakout'); ?></label>
        				<span class="freeText"><?php echo esc_html( $petition->open_message_button ); ?></span> <a href="<?php site_url(); ?>/wp-admin/admin.php?page=dk_speakout_upgrade"><img src="<?php echo content_url(); ?>/plugins/speakout/images/lock.png"></a> <a href="https://speakoutpetitions.com/faqconc/can-i-change-read-the-petition-text/" target="_blank">?</a>
        				
        			</div>
        		</div>
        		<div class="misc-pub-section">
                    <div class="dk-speakout-checkbox">
        				<label for="open_editable_message_button" class="dk-speakout-inline"><?php _e( 'Text to open/close editable petition message', 'speakout'); ?></label>
        				<span class="freeText"><?php echo esc_html( $petition->open_editable_message_button ); ?></span> <a href="<?php site_url(); ?>/wp-admin/admin.php?page=dk_speakout_upgrade"><img src="<?php echo content_url(); ?>/plugins/speakout/images/lock.png"></a> <a href="https://speakoutpetitions.com/faqconc/can-i-change-read-the-petition-text/" target="_blank">?</a>
        				
        			</div>
        		</div>        		
        		<!-- Email Opt-in -->
        		<div class="misc-pub-section">
        			<div class="dk-speakout-checkbox">
        				<input type="checkbox" name="displays-optin" id="displays-optin" <?php if ( $petition->displays_optin == '1' ) echo 'checked="checked"'; ?> />
        				<label for="displays-optin" id="displays-optin-label" class="dk-speakout-inline"><?php _e( 'Display opt-in checkbox', 'speakout'); ?></label>
        			</div>
        			<div class="margin-20-left dk-speakout-optin dk-speakout-subsection <?php if ( $petition->displays_optin != '1' ) echo 'dk-speakout-hidden'; ?>">
        				<label for="optin-label"><?php _e( 'Label', 'speakout'); ?>:</label>
        				<input id="optin-label" name="optin-label" value="<?php echo esc_html( $petition->optin_label ); ?>" size="30" maxlength="200" type="text" />
        			</div>
        		</div>
        		
        		<div class="misc-pub-section">
        		    <div class="dk-speakout-checkbox">
        				<input type="checkbox" name="display_petition_message" id="display_petition_message" <?php if ( $petition->display_petition_message == 1 || $action == "create") echo 'checked="checked"'; ?> />
        				<label for="display_petition_message" class="dk-speakout-inline"><?php _e( 'Display petition message', 'speakout'); ?></label>
        			</div>
        		</div>
        		
        		<div class="misc-pub-section">
        			<div class="dk-speakout-checkbox ">
        				<input type="checkbox" name="display-address" id="display-address" <?php if ( count( $petition->address_fields ) > 0 ) echo 'checked="checked"'; ?> />
        				<label for="display-address" class="dk-speakout-inline"><?php _e( 'Display address fields', 'speakout'); ?></label>
        			</div>
        			
        			<div class="dk-speakout-address dk-speakout-subsection <?php if( count( $petition->address_fields ) == 0 ) echo 'dk-speakout-hidden'; ?>">
        			    
        			    <table id="dk-speakout-address-block" class="margin-20-left" cellspacing = "0">
        			        <tr style="font-size:80%;">
        			            <td><?php _e( 'Display', 'speakout'); ?>?</td>
        			            <td><?php _e( 'Required', 'speakout'); ?>?</td>
        			            <td></td>
        			        </tr>
        			        <tr>
        			            <td><input type="checkbox" id="street" name="street" <?php if ( in_array( 'street', $petition->address_fields ) ) echo 'checked="checked"'; ?> /></td>
        			            <td><input type="checkbox" id="street-required"  name="street-required" <?php if ( $petition->street_required == 1 ) echo 'checked="checked"'; ?> /></td>
        						<td class="keep-left"><label for="street" ><?php _e( 'Street', 'speakout'); ?></label></td>
                            </tr>
                            <tr>
        						<td><input type="checkbox" id="city" name="city" <?php if ( in_array( 'city', $petition->address_fields ) ) echo 'checked="checked"'; ?> /></td>
        						<td><input type="checkbox" id="city-required" name="city-required" <?php if ( $petition->city_required == 1 ) echo 'checked="checked"'; ?> /></td>
        						<td class="keep-left"><label for="city"><?php _e( 'City', 'speakout'); ?></label></td>
                            </tr>
                            <tr>
        						<td><input type="checkbox" id="state" name="state" <?php if ( in_array( 'state', $petition->address_fields ) ) echo 'checked="checked"'; ?> /></td>
        						<td style="text-align:center"><input type="checkbox" id="state-required"  name="state-required" <?php if ( $petition->state_required == 1 ) echo 'checked="checked"'; ?> /></td>
        						<td class="keep-left"><label for="state"><?php _e( 'State / Province', 'speakout'); ?></label></td>
                            </tr>
                            <tr>
        						<td valign="top"><input type="checkbox" id="postcode" name="postcode" <?php if ( in_array( 'postcode', $petition->address_fields ) ) echo 'checked="checked"'; ?> /></td>
        						<td valign="top" style="text-align:center"><input type="checkbox" id="postcode-required"  name="postcode-required"<?php if ( $petition->postcode_required == 1 ) echo 'checked="checked"'; ?> /></td>
        						<td class="keep-left"><label for="postcode"><?php _e( 'Postal Code - see settings for EU style', 'speakout'); ?></label></td>
                            </tr>
                            <tr>
        						<td><input type="checkbox" id="country" name="country" <?php if ( in_array( 'country', $petition->address_fields ) ) echo 'checked="checked"'; ?> /></td>
        						<td style="text-align:center"><input type="checkbox" id="country-required"  name="country-required"<?php if ( $petition->country_required == 1 ) echo 'checked="checked"'; ?> /></td>
        						<td class="keep-left"><label for="country"><?php _e( 'Country', 'speakout'); ?></label></td>
        					</tr>
        				
        				</table>
        				
                   </div>
        		</div>
        
        		<!-- Custom Field #1 -->
        		<div class="misc-pub-section lineAbove lineBelow">
        			<div class="dk-speakout-checkbox">
        				<input type="checkbox" name="displays-custom-field" id="displays-custom-field" <?php if ( $petition->displays_custom_field == 1 ) echo 'checked="checked"'; ?> />
        				<label for="displays-custom-field" class="dk-speakout-inline"><?php _e( 'Display custom field 1', 'speakout'); ?></label> <a href="https://speakoutpetitions.com/faqconc/can-i-add-a-custom-field/" target="_blank">?</a><i class="fa-solid fa-info"></i>
        			</div>
        			
        			<div class="margin-20-left dk-speakout-custom-field dk-speakout-subsection <?php if( $petition->displays_custom_field != 1 ) echo 'dk-speakout-hidden'; ?>">							    
        			    <input type="checkbox" id="custom-field-included" name="custom-field-included" value="1" <?php if ( $petition->custom_field_included == 1 ) echo 'checked="checked"'; ?> /> 
        				<label for="custom-field-included"><?php _e( 'Include with petition', 'speakout'); ?></label><br />
        				
        				<input type="checkbox" id="custom-field-required" name="custom-field-required" value="1"  <?php if ( $petition->custom_field_required == 1 ) echo 'checked="checked"'; ?> /> 
        				<label for="custom-field-required"><?php _e( 'Required', 'speakout'); ?></label><br />
        				
        				<label for="custom-field-label"><?php _e( 'Label', 'speakout'); ?>:</label>
        				<input id="custom-field-label" name="custom-field-label" value="<?php 
                            if($petition->custom_field_label > ""){
                                echo trim( esc_html( $petition->custom_field_label ) );
                            }
                            else{
                                echo "Custom field 1";
                            }
                           ?>" size="30" maxlength="200" type="text" <?php if ( $petition->displays_custom_field == 1 ) echo 'required="required"'; ?> />
        				
        				<br /><?php _e( 'Location', 'speakout'); ?><br /><input type="radio" name="custom-field-location" id="custom-field-location-top" value="1" <?php if ($petition->custom_field_location == 1) {echo " CHECKED";} ?> />
        				<label for="custom-field-location-top"><?php _e( 'Top', 'speakout'); ?></label><br />
                        <input type="radio" name="custom-field-location" id="custom-field-location-middle" value="2" <?php if ($petition->custom_field_location == 2) {echo " CHECKED";}  ?>/>
        				<label for="custom-field-location-middle"><?php _e( 'Above email', 'speakout'); ?></label><br />
        
                        <input type="radio" name="custom-field-location" id="custom-field-location-bottom" value="3" <?php if ($petition->custom_field_location == 3) {echo " CHECKED"; } ?>/>
        				<label for="custom-field-location-bottom"><?php _e( 'Bottom', 'speakout'); ?></label> 				
        			</div>
        		</div>
 
        		<!-- Custom Field #2 -->
        		<div class="misc-pub-section lineBelow">
        			<div class="dk-speakout-checkbox">
        				<span class="freeCheck"><a href="<?php site_url(); ?>/wp-admin/admin.php?page=dk_speakout_upgrade"><img src="<?php echo content_url(); ?>/plugins/speakout/images/lock.png"></a></span>
        				<label for="displays-custom-field2" class="dk-speakout-inline"><?php _e( 'Display custom field 2', 'speakout'); ?></label>
        			</div>
        		</div>
                
        		<!-- Custom Field #3 -->
        		<div class="misc-pub-section lineBelow">
        			<div class="dk-speakout-checkbox">
        				<span class="freeCheck"><a href="<?php site_url(); ?>/wp-admin/admin.php?page=dk_speakout_upgrade"><img src="<?php echo content_url(); ?>/plugins/speakout/images/lock.png"></a></span>
        				<label for="displays-custom-field3" class="dk-speakout-inline"><?php _e( 'Display custom field 3', 'speakout'); ?></label>
        			</div>
        		</div>
                
                
        		<!-- Custom Field #4 -->
        		<div class="misc-pub-section lineBelow">
        			<div class="dk-speakout-checkbox">
        				<span class="freeCheck"><a href="<?php site_url(); ?>/wp-admin/admin.php?page=dk_speakout_upgrade"><img src="<?php echo content_url(); ?>/plugins/speakout/images/lock.png"></a></span>
        				<label for="displays-custom-field4" class="dk-speakout-inline"><?php _e( 'Display custom field 4', 'speakout'); ?></label>
        			</div>
        		</div>
                
        		<!-- Custom Field #5 -->
        		<div class="misc-pub-section lineBelow">
        			<div class="dk-speakout-checkbox">
        				<span class="freeCheck"><a href="<?php site_url(); ?>/wp-admin/admin.php?page=dk_speakout_upgrade"><img src="<?php echo content_url(); ?>/plugins/speakout/images/lock.png"></a></span>
        				<label for="displays-custom5-field" class="dk-speakout-inline"><?php _e( 'Display custom drop-down', 'speakout'); ?> 1</label>
        			</div>
        		</div>
        		
          		<!-- Custom Field #6 -->
        		<div class="misc-pub-section lineBelow">
        			<div class="dk-speakout-checkbox">
        				<span class="freeCheck"><a href="<?php site_url(); ?>/wp-admin/admin.php?page=dk_speakout_upgrade"><img src="<?php echo content_url(); ?>/plugins/speakout/images/lock.png"></a></span>
        				<label for="displays-custom-field6" class="dk-speakout-inline"><?php _e( 'Display custom checkbox 1', 'speakout'); ?></label>
        			</div>
        		</div>
                      		
        		
          		<!-- Custom Field #7 -->
        		<div class="misc-pub-section lineBelow">
        			<div class="dk-speakout-checkbox">
        				<span class="freeCheck"><a href="<?php site_url(); ?>/wp-admin/admin.php?page=dk_speakout_upgrade"><img src="<?php echo content_url(); ?>/plugins/speakout/images/lock.png"></a></span>
        				<label for="displays-custom-field7" class="dk-speakout-inline"><?php _e( 'Display custom checkbox 2', 'speakout'); ?></label>
        			</div>
        		</div>
 
                
                
                <!-- Custom Field #8 -->
        		<div class="misc-pub-section lineBelow">
        			<div class="dk-speakout-checkbox">
        				<span class="freeCheck"><a href="<?php site_url(); ?>/wp-admin/admin.php?page=dk_speakout_upgrade"><img src="<?php echo content_url(); ?>/plugins/speakout/images/lock.png"></a></span>
        				<label for="displays-custom-field8" class="dk-speakout-inline"><?php _e( 'Display custom checkbox 3', 'speakout'); ?></label>
        			</div>
        			
        		</div>
                
                <!-- Custom Field #9 -->
        		<div class="misc-pub-section lineBelow">
        			<div class="dk-speakout-checkbox">
        				<span class="freeCheck"><a href="<?php site_url(); ?>/wp-admin/admin.php?page=dk_speakout_upgrade"><img src="<?php echo content_url(); ?>/plugins/speakout/images/lock.png"></a></span>
        				<label for="displays-custom-field9" class="dk-speakout-inline"><?php _e( 'Display custom checkbox 4', 'speakout'); ?></label>
        			</div>
        			
        		</div>
        		
        		<!-- Custom Message -->
        		<div class="misc-pub-section   misc-pub-section-last">
        		    
        			<div class="dk-speakout-checkbox">
        				<span class="freeCheck"><a href="<?php site_url(); ?>/wp-admin/admin.php?page=dk_speakout_upgrade"><img src="<?php echo content_url(); ?>/plugins/speakout/images/lock.png"></a></span>
        				<label for="displays-custom-message" class="dk-speakout-inline"><?php _e( 'Display custom message', 'speakout'); ?></label>
        			</div><span class="margin-20-left"><?php _e( 'Displayed beneath `Thank you` after petition is signed', 'speakout'); ?></span><br />
        			
        			<div class="margin-20-left dk-speakout-custom-message dk-speakout-subsection <?php if( $petition->displays_custom_message != 1 ) echo 'dk-speakout-hidden'; ?>">
        				<label for="custom-message-label"><?php _e( 'Message', 'speakout'); ?>:</label>
        				<input id="custom-message-label" name="custom-message-label" value="<?php echo trim( esc_html( $petition->custom_message_label ) ); ?>" size="30" maxlength="200" type="text" />
        			</div>
        			
        		</div>
    		</div>
    	</div>
    </div> <!-- end tab 3 -->
    
    <div id="dk-petition-tab-04" class="dk-speakout-hidden dk-petition-tabcontent">
        <div class="postbox">
            <div class="optin-warning"><?php _e("These features are only available in the Pro version", 'speakout'); ?></div>
            <div id="minor-publishing">
                
                <div class="misc-pub-section">
                    <div class="dk-speakout-checkbox ">
                        <span class="freeCheck"><a href="<?php site_url(); ?>/wp-admin/admin.php?page=dk_speakout_upgrade"><img src="<?php echo content_url(); ?>/plugins/speakout/images/lock.png"></a></span>
                        <label for="dk-speakout-activecampaign-enable" class="dk-speakout-inline"><?php _e( 'Enable ActiveCampaign', 'speakout'); ?></label>
                    </div>
                </div>   

                <div class="misc-pub-section">
                
                    <div class="dk-speakout-checkbox ">
                        <span class="freeCheck"><a href="<?php site_url(); ?>/wp-admin/admin.php?page=dk_speakout_upgrade"><img src="<?php echo content_url(); ?>/plugins/speakout/images/lock.png"></a></span>
                        <label for="dk-speakout-mailchimp-enable" class="dk-speakout-inline"><?php _e( 'Enable MailChimp', 'speakout'); ?></label>
                    </div>
                </div>
                    
                <div class="misc-pub-section">
                
                    <div class="dk-speakout-checkbox ">
                        <span class="freeCheck"><a href="<?php site_url(); ?>/wp-admin/admin.php?page=dk_speakout_upgrade"><img src="<?php echo content_url(); ?>/plugins/speakout/images/lock.png"></a></span>
                        <label for="dk-speakout-mailerlite-enable" class="dk-speakout-inline"><?php _e( 'Enable Mailerlite', 'speakout'); ?></label>
                    </div>
                </div>
            
            
                <div class="misc-pub-section">
                
                    <div class="dk-speakout-checkbox ">
                        <span class="freeCheck"><a href="<?php site_url(); ?>/wp-admin/admin.php?page=dk_speakout_upgrade"><img src="<?php echo content_url(); ?>/plugins/speakout/images/lock.png"></a></span>
                        <label for="dk-speakout-sendy-enable" class="dk-speakout-inline"><?php _e( 'Enable Sendy', 'speakout'); ?></label>
                    </div>
                </div>
            
            </div>
        </div>
    </div> <!-- end tab 4 -->
</div>

<input type="submit" name="Submit" id="dk_speakout_submit" value="<?php echo esc_html( $button_text ); ?>" class="button-primary" 
		
	</form>

</div>