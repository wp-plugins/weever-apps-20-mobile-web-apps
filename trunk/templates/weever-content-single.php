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
        <?php if ( get_option( 'weever_remove_image_links', true ) ): ?>
            <?php ob_start(); ?>
            <?php the_content(); ?>
            <?php $content = ob_get_contents(); ?>
            <?php ob_end_clean(); ?>
            <?php
            // Remove the link around other images unless the href starts with tel:, mailto:, or #!/ (ie, unless it's an internal link).
            $content =
                preg_replace(
                    array('{<a(?=\s|>)(?!(?:[^>=]|=([\'"])(?:(?!\1).)*\1)*?\shref=[\'"]/?(#!/|tel:|mailto:)(.*?)[\'"]?)[^>]*><img}',
                        '{ wp-image-[0-9]*" /></a>}'),
                    array('<img','" />'),
                    $content
                );
            ?>
            <?php echo $content; ?>
        <?php else: ?>
            <?php the_content(); ?>
        <?php endif; ?>
    </div>
</div>
