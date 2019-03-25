/**
 * Customize Preview.
 *
 * Binds Customizer settings to appropriate selectors in the preview window.
 *
 * @file Live Customizer settings preview.
 * @version 1.0.0
 * @license GPL-3.0-or-later
 * @since 1.0.0
 */

( function( $ ) {
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site__title a' ).text( to );
		});
	});
	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site__desc' ).text( to );
		});
	});
	wp.customize( '404-title', function( value ) {
		value.bind( function( to ) {
			$( '.page__title-error' ).text( to );
		});
	});
	wp.customize( 'comments-title', function( value ) {
		value.bind( function( to ) {
			$( '.comments__title' ).text( to );
		});
	});
	wp.customize( 'reply-title', function( value ) {
		value.bind( function( to ) {
			$( '.comment-reply-title' ).text( to );
		});
	});
	wp.customize( 'copyright', function( value ) {
		value.bind( function( to ) {
			$( '.copyright' ).text( to );
		});
	});

	// Header text color.
	wp.customize( 'header_textcolor', function( value ) {
		value.bind( function( to ) {
			if ( 'blank' === to ) {
				$( '.site__title, .site__desc' ).css({
					'clip': 'rect(1px, 1px, 1px, 1px)',
					'position': 'absolute'
				});
			} else {
				$( '.site__title, .site__desc' ).css({
					'clip': 'auto',
					'position': 'relative'
				});
				$( '.site__title a, .site__desc' ).css({
					'color': to
				});
			}
		});
	});
}( jQuery ) );
