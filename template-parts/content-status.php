<?php
/**
 * Status Post Content
 *
 * Template for showing status posts. This post format doesn't have a title.
 *
 * @package Magic Hat
 * @subpackage Post Formats
 * @since 1.0.0
 */

?>

<div class="entry__content status">
    <?php the_content(); ?>
</div>

<footer class="entry__meta entry__meta-footer">
    <?php echo magic_hat_get_entry_meta(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</footer><!-- .entry__meta-footer -->
