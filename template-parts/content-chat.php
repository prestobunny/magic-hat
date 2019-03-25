<?php
/**
 * Chat Post Content
 *
 * @package Magic Hat
 * @subpackage Post Formats
 * @since 1.0.0
 */

?>

<?php magic_hat_entry_header(); ?>

<?php if ( is_singular() ) { ?>
    <div class="entry__content">
        <?php the_content(); ?>
    </div>
    
    <?php if ( ! post_password_required() ) { ?>
        <footer class="entry__meta entry__meta-footer">
            <?php magic_hat_entry_footer(); ?>
        </footer><!-- .entry__meta-footer -->
    <?php } ?>
<?php } else { ?>
    <div class="entry__content">
        <?php magic_hat_post_format_chat_excerpt(); ?>
    </div>
<?php } ?>
