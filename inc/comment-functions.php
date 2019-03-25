<?php
/**
 * Comment Functions
 *
 * Functions used to modify comment display and define Ajax functionality.
 *
 * @package Magic Hat
 * @subpackage Comments
 * @since 1.0.0
 */

/**
 * Registers, localizes, and enqueues the script for the comment form and comment Ajax.
 *
 * @link https://codex.wordpress.org/Option_Reference#Discussion
 * @see wp_handle_comment_submission()
 *
 * @since 1.0.0
 */
function magic_hat_enqueue_comments_scripts() {
	wp_register_script( 'comment-ajax', get_template_directory_uri() . '/assets/js/comment-ajax.min.js', array( 'jquery', 'wp-a11y' ), null, true );

  /* Include Ajax if the user can moderate comments so the inline delete button works. */
	if ( get_theme_mod( 'use-comments-ajax', true ) && is_singular() && ( comments_open() || current_user_can( 'moderate_comments' ) ) ) {
		global $wp_rewrite;
		if ( $wp_rewrite->using_permalinks() ) {
			$base = user_trailingslashit( trailingslashit( get_permalink() ) . $wp_rewrite->comments_pagination_base . '-%#%', 'commentpaged' );
		} else {
			$base = add_query_arg( 'cpage', '%#%' );
		}
		/**
		 * Let's replace the built-in error messages and send over some comment settings.
         *
		 * @type string $ajaxurl		Admin path to admin-ajax.php.
		 * @type string $baseurl		Base comment page URL.
         * @type string $updateMsg      Passed to wp.a11y.speak when the comments refresh.
		 * @type string $commentOrder	Whether to show newest or oldest comments first.
		 * @type bool $pageComments		Whether comments are paginated.
		 * @type bool $commentsPerPage	Number of comments per page.
		 * @type bool $nameRequired 	Whether the site requires commenters' name/email.
		 * @type string $serverError	Error displayed when database submission fails (500).
		 * @type string $floodError		Error displayed when user posts too quickly (429).
		 * @type string $duplicateError Error displayed for duplicate comments (409).
		 * @type string $timeout		Error displayed when Ajax returns "timeout" status.
		 * @type string $postSuccess	Message displayed on successful submission.
		 * @type string $deleteSuccess	Message displayed when comment is deleted.
		 * @type string $messageEmpty	Error displayed when comment is empty.
		 * @type string $authorEmpty	Error displayed when name is empty.
		 * @type string $emailEmpty		Error displayed when email is empty.
		 * @type string $emailBad		Error displayed when email is the wrong format.
		 */
		wp_localize_script( 'comment-ajax', 'magic_hat', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'baseurl' => $base,
            'updateMsg' => esc_html__( 'Reloading the comments section...', 'magic-hat' ),
			'commentOrder' => get_option( 'comment_order' ),
			'pageComments' => get_option( 'page_comments' ),
			'commentsPerPage' => get_option( 'comments_per_page' ),
			'nameRequired' => get_option( 'require_name_email' ),
			'serverError' => esc_html__( 'There was a problem submitting your comment. Try again later.', 'magic-hat' ),
			'floodError' => esc_html__( 'Wait a moment before posting another comment.', 'magic-hat' ),
			'duplicateError' => esc_html__( "You've already posted this comment!", 'magic-hat' ),
			'timeout' => esc_html__( 'The server timed out. Please try again.', 'magic-hat' ),
			'postSuccess' => esc_html__( 'Your comment was submitted.', 'magic-hat' ),
			'deleteSuccess' => esc_html__( 'The comment was deleted.', 'magic-hat' ),
			'messageEmpty' => esc_html__( "Your message can't be blank.", 'magic-hat' ),
			'authorEmpty' => esc_html__( "Your name can't be blank.", 'magic-hat' ),
			'emailEmpty' => esc_html__( "Your email can't be blank.", 'magic-hat' ),
			'emailBad' => esc_html__( "Double-check your email formatting.", 'magic-hat' ),
		) );
		wp_enqueue_script( 'comment-ajax' );
    }

    /* Comment Reply Form Js */
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'magic_hat_enqueue_comments_scripts' );

/* Allow comment deletion via Ajax on the front end
add_action( 'wp_ajax_delete_comment', 'wp_ajax_delete_comment' );
add_action( 'wp_ajax_nopriv_delete_comment', 'wp_ajax_delete_comment' );
*/

if ( ! function_exists( 'magic_hat_comment_handler' ) ) :
/**
 * Handles Ajax comment submission on the server side.
 *
 * @link https://rudrastyh.com/wordpress/ajax-comments.html
 *
 * @since 1.0.0
 */
function magic_hat_comment_handler() {
    $comment = wp_handle_comment_submission( wp_unslash( $_POST ) );

    /* Make sure there wasn't an error */
    if ( is_wp_error( $comment ) ) {
        $error = intval( $comment->get_error_data() );
        if ( ! empty( $error ) ) {
            wp_die(
                '<p>' . esc_html( $comment->get_error_message() ) . '</p>',
                esc_html__( 'Posting comment failed.', 'magic-hat' ),
                array(
                    'response' => esc_html( $error ),
                    'back_link' => true
                )
            );
        } else {
            wp_die( esc_html__( 'Unknown Error', 'magic-hat' ) );
        }
    }

    if ( $comment->comment_approved == 1) {
        if ( get_option( 'comments_notify' ) == 1 ) {
            wp_notify_postauthor( $comment->comment_ID );
        }
    } else {
        if ( get_option( 'moderation_notify' ) == 1 ) {
            wp_notify_moderator( $comment->comment_ID );
        }
    }

    $user = wp_get_current_user();
    if ( isset( $_POST['wp-comment-cookies-consent'] ) || ! get_option( 'show_comments_cookies_opt_in' ) ) {
        do_action('set_comment_cookies', $comment, $user);
    }

    $depth = 1;
    $parent = $comment->comment_parent;
    while ( $parent ) {
        $depth++;
        $parent = get_comment( $parent );
        $parent = $parent->comment_parent;
    }

    magic_hat_comment_callback( $comment, array(), $depth );
    echo '</li>';
    die();
}
endif;
add_action( 'wp_ajax_magic_hat_comment_handler', 'magic_hat_comment_handler' );
add_action( 'wp_ajax_nopriv_magic_hat_comment_handler', 'magic_hat_comment_handler' );

if ( ! function_exists( 'magic_hat_comment_labels' ) ) :
/**
 * Adjusts arguments for the comment form to show custom labels for everything. You
 * should modify this function if you want to change the display of the comment form
 * fields, which are also set here.
 *
 * If you modify the IDs in the comment form, be sure to edit the variables at the top
 * of comment-ajax.js to reflect your new IDs, or use your own comment AJAX script.
 *
 * @since 1.0.0
 *
 * @return array	The list of labels which can be merged with existing arguments
 * 					for {@see comment_form}.
 */
function magic_hat_comment_labels() {
    /* String formatting gets screwed up if we try to set this in the array :T */
		/* translators: %s is the name of the parent comment. */
    $reply_to = empty( get_theme_mod( 'reply-to-title' ) ) ? esc_html__( 'Reply to %s', 'magic-hat' ) : esc_html( get_theme_mod( 'reply-to-title' ) );
    $labels = array(
        'title_reply' => esc_html( get_theme_mod( 'reply-title', __( 'Share your thoughts', 'magic-hat' ) ) ),
        'title_reply_to' => $reply_to,
        'cancel_reply_link' => esc_html( get_theme_mod( 'cancel-reply-title', __( 'Cancel Reply', 'magic-hat' ) ) ),
        'label_submit' => esc_html( get_theme_mod( 'comment-submit-title', __( 'Post Comment', 'magic-hat' ) ) ),
    );

    /* Wrap the before/after notes in p tags if they exist */
    $notes_before = get_theme_mod( 'comment-notes-before', '' );
    $notes_before = empty( $notes_before ) ? '' : '<p class="comment-form-notes-before">' . esc_html( $notes_before ) . '</p>';
    $labels['comment_notes_before'] = $notes_before;

    $notes_after = get_theme_mod( 'comment-notes-after', '' );
    $notes_after = empty( $notes_after ) ? '' : '<p class="comment-form-notes-after">' . esc_html( $notes_after ) . '</p>';
    $labels['comment_notes_after'] = $notes_after;

    $commenter = wp_get_current_commenter();
    $req = get_option( 'require_name_email' );
    $aria_req = $req ? " aria-required='true'" : '';
    $req = $req ? ' class="reqruied"' : '';
    $consent = empty( $commenter['comment_author_email'] ) ? '' : ' checked="checked"';

    /* Get the custom labels for the comment field. If empty, use a generic label that's
    only visible to screenreaders. */
    $comment_label = get_theme_mod( 'comment-field-title', esc_html_x( 'Message', 'noun', 'magic-hat' ) );
    $comment_label = empty( $comment_label ) ? '<label class="required screen-reader-text" for="comment">' . esc_html__( 'Your Comment', 'magic-hat' ) : '<label class="required" for="comment">' . esc_html( $comment_label ) ;

    $labels['comment_field'] = '
    <div class="comment-form-comment">' .
        $comment_label . '
        <textarea aria-describedby="comment-error" id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea>
        <p id="comment-error" class="error"></p>
    </div>
    <p id="submit-error" class="error"></p>';

    $fields = array();
    $fields['author'] = '
    <div class="comment-form-info">
        <div class="comment-form-author">
            <label' . $req . ' for="author">' . get_theme_mod( 'author-field-title', esc_html_x( 'Name', 'noun', 'magic-hat' ) ) . '</label>
            <input aria-describedby="author-error" id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '"' . $aria_req . ' />
            <p id="author-error" class="error"></p>
        </div>';
    $fields['email'] = '
        <div class="comment-form-email">
            <label' . $req . ' for="email">' . esc_html( get_theme_mod( 'email-field-title', _x( 'Email', 'noun', 'magic-hat' ) ) ) . '</label>
            <input aria-describedby="email-error" id="email" name="email" type="email" value="' . esc_attr(  $commenter['comment_author_email'] ) . '"' . $aria_req . ' />
            <p id="email-error" class="error"></p>
        </div>';
    if ( get_theme_mod( 'use-commenter-url', false ) ) {
        $fields['url'] = '
        <div class="comment-form-url">
            <label for="url">' . esc_html( get_theme_mod( 'url-field-title', __( 'Website', 'magic-hat' ) ) ) . '</label>' .
            '<input id="url" name="url" type="url" value="' . esc_attr( $commenter['comment_author_url'] ) . '"/>
        </div>
    </div>';
    } else {
        $fields['email'] .= '
    </div>';
    }

    if ( get_option( 'show_comments_cookies_opt_in' ) ) {
        $fields['cookies'] = '
        <div class="comment-form-cookies-consent">
            <input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . $consent . ' />
            <label for="wp-comment-cookies-consent">' . esc_html( get_theme_mod( 'cookies-field-title', __( 'Save my info for the next time I comment.', 'magic-hat' ) ) ) . '</label>
        </div>';
    }

    $labels['fields'] = $fields;

    return $labels;
}
endif;

if ( ! function_exists( 'magic_hat_comment_form_fields' ) ) :
/**
 * Moves the comment field to the end of the form, wraps the name/email/url fields in a
 * div for formatting and changes their container paragraph(!) elements to divs. This
 * function also handles removing the website field if desired.
 *
 * @since 1.0.0
 *
 * @param array $fields		The comment field elements as strings of html.
 * @return array			The updated fields array.
 */
function magic_hat_comment_form_fields( $fields ) {
    $comment_field = $fields['comment'];
    $cookies_field = isset( $fields['cookies'] ) ? $fields['cookies'] : false;
    unset( $fields['comment'] );
    $fields['comment'] = $comment_field;
    if ( $cookies_field ) {
        unset( $fields['cookies'] );
        $fields['cookies'] = $cookies_field;
    }

    return $fields;
}
endif;
add_filter( 'comment_form_fields', 'magic_hat_comment_form_fields' );

if ( ! function_exists( 'magic_hat_comment_callback' ) ) :
/**
 * Echoes each comment in a list (though without a closing li tag), to be used as a callback
 * for {@see wp_list_comments} and by {@see magic_hat_comment_handler}.
 *
 * @todo Ajax mark-as-spam from front end
 *
 * @since 1.0.0
 *
 * @param WP_Comment $comment   Current comment object.
 * @param array $args           Comment display args which don't actually get used here.
 * @param int $depth            Current comment depth, used for the reply link.
 */
function magic_hat_comment_callback( $comment, $args, $depth ) {
    $id = $comment->comment_ID;
    $post_id = $comment->comment_post_ID;

    switch ( $comment->comment_type ) {
        case 'pingback' :
            $comment_avatar = '';
            /* translators: %s is the author link for the pingback */
            $comment_title = sprintf( __( '%s mentioned this', 'magic-hat' ), get_comment_author_link( $id ) );
            $comment_approved = '';
            break;
        case 'trackback' :
            $comment_avatar = '';
            /* translators: %s is the author link for the trackback */
            $comment_title = sprintf( __( 'Mentions %s', 'magic-hat' ), get_comment_author_link( $id ) );
            $comment_approved = '';
            break;
        default :
            $comment_avatar = get_avatar( $comment, 32 );
            $comment_title = get_comment_author_link( $id );
            $comment_approved = $comment->comment_approved == 0 ? '<p class="comment__content-unapproved">' . esc_html__( "Your comment will appear once it's been approved.", 'magic-hat' ) . '</p>' : '';
    }

    echo '
    <li id="comment-' . esc_attr( $id ) . '" ' . comment_class( '', $id, $post_id, false ) . '">
        <article class="comment__body">
            <footer class="comment__footer">
                <div class="footer__vcard">
                    ' . wp_kses_post( $comment_avatar ) . '
                    <div class="vcard__author">' . wp_kses_post( $comment_title ) . '</div>
                </div>
                <div class="comment__meta">
                    <time class="meta meta-time" itemprop="datePublished" datetime="' . esc_attr( get_comment_date( 'c', $id ) ) . '">' . esc_html( magic_hat_relative_time( get_comment_date( 'U', $id ) ) ) . '</time>';

                    if ( current_user_can( 'moderate_comments' ) ) {
                        echo '
                        <a class="meta meta-link meta-link-edit" href="' . esc_url( get_edit_comment_link( $id ) ) . '">';
                        /**
                         * Filters the content of the "edit comment" link, by default an svg.
                         *
                         * @param string    The markup for the content of the link, default an svg pencil icon.
                         * @return string   Filtered markup.
                         */
                        /* translators: "Edit comment {id} by {author}" */
                        echo apply_filters( 'magic_hat_svg_edit_comment', '<svg xmlns="http://www.w3.org/2000/svg" alt="' . sprintf( esc_html__( 'Edit comment %1$d by %2$s', 'magic-hat' ), $id, $comment->comment_author ) . '" width="12" height="12" viewBox="0 0 24 24"><path d="M19.769 9.923l-12.642 12.639-7.127 1.438 1.438-7.128 12.641-12.64 5.69 5.691zm1.414-1.414l2.817-2.82-5.691-5.689-2.816 2.817 5.69 5.692z"/></svg>' );  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        echo '
                        </a>';

												echo '
                        <a id="spam-link" class="meta meta-link meta-link-spam" href="' . esc_url( admin_url( 'comment.php?action=cdc&dt=spam&c=' . $id ) ) . '">';
												/**
												 * Filters the content of the "mark comment as spam" link, by default an svg.
												 *
												 * @param string	The markup for the content of the link, default a triangular warning icon.
												 * @return string Filtered markup.
												 */
												/* translators: "Mark comment {id} by {author} as spam" */
												echo apply_filters( 'magic_hat_svg_spam_comment', '<svg xmlns="http://www.w3.org/2000/svg" alt="' . sprintf( esc_html__( 'Mark comment %1$d by %2$s as spam', 'magic-hat' ), $id, $comment->comment_author ) . '" width="12" height="12" viewBox="0 0 24 24"><path d="M12 1l-12 22h24l-12-22zm-1 8h2v7h-2v-7zm1 11.25c-.69 0-1.25-.56-1.25-1.25s.56-1.25 1.25-1.25 1.25.56 1.25 1.25-.56 1.25-1.25 1.25z"/></svg>'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        echo '
												</a>';

                        echo '
                        <a id="delete-link" class="meta meta-link meta-link-delete" href="' . esc_url( admin_url( 'comment.php?action=cdc&c=' . $id ) ) . '" data-id="' . esc_attr( $id ) . '" data-nonce="' . esc_attr( wp_create_nonce( 'delete-comment_' . $id ) ) . '">';
                        /**
                         * Filters the content of the "delete comment" link, by default an svg.
                         *
                         * @param string    The markup for the content of the link.
                         * @return string   Filtered markup.
                         */
                        /* translators: "Delete comment {id} by {author}" */
                        echo apply_filters( 'magic_hat_svg_delete_comment', '<svg aria-label="' . sprintf( esc_html__( 'Delete comment %1$d by %2$s', 'magic-hat' ), $id, $comment->comment_author ) . '" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"><path d="M24 20.188l-8.315-8.209 8.2-8.282-3.697-3.697-8.212 8.318-8.31-8.203-3.666 3.666 8.321 8.24-8.206 8.313 3.666 3.666 8.237-8.318 8.285 8.203z"/></svg>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        echo '
                        </a>';
                    }
                echo '
                </div>';
            echo '
            </footer>
            <div class="comment__content">' .
                wp_kses_post( $comment_approved ) .
                wp_kses_post( apply_filters( 'comment_text', get_comment_text( $comment ), $comment ) ) . '
            </div>
            <div class="comment__reply">';
                comment_reply_link( array( 'depth' => $depth, 'max_depth' => get_option( 'thread_comments_depth' ), ), $id, $post_id );
            echo '
            </div>
        </article>';
}
endif;

/**
 * Determines whether posting or deleting a comment via AJAX requires loading a new page.
 *
 * @since 1.0.0
 *
 * @return string 'overflow' and 'total' data attributes to be added to comment list element
 */
function magic_hat_get_overflow() {
    global $wp_query;
    if ( ! get_option( 'page_comments' ) ) {
        return '';
    }
    $per_page = (int) get_option('comments_per_page');
    $post_id = get_the_ID();
    $comments = get_comments( array( 'post_id' => $post_id ) );
    if ( get_option( 'thread_comments' ) ) {
        $walker = new Walker_Comment;
        $comments = $walker->get_number_of_root_elements( $comments );
    } else {
        $comments = count( $comments );
    }
    return 'data-overflow="' . $comments % $per_page . '" data-total="' . get_comment_pages_count() . '"';
}
