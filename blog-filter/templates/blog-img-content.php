<?php
if (! defined('ABSPATH')) exit; // Exit if accessed directly

// We only proceed if blog images are enabled.
if ($blog_image == "yes") {

    // 1. GET IMAGE DATA
    $image_id = get_post_thumbnail_id();
    $thumbnail_url = get_the_post_thumbnail_url(null, $blog_image_quality);

    // Proceed only if a thumbnail actually exists
    if ($thumbnail_url) {
        $image_title = get_the_title($image_id);
        $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', TRUE);

        if (empty($image_alt)) {
            $image_alt = esc_attr($image_title);
        }

        // Determine the background image style for the <figure> element if fixed grid is on.
        $background_image_style = ($blog_fixed_grid == 'yes')
            ? 'background-image: url(' . esc_url($thumbnail_url) . ');'
            : '';

        // Map hover effects to their corresponding CSS classes.
        $hover_effect_classes = [
            'hover1' => 'snip1550',
            'none'   => ''
        ];
        $figure_class = $hover_effect_classes[$blog_image_hover_effect] ?? '';
        $main_container_class = 'fit-in-content';
?>

        <figure class="<?php echo esc_attr($figure_class); ?> <?php echo esc_attr($main_container_class); ?>" style="<?php echo esc_attr($background_image_style); ?>">

            <?php // The actual image tag is only needed if not using a fixed grid background. ?>
            <?php if ($blog_fixed_grid != 'yes'): ?>
                <img title="<?php echo esc_attr($image_title); ?>" class="portfolio_thumbnail" src="<?php echo esc_url($thumbnail_url); ?> " alt="<?php echo esc_attr($image_alt); ?>">
            <?php endif; ?>

        </figure>

<?php
    } // End of the 'if ($thumbnail_url)' check.
} // End of the 'if ($blog_image == "yes")' check.
?>