jQuery( document ).ready( function( $ ) {
	'use strict';

	// first pagination button is clicked
	$( '.dk-speakout-signaturelist-first' ).click( function( e ) {
		e.preventDefault();
		get_signaturelist( $( this ) );
	});

	// next pagination button is clicked
	$( '.dk-speakout-signaturelist-next' ).click( function( e ) {
		e.preventDefault();
		get_signaturelist( $( this ) );
	});

	// prev pagination button is clicked
	$( '.dk-speakout-signaturelist-prev' ).click( function( e ) {
		e.preventDefault();
		get_signaturelist( $( this ) );
	});
	
	// last pagination button is clicked
	$( '.dk-speakout-signaturelist-last' ).click( function( e ) {
		e.preventDefault();
		get_signaturelist( $( this ) );
	});


	// pagination: query new signatures and display results
	function get_signaturelist( button, link ) {

		// change button appearance to disabled while ajax request is processing
		$( this ).addClass( 'dk-speakout-signaturelist-disabled' );
		var link   = button.attr( 'rel' ).split( ',' ),
			id     = link[0],
			start  = link[1],
			limit  = link[2],
			total  = link[3],
			status = link[4],
			layout = link[5],
            hideUnconfirmed = link[6],
			ajax   = {
				action: 'dk_speakout_paginate_signaturelist',
				id:         id,
				start:      start,
				limit:      limit,
				layout:     layout,
				dateformat: dk_speakout_signaturelist_js.dateformat,
                hideUnconfirmed: hideUnconfirmed
			};

		if ( status === '1' ) {
			// submit data and handle ajax response
			$.post( dk_speakout_signaturelist_js.ajaxurl, ajax,
				function( response ) {
					var first_link = get_first_link( id, start, limit, total, layout, hideUnconfirmed );
					var prev_link = get_prev_link( id, start, limit, total, layout, hideUnconfirmed );
					var next_link = get_next_link( id, start, limit, total, layout, hideUnconfirmed );
					var last_link = get_last_link( id, start, limit, total, layout, hideUnconfirmed );
					toggle_button_display( id, first_link, prev_link, next_link, last_link );

					switch (layout){
					case "3":
    					$( '.dk-speakout-signaturelist-' + id + ' tr:not(:last-child)' ).remove();
    					$( '.dk-speakout-signaturelist-' + id ).prepend( response );
					    break;
					case "2":
					    $( '.dk-speakout-signaturelist-' + id ).empty();
					    $( '.dk-speakout-signaturelist-' + id ).html( response );
					    break;
					case "1":
					   	$( '.dk-speakout-signaturelist-' + id ).empty();
					    $( '.dk-speakout-signaturelist-' + id ).html( response );
					   break; 
					default:
    					$( '.dk-speakout-signaturelist-' + id + ' tr:not(:last-child)' ).remove();
    					$( '.dk-speakout-signaturelist-' + id ).prepend( response );
					break;
					}
					$( '.dk-speakout-signaturelist-first' ).attr( 'rel', first_link );
					$( '.dk-speakout-signaturelist-prev' ).attr( 'rel', prev_link );
					$( '.dk-speakout-signaturelist-next' ).attr( 'rel', next_link );
					$( '.dk-speakout-signaturelist-last' ).attr( 'rel', last_link );
				}
			);
		}
	}

	// format new link for prev pagination button
	function get_first_link( id, start, limit, total, layout ) {
		var start = parseInt( start ),
			limit = parseInt( limit ),
			total = parseInt( total ),
			layout = parseInt( layout ),
			new_start = '',
			status    = '',
			link      = '';

		if ( start > 0 ) {
			status = '1';
		}
		else {
			status = '0';
		}

		link = id + ',0,' + limit + ',' + total + ',' + status + ',' + layout;

		return link;
	}
		// format new link for prev pagination button
	function get_prev_link( id, start, limit, total, layout ) {
		var start = parseInt( start ),
			limit = parseInt( limit ),
			total = parseInt( total ),
			layout = parseInt( layout ),
			new_start = '',
			status    = '',
			link      = '';

		if ( start - limit >= 0 ) {
			new_start = start - limit;
			status = '1';
		}
		else {
			new_start = total;
			status = '0';
		}

		link = id + ',' + new_start + ',' + limit + ',' + total + ',' + status + ',' + layout;

		return link;
	}
	// format new link for next pagination button
	function get_next_link( id, start, limit, total, layout ) {
		var start = parseInt( start ),
			limit = parseInt( limit ),
			total = parseInt( total ),
			layout = parseInt( layout ),
			new_start = '',
			status    = '',
			link      = '';

		if ( start + limit  < total ) {
			new_start = start + limit;
			status = '1';
		}
		else {
			new_start = total;
			status = '0';
		}

		link = id + ',' + new_start + ',' + limit + ',' + total + ',' + status + ',' + layout;		
		return link;
	}

	// format new link for last pagination button
	function get_last_link( id, start, limit, total, layout ) {
	    
		var start = parseInt( start ),
		    limit = parseInt( limit ),
			total = parseInt( total ),
			layout = parseInt( layout ),
			new_start = '',
			status    = '',
			link      = '';

		if ( start < total ) {
		    new_start = total - limit;
			status = '1';
		}
		else {
		    new_start = '0';
			status = '0';
		}

		link = id + ',' + new_start + ',' + limit + ',' + total + ',' + status+ ',' + layout;		
		return link;
	}
	
	function toggle_button_display( id, first_link, prev_link, next_link, last_link ) {
		if ( prev_link.split( ',' )[4] === '0' ) {
			$( 'a.dk-speakout-signaturelist-prev' ).addClass( 'dk-speakout-signaturelist-disabled' );
			$( 'a.dk-speakout-signaturelist-first' ).addClass( 'dk-speakout-signaturelist-disabled' );
		}
		else {
		    $( 'a.dk-speakout-signaturelist-prev' ).removeClass( 'dk-speakout-signaturelist-disabled' );
		    $( 'a.dk-speakout-signaturelist-first' ).removeClass( 'dk-speakout-signaturelist-disabled' );
		}		
		
		if ( next_link.split( ',' )[4] === '0' ) {
		    $( 'a.dk-speakout-signaturelist-last' ).addClass( 'dk-speakout-signaturelist-disabled' );
		    $( 'a.dk-speakout-signaturelist-next' ).addClass( 'dk-speakout-signaturelist-disabled' );
		}
		else {
			$( 'a.dk-speakout-signaturelist-last' ).removeClass( 'dk-speakout-signaturelist-disabled' );
			$( 'a.dk-speakout-signaturelist-next' ).removeClass( 'dk-speakout-signaturelist-disabled' );
		}


	}

});