<?php
/**
 * Weever content template
 * 
 * You can customize this template by placing the file weever-content-single.php in 
 * your current theme, or add weever-content-single-{posttype}.php to customize by post type.
 * 
 */
// the_post() has already been called by Weever Controller
?>
<div class="item-page">
    <?php if ( ! isset( $_GET['content_header'] ) or $_GET['content_header'] != 'false' ): ?>
    <h1 class="wx-article-title">
        <?php echo get_the_title(); ?>
    </h1>
    <?php endif; ?>

    <div>
    <?php the_content(); ?>
    </div>
</div>
