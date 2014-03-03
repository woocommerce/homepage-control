jQuery( document ).ready( function ( e ) {
	jQuery( '.homepage-control-wrap tbody' ).sortable();
	jQuery( '.homepage-control-wrap tbody' ).disableSelection();

	jQuery( '.homepage-control-wrap tbody' ).bind( 'sortstop', function ( e, ui ) {
		var orderString = '';

		jQuery( e.target ).find( 'tr' ).each( function ( i, e ) {
			if ( i > 0 ) { orderString += ','; }
			orderString += jQuery( this ).attr( 'id' );
		});

		jQuery( 'input[id="component_order"]' ).attr( 'value', orderString );
	});

	jQuery( '.homepage-control-wrap .visibility' ).bind( 'click', function ( e ) {
		var disabledString = '';

		jQuery( this ).parents( 'tr.item-row' ).toggleClass( 'disabled' );

		jQuery( this ).parents( '.homepage-control-wrap' ).find( 'tr.disabled' ).each( function ( i, e ) {
			if ( i > 0 ) { disabledString += ','; }
			disabledString += jQuery( this ).attr( 'id' );
		});

		jQuery( 'input[id="disabled_components"]' ).attr( 'value', disabledString );
	});
});