jQuery( document ).ready( function( $ ) {
	'use strict';

	// display required asterisks
	$( '.dk-speakout-petition label.required' ).append( '<span> *</span>');

/*
-------------------------------
	Form submission
-------------------------------
*/
	$( '.dk-speakout-submit' ).click( function( e ) {
		e.preventDefault();
		var id             = $( this ).attr( 'name' ),
			lang           = $( '#dk-speakout-lang-' + id ).val(),
			honorific      = $( '#dk-speakout-honorific-' + id ).val(),
			firstname      = $( '#dk-speakout-first-name-' + id ).val(),
			lastname       = $( '#dk-speakout-last-name-' + id ).val(),
			email          = $( '#dk-speakout-email-' + id ).val(),
			street         = $( '#dk-speakout-street-' + id ).val(),
			city           = $( '#dk-speakout-city-' + id ).val(),
			state          = $( '#dk-speakout-state-' + id ).val(),
			postcode       = $( '#dk-speakout-postcode-' + id ).val(),
			country        = $( '#dk-speakout-country-' + id ).val(),
			custom_field   = $( '#dk-speakout-custom-field-' + id ).val(),
            custom_field2   = $( '#dk-speakout-custom-field2-' + id ).val(),
            custom_field3   = $( '#dk-speakout-custom-field3-' + id ).val(),
            custom_field4   = $( '#dk-speakout-custom-field4-' + id ).val(),
            custom_field5   = $( '#dk-speakout-custom-field5-' + id ).val(),
            custom_field6   = $( '#dk-speakout-custom-field6-' + id ).val(),
            custom_field7   = $( '#dk-speakout-custom-field7-' + id ).val(),
			custom_message = $( '.dk-speakout-message-' + id ).val(),
			optin          = '',
			bcc            = '',
            anonymise      = '',
			privacypolicy  = $( '#dk-speakout-privacypolicy-' + id).is(':checked'),
			redirect_url   = $( '#dk-speakout-redirect-url-' + id).val(),
            redirect_delay   = $( '#dk-speakout-redirect-delay-' + id).val(),
            url_target       = $( '#dk-speakout-url-target-' + id).val(),
            petition_fade     = $( '#dk-speakout-petition-fade-' + id).val(),
            requires_confirmation = $( '#dk-speakout-requires_confirmation-' + id).val(),
			ajaxloader     = $( '#dk-speakout-ajaxloader-' + id ),
			hide_email_field = $( '#dk-speakout-hide-email-field-' + id ).val();


		// toggle use of .text() / .val() to read from edited textarea properly on Firefox
		if ( $( '#dk-speakout-textval-' + id ).val() === 'text' ) {
			custom_message = $( '.dk-speakout-message-' + id ).text();
		}

		if ( $( '#dk-speakout-optin-' + id ).prop( 'checked' ) ) {
			optin = 'on';
		}
		if ( $( '#dk-speakout-bcc-' + id ).prop( 'checked' ) ) {
			bcc = 'on';
		}
        if ( $( '#dk-speakout-anonymise-' + id ).prop( 'checked' ) ) {
			anonymise = 'on';
		}       

		// make sure error notices are turned off before checking for new errors
		$( '#dk-speakout-petition-' + id + ' input' ).removeClass( 'dk-speakout-error' );

		// validate form values
		var errors = 0,
		// allow for new gtlds
			emailRegEx = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

	
		// if email is empty or doesn't fit regext and IS being displayed
		if ( ( email === '' || emailRegEx.test( email ) === false ) && hide_email_field == '0'  ) {
			$( '#dk-speakout-email-' + id ).addClass( 'dk-speakout-error' );
			errors ++;
		}
        else{
            $( '#dk-speakout-email-' + id ).removeClass( 'dk-speakout-error' ); 
        }
        
		if ( firstname === '' ) {
			$( '#dk-speakout-first-name-' + id ).addClass( 'dk-speakout-error' );
			errors ++;
		}
        else{
            $( '#dk-speakout-first-name-' + id ).removeClass( 'dk-speakout-error' );
        }
        
		if ( lastname === '' ) {
			$( '#dk-speakout-last-name-' + id ).addClass( 'dk-speakout-error' );
			errors ++;
		}
        else{
        $( '#dk-speakout-last-name-' + id ).removeClass( 'dk-speakout-error' );
        }
		
		if ( $('#dk-speakout-privacypolicy-' + id).length  && privacypolicy === false ){
			$( '#dk-speakout-privacypolicy-' + id ).parent().addClass( 'dk-speakout-error' ); 
			errors ++;
		}
        else{
            $( '#dk-speakout-privacypolicy-' + id ).removeClass( 'dk-speakout-error' );
        }
		
		//test for required fields if set
		if ( $('#dk-speakout-custom-field-'+ id).prop('required')  && custom_field === "") {
            $( '#dk-speakout-custom-field-'  + id ).parent().addClass( 'dk-speakout-error' ); 
			errors ++; 
		}
        else{
            $( '#dk-speakout-custom-field-' + id ).removeClass( 'dk-speakout-error' );
        }
        
        if ($('#dk-speakout-custom-field2-'+ id).prop('required')   && custom_field2 === "") {
            $( '#dk-speakout-custom-field2-'  + id ).addClass( 'dk-speakout-error' ); 
			errors ++;
		}
        else{
            $( '#dk-speakout-custom-field2-' + id ).removeClass( 'dk-speakout-error' );
        }
        
        if ($('#dk-speakout-custom-field3-'+ id).prop('required')  && custom_field3 === "") {
            $( '#dk-speakout-custom-field3-'  + id ).addClass( 'dk-speakout-error' ); 
			errors ++;
		}
        else{
            $( '#dk-speakout-custom-field3-' + id ).removeClass( 'dk-speakout-error' );
        }
        
        if ($('#dk-speakout-custom-field4-'+ id).prop('required')  && custom_field4 === "") {
            $( '#dk-speakout-custom-field4-'  + id ).addClass( 'dk-speakout-error' ); 
			errors ++;
		}
        else{
            $( '#dk-speakout-custom-field4-' + id ).removeClass( 'dk-speakout-error' );
        }
        
        if ($('#dk-speakout-custom-field5-'+ id).prop('required')  && custom_field5 === "") {
            $( '#dk-speakout-custom-field5-'  + id ).addClass( 'dk-speakout-error' ); 
			errors ++;
		}
        else{
            $( '#dk-speakout-custom-field5-' + id ).removeClass( 'dk-speakout-error' );
        }
        
        if ($('#dk-speakout-custom-field6-'+ id).prop('required')  && custom_field6 === "") {
            $( '#dk-speakout-custom-field6-'  + id ).addClass( 'dk-speakout-error' ); 
			errors ++;
		}
        else{
            $( '#dk-speakout-custom-field6-' + id ).removeClass( 'dk-speakout-error' );
        }
        
        if ($('#dk-speakout-custom-field7-'+ id).prop('required')  && custom_field7 === "") {
            $( '#dk-speakout-custom-field7-'  + id ).addClass( 'dk-speakout-error' ); 
			errors ++;
		}
        else{
            $( '#dk-speakout-custom-field7-' + id ).removeClass( 'dk-speakout-error' );
        }
        
		if (document.getElementById('dk-speakout-street-' + id) && $( '#dk-speakout-street-'  + id ).prop('required') && street === "") {
            $( '#dk-speakout-street-'  + id ).addClass( 'dk-speakout-error' ); 
			errors ++;
		}
        else{
            $( '#dk-speakout-street-' + id ).removeClass( 'dk-speakout-error' );
        }
        
        if (document.getElementById('dk-speakout-city-' + id) && $( '#dk-speakout-city-'  + id ).prop('required') && city === "") {
			$( '#dk-speakout-city-'  + id ).addClass( 'dk-speakout-error' ); 
			errors ++;
		}
        else{
            $( '#dk-speakout-city-' + id ).removeClass( 'dk-speakout-error' );
        }
        
		if (document.getElementById('dk-speakout-state-' + id) && $( '#dk-speakout-state-'  + id ).prop('required') && state === "") {
            $( '#dk-speakout-state-'  + id ).addClass( 'dk-speakout-error' ); 
			errors ++;
		}
        else{
            $( '#dk-speakout-state-' + id ).removeClass( 'dk-speakout-error' );
        }
        
		if (document.getElementById('dk-speakout-postcode-' + id) && $( '#dk-speakout-postcode-'  + id ).prop('required') && postcode === "") {
            $( '#dk-speakout-postcode-'  + id ).addClass( 'dk-speakout-error' ); 
			errors ++;
        }
        else{
            $( '#dk-speakout-postcode-' + id ).removeClass( 'dk-speakout-error' );
        }
        
		if (document.getElementById('dk-speakout-country-' + id) && $( '#dk-speakout-country-'  + id ).prop('required') && country === "") {
            $( '#dk-speakout-country-'  + id ).addClass( 'dk-speakout-error' ); 
			errors ++;
        }
        else{
            $( '#dk-speakout-country-' + id ).removeClass( 'dk-speakout-error' );
        }

        // if no errors found, submit the data via ajax
		if ( errors === 0 && $( this ).attr( 'rel' ) !== 'disabled' ) {

			// set rel to disabled as flag to block double clicks
			$( this ).attr( 'rel', 'disabled' );

			var data = {
				action:         'dk_speakout_sendmail',
				id:             id,
				honorific:		honorific,
				first_name:     firstname,
				last_name:      lastname,
				email:          email,
				street:         street,
				city:           city,
				state:          state,
				postcode:       postcode,
				country:        country,
				custom_field:   custom_field,
                custom_field2:   custom_field2,
                custom_field3:   custom_field3,
                custom_field4:   custom_field4,
                custom_field5:   custom_field5,
                custom_field6:   custom_field6,
                custom_field7:   custom_field7,
				custom_message: custom_message,
				optin:          optin,
				bcc:            bcc,
                anonymise:      anonymise,
				privacypolicy:  privacypolicy,
				lang:           lang,
				redirect_url:   redirect_url,
                redirect_delay:  redirect_delay,
                petition_fade:   petition_fade
			};

			// display AJAX loading animation
			ajaxloader.css({ 'visibility' : 'visible'});

			// submit form data and handle ajax response
			$.post( dk_speakout_js.ajaxurl, data,
				function( response ) {
				    var response_class = 'dk-speakout-response-success';
					if ( response.status === 'error' ) {
						response_class = 'dk-speakout-response-error';
					}
					//if successfully signed and there is a redirect URL in the form
					if (response.status !== 'error' && redirect_url > ""){
					   	setTimeout(function () {
							//are we opening in a new window?
					   	    if(url_target == 0){
							   window.location.href = redirect_url;
					   	    }
					   	    else{
					   	            //this triggers blockup blockers :P
					   	            var redirectWindow = window.open(redirect_url, '_blank');
                                    $.ajax({
                                        type: 'POST',
                                        url: '/echo/json/',
                                        success: function (data) {
                                            redirectWindow.location;
                                        }
                                    });
					   	    }
						}, redirect_delay); //delay redirection by n milliseconds e.g. 5000 = 5 seconds
					}
					if(petition_fade=='enabled'){
					    $( '#dk-speakout-petition-' + id + ' .dk-speakout-petition' ).fadeTo( 400, 0.35 );
					}
					else{
					    $( '#dk-speakout-petition-' + id + ' .dk-speakout-petition' ).hide();
					}
					$( '#dk-speakout-petition-' + id + ' .dk-speakout-response' ).addClass( response_class );
					$( '#dk-speakout-petition-' + id + ' .dk-speakout-response' ).fadeIn().html( response.message );
					ajaxloader.css({ 'visibility' : 'hidden'});
                
                	// advance the total but only if confirmation not required and response status has no error
                    if(requires_confirmation == 0 && response.status !== 'error'){
                        var total = $( '.dk-speakout-signature-count span' )[0].textContent;
                        $( '.dk-speakout-signature-count span' )[0].textContent = Number(total.replaceAll(',', ''))+1;
                        $( '.dk-speakout-signature-count span' ).effect( 'highlight', {color:'#FFFF00'}, 3000 );
                    }
				}, 'json'
			);
		}

	});

	// launch Facebook sharing window
	$( '.dk-speakout-facebook' ).click( function( e ) {
		e.preventDefault();

		var id           = $( this ).attr( 'rel' ),
			posttitle    = $( '#dk-speakout-posttitle-' + id ).val(),
			share_url    = document.URL,
			facebook_url = 'http://www.facebook.com/sharer.php?u=' + share_url + '&amp;t=' + posttitle;

		window.open( facebook_url, 'facebook', 'height=400,width=550,left=100,top=100,resizable=yes,location=no,status=no,toolbar=no' );
	});

	// launch x sharing window
	$( '.dk-speakout-x' ).click( function( e ) {
		e.preventDefault();

		var id          = $( this ).attr( 'rel' ),
			tweet       = $( '#dk-speakout-tweet-' + id ).val(),
			current_url = document.URL,
			share_url   = current_url.split('#')[0],
			x_url = 'http://x.com/share?url=' + share_url + '&text=' + tweet;

	window.open( x_url, 'x', 'height=400,width=550,left=100,top=100,resizable=yes,location=no,status=no,toolbar=no' );
	});

/*
-------------------------------
	Petition reader popup
-------------------------------
 */
	$('a.dk-speakout-readme').click( function( e ) {
		e.preventDefault();

		var id = $( this ).attr( 'rel' ),
			sourceOffset = $(this).offset(),
			sourceTop    = sourceOffset.top - $(window).scrollTop(),
			sourceLeft   = sourceOffset.left - $(window).scrollLeft(),
			screenHeight = $( document ).height(),
			screenWidth  = $( window ).width(),
			windowHeight = $( window ).height(),
			windowWidth  = $( window ).width(),
			readerHeight = 520,
			readerWidth  = 640,
			readerTop    = ( ( windowHeight / 2 ) - ( readerHeight / 2 ) ),
			readerLeft   = ( ( windowWidth / 2 ) - ( readerWidth / 2 ) ),
			petitionText = $( 'div#dk-speakout-message-' + id ).html(),
			reader       = '<div id="dk-speakout-reader"><div id="dk-speakout-reader-close"></div><div id="dk-speakout-reader-content"></div></div>';

		// set this to toggle use of .val() / .text() so that Firefox  will read from editable-message textarea as expected
		$( '#dk-speakout-textval-' + id ).val('text');

		// use textarea for editable petition messages
		if ( petitionText === undefined ) {
			petitionText = $( '#dk-speakout-message-editable-' + id ).html();
		}

		$( '#dk-speakout-windowshade' ).css( {
				'width'  : screenWidth,
				'height' : screenHeight
			});
			$( '#dk-speakout-windowshade' ).fadeTo( 500, 0.8 );

		if ( $( '#dk-speakout-reader' ).length > 0 ) {
			$( '#dk-speakout-reader' ).remove();
		}

		$( 'body' ).append( reader );

		$('#dk-speakout-reader').css({
			position   : 'fixed',
			left       : sourceLeft,
			top        : sourceTop,
			zIndex     : 100002

	});

		$('#dk-speakout-reader').animate({
			width  : readerWidth,
			height : readerHeight,
			top    : readerTop,
			left   : readerLeft
		}, 500, function() {
			$( '#dk-speakout-reader-content' ).html( petitionText );
		});

		/* Close the pop-up petition reader */
		// by clicking windowshade area
		$( '#dk-speakout-windowshade' ).click( function () {
			$( this ).fadeOut( 'slow' );
			// write edited text to form - using .text() because target textarea has display: none
			$( '.dk-speakout-message-' + id ).text( $( '#dk-speakout-reader textarea' ).val() );
			$( '#dk-speakout-reader' ).remove();
		});
		// or by clicking the close button
		$( 'body' ).on( 'click', '#dk-speakout-reader-close', function() {
			$( '#dk-speakout-windowshade' ).fadeOut( 'slow' );
			// write edited text to form - using .text() because target textarea has display: none
			$( '.dk-speakout-message-' + id ).text( $( '#dk-speakout-reader textarea' ).val() );
			$( '#dk-speakout-reader' ).remove();
		});
		// or by pressing ESC
		$( document ).keyup( function( e ) {
			if ( e.keyCode === 27 ) {
				$( '#dk-speakout-windowshade' ).fadeOut( 'slow' );
				// write edited text to form - using .text() because target textarea has display: none
				$( '.dk-speakout-message-' + id ).text( $( '#dk-speakout-reader textarea' ).val() );
				$( '#dk-speakout-reader' ).remove();
			}
		});

	});

/*
	Toggle form labels depending on input field focus
	Leaving this in for now to support older custom themes
	But it will be removed in future updates
 */

	$( '.dk-speakout-petition-wrap input[type=text]' ).focus( function( e ) {
		var label = $( this ).siblings( 'label' );
		if ( $( this ).val() === '' ) {
			$( this ).siblings( 'label' ).addClass( 'dk-speakout-focus' ).removeClass( 'dk-speakout-blur' );
		}
		$( this ).blur( function(){
			if ( this.value === '' ) {
				label.addClass( 'dk-speakout-blur' ).removeClass( 'dk-speakout-focus' );
			}
		}).focus( function() {
			label.addClass( 'dk-speakout-focus' ).removeClass( 'dk-speakout-blur' );
		}).keydown( function( e ) {
			label.addClass( 'dk-speakout-focus' ).removeClass( 'dk-speakout-blur' );
			$( this ).unbind( e );
		});
	});

	// hide labels on filled input fields when page is reloaded
	$( '.dk-speakout-petition-wrap input[type=text]' ).each( function() {
		if ( $( this ).val() !== '' ) {
			$( this ).siblings( 'label' ).addClass( 'dk-speakout-focus' );
		}
	});

});