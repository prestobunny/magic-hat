/**
 * Comment Page/Submission Ajax.
 *
 * Handles loading comment pages and posting comments via ajax.
 *
 * @file Ajax commenting functionality.
 * @version 1.0.0
 * @license GPL-3.0-or-later
 * @since  1.0.0
 * @todo Spam/delete/edit functions as ajax
 */
/*eslint camelcase: 0*/

jQuery( function( $ ) {
    var focusError = null;

    /* Change these if you've modified the ID values in the comment form!
    If your comment ID numbering is different (i.e., not "#comment-{n}"), you
    should also edit the selector in deleteComment. */
    var commentsTitle =  $( '#comments-title' ),
        comments =       '#comments-list-nav',
        commentsStatus = $( '#comments-status' ),
        commentsPage =   $( '#comments-page-title' ),
        commentsList =   $( '#comments-list' ),
        commentsFiller = $( '#comments-filler' ),
        commentsNav =    '#comments-nav',
        deleteLink =     '#delete-link',
        respondForm =    $( '#respond' ),
        cancelLink =     $( '#cancel-comment-reply-link' ),
        author =         $( '#author' ),
        authorError =    $( '#author-error' ),
        email =          $( '#email' ),
        emailError =     $( '#email-error' ),
        comment =        $( '#comment' ),
        commentError =   $( '#comment-error' ),
        submitForm =     $( '#commentform' ),
        submitLink =     $( '#submit' ),
        submitError =    $( '#submit-error' );
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,})?$/;
        var emailValue = email.val();

        /**
         * Whether jQuery .on() function can be used.
         *
         * @since 1.0.0
         *
         * @type {bool}
         */
        var canUseOn = !! $.fn.on;
        var currentPage = 1,
            totalPages = 1,
            overflow = 0;

        var responseList = respondForm.prev( 'li' ).find( '.children' ).first();
        var link;

    $.extend( $.fn, {

        /**
         * Loads a new page of comments asynchronously, setting the comments
         * filler element to roughly the height of the old comments section to
         * prevent elements from jumping around during load time.
         *
         * @since 1.0.0
         *
         * @param {string} link     The comments page link to load.
         */
        loadComments: function( link ) {
            $( comments ).fadeOut( 500, function() {
                commentsFiller.css( 'height', $( comments ).css( 'height' ) );
                $( this ).load( link + ' ' + comments + ' > *', function() {
                    commentsFiller.css( 'height', 0 );
                    $( this ).fadeIn( 500 );
                    commentsTitle[0].scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                });
            });
        },

        /**
         * Wrapper function to call @see loadComments from a comment page link.
         *
         * @since 1.0.0
         *
         * @param {Object} e        Event object
         */
        loadCommentsPage: function( e ) {
            e.preventDefault();
            $( this ).loadComments( $( this ).attr( 'href' ) );
            commentsPage.focus();
        }

        /**
         * Deletes a comment via AJAX.
         *
         * @todo Error messages on comment deletion failure
         * @todo Proper reloading/message display for deleting pings
         *
         * @param {Object} e        Event object
         */
        /*
        deleteComment: function( e ) {
            var id = $( this ).data( 'id' ),
                nonce = $( this ).data( 'nonce' ),
                root = $( '#comment-' + id ).hasClass( 'depth-1' ) ? true : false;

            e.preventDefault();

            $.ajax({
                type: 'POST',
                url: magic_hat.ajaxurl,
                data: {
                    action: 'delete_comment',
                    id: id,
                    _ajax_nonce: nonce,
                    delete: 1
                },
                success: function() {
                    successMessage( magic_hat.deleteSuccess );

                    if ( isNaN( currentPage ) ) {
                        currentPage = 1;
                    }
                    link = magic_hat.baseurl.replace( '%#%', currentPage );
                    if ( true == magic_hat.pageComments && true == root && 1 == overflow ) {
                        link = magic_hat.baseurl.replace( '%#%', totalPages - 1 );
                    }
                    $( this ).loadComments( link );
                }
            });
        }
        */
    });

    /**
     * Validates comment form fields on submission attempt. The comment and
     * author fields are only checked to make sure they aren't empty; the email
     * field is checked for emptiness and against a format regex.
     *
     * @since 1.0.0
     *
     * @param {comment} [field='comment']   The field to validate. Accepts
     *                                      'comment', 'email', or 'author'.
     * @return {bool}                       Whether validation passed.
     */
    function validate() {
        if ( '' == comment.val() ) {

            // Comment is blank
            comment.addClass( 'input-error' );
            commentError.text( magic_hat.messageEmpty );
            commentError.attr( 'role', 'alert' );
            focusError = comment;
        } else {

            // Comment is OK!
            comment.removeClass( 'input-error' );
            commentError.text( '' );
            commentError.removeAttr( 'role' );
        }

        // Only check author/email if those fields are required
        if ( true == magic_hat.nameRequired ) {
            if ( '' == emailValue || ! emailReg.test( emailValue ) ) {
                email.addClass( 'input-error' );
                if ( '' == emailValue ) {
                  emailError.text( magic_hat.emailEmpty );
                } else {
                  emailError.text( magic_hat.emailBad );
                }
                emailError.attr( 'role', 'alert' );
                focusError = email;
            } else {

                // Email is OK!
                email.removeClass( 'input-error' );
                emailError.text( '' );
                emailError.removeAttr( 'role' );
            }

            if ( '' == author.val() ) {

                // Author is blank
                author.addClass( 'input-error' );
                authorError.text( magic_hat.authorEmpty );
                authorError.attr( 'role', 'alert' );
                focusError = author;
            } else {

                // Author is OK!
                author.removeClass( 'input-error' );
                authorError.text( '' );
                authorError.removeAttr( 'role' );
            }
        }

        if ( null != focusError ) {
            if ( ! author.hasClass( 'input-error' ) && ! email.hasClass( 'input-error' ) && ! comment.hasClass( 'input-error' ) ) {
                focusError = null;
            } else {
                focusError[0].scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                focusError.focus();
            }
        }
    }

    /**
     * Causes the specified success message to appear when an event is
     * successful while handling the appropriate class/attribution changes. The
     * message fades out after three seconds.
     *
     * @since 1.0.0
     *
     * @param {string} msg  Content of the success message.
     */
    function successMessage( msg ) {
        commentsStatus.attr( 'class', 'success' );
        commentsStatus.attr( 'role', 'alert' );
        commentsStatus.text( msg );
        commentsStatus.fadeIn();
        commentsStatus.delay( 3000 ).fadeOut();
        wp.a11y.speak( magic_hat.updateMsg, 'polite' );
    }

    if ( true == magic_hat.pageComments ) {
        currentPage = $( commentsNav ).children( '.current' ).first().text();
        currentPage = ( null !== currentPage ) ? parseInt( currentPage ) : 1;

        totalPages = commentsList.data( 'total' );
        totalPages = ( null !== totalPages ) ? parseInt( totalPages ) : 1;

        overflow = commentsList.data( 'overflow' );
        overflow = ( null !== overflow ) ? parseInt( overflow ) : 1;
    }

    if ( canUseOn ) {
      $( document )
      .on( 'click', commentsNav + ' a', function( e ) {
        $( this ).loadCommentsPage( e );
      })
      .on( 'click', deleteLink + ' a', function( e ) {
        $( this ).deleteComment( e );
      });
    } else {
      $( commentsNav ).live( 'click', 'a', function( e ) {
        $( this ).loadCommentsPage( e );
      });
      $( deleteLink ).live( 'click', 'a', function( e ) {
        $( this ).deleteComment( e );
      });
    }

	submitForm.submit( function() {
        validate();

		if ( ! submitLink.hasClass( 'button-loading' ) && null == focusError ) {
      $.ajax({
				type: 'POST',
				url: magic_hat.ajaxurl,
				data: $( this ).serialize() + '&action=magic_hat_comment_handler',
				beforeSend: function( xhr ) {
          submitLink.addClass( 'button-loading' );
				},
				error: function( request, status, error ) {
          if ( 'timeout' == status ) {
            submitError.text( magic_hat.timeout );
          }
          submitError.attr( 'role', 'alert' );
        },
        statusCode: {
          409: function() {
            submitError.text( magic_hat.duplicateError );
          },
          429: function() {
            submitError.text( magic_hat.floodError );
          },
          500: function() {
            submitError.text( magic_hat.serverError );
          }
        },
				success: function( addedCommentHTML ) {
          successMessage( magic_hat.postSuccess );

          if ( 0 < commentsList.length ) { /* There are other comments already. */
            if ( respondForm.parent().hasClass( 'comment-list' ) ) { /* First depth 2 comment */
              addedCommentHTML = '<ol class="children">' + addedCommentHTML + '</ol>';
              respondForm.prev().append( addedCommentHTML );
              cancelLink.trigger( 'click' );
            } else if ( respondForm.parent().hasClass( 'children' ) ) { /* Depth 3+ comment */
              if ( 0 === responseList.length ) { /* This is the first response of this depth to the parent */
                addedCommentHTML = '<ol class="children">' + addedCommentHTML + '</ol>';
                respondForm.prev().append( addedCommentHTML );
              } else { /* There are other resposnes at this depth there already */
                responseList.append( addedCommentHTML );
              }
              cancelLink.trigger( 'click' );
            } else { /* Depth 1 comment */
              if ( magic_hat.pageComments && ( 0 == overflow || currentPage !== totalPages ) ) {

                /* We need to show a new page entirely */
                if ( 0 == overflow ) { /* Last comment page is full */

                  /* "Create" a new page */
                  link = magic_hat.baseurl.replace( '%#%', totalPages + 1 );
                } else { /* Just show the last comments page */
                  link = magic_hat.baseurl.replace( '%#%', totalPages );
                }

                $( this ).loadComments( link );
              } else { /* We're on the newest page and it's not full */
                if ( 'desc' == magic_hat.commentOrder ) { /* Descending comments */
                  commentsList.prepend( $( addedCommentHTML ) );
                } else { /* Ascending comments */
                  commentsList.append( $( addedCommentHTML ) );
                }
              }
            }
          } else { /* This is the first comment on the post */
            addedCommentHTML = '<ol id="comments-list" class="comment-list">' + addedCommentHTML + '</ol>';
            respondForm.before( $( addedCommentHTML ) );
          }
          $( '#comment' ).val( '' );
        },
        complete: function() {
          submitLink.removeClass( 'button-loading' );
        }
      });
    }
    return false;
	});
});
