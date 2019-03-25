/**
 * Navigation & Skip Link Focus Fix.
 *
 * This file includes the code to handle child-menu keyboard navigation and setting correct
 * keyboard focus when a user selects "skip to main content" on mobile/IE.
 *
 * @since 1.0.0
 */

jQuery( function( $ ) {
  var menu = $( '#main-menu' );
  var button = $( '#nav-main__toggle' );

	/**
	 * Fixes skip link focus bug.
	 *
	 * @link https://github.com/selfthinker/dokuwiki_template_writr/
	 * @author Anika Henke <anika@selfthinker.org>
	 * @version 2016-05-02
	 * @license GPL-2.0-or-later
	 */
	$.extend( $.expr[':'], {
		focusable: function( el ) {
			var $element = $( el );
			return $element.is( ':input:enabled, a[href], area[href], object, [tabindex] ' ) && ! $element.is( ':hidden' );
		}
	});

	function focusOnElement( $element ) {
		if ( ! $element.length ) {
			return;
		}
		if ( ! $element.is( ':focusable' ) ) {

			// add tabindex to make focusable and remove again
			$element.attr( 'tabindex', -1 ).on( 'blur focusout', function() {
				$( this ).removeAttr( 'tabindex' );
			});
		}
		$element.focus();
	}

	$( document ).ready( function() {

		// if there is a '#' in the URL (someone linking directly to a page with an anchor)
		if ( document.location.hash ) {
			focusOnElement( $( document.location.hash ) );
		}

		// if the hash has been changed (activation of an in-page link)
		$( window ).bind( 'hashchange', function() {
			var hash = '#' + window.location.hash.replace( /^#/, '' );
			focusOnElement( $( hash ) );
		});
	});

	/**
	 * Allows a button to toggle the main menu on mobile.
	 *
	 * @since 1.0.0
	 */
	if ( ! menu ) {
		button.hide();
		return;
	}

	menu.hide();

	$( '#nav-main__toggle' ).click( function() {
		var button = $( this );
		var menu = $( '#main-menu' );
		menu.slideToggle( 'medium', function() {

			/* Lazy fix for menu format if user starts on desktop and resizes to mobile :P */
			if ( $( this ).is( ':visible' ) ) {
				$( this ).css( 'display', 'inline-block' );
			}
		});
		if ( menu.hasClass( 'toggled' ) ) {
			menu.removeClass( 'toggled' );
			button.attr( 'aria-expanded', 'false' );
		} else {
			menu.addClass( 'toggled' );
			button.attr( 'aria-expanded', 'true' );
		}
	});

	/**
	 * Adds tab key support to dropdowns.
	 *
	 * @since 1.0.0
	 */
	$( '#main-menu a' ).focus( function() {
		$( this ).siblings( 'li ul' ).addClass( 'focused' );
	}).blur( function() {
		$( this ).siblings( 'li ul' ).removeClass( 'focused' );
	});

	/**
	 * Adds touch support to dropdowns for tablets.
	 *
	 * @author Automattic
	 * @version 2016-11-26
	 * @license GPL-2.0-or-later
	 * @since 1.0.0
	 */
	if ( 'ontouchstart' in window ) {
		$( '.menu-item-has-children > a' ).each( function() {
			$( this ).on( 'touchstart', function( e ) {
				var menuItem = $( this ).parent()[0];

				if ( ! menuItem.hasClass( 'focus' ) ) {
					e.preventDefault();
					menuItem.parent()[0].children().each( function() {
						if ( $( this ) === menuItem ) {
							return;
						}
						$( this ).removeClass( 'focus' );
					});
					menuItem.addClass( 'focus' );
				} else {
					menuItem.removeClass( 'focus' );
				}
			});
		});
	}
});
