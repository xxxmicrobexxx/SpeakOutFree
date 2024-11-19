<div class="wrap" id="dk-speakout">

	<div id="icon-dk-speakout" class="icon32"><br /></div>
	<h2><?php echo esc_html( $page_title ); ?>
	<?php
        
            $upgradeDisplay = ' <span class="upgrade-message"> ' . __('In the free version you are limited to one petition', 'speakout') . '. '  . __('Any additional existing petitions will still work but cannot be edited', 'speakout') . '. <a href="' . site_url() . '/wp-admin/admin.php?page=dk_speakout_upgrade">' . __("Upgrade to Pro", "speakout") . '</a></span></h2>';
        
            echo wp_kses(  $upgradeDisplay , $allowed_tags );
            
        if ( $message_update ) echo '<div id="message" class="updated"><p>' . $message_update . '</p></div>' 
    ?>

	<div class="tablenav">
		<ul class='subsubsub'>
			<li class='table-label'><?php _e( 'All Petitions', 'speakout' ); ?> <span class="count">(<?php echo abs( $count ); ?>)</span></li>
		</ul>
		<?php echo dk_speakout_SpeakOut::pagination( $query_limit, $count, 'speakout', $current_page, site_url( 'wp-admin/admin.php?page=dk_speakout_top' ), true ); ?>
	</div>

	<table class="widefat">
		<thead>
			<tr>
				<th><?php _e( 'ID', 'speakout' ); ?></th>
				<th><?php _e( 'Petition name', 'speakout' ); ?></th>
				<th><?php _e( 'Shortcodes', 'speakout' ); ?></th>
				<th class="dk-speakout-right"><?php _e( 'Signatures', 'speakout' ); ?></th>
				<th class="dk-speakout-right"><?php _e( 'Goal', 'speakout' ); ?></th>
				<th></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th><?php _e( 'ID', 'speakout' ); ?></th>
				<th><?php _e( 'Petition name', 'speakout' ); ?></th>
				<th><?php _e( 'Shortcodes', 'speakout' ); ?></th>
				<th class="dk-speakout-right"><?php _e( 'Signatures', 'speakout' ); ?></th>
				<th class="dk-speakout-right"><?php _e( 'Goal', 'speakout' ); ?></th>
				<th></th>
			</tr>
		</tfoot>
		<tbody>
		<?php if ( $count == 0 ) echo '<tr><td colspan="5">' . __( "No petitions found.", "speakout" ) . ' </td></tr>'; 
		    $petitionNumber = 0;
		?>
		<?php foreach ( $petitions as $petition ) : ?>
			<?php 
			    
			    $petitionNumber++;
                $edit_url       = esc_url( wp_nonce_url( site_url() . '/wp-admin/admin.php?page=dk_speakout_addnewpage', 'dk_speakout-edit_petition' . $petition->id ) ) ; 
                $delete_url     = esc_url( wp_nonce_url( site_url() . '/wp-admin/admin.php?page=dk_speakout_top&action=delete&id=' . $petition->id, 'dk_speakout-delete_petition' . $petition->id ) ); 
                $duplicate_url  = esc_url( wp_nonce_url( site_url() . '/wp-admin/admin.php?page=dk_speakout_addnew&action=duplicate&id=' . $petition->id, 'dk_speakout-edit_petition' . $petition->id ) );
                $signatures_url = esc_url( site_url() . '/wp-admin/admin.php?page=dk_speakout_signatures&action=petition&pid=' . $petition->id );  ?>
			
			<tr class="dk-speakout-tablerow">
				<td>
					<?php	// petition ID column
                        if( $petitionNumber == 1){
                            echo wp_kses(   '<a class="row-title" href="' . $edit_url . '">' . stripslashes( esc_html( $petition->id ) ). '</a>', $allowed_tags );
                            $edit_url = wp_nonce_url( esc_url( site_url() . '/wp-admin/admin.php?page=dk_speakout_addnew_page', 'dk_speakout-edit_petition' . $petition->id ) ) ;    
                        }
                        else{
                            echo wp_kses(  $petition->id , $allowed_tags ); 
                        }
                    ?>
					
				</td>
				
				<td>
					<?php // petition title column
                        if($petitionNumber == 1 ){
                            $displayTitle = '<a class="row-title" href="'. $edit_url . '" >' . stripslashes( esc_html( $petition->title ) ) . '</a>';   
                        }
                        else{
                            $displayTitle = '<a href="' . site_url() . '/wp-admin/admin.php?page=dk_speakout_upgrade"><img src="' . content_url() .'/plugins/speakout/images/lock.png"></a> ' . stripslashes( esc_html( $petition->title ) );
                        }
                        echo wp_kses(  $displayTitle, $allowed_tags );
                    ?>
                    
					<div class="row-actions">
                    <?php // actions column
                            if($petitionNumber == 1){
                                $displayMenu  = '<span class="edit"><a href="' .$edit_url . '">' . __( 'Edit', 'speakout' ) . '</a></span>';
                            }
                            else{
        						$displayMenu = '<span><a href="' . $delete_url . '" class="dk-speakout-delete-petition">' . __( 'Delete', 'speakout' ) . '</a></span>';
                            }
                        echo wp_kses( $displayMenu  , $allowed_tags );
                    ?>
					</div>
				</td>

				<td><?php echo '[emailpetition id="' . $petition->id . '"] - ' .  __( 'display petition', 'speakout' )  . 
				'<br />[signaturelist id="' . $petition->id . '"] - ' . __( 'display signature list', 'speakout' ) . 
				'<br />[signaturecount id="' . $petition->id . '"] - ' . __( 'display signature count', 'speakout' )  .
				'<br />[signaturegoal id="' . $petition->id . '"] - ' . __( 'display signature goal', 'speakout' ) .
				'<br />[petitiontitle id="' . $petition->id . '"] - ' . __( 'display petition title', 'speakout' ) .
				'<br />[petitionmessage id="' . $petition->id . '"] - ' . __( 'display petition message', 'speakout' ) ; ?></td>
				
				<td class="dk-speakout-right"><?php echo number_format( $petition->signatures ); ?></td>
				<td class="dk-speakout-right">
					<?php echo number_format( $petition->goal ); ?>
					<div class="dk_speakout_clear"></div>
					<?php echo dk_speakout_SpeakOut::progress_bar( $petition->goal, $petition->signatures, 65 ); ?>
				</td>
				<td class="dk-speakout-right" style="vertical-align: middle"><a class="button" href="<?php echo esc_url( $signatures_url ); ?>"><?php _e( 'View Signatures', 'speakout' ); ?></a></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<div class="tablenav">
		<?php echo dk_speakout_SpeakOut::pagination( $query_limit, $count, 'speakout', $current_page, site_url( 'wp-admin/admin.php?page=dk_speakout' ), false ); ?>
	</div>

	<div id="dk-speakout-delete-confirmation" class="dk-speakout-hidden"><?php _e( 'Delete this petition permanently? All of the petition\'s signatures will be deleted as well.', 'speakout' ); ?></div>

</div>