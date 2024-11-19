jQuery( document ).ready( function( $ ) {
	'use strict';
	
/* Add New page
------------------------------------------------------------------- */
	$( 'input#requires_confirmation' ).change( function () {
		if ( $( this ).prop( 'checked' ) ) {
			$( 'div.dk-speakout-returnurl' ).slideDown();
			$( '#dk-speakout input#return_url' ).focus();
		} else {
			$( 'div.dk-speakout-returnurl' ).slideUp();
		}
	});

	// open or close signature goal settings
	$( 'input#has_goal' ).change( function () {
		if ( $( this ).prop( 'checked' ) ) {
			$( 'div.dk-speakout-goal' ).slideDown();
			$( '#dk-speakout input#goal' ).focus();
		} else {
			$( 'div.dk-speakout-goal' ).slideUp();
		}
	});

	$( '#increase_goal' ).change( function () {
		if ( $( this ).prop( 'checked' ) ) {
            $('.goal-options').show();
			$( '#dk-speakout input#goal_bump' ).focus();
		} else {
			$('.goal-options').hide();
		}
	});	
	
	// open or close address fields settings
	$( 'input#display-address' ).change( function () {
		if ( $( this ).prop( 'checked' ) ) {
			$( 'div.dk-speakout-address' ).slideDown();
		} else {
			$( 'div.dk-speakout-address' ).slideUp();
		}
	});

	// open or close custom field settings
	$( 'input#displays-custom-field' ).change( function () {
		if ( $( this ).prop( 'checked' ) ) {
			$( 'div.dk-speakout-custom-field' ).slideDown();
			$( '#dk-speakout input#custom-field-label' ).focus();
		} else {
			$( 'div.dk-speakout-custom-field' ).slideUp();
		}
	});

	// open or close email opt-in settings
	$( 'input#displays-optin' ).change( function () {
		if ( $( this ).prop( 'checked' ) ) {
			$( 'div.dk-speakout-optin' ).slideDown();
			$( '#dk-speakout input#optin-label' ).focus();
		} else {
			$( 'div.dk-speakout-optin' ).slideUp();
		}
	});

	// if hide_email_field is checked in db
	if ( $( 'input#hide_email_field' ).prop( 'checked' ) ) {
        $( '#displays-optin' ).attr( "disabled", true );
        $( '#displays-optin-label' ).css( 'color', 'grey' );
        $( 'input#hide_email_field' ).attr( "disabled", true );
        $( 'input#sends_email' ).attr( "disabled", true );
        $( 'input#requires_confirmation' ).attr( "disabled", true );
        $( '#requires_confirmation-label' ).css( 'color', 'grey' );
	}

	// if changing hide_email_address do some things
	$( 'input#hide_email_field' ).change( function () {
		if ( $( this ).prop( 'checked' ) ) {
			$( 'input#displays-optin' ).attr( "disabled", true );
			$( 'input#displays-optin' ).prop( "checked", false );
			$( '#displays-optin' ).attr("disabled", true );
			$( '#displays-optin-label' ).css( 'color', 'grey' );
			$( 'div.dk-speakout-optin' ).slideUp();
			$( 'input#requires_confirmation' ).attr( "disabled", true );
            $( '#requires_confirmation-label' ).css( 'color', 'grey' );
            $( '.hide_email_warning' ).css( 'color', 'red' );			
		} else {
			$( '#displays-optin' ).attr( "disabled", false );
			$( '#displays-optin-label' ).css( 'color', 'black' );
			$( 'input#requires_confirmation' ).attr( "disabled", false );
            $( '#requires_confirmation-label' ).css( 'color', 'black' );
             $( '.hide_email_warning' ).css( 'color', 'black' );
		}
	});
	
	// open or close email header settings on start
	if ( $( 'input#sends_email' ).prop( 'checked' ) ) {
		$( 'div.dk-speakout-email-headers' ).hide();
		$( 'div.dk-speakout-hide-email-option' ).show();
        $( '.sends_email_label' ).append(' - you should definitely read <a href="https://speakoutpetitions.com/faqconc/why-cant-no-email-address-be-reversed/" target="_blank">this</a>' );		
	}
	
	$( 'input#sends_email' ).change( function () {	    
		if ( $( this ).prop( 'checked' ) ) {
			$( 'div.dk-speakout-email-headers' ).slideUp();
			$( 'div.dk-speakout-hide-email-option' ).slideDown();
            $( '.sends_email_label' ).append(' - you should definitely read <a href="https://speakoutpetitions.com/faqconc/why-cant-no-email-address-be-reversed/" target="_blank">this</a>' );	
		} else {
			$( 'div.dk-speakout-email-headers' ).slideDown();
			$( 'div.dk-speakout-hide-email-option' ).slideUp();
			$( '#hide_email_field' ).prop('checked', false);
			$( '.sends_email_label' ).text('Do not send email (only collect signatures)' );
		}
	}); 
	
    	// auto-focus the title field on add/edit petitions form if empty
	if ( $( '#dk-speakout input#title' ).val() === '' ) {
		$( '#dk-speakout input#title' ).focus();
	}

	// validate form values before submitting
	$( '#dk_speakout_submit' ).click( function() {

		$( '.dk-speakout-error' ).removeClass( 'dk-speakout-error' );

		var errors     = 0,
			emailRegEx = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,6})?$/,
			email      = $( '#dk-speakout-edit-petition #target_email' ).val(),
			emailCC    = $( '#dk-speakout-edit-petition #target_email_CC' ).val(),
			subject    = $( '#dk-speakout-edit-petition #email_subject' ).val(),
			message    = $( '#dk-speakout-edit-petition #petition_message' ).val(),
			customlabel = $( '#dk-speakout-edit-petition #custom-field-label' ).val(),
			//messagedisplay = $( '#dk-speakout-edit-petition input#display_petition_message' ).prop(),
			goal       = $( '#dk-speakout-edit-petition #goal' ).val(),
            day        = $( '#dk-speakout-edit-petition #day' ).val(),
            year       = $( '#dk-speakout-edit-petition #year' ).val(),
            hour       = $( '#dk-speakout-edit-petition #hour' ).val(),
            minutes    = $( '#dk-speakout-edit-petition #minutes' ).val(),
            delay       = Math.floor($( '#dk-speakout-edit-petition #redirect_delay' ).val());

		// if "Do not send email (only collect signatures)" checkbox is not checked
		if ( !$( 'input#sends_email' ).prop( 'checked' ) ) {
			// remove any spaces
			var emails = email.split( ',' );
			for ( var i=0; i < emails.length; i++ ) {
				if ( emails[i].trim() === '' || !emailRegEx.test( emails[i].trim() ) ) { // must include valid email address
					$( '#dk-speakout-edit-petition #target_email' ).addClass( 'dk-speakout-error' );
					errors ++;
				}
			}			
			if ( subject === '' ) { // must include subject
				$( '#dk-speakout-edit-petition #email_subject' ).addClass( 'dk-speakout-error' );
				errors ++;
			}
		}
		// remove any spaces
			var emailsCC = emailCC.split( ',' );
			for ( var x=0; x < emailsCC.length; x++ ) {
				if (emailsCC[x].trim() !== '' && !emailRegEx.test( emailsCC[x].trim() ) ) { // If email address included it must be valid
					$( '#dk-speakout-edit-petition #target_email_CC' ).addClass( 'dk-speakout-error' );
					errors ++;
				}
			}

		// must include petition message if message is being displayed
		if ( message === '' && $( 'input#display_petition_message' ).prop( 'checked' ) ) { 	
			$( '#dk-speakout-edit-petition #petition_message' ).addClass( 'dk-speakout-error' );
			errors ++;
		}

		// must include custom field label if using custom field
		if ( $( 'input#displays-custom-field' ).prop( 'checked' )  && customlabel === "") { 
			$( '#dk-speakout-edit-petition #custom-field-label' ).addClass( 'dk-speakout-error' );
			errors ++;
		}
		
		// if "Set signature goal" checkbox is checked
		if ( $( 'input#has_goal' ).prop( 'checked' ) ) {
			if ( isNaN( goal ) ) { // only numbers are allowed
				$( '#dk-speakout-edit-petition #goal' ).addClass( 'dk-speakout-error' );
				errors ++;
			}
		}

		// if no errors found, submit the form
		if ( errors === 0 ) {

			// uncheck all address fields if "Display address fields" is not checked
			if ( ! $( 'input#display-address' ).prop( 'checked' ) ) {
				$( '#street' ).removeAttr( 'checked' );
				$( '#city' ).removeAttr( 'checked' );
				$( '#state' ).removeAttr( 'checked' );
				$( '#postcode' ).removeAttr( 'checked' );
				$( '#country' ).removeAttr( 'checked' );
				$( '#address-required' ).removeAttr( 'checked' );
			}

			$( 'form#dk-speakout-edit-petition' ).submit();
		}
		else {
			$( '.dk-speakout-error-msg' ).fadeIn();
		}

		return false;

	});

	// display character count for X Message field
	// max characters is 260 to accomodate the shortnened URL provided by X when submitted

	function dkSpeakoutXCount() {
		var max_characters = 260;
		var text = $( '#x_message' ).val();
		var character_count = text.length;
		var characters_left = max_characters - character_count;

		if ( character_count <= max_characters ) {
			$( '#x-counter' ).html( characters_left ).css( {'color':'#000','font-weight':'normal'} );
		}
		else {
			$( '#x-counter' ).html( characters_left + ' <<<' ).css({'color':'#c00', 'font-weight':'bold'});
		}
	}

	if ( $( '#x_message' ).length > 0 ) {
		dkSpeakoutXCount();
	}

	$( '#x_message' ).keyup( function() {
		dkSpeakoutXCount();
	});

/* Petitions page
------------------------------------------------------------------- */
	// display confirmation box when user tries to delete a petition
	// warns that all signatures associated with the petition will also be deleted
	$( '.dk-speakout-delete-petition' ).click( function( e ) {
		e.preventDefault();

		var delete_link = $( this ).attr( 'href' );
		// confirmation message is contained in a hidden div in the HTML
		// so that it is accessible to PHP translation methods
		var confirm_message = $( '#dk-speakout-delete-confirmation' ).html();
		// add new line characters for nicer confirm msg display
		confirm_message = confirm_message.replace( '? ', '?\n\n' );
		// display confirmation box
		var confirm_delete = confirm( confirm_message );
		// if user presses OK, process delete link
		if ( confirm_delete === true ) {
			document.location = delete_link;
		}
	});

/* Signatures page
------------------------------------------------------------------- */
	// Select box navigation on Signatures page
	// to switch between different petitions
	$('#dk-speakout-switch-petition').change( function() {
		var page    = 'dk_speakout_signatures',
			action  = 'petition',
			pid     = $('#dk-speakout-switch-petition option:selected').val(),
			baseurl = String( document.location ).split( '?' ),
			newurl  = baseurl[0] + '?page=' + page + '&action=' + action + '&pid=' + pid;
		document.location = newurl;
	});
    
    // select all signatures
    $("#dk-speakout-signatures-checkAll").click(function(){
        $('input:checkbox').not(this).prop('checked', this.checked);
    });
    

	// Bulk action on Signatures page
	$('#dk-speakout-bulk-action-form').submit( function() {
    
        //get the petition ID
        var pid     = $('#dk-speakout-switch-petition option:selected').val();
        
        // action based on selection
        switch ($('#dk-speakout-bulk-action-form option:selected').val()){
            case "csv-download":
                var page    = 'dk_speakout_signatures',
                baseurl = String( document.location ).split( '?' ),
                newurl  = baseurl[0] + '?page=' + page + '&pid=' + pid + '&csv=signatures&_wpnonce=' + $('#csvnonce').val();
            document.location = newurl;
    		return false;

    		case "display-unconfirmed":
        		    var link = window.location.href +"&action=displayunconfirmed&pid=" + pid + "&_wpnonce=" + $('#confnonce').val();
        	        document.location = link;
    		 return false;
    		 
    		case "confirm-resend":
    		    getSiglist()
        		// confirmation message is contained in a hidden div in the HTML
        		// so that it is accessible to PHP translation methods
        		var confirm_message = $( '#dk-speakout-reconfirm-confirmation' ).html();
        		// add new line characters for nicer confirm msg display
        		confirm_message = confirm_message.replace( '? ', '?\n\n' );
        		// display confirm box
        		var confirm_delete = confirm( confirm_message );
        		// if user presses OK, process delete link
        		
        		if ( confirm_delete === true ) {
        		    var link = window.location.href +"&action=reconfirm&pid=" + pid + "&_wpnonce=" + $('#confnonce').val() + "&siglist=" + siglist;
        	        document.location = link;
    		    }
    		 return false;
 
                 //get a list of checked signatures
                var siglist;
                var sigcount;
                function getSiglist(){
                    siglist = "";
                    sigcount = 0;
                    $('.dk-speakout-signaturecheck:checked').each(function() {
                        siglist = siglist + $(this).val() + "|";
                        sigcount++
                    });
                }
    
         	case "reconfirm-selected":
         		getSiglist();
        		// confirmation message is contained in a hidden div in the HTML
        		// so that it is accessible to PHP translation methods
        		var confirm_message = $( '#dk-speakout-reconfirm-selected-confirmation' ).html();
        		// add new line characters for nicer confirm msg display
        		confirm_message = confirm_message.replace( '? ', '?\n\n' );
        		// display confirm box
        		var confirm_delete = confirm( confirm_message );
        		// if user presses OK, process delete link
        		
        		if ( confirm_delete === true ) {
        		    var link = window.location.href +"&action=reconfirmselected&pid=" + pid + "&_wpnonce=" + $('#confnonce').val() + "&siglist=" + siglist;
        	        document.location = link;
    		    }
    		 return false;
     
     
         	case "forceconfirm-selected":
         	    getSiglist();
                
        		// confirmation message is contained in a hidden div in the HTML
        		// so that it is accessible to PHP translation methods
        		confirm_message = $( '#dk-speakout-forceconfirm-confirmation-open' ).html() + sigcount + $( '#dk-speakout-confirmation-close' ).html();
        		//handle plurals
                confirm_message = (confirm_message > 1) ? confirm_message + "s." : confirm_message + ".";
                // add new line characters for nicer confirm msg display
                confirm_message = confirm_message.replace( '? ', '?\n\n' );
                // display confirm box
                var confirm_force = confirm( confirm_message );
                
                // if user presses OK, process link
                if ( confirm_force === true ) {
                    $.ajax({
                        url: window.location.href,
                        data: "&action=forceconfirm" + "&_wpnonce=" + $('#confnonce').val() + "&pid=" + pid + "&siglist=" + siglist,
                        success: function(response) {
                            document.location = window.location.href
                        }
                    });
                }
                return false;  
                
            case "delete-selected":
                getSiglist();
                
                // confirmation message is contained in a hidden div in the HTML
                // so that it is accessible to PHP translation methods
                confirm_message = $( '#dk-speakout-delete-confirmation-open' ).html() + sigcount + $( '#dk-speakout-confirmation-close' ).html();
                //handle plurals
                confirm_message = (confirm_message > 1) ? confirm_message + "s." : confirm_message + "."; 
                
        		// add new line characters for nicer confirm msg display
        		confirm_message = confirm_message.replace( '? ', '?\n\n' );
        		// display confirm box
        		confirm_delete = confirm( confirm_message );
                
        		// if user presses OK, process link
            	if ( confirm_delete === true ) {
            	    $.ajax({
                        url: window.location.href,
                        data: "&action=deleteselected" + "&_wpnonce=" + $('#confnonce').val() + "&pid=" + pid + "&siglist=" + siglist,
                        success: function(response) {
                            document.location = window.location.href
                        }
                    });
                }
                return false;  
        }
        
    });
    
    // don't allow selection of signatures for CSV export
    $("#dk-speakout-action-selector").on('change',function(){
        if($("#dk-speakout-action-selector").val() == "csv-download"){
            $('[type="checkbox"]').prop("disabled", true);
        }
        else{
            $('[type="checkbox"]').prop("disabled", false);
        }
    });

    // change options text based on checkboxes
    $("#dk-speakout-signaturelist").contents().find(":checkbox").on('change', function(){ 
        // if we don't have any signatures selected
        if($("#dk-speakout-signaturelist input:checkbox:checked").length === 0){
            $("#dk-speakout-action-selector option[value='delete-selected']").prop("disabled", true);
            $("#dk-speakout-action-selector option[value='reconfirm-selected']").prop("disabled", true);
            $("#dk-speakout-action-selector option[value='reconfirm-selected']").text( $("#dk-speakout-reconfirm-unselected-text").val() );
            $("#dk-speakout-action-selector option[value='forceconfirm-selected']").prop("disabled", true);
            $("#dk-speakout-action-selector option[value='forceconfirm-selected']").text( $("#dk-speakout-confirm-unselected-text").val() );   
            $("#dk-speakout-action-selector option[value='delete-selected']").prop("disabled", true);
            $("#dk-speakout-action-selector option[value='delete-selected']").text( $("#dk-speakout-delete-unselected-text").val() ); 
            //if no petition or signatures disable the button
            if( $('#dk-speakout-switch-petition option:selected').val() === ""){
                $("#dk-speakout-doaction").prop("disabled", true);
            }
        }
        else{ // if we do have signatures selected
            // but we don't have a petition selected
            if( $('#dk-speakout-switch-petition option:selected').val() === ""){
                $("#dk-speakout-action-selector option[value='reconfirm-selected']").text( $("#dk-speakout-reconfirm-selected-no-petition-text").val() );
                $("#dk-speakout-action-selector option[value='forceconfirm-selected']").text( $("#dk-speakout-force-confirm-selected-no-petition-text").val() );
            }
            else{ // or we do have petition selected
                $("#dk-speakout-action-selector option[value='reconfirm-selected']").removeAttr('disabled'); 
                $("#dk-speakout-action-selector option[value='reconfirm-selected']").text( $("#dk-speakout-reconfirm-selected-text").val() );
                $("#dk-speakout-action-selector option[value='forceconfirm-selected']").removeAttr('disabled'); 
                $("#dk-speakout-action-selector option[value='forceconfirm-selected']").text( $("#dk-speakout-force-confirm-selected-text").val() );
            }
            $("#dk-speakout-doaction").removeAttr('disabled');
            $("#dk-speakout-action-selector option[value='delete-selected']").removeAttr('disabled'); 
            $("#dk-speakout-action-selector option[value='delete-selected']").text( $("#dk-speakout-delete-selected-text").val() );
        }
    });
    
	// display confirmation box when user tries to re-send confirmation emails
	// warns that a bunch of emails will be sent out if they hit OK
	$( 'a#dk-speakout-reconfirm' ).click( function( e ) {
		e.preventDefault();

		var link = $( this ).attr( 'href' );
		// confirmation message is contained in a hidden div in the HTML
		// so that it is accessible to PHP translation methods
		var confirm_message = $( '#dk-speakout-reconfirm-confirmation' ).html();
		// add new line characters for nicer confirm msg display
		confirm_message = confirm_message.replace( '? ', '?\n\n' );
		// display confirm box
		var confirm_delete = confirm( confirm_message );
		// if user presses OK, process delete link
		if ( confirm_delete === true ) {
			document.location = link;
		}
	});

	// stripe the table rows
	$( 'tr.dk-speakout-tablerow:even' ).addClass( 'dk-speakout-tablerow-even' );

/* Settings page
------------------------------------------------------------------- */

 // check the email display in public signature list
	$( 'input#sig_email' ).change( function () {
	   if ( $( this ).prop( 'checked' ) ) {
            $('.emailWarning').css( 'color', 'red' );
	        $('.emailWarning').html(' Are you sure you want to publicly display email addresses?');
	   }
	   else{
	       $('.emailWarning').html('');
	   }
	});


/* Pagination for Signatures and Petitions pages
------------------------------------------------------------------- */
	// when new page number is entered using the form on paginated admin pages,
	// construct a new url string to pass along the variables needed to update page
	// and redirect to the new url
	$( '#dk-speakout-pager' ).submit( function() {
		var page        = $( '#dk-speakout-page' ).val(),
			paged       = $( '#dk-speakout-paged' ).val(),
			total_pages = $( '#dk-speakout-total-pages' ).val(),
			baseurl     = String( document.location ).split( '?' ),
			newurl      = baseurl[0] + '?page=' + page + '&paged=' + paged + '&total_pages=' + total_pages;
		document.location = newurl;
		return false;
	});

/* Petition page
------------------------------------------------------------------- */
	// make the correct tab active on page load
	var addNewCurrentTab = $( 'input#dk-petition-tabbar' ).val();
	$( '#' + addNewCurrentTab ).show();
	$( 'ul#dk-petition-tabbar li a.' + addNewCurrentTab ).addClass( 'dk-petition-active' );

	// switch tabs when they are clicked
	$( 'ul#dk-petition-tabbar li a' ).click( function( e ) {
		e.preventDefault();

		// tab bar display
		$( 'ul#dk-petition-tabbar li a' ).removeClass( 'dk-petition-active' );
		$( this ).addClass( 'dk-petition-active' );

		// content sections display
		$( '.dk-petition-tabcontent' ).hide();

		var newTab = $( this ).attr( 'rel' );
		$( 'input#dk-petition-tabbar' ).val( newTab );

		$( '#' + newTab ).show();
	});

//fetch lists and fields json from the hidden field

if($("#alllistsReturned_new").val() > ""){
    var allLists_new = $("#alllistsReturned_new").val();
    var lists = JSON.parse(allLists_new);
}	
	//est the preselected Values of all fields
	var mappingFieldsArrayIds = [{id:'activecampaign-map1value', selected_id:"selected_activecampaign_map1field"}, {id:"activecampaign-map5value",selected_id:"selected_activecampaign_map5field"}, {id:"activecampaign-map6value",selected_id:"selected_activecampaign_map6field"}, {id:"activecampaign-map7value",selected_id:"selected_activecampaign_map7field"}, {id:"activecampaign-map8value",selected_id:"selected_activecampaign_map8field"}, {id:"activecampaign-map9value",selected_id:"selected_activecampaign_map9field"}, {id:"activecampaign-map10value",selected_id:"selected_activecampaign_map10field"}, {id:"activecampaign-map11value",selected_id:"selected_activecampaign_map11field"}, {id:"activecampaign-map12value",selected_id:"selected_activecampaign_map12field"}, {id:"activecampaign-map13value",selected_id:"selected_activecampaign_map13field"}, {id:"activecampaign-map14value",selected_id:"selected_activecampaign_map14field"}]
	
	
	$.each(mappingFieldsArrayIds, function(index, value){
		var checkHiddenValueExists = ($("#"+value['selected_id']).length > 0)
		if(checkHiddenValueExists){
			var hiddenSelectedValue = $("#"+value['selected_id']).val();
			if(hiddenSelectedValue && hiddenSelectedValue!=''){
				var checkDropdownOptionExists = ($("#"+value['id']+" option[value="+hiddenSelectedValue+"]").length > 0);
				$('#'+value['id']).val(hiddenSelectedValue);
			}
				
		}
	});

    // populate activecampaign mapping fields for selected list
    $('#activecampaign-list-id').change(function(){
        
        //get the json arrays from addnew.view.php
    	var allFields = $("#allFieldsReturned").val();
    	    allFields = JSON.parse(allFields);
    	    
    	var selectedFields = $("#selectedFieldsReturned").val();
    	    selectedFields = JSON.parse(selectedFields);
    	
    	var listID = ["list" + $(this).val()];
    	var optionList ="";
        
		// Fetch the custom fields
    	var current_val = $(this).val();
            if (lists[current_val]) {
                var fields_options = '';
                for (i in lists[current_val]) {
                    var field_title = lists[current_val][i].title;
					var field_id = lists[current_val][i].id;
                    fields_options += '<option value = "' + field_id + '">' + field_title + '</option>';
                }                
            }
        //empty options and add default 'unused'
        for (var i =1; i < 15; i++){
            //except for fixed fields
            if( i == 2 || i ==3 || i == 4) continue;
            $('#activecampaign-map'+ i + 'value option').remove();
            $('#activecampaign-map'+ i + 'value').append($('<option>', {
                    value: "",
                    text: "Unused"
            }));
			$('#activecampaign-map'+ i + 'value').append(fields_options);
        }
    });
    
// populate activecampaign field mapping from database if previously saved and exists
if ($('#allFieldsReturned').length && $("#allFieldsReturned").val() > ""){ 
    // get the json arrays from addnew.view.php
    // all fields
    var allFields = $("#allFieldsReturned").val();
        allFields = JSON.parse(allFields);
    
    // get a list of saved mapping
    var selectedFields = $("#selectedFieldsReturned").val();
    	selectedFields = JSON.parse(selectedFields);
    
	// get our list ID
	var listID = $("#activecampaign-list-id").val() > "" ? $("#activecampaign-list-id").val() : 1 ;
	    listID = ["list" + listID];

    // get array for list ID
    var list = allFields.fields[listID];
    sortActiveCampaignTitle(list);	

    var optionList = "";
    
    //build the options list
    for(var x =1; x < 15; x++){
        var mapped = ["map"+x+"field"];
        optionList = optionList.concat("<option value=''>Unused</option>");
        
        for(var i = 0; i < list.length; i++) {
            // mark saved options as SELECTED
            if(selectedFields[mapped] == list[i].id){

                optionList = optionList.concat("<option value='"+ list[i].id +"' SELECTED='SELECTED' " + ">" + list[i].title+"</option>");
            }
            else{
                optionList = optionList.concat("<option value='"+ list[i].id  + "'>" + list[i].title+"</option>");
            }
        }
        $('#activecampaign-map'+x+"value").append(optionList);
        optionList="";
	}
}

      //sort the array alphabetically
      function sortActiveCampaignTitle(array, order) {
        return array.sort(order === 'DESC'
            ? function (b, a) {
                a = a.title.slice(-1);
                b = b.title.slice(-1);
                return isNaN(b) - isNaN(a) || a > b || -(a < b);
            }
            : function (a, b) {
                a = a.title.slice(-1);
                b = b.title.slice(-1);
                return isNaN(a) - isNaN(b) || a > b || -(a < b);
            });
        }
    
/* Settings page
------------------------------------------------------------------- */
	// make the correct tab active on page load
	var currentTab = $( 'input#dk-speakout-tab' ).val();

		//hide the save button on the license page load
		if($( 'input#dk-speakout-tab' ).val() == 'dk-speakout-tab-07'){
		    $( '.button-primary' ).hide();
		}else{
		    $( '.button-primary' ).show();
		}
		
	$( '#' + currentTab ).show();
	$( 'ul#dk-speakout-tabbar li a.' + currentTab ).addClass( 'dk-speakout-active' );

	// switch tabs when they are clicked
	$( 'ul#dk-speakout-tabbar li a' ).click( function( e ) {
		e.preventDefault();

	//dk-speakout-tab-07
		// tab bar display
		$( 'ul#dk-speakout-tabbar li a' ).removeClass( 'dk-speakout-active' );
		$( this ).addClass( 'dk-speakout-active' );

		// content sections display
		$( '.dk-speakout-tabcontent' ).hide();

		var newTab = $( this ).attr( 'rel' );
		$( 'input#dk-speakout-tab' ).val( newTab );
		
		//hide the save button on the license page switch
	    $( '.button-primary' ).show();
		if($( 'input#dk-speakout-tab' ).val() == 'dk-speakout-tab-07'){
		    $( '.button-primary' ).hide();
		}

		$( '#' + newTab ).show();
	});
});