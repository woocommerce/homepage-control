jQuery(document).ready(function($) {
	"use strict";
	$( '.homepage-control' ).sortable();
	$( '.homepage-control' ).disableSelection();

	$( '.homepage-control' ).bind( 'sortstop', function ( e, ui ) {
		var components = new Array();
		var disabled = '[disabled]';

		$( e.target ).find( 'li' ).each( function ( i, e ) {
			if ( $( this ).hasClass( 'disabled' ) ) {
				components.push( disabled + $( this ).attr( 'id' ) );
			} else {
				components.push( $( this ).attr( 'id' ) );
			}
		});

		components = components.join( ',' );

		$( 'input[data-customize-setting-link="homepage_control"]' ).attr( 'value', components ).trigger( 'change' );
	});

	$( '.homepage-control .visibility' ).bind( 'click', function ( e ) {
		var components = new Array();
		var disabled = '[disabled]';

		$( this ).parent( 'li' ).toggleClass( 'disabled' );

		$( this ).parents( '.homepage-control' ).find( 'li' ).each( function ( i, e ) {
			if ( $( this ).hasClass( 'disabled' ) ) {
				components.push( disabled + $( this ).attr( 'id' ) );
			} else {
				components.push( $( this ).attr( 'id' ) );
			}
		});

		components = components.join( ',' );

		$( 'input[data-customize-setting-link="homepage_control"]' ).attr( 'value', components ).trigger( 'change' );
	});
});