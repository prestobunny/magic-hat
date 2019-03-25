<?php
/**
 * Gallery Post Content
 *
 * @package Magic Hat
 * @subpackage Post Formats
 * @since 1.0.0
 */

$magic_hat_id = get_the_ID();
?>

<?php magic_hat_entry_header( false, $magic_hat_id ); ?>

<?php if ( ! post_password_required() ) {
    /* Get the gallery HTML from either a shortcode or an array of images */
    if ( rwmb_meta( '_format_gallery_type' ) == 'shortcode' ) {
        $magic_hat_content = do_shortcode( rwmb_meta( '_format_gallery_shortcode' ) );
    } else {
        $magic_hat_content = magic_hat_get_gallery(
            rwmb_meta( '_format_gallery_images',
            array( 'size' => 'full' ) ),
            array(
                'id' => $magic_hat_id,
                'class' => 'gallery-col-2',
                'link' => 'file',
            )
        );

        /** The gallery function adds a figure element, which we need to edit. */
        $magic_hat_pos = strpos( $magic_hat_content, '<figure class="gallery' );
        if ( $magic_hat_pos !== false ) {
            $magic_hat_aria = empty( get_the_content() ) ? '<figure aria-labelledby="entry__header" class="gallery' : "<figure aria-labelledby=\"entry__header\" aria-describedby=\"entry__content-{$magic_hat_id}\" class=\"gallery";
            $magic_hat_content = substr_replace( $magic_hat_content, $magic_hat_aria, $magic_hat_pos, strlen( '<figure class="gallery' ) );
        }
    }

    echo $magic_hat_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
} ?>

<div id="entry__content-<?php echo esc_attr( $magic_hat_id ); ?>" class="entry__content">
    <?php magic_hat_content_excerpt(); ?>
</div>

<?php if ( is_singular() ) { ?>
    <footer class="entry__meta entry__meta-footer">
        <?php magic_hat_entry_footer(); ?>
    </footer><!-- .entry__meta-footer -->
<?php } ?>
