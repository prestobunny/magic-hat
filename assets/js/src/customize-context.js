/**
 * Customize Context.
 *
 * Script to hide/expand certain postMessage-enabled controls depending on the context.
 * Enqueued at customize_controls_enqueue_scripts in order to access the 'ready' event.
 *
 * @file Customizer contextual control display.
 * @version 1.0.0
 * @license GPL-3.0-or-later
 * @since 1.0.0
 */
( function( $ ) {

	wp.customize.bind( 'ready', function() {

        /* Hide comment controls if comments aren't showing */
        var commentsTitle = this.control( 'comments-title' ).container;
        var replyTitle = this.control( 'reply-title' ).container;

        /* Show background controls only if the layout is set to boxed. */
        if ( 0 == $( '#use-boxed' ).val() ) {
            $( '#customize-control-background_color' ).hide();
            $( '#customize-control-background_image' ).hide();
            $( '#customize-control-background_position' ).hide();
            $( '#customize-control-background_size' ).hide();
            $( '#customize-control-background_repeat' ).hide();
            $( '#customize-control-background_attachment' ).hide();
        }

        function hideBgControls() {
            var boxed = $( '#use-boxed' );
            if ( 'checked' == boxed.attr( 'checked' ) ) {
                $( '#customize-control-background_color' ).slideDown();
                $( '#customize-control-background_image' ).slideDown();
                $( '#customize-control-background_position' ).slideDown();
                $( '#customize-control-background_size' ).slideDown();
                $( '#customize-control-background_repeat' ).slideDown();
                $( '#customize-control-background_attachment' ).slideDown();
            } else {
                $( '#customize-control-background_color' ).slideUp();
                $( '#customize-control-background_image' ).slideUp();
                $( '#customize-control-background_position' ).slideUp();
                $( '#customize-control-background_size' ).slideUp();
                $( '#customize-control-background_repeat' ).slideUp();
                $( '#customize-control-background_attachment' ).slideUp();
            }
        }

        $( '#use-boxed' ).change( hideBgControls );

        wp.customize.control( 'has-comments', function( control ) {
            control.onChangeActive = function( active ) {
                if ( active ) {
                    commentsTitle.slideDown();
                    replyTitle.slideDown();
                } else {
                    commentsTitle.hide();
                    replyTitle.hide();
                }
            };
        });
    });
} ( jQuery ) );
