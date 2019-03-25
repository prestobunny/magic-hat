<?php
/**
 * Quote Post Content
 *
 * Template for showing quote posts. This post format doesn't have a title.
 *
 * @package Magic Hat
 * @subpackage Post Formats
 * @since 1.0.0
 */

$magic_hat_source = wp_kses_post( rwmb_meta( '_format_quote_source_name' ) );
$magic_hat_url = esc_url( rwmb_meta( '_format_quote_source_url' ) );
if ( ! empty( $magic_hat_url ) ) {
    $magic_hat_source = '<a href="' . $magic_hat_url . '">' . $magic_hat_source . '</a>';
}
?>

<?php if ( ! post_password_required() ) { ?>
    <figure class="entry__content">
        <blockquote>
            <?php strip_tags( the_content() ); ?>
        </blockquote>
        <?php if ( ! empty( $magic_hat_source ) ) { ?>
            <footer>
                <cite><?php echo $magic_hat_source; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></cite>
            </footer>
        <?php } ?>
    </figure>
<?php } else { ?>
    <div class="entry__content">
        <?php the_content(); ?>
    </div>
<?php } ?>

<footer class="entry__meta entry__meta-footer">
    <?php echo magic_hat_get_entry_meta(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</footer><!-- .entry__meta-footer -->
