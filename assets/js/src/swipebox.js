/**
 * Swipebox.
 *
 * Lightbox plugin edited to always show navigation bar and to close when the user clicks
 * anywhere outside the image, not just on the close button.
 *
 * @link https://github.com/brutaldesign/swipebox/
 * @file Defines the Swipebox lightbox functions and display.
 * @author Constantin Saguin <hello@csag.co>
 * @version 1.4.4
 * @license MIT
 * @since 1.0.0
 */
 /*jshint multistr: true, unused:false */
 /*eslint camelcase: 0, no-multi-str: 0*/

jQuery( function( window, document, $, undefined ) {
	$.swipebox = function( elem, options ) {

		// Default options
		var ui,
			defaults = {
				useCSS: true,
				useSVG: true,
				initialIndexOnArray: 0,
				removeBarsOnMobile: true,
				hideCloseButtonOnMobile: false,
				hideBarsDelay: 3000,
				videoMaxWidth: 1140,
				vimeoColor: 'cccccc',
				beforeOpen: null,
				afterOpen: null,
				afterClose: null,
				afterMedia: null,
				nextSlide: null,
				prevSlide: null,
				loopAtEnd: false,
				autoplayVideos: false,
				queryStringData: {},
				toggleClassOnLoad: ''
			},

			plugin = this,
			elements = [], // slides array [ { href:'...', title:'...' }, ...],
			$elem,
			selector = elem.selector,
			isMobile = navigator.userAgent.match( /(iPad)|(iPhone)|(iPod)|(Android)|(PlayBook)|(BB10)|(BlackBerry)|(Opera Mini)|(IEMobile)|(webOS)|(MeeGo)/i ),
			isTouch = null !== isMobile || document.createTouch !== undefined || ( 'ontouchstart' in window ) || ( 'onmsgesturechange' in window ) || navigator.msMaxTouchPoints,
			supportSVG = !! document.createElementNS && !! document.createElementNS( 'http://www.w3.org/2000/svg', 'svg' ).createSVGRect,
			winWidth = window.innerWidth ? window.innerWidth : $( window ).width(),
			winHeight = window.innerHeight ? window.innerHeight : $( window ).height(),
			currentX = 0,
			html = '<div id="swipebox-overlay" class="swipebox-overlay">\
					<div id="swipebox-container" class="swipebox-container" aria-modal="true" aria-label="' + magic_hat.title + '">\
						<div id="swipebox-top-bar" class="swipebox-bar swipebox-bar-top">\
							<div id="swipebox-title" class="swipebox-title"></div>\
							<button id="swipebox-close" class="swipebox-button swipebox-button-close" aria-label="' + magic_hat.close + '"></button>\
						</div>\
						<div id="swipebox-slider" class="swipebox-slider"></div>\
						<div id="swipebox-bottom-bar" class="swipebox-bar swipebox-bar-bottom">\
							<div id="swipebox-arrows" class="swipebox-arrows">\
								<button id="swipebox-prev" class="swipebox-button swipebox-button-prev" aria-label="' + magic_hat.previous + '"></button>\
								<button id="swipebox-next" class="swipebox-button swipebox-button-next" aria-label="' + magic_hat.next + '"></button>\
							</div>\
						</div>\
					</div>\
			</div>';

		plugin.settings = {};

		$.swipebox.close = function() {
			ui.closeSlide();
		};

		$.swipebox.extend = function() {
			return ui;
		};

		plugin.init = function() {

			plugin.settings = $.extend({}, defaults, options );

			if ( $.isArray( elem ) ) {

				elements = elem;
				ui.target = $( window );
				ui.init( plugin.settings.initialIndexOnArray );

			} else {

				$( document ).on( 'click', selector, function( event ) {
          var index, relType, relVal;

					// console.log( isTouch );

					if ( 'slide current' === event.target.parentNode.className ) {

						return false;
					}

					if ( ! $.isArray( elem ) ) {
						ui.destroy();
						$elem = $( selector );
						ui.actions();
					}

					elements = [];

					// Allow for HTML5 compliant attribute before legacy use of rel
					if ( ! relVal ) {
						relType = 'data-rel';
						relVal = $( this ).attr( relType );
					}

					if ( ! relVal ) {
						relType = 'rel';
						relVal = $( this ).attr( relType );
					}

					if ( relVal && '' !== relVal && 'nofollow' !== relVal ) {
						$elem = $( selector ).filter( '[' + relType + '="' + relVal + '"]' );
					} else {
						$elem = $( selector );
					}

					$elem.each( function() {

						var title = null,
							href = null;

						if ( $( this ).attr( 'title' ) ) {
							title = $( this ).attr( 'title' );
						}


						if ( $( this ).attr( 'href' ) ) {
							href = $( this ).attr( 'href' );
						}

						elements.push({
							href: href,
							title: title
						});
					});

					index = $elem.index( $( this ) );
					event.preventDefault();
					event.stopPropagation();
					ui.target = $( event.target );
					ui.init( index );
				});
			}
		};

		ui = {

			/**
			 * Initiate Swipebox
			 */
			init: function( index ) {
				if ( plugin.settings.beforeOpen ) {
					plugin.settings.beforeOpen();
				}
				this.target.trigger( 'swipebox-start' );
				$.swipebox.isOpen = true;
				this.build();
				this.openSlide( index );
				this.openMedia( index );
				this.preloadMedia( index + 1 );
				this.preloadMedia( index - 1 );
				if ( plugin.settings.afterOpen ) {
					plugin.settings.afterOpen( index );
				}
			},

			/**
			 * Built HTML containers and fire main functions
			 */
			build: function() {
				var $this = this,
bg;

				$( 'body' ).append( html );

				if ( supportSVG && true === plugin.settings.useSVG ) {
					bg = $( '#swipebox-close' ).css( 'background-image' );
					bg = bg.replace( 'png', 'svg' );
					$( '#swipebox-prev, #swipebox-next, #swipebox-close' ).css({
						'background-image': bg
					});
				}

				if ( isMobile && plugin.settings.removeBarsOnMobile ) {
					$( '#swipebox-bottom-bar, #swipebox-top-bar' ).remove();
				}

				$.each( elements,  function() {
					$( '#swipebox-slider' ).append( '<div class="slide"></div>' );
				});

				$this.setDim();
				$this.actions();

				if ( isTouch ) {
					$this.gesture();
				}

				// Devices can have both touch and keyboard input so always allow key events
				$this.keyboard();
				$this.resize();

			},

			/**
			 * Set dimensions depending on windows width and height
			 */
			setDim: function() {

				var width, height,
sliderCss = {};

				// Reset dimensions on mobile orientation change
				if ( 'onorientationchange' in window ) {

					window.addEventListener( 'orientationchange', function() {
						if ( 0 === window.orientation ) {
							width = winWidth;
							height = winHeight;
						} else if ( 90 === window.orientation || -90 === window.orientation ) {
							width = winHeight;
							height = winWidth;
						}
					}, false );


				} else {

					width = window.innerWidth ? window.innerWidth : $( window ).width();
					height = window.innerHeight ? window.innerHeight : $( window ).height();
				}

				sliderCss = {
					width: width,
					height: height
				};

				$( '#swipebox-overlay' ).css( sliderCss );

			},

			/**
			 * Reset dimensions on window resize envent
			 */
			resize: function() {
				var $this = this;

				$( window ).resize( function() {
					$this.setDim();
				}).resize();
			},

			/**
			 * Check if device supports CSS transitions
			 */
			supportTransition: function() {

				var prefixes = 'transition WebkitTransition MozTransition OTransition msTransition KhtmlTransition'.split( ' ' ),
					i;

				for ( i = 0; i < prefixes.length; i++ ) {
					if ( document.createElement( 'div' ).style[ prefixes[i] ] !== undefined ) {
						return prefixes[i];
					}
				}
				return false;
			},

			/**
			 * Check if CSS transitions are allowed (options + devicesupport)
			 */
			doCssTrans: function() {
				if ( plugin.settings.useCSS && this.supportTransition() ) {
					return true;
				}
			},

			/**
			 * Touch navigation
			 */
			gesture: function() {

				var $this = this,
					index,
					hDistance,
					vDistance,
					hDistanceLast,
					vDistanceLast,
					hDistancePercent,
					vSwipe = false,
					hSwipe = false,
					hSwipMinDistance = 10,
					vSwipMinDistance = 50,
					startCoords = {},
					endCoords = {},
					bars = $( '#swipebox-top-bar, #swipebox-bottom-bar' ),
					slider = $( '#swipebox-slider' );


				$( 'body' ).bind( 'touchstart', function( event ) {

					$( this ).addClass( 'touching' );
					index = $( '#swipebox-slider .slide' ).index( $( '#swipebox-slider .slide.current' ) );
					endCoords = event.originalEvent.targetTouches[0];
					startCoords.pageX = event.originalEvent.targetTouches[0].pageX;
					startCoords.pageY = event.originalEvent.targetTouches[0].pageY;

					$( '#swipebox-slider' ).css({
						'-webkit-transform': 'translate3d(' + currentX + '%, 0, 0)',
						'transform': 'translate3d(' + currentX + '%, 0, 0)'
					});

					$( '.touching' ).bind( 'touchmove', function( event ) {
            var opacity = 0.75 - Math.abs( vDistance ) / slider.height();
						event.preventDefault();
						event.stopPropagation();
						endCoords = event.originalEvent.targetTouches[0];

						if ( ! hSwipe ) {
							vDistanceLast = vDistance;
							vDistance = endCoords.pageY - startCoords.pageY;
							if ( Math.abs( vDistance ) >= vSwipMinDistance || vSwipe ) {

								slider.css({ 'top': vDistance + 'px' });
								slider.css({ 'opacity': opacity });

								vSwipe = true;
							}
						}

						hDistanceLast = hDistance;
						hDistance = endCoords.pageX - startCoords.pageX;
						hDistancePercent = hDistance * 100 / winWidth;

						if ( ! hSwipe && ! vSwipe && Math.abs( hDistance ) >= hSwipMinDistance ) {
							$( '#swipebox-slider' ).css({
								'-webkit-transition': '',
								'transition': ''
							});
							hSwipe = true;
						}

						if ( hSwipe ) {

							// swipe left
							if ( 0 < hDistance ) {

								// first slide
								if ( 0 === index ) {

									// console.log( 'first' );
									$( '#swipebox-overlay' ).addClass( 'leftSpringTouch' );
								} else {

									// Follow gesture
									$( '#swipebox-overlay' ).removeClass( 'leftSpringTouch' ).removeClass( 'rightSpringTouch' );
									$( '#swipebox-slider' ).css({
										'-webkit-transform': 'translate3d(' + ( currentX + hDistancePercent ) + '%, 0, 0)',
										'transform': 'translate3d(' + ( currentX + hDistancePercent ) + '%, 0, 0)'
									});
								}

							// swipe rught
							} else if ( 0 > hDistance ) {

								// last Slide
								if ( elements.length === index + 1 ) {

									// console.log( 'last' );
									$( '#swipebox-overlay' ).addClass( 'rightSpringTouch' );
								} else {
									$( '#swipebox-overlay' ).removeClass( 'leftSpringTouch' ).removeClass( 'rightSpringTouch' );
									$( '#swipebox-slider' ).css({
										'-webkit-transform': 'translate3d(' + ( currentX + hDistancePercent ) + '%, 0, 0)',
										'transform': 'translate3d(' + ( currentX + hDistancePercent ) + '%, 0, 0)'
									});
								}

							}
						}
					});

					return false;

				}).bind( 'touchend', function( event ) {
          var vOffset = 0 < vDistance ? slider.height() : -slider.height();
					event.preventDefault();
					event.stopPropagation();

					$( '#swipebox-slider' ).css({
						'-webkit-transition': '-webkit-transform 0.4s ease',
						'transition': 'transform 0.4s ease'
					});

					vDistance = endCoords.pageY - startCoords.pageY;
					hDistance = endCoords.pageX - startCoords.pageX;
					hDistancePercent = hDistance * 100 / winWidth;

					// Swipe to bottom to close
					if ( vSwipe ) {
						vSwipe = false;
						if ( Math.abs( vDistance ) >= 2 * vSwipMinDistance && Math.abs( vDistance ) > Math.abs( vDistanceLast ) ) {
							slider.animate({ top: vOffset + 'px', 'opacity': 0 },
								300,
								function() {
									$this.closeSlide();
								});
						} else {
							slider.animate({ top: 0, 'opacity': 1 }, 300 );
						}

					} else if ( hSwipe ) {

						hSwipe = false;

						// swipeLeft
						if ( hDistance >= hSwipMinDistance && hDistance >= hDistanceLast ) {

							$this.getPrev();

						// swipeRight
						} else if ( hDistance <= -hSwipMinDistance && hDistance <= hDistanceLast ) {

							$this.getNext();
						}

					}

					$( '#swipebox-slider' ).css({
						'-webkit-transform': 'translate3d(' + currentX + '%, 0, 0)',
						'transform': 'translate3d(' + currentX + '%, 0, 0)'
					});

					$( '#swipebox-overlay' ).removeClass( 'leftSpringTouch' ).removeClass( 'rightSpringTouch' );
					$( '.touching' ).off( 'touchmove' ).removeClass( 'touching' );

				});
			},

			/**
			 * Keyboard navigation
			 */
			keyboard: function() {
				var $this = this;
				$( window ).bind( 'keyup', function( event ) {
					event.preventDefault();
					event.stopPropagation();

					if ( 37 === event.keyCode ) {

						$this.getPrev();

					} else if ( 39 === event.keyCode ) {

						$this.getNext();

					} else if ( 27 === event.keyCode ) {

						$this.closeSlide();
					}
				});
			},

			/**
			 * Navigation events : go to next slide, go to prevous slide and close
			 */
			actions: function() {
				var $this = this,
					action = 'touchend click'; // Just detect for both event types to allow for multi-input

				if ( 2 > elements.length ) {

					$( '#swipebox-bottom-bar' ).hide();

					if ( undefined === elements[ 1 ]) {
						$( '#swipebox-top-bar' ).hide();
					}

				} else {
					$( '#swipebox-prev' ).bind( action, function( event ) {
						event.preventDefault();
						event.stopPropagation();
						$this.getPrev();
					});

					$( '#swipebox-next' ).bind( action, function( event ) {
						event.preventDefault();
						event.stopPropagation();
						$this.getNext();
					});
				}

				$( '#swipebox-overlay' ).bind( action, function() {
					$this.closeSlide();
				});
			},

			/**
			 * Set current slide
			 */
			setSlide: function( index, isFirst ) {
        var slider = $( '#swipebox-slider' );

				isFirst = isFirst || false;

				currentX = -index * 100;

				if ( this.doCssTrans() ) {
					slider.css({
						'-webkit-transform': 'translate3d(' + ( -index * 100 ) + '%, 0, 0)',
						'transform': 'translate3d(' + ( -index * 100 ) + '%, 0, 0)'
					});
				} else {
					slider.animate({ left: ( -index * 100 ) + '%' });
				}

				$( '#swipebox-slider .slide' ).removeClass( 'current' );
				$( '#swipebox-slider .slide' ).eq( index ).addClass( 'current' );
				this.setTitle( index );

				if ( isFirst ) {
					slider.fadeIn();
				}

				$( '#swipebox-prev, #swipebox-next' ).removeClass( 'disabled' ).attr( 'aria-disabled', 'false' );

				if ( 0 === index ) {
					$( '#swipebox-prev' ).addClass( 'disabled' ).attr( 'aria-disabled', 'true' );
				} else if ( index === elements.length - 1 && true !== plugin.settings.loopAtEnd ) {
					$( '#swipebox-next' ).addClass( 'disabled' ).attr( 'aria-disabled', 'true' );
				}
			},

			/**
			 * Open slide
			 */
			openSlide: function( index ) {
				$( 'html' ).addClass( 'swipebox-html' );
				if ( isTouch ) {
					$( 'html' ).addClass( 'swipebox-touch' );

					if ( plugin.settings.hideCloseButtonOnMobile ) {
						$( 'html' ).addClass( 'swipebox-no-close-button' );
					}
				} else {
					$( 'html' ).addClass( 'swipebox-no-touch' );
				}
				$( window ).trigger( 'resize' ); // fix scroll bar visibility on desktop
				this.setSlide( index, true );
			},

			/**
			 * Set a time out if the media is a video
			 */
			preloadMedia: function( index ) {
				var $this = this,
					src = null;

				if ( elements[ index ] !== undefined ) {
					src = elements[ index ].href;
				}

				if ( ! $this.isVideo( src ) ) {
					setTimeout( function() {
						$this.openMedia( index );
					}, 1000 );
				} else {
					$this.openMedia( index );
				}
			},

			/**
			 * Open
			 */
			openMedia: function( index ) {
				var $this = this,
					src,
					slide;

				if ( elements[ index ] !== undefined ) {
					src = elements[ index ].href;
				}

				if ( 0 > index || index >= elements.length ) {
					return false;
				}

				slide = $( '#swipebox-slider .slide' ).eq( index );

				if ( ! $this.isVideo( src ) ) {
					slide.addClass( 'slide-loading' );
					$this.loadMedia( src, function() {
						slide.removeClass( 'slide-loading' );
						slide.html( this );

						if ( plugin.settings.afterMedia ) {
							plugin.settings.afterMedia( index );
						}
					});
				} else {
					slide.html( $this.getVideo( src ) );

					if ( plugin.settings.afterMedia ) {
						plugin.settings.afterMedia( index );
					}
				}

			},

			/**
			 * Set link title attribute as caption
			 */
			setTitle: function( index ) {
				var title = null;

				$( '#swipebox-title' ).empty();

				if ( elements[ index ] !== undefined ) {
					title = elements[ index ].title;
				}

				if ( title ) {
					$( '#swipebox-top-bar' ).show();
					$( '#swipebox-title' ).append( title );
				} else {
					$( '#swipebox-top-bar' ).hide();
				}
			},

			/**
			 * Check if the URL is a video
			 */
			isVideo: function( src ) {

				if ( src ) {
					if ( src.match( /(youtube\.com|youtube-nocookie\.com)\/watch\?v=([a-zA-Z0-9\-_]+)/ ) || src.match( /vimeo\.com\/([0-9]*)/ ) || src.match( /youtu\.be\/([a-zA-Z0-9\-_]+)/ ) ) {
						return true;
					}

					if ( 0 <= src.toLowerCase().indexOf( 'swipeboxvideo=1' ) ) {

						return true;
					}
				}

			},

			/**
			 * Parse URI querystring and:
			 * - overrides value provided via dictionary
			 * - rebuild it again returning a string
			 */
			parseUri: function( uri, customData ) {
				var a = document.createElement( 'a' ),
					qs = {};

				// Decode the URI
				a.href = decodeURIComponent( uri );

				// QueryString to Object
				if ( a.search ) {
					qs = JSON.parse( '{"' + a.search.toLowerCase().replace( '?', '' ).replace( /&/g, '","' ).replace( /=/g, '":"' ) + '"}' );
				}

				// Extend with custom data
				if ( $.isPlainObject( customData ) ) {
					qs = $.extend( qs, customData, plugin.settings.queryStringData ); // The dev has always the final word
				}

				// Return querystring as a string
				return $
					.map( qs, function( val, key ) {
						if ( val && '' < val ) {
							return encodeURIComponent( key ) + '=' + encodeURIComponent( val );
						}
					})
					.join( '&' );
			},

			/**
			 * Get video iframe code from URL
			 */
			getVideo: function( url ) {
				var iframe = '',
					youtubeUrl = url.match( /((?:www\.)?youtube\.com|(?:www\.)?youtube-nocookie\.com)\/watch\?v=([a-zA-Z0-9\-_]+)/ ),
					youtubeShortUrl = url.match( /(?:www\.)?youtu\.be\/([a-zA-Z0-9\-_]+)/ ),
					vimeoUrl = url.match( /(?:www\.)?vimeo\.com\/([0-9]*)/ ),
					qs = '';
				if ( youtubeUrl || youtubeShortUrl ) {
					if ( youtubeShortUrl ) {
						youtubeUrl = youtubeShortUrl;
					}
					qs = ui.parseUri( url, {
						'autoplay': ( plugin.settings.autoplayVideos ? '1' : '0' ),
						'v': ''
					});
					iframe = '<iframe width="560" height="315" src="//' + youtubeUrl[1] + '/embed/' + youtubeUrl[2] + '?' + qs + '" frameborder="0" allowfullscreen></iframe>';

				} else if ( vimeoUrl ) {
					qs = ui.parseUri( url, {
						'autoplay': ( plugin.settings.autoplayVideos ? '1' : '0' ),
						'byline': '0',
						'portrait': '0',
						'color': plugin.settings.vimeoColor
					});
					iframe = '<iframe width="560" height="315"  src="//player.vimeo.com/video/' + vimeoUrl[1] + '?' + qs + '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';

				} else {
					iframe = '<iframe width="560" height="315" src="' + url + '" frameborder="0" allowfullscreen></iframe>';
				}

				return '<div class="swipebox-video-container" style="max-width:' + plugin.settings.videoMaxWidth + 'px"><div class="swipebox-video">' + iframe + '</div></div>';
			},

			/**
			 * Load image
			 */
			loadMedia: function( src, callback ) {
        var img;

        if ( 0 === src.trim().indexOf( '#' ) ) {

          // Inline content
          callback.call(
            $( '<div>', {
              'class': 'swipebox-inline-container'
            })
            .append(
              $( src )
              .clone()
              .toggleClass( plugin.settings.toggleClassOnLoad )
            )
          );
        } else {

          // Everything else
          if ( ! this.isVideo( src ) ) {
            img = $( '<img>' ).on( 'load', function() {
              callback.call( img );
            });
            img.attr( 'src', src );
          }
        }
      },

			/**
			 * Get next slide
			 */
			getNext: function() {
				var $this = this,
					src,
					index = $( '#swipebox-slider .slide' ).index( $( '#swipebox-slider .slide.current' ) );
				if ( index + 1 < elements.length ) {

					src = $( '#swipebox-slider .slide' ).eq( index ).contents().find( 'iframe' ).attr( 'src' );
					$( '#swipebox-slider .slide' ).eq( index ).contents().find( 'iframe' ).attr( 'src', src );
					index++;
					$this.setSlide( index );
					$this.preloadMedia( index + 1 );
					if ( plugin.settings.nextSlide ) {
						plugin.settings.nextSlide( index );
					}
				} else {

					if ( true === plugin.settings.loopAtEnd ) {
						src = $( '#swipebox-slider .slide' ).eq( index ).contents().find( 'iframe' ).attr( 'src' );
						$( '#swipebox-slider .slide' ).eq( index ).contents().find( 'iframe' ).attr( 'src', src );
						index = 0;
						$this.preloadMedia( index );
						$this.setSlide( index );
						$this.preloadMedia( index + 1 );
						if ( plugin.settings.nextSlide ) {
							plugin.settings.nextSlide( index );
						}
					} else {
						$( '#swipebox-overlay' ).addClass( 'rightSpring' );
						setTimeout( function() {
							$( '#swipebox-overlay' ).removeClass( 'rightSpring' );
						}, 500 );
					}
				}
			},

			/**
			 * Get previous slide
			 */
			getPrev: function() {
				var index = $( '#swipebox-slider .slide' ).index( $( '#swipebox-slider .slide.current' ) ),
					src;
				if ( 0 < index ) {
					src = $( '#swipebox-slider .slide' ).eq( index ).contents().find( 'iframe' ).attr( 'src' );
					$( '#swipebox-slider .slide' ).eq( index ).contents().find( 'iframe' ).attr( 'src', src );
					index--;
					this.setSlide( index );
					this.preloadMedia( index - 1 );
					if ( plugin.settings.prevSlide ) {
						plugin.settings.prevSlide( index );
					}
				} else {
					$( '#swipebox-overlay' ).addClass( 'leftSpring' );
					setTimeout( function() {
						$( '#swipebox-overlay' ).removeClass( 'leftSpring' );
					}, 500 );
				}
			},
			nextSlide: function( index ) {

				// Callback for next slide
			},

			prevSlide: function( index ) {

				// Callback for prev slide
			},

			/**
			 * Close
			 */
			closeSlide: function() {
				$( 'html' ).removeClass( 'swipebox-html' );
				$( 'html' ).removeClass( 'swipebox-touch' );
				$( window ).trigger( 'resize' );
				this.destroy();
			},

			/**
			 * Destroy the whole thing
			 */
			destroy: function() {
				$( window ).unbind( 'keyup' );
				$( 'body' ).unbind( 'touchstart' );
				$( 'body' ).unbind( 'touchmove' );
				$( 'body' ).unbind( 'touchend' );
				$( '#swipebox-slider' ).unbind();
				$( '#swipebox-overlay' ).remove();

				if ( ! $.isArray( elem ) ) {
					elem.removeData( '_swipebox' );
				}

				if ( this.target ) {
					this.target.trigger( 'swipebox-destroy' );
				}

				$.swipebox.isOpen = false;

				if ( plugin.settings.afterClose ) {
					plugin.settings.afterClose();
				}
			}
		};

		plugin.init();
	};

	$.fn.swipebox = function( options ) {
    var swipebox = new $.swipebox( this, options );

		if ( ! $.data( this, '_swipebox' ) ) {
			this.data( '_swipebox', swipebox );
		}
		return this.data( '_swipebox' );

	};

}( window, document, jQuery ) );

if ( jQuery( '[data-lightbox]' ).length ) {
	jQuery( '[data-lightbox] a' ).swipebox();
}
