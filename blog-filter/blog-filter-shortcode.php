<?php

/**
 * Plugin Shortcode: [AWL-BlogFilter]
 *
 * This file contains the main shortcode function that renders the blog filter gallery.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_shortcode('AWL-BlogFilter', 'bf_blog_filter_shortcode');

// Backward compatibility wrapper
if (! function_exists('awl_blog_filter_shortcode')) {
    function awl_blog_filter_shortcode($user_atts)
    {
        return bf_blog_filter_shortcode($user_atts);
    }
}

/**
 * Renders the blog filter gallery based on shortcode attributes.
 *
 * @param array $atts User-defined shortcode attributes.
 * @return string HTML output for the gallery.
 */
function bf_blog_filter_shortcode($user_atts)
{

    // 1. --- Enqueue Scripts and Styles ---
    // This ensures all necessary assets are loaded for the gallery to function.
    wp_enqueue_script('imagesloaded');
    wp_enqueue_script('awl-bf-filterizr-js');

    wp_enqueue_style('awl-bf-filter-output-css');
    wp_enqueue_style('awl-bf-hover-css');
    // Bootstrap JS & CSS (conditionally loaded based on shortcode attributes)
    $defaults = bfg_get_shortcode_defaults();
    $temp_atts = shortcode_atts($defaults, $user_atts, 'AWL-BlogFilter');
    if ($temp_atts['disable_bootstrap_js'] !== 'yes') {
        wp_enqueue_script('awl-bf-bootstrap-js');
    }
    if ($temp_atts['disable_bootstrap_css'] !== 'yes') {
        wp_enqueue_style('awl-bf-bootstrap-css');
    }

    // 2. --- Process Shortcode Attributes ---
    $defaults = bfg_get_shortcode_defaults(); // Use the central defaults function


    $atts = shortcode_atts($defaults, $user_atts, 'AWL-BlogFilter');

    // --- START: BACKWARD COMPATIBILITY LAYER ---
    // This ensures old shortcodes using `selected_categories` or `selected_tags` still work.
    // If the new 'selected_terms' is empty, check if an old attribute has a value.
    if (empty($atts['selected_terms'])) {
        if (!empty($user_atts['selected_categories'])) {
            // If the old 'selected_categories' exists, use its value for the new 'selected_terms'.
            $atts['selected_terms'] = $user_atts['selected_categories'];
        } elseif (!empty($user_atts['selected_tags'])) {
            // If the old 'selected_tags' exists, use its value for the new 'selected_terms'.
            $atts['selected_terms'] = $user_atts['selected_tags'];
        }
    }




    if (empty($atts['blog_filtering']) || $atts['blog_filtering'] == 'blog_category') {
        if (!empty($user_atts['blog_filtering'])) {
            $atts['blog_filtering'] = 'category';
        }
    }
    if (empty($atts['blog_filtering']) || $atts['blog_filtering'] == 'blog_tag') {
        if (!empty($user_atts['blog_filtering'])) {
            $atts['blog_filtering'] = 'post_tag';
        }
    }

    // --- END: BACKWARD COMPATIBILITY LAYER ---
    // Now, extract all attributes into local variables.
    extract($atts);

    $unique_id = wp_rand(1, 1000);

    //color dark code
    list($r, $g, $b) = sscanf($blog_desc_box_color, "#%02x%02x%02x");
    $r = $r - 24;
    $g = $g - 22;
    $b = $b - 19;

    // Start output buffering to capture all HTML.
    ob_start();

    // Include the dynamic CSS file.
    require('blog-filter-output-css.php');
?>
    <div id="BlogFilterMain-<?php echo esc_attr($unique_id); ?>" class="blog_filter_main" version="<?php echo esc_attr(BF_PLUGIN_VER); ?>"
        data-post-type="<?php echo esc_attr($post_type); ?>" data-initload="<?php echo esc_attr($blog_per_page_and_init_load); ?>">
        <?php
        // 3. --- Prepare and Run The Main Query ---
        $paged = (get_query_var('paged')) ? get_query_var('paged') : ((get_query_var('page')) ? get_query_var('page') : 1);
        $posts_per_page = ($blog_pagination == 'no' && $blog_load_more == 'no') ? -1 : (int) $blog_per_page_and_init_load;

        // If pagination is OFF and someone is on page >1, force a 404:
        if ($blog_pagination === 'no' && get_query_var('paged') > 1) {
            global $wp_query;
            // Tell WP this is a 404
            $wp_query->set_404();
            status_header(404);
            nocache_headers();
            // Load your theme’s 404 template and bail out
            include(get_query_template('404'));
            exit;
        }

        $custom_query_args = array(
            'post_type'      => $post_type,
            'post_status'    => 'publish',
            'posts_per_page' => $posts_per_page,
            'paged'          => $paged,
            // Only compute pagination counts when needed
            'no_found_rows'  => ($blog_pagination === 'no'),
        );

        // --- START: NEW, ROBUST TAXONOMY LOGIC ---
        // Explode, trim and cast to int in one go
        $selected_terms_array = ! empty($selected_terms)
            ? array_map('intval', array_map('trim', explode(',', $selected_terms)))
            : array();



        $tax_query = [];

        // Build the final tax_query
        $tax_query = array('relation' => 'AND');


        // Only add an IN clause if there’s anything left to include
        // 2) only push our taxonomy if the user actually selected terms
        if (! empty($selected_terms_array)) {
            $tax_query[] = [
                'taxonomy'         => $blog_filtering,
                'field'            => 'term_id',
                'terms'            => $selected_terms_array,
                'operator'         => 'IN',
                'include_children' => false,
            ];
        }

        // … you could push other tax_queries here if needed …

        // Attach tax_query only when you have at least one condition.
        if (! empty($tax_query) && count($tax_query) > 1) {
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
            $custom_query_args['tax_query'] = $tax_query;
        }

        $custom_query = new WP_Query($custom_query_args);

        // 4. --- Generate HTML Output ---
        $taxonomies = get_object_taxonomies($post_type);
        if (!empty($taxonomies) && $post_type != 'page') {
            include(BF_PLUGIN_DIR . "filtering/filters.php");
        }
        ?>
        <div class="filtr-container filters-div bf_gallery_1-<?php echo esc_attr($unique_id); ?>" style="width:100%">
            <?php
            if ($custom_query->have_posts()) {
                include('templates/blog-filter-content.php');
            } else {
                echo '<p class="bfg-no-posts-found">' . esc_html__('No posts found.', 'blog-filter') . '</p>';
            }
            ?>
            <div class="blog_loader blog_loader-<?php echo esc_attr($unique_id); ?>"></div>
        </div>

        <?php // Load More, Scroll, and Pagination Controls
        if ($blog_load_more == "yes") { ?>
            <div class="row text-center" style="padding:35px;"><button id="load-more-<?php echo esc_attr($unique_id); ?>"
                    class="btn snip0047 snip0047-<?php echo esc_attr($unique_id); ?>"><span
                        style="pointer-events: none;"><?php echo esc_html($load_more_text); ?></span><i class="bf-spinner-icon" style="pointer-events: none;"><svg class="bf-spinner" width="16" height="16" viewBox="0 0 50 50"><circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle></svg></i></button></div>
        <?php } ?>
        <div class="load-scroll-block" data-scrollflage="1">
            <div class="lds-ellipsis">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
        <div class="no-more-posts"><?php echo esc_html($no_more_text); ?></div>
        <?php if ($blog_pagination == "yes") { ?>
            <div class="blog_pagination-<?php echo esc_attr($unique_id); ?>">
                <?php
                echo wp_kses_post(paginate_links(array(
                    'total' => $custom_query->max_num_pages,
                    'current' => $paged,
                    'prev_text' => '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>',
                    'next_text' => '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>',
                )));
                ?>
            </div>
        <?php }

        // --- FIX: Include the correct file for JavaScript output ---
        // An AJAX handler file should never be included directly.
        include(BF_PLUGIN_DIR . "filtering/filters-ajax.php");
        ?>
    </div>
<?php
    // 5. --- Cleanup ---
    // Restore original Post Data and clean up the output buffer.
    wp_reset_postdata();
    return ob_get_clean();
}
