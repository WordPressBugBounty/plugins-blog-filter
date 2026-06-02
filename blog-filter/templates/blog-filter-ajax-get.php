<?php
if (! defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Blog Filter - AJAX Load More Handler
 *
 * This file is responsible for handling the "Load More" and "Infinite Scroll" AJAX requests.
 * It builds a custom WP_Query based on the current filters and returns the HTML for the new posts.
 *
 * @package Blog-Filter-Premium
 */

// Check for the security Nonce sent from the JavaScript.
check_ajax_referer('load_more_nonce', 'nonce');

//--------------------------------------------------------------------------
// 1. Retrieve and Sanitize Data from the AJAX Request
//--------------------------------------------------------------------------


// Get the original shortcode attributes that were passed from the JavaScript.
// We use map_deep and wp_unslash for multi-dimensional array sanitization.
$user_atts = isset($_POST['bfg_query_vars']) ? map_deep(wp_unslash($_POST['bfg_query_vars']), 'sanitize_text_field') : array();

$defaults = bfg_get_shortcode_defaults();

// CORRECTED: Use shortcode_atts() to merge user's attributes with defaults.
$atts = shortcode_atts($defaults, $user_atts, 'AWL-BlogFilter');

// --- START: BACKWARD COMPATIBILITY LAYER ---
if (empty($atts['selected_terms'])) {
    if (!empty($user_atts['selected_categories'])) {
        $atts['selected_terms'] = $user_atts['selected_categories'];
    } elseif (!empty($user_atts['selected_tags'])) {
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

// Explicitly define variables instead of using extract().
$post_type = $atts['post_type'];
$blog_direction = $atts['blog_direction'];
$blog_fixed_grid = $atts['blog_fixed_grid'];
$blog_template = $atts['blog_template'];
$blog_col_large_desktops = $atts['blog_col_large_desktops'];
$blog_col_desktops = $atts['blog_col_desktops'];
$blog_col_tablets = $atts['blog_col_tablets'];
$blog_col_phones = $atts['blog_col_phones'];
$blog_image = $atts['blog_image'];
$blog_image_hover_effect = $atts['blog_image_hover_effect'];
$blog_image_quality = $atts['blog_image_quality'];
$blog_title = $atts['blog_title'];
$blog_title_font_size = $atts['blog_title_font_size'];
$blog_title_color = $atts['blog_title_color'];
$blog_title_below_image = $atts['blog_title_below_image'];
$blog_desc = $atts['blog_desc'];
$blog_desc_characters = $atts['blog_desc_characters'];
$blog_desc_font_size = $atts['blog_desc_font_size'];
$blog_desc_color = $atts['blog_desc_color'];
$blog_desc_box_color = $atts['blog_desc_box_color'];
$three_dots = $atts['three_dots'];
$link_on_date = $atts['link_on_date'];
$blog_read_more = $atts['blog_read_more'];
$blog_read_more_text = $atts['blog_read_more_text'];
$blog_date = $atts['blog_date'];
$blog_date_below_image = $atts['blog_date_below_image'];
$blog_author = $atts['blog_author'];
$blog_author_below_image = $atts['blog_author_below_image'];
$blog_categories = $atts['blog_categories'];
$blog_tags = $atts['blog_tags'];
$blog_pagination = $atts['blog_pagination'];
$blog_load_more = $atts['blog_load_more'];
$blog_pagination_loadmore_color = $atts['blog_pagination_loadmore_color'];
$blog_per_page_and_init_load = $atts['blog_per_page_and_init_load'];
$load_more_text = $atts['load_more_text'];
$no_more_text = $atts['no_more_text'];
$blog_filters = $atts['blog_filters'];
$filter_post_count = $atts['filter_post_count'];
$blog_filter_all = $atts['blog_filter_all'];
$blog_all_text = $atts['blog_all_text'];
$blog_first_filter_selected = $atts['blog_first_filter_selected'];
$blog_search = $atts['blog_search'];
$blog_search_text = $atts['blog_search_text'];
$blog_buttons_color = $atts['blog_buttons_color'];
$blog_filtering = $atts['blog_filtering'];
$selected_terms = $atts['selected_terms'];
$disable_bootstrap_css = $atts['disable_bootstrap_css'];
$disable_bootstrap_js = $atts['disable_bootstrap_js'];
$custom_css = $atts['custom_css'];
$blog_order_by = isset($atts['blog_order_by']) ? $atts['blog_order_by'] : 'date';
$blog_order = isset($atts['blog_order']) ? $atts['blog_order'] : 'DESC';

// Get data sent directly from the AJAX call.
$displayed_posts = isset($_POST['displayed_posts']) ? array_map('intval', wp_unslash((array) $_POST['displayed_posts'])) : array();

// Sanitize targetFilter - must be 'all' or integer term ID(s)
$targetFilter = 'all';
if (isset($_POST['targetFilter'])) {
    // Sanitize immediately to satisfy security checks
    $raw_filter = map_deep(wp_unslash($_POST['targetFilter']), 'sanitize_text_field');
    if (is_array($raw_filter)) {
        $targetFilter = array_map('intval', $raw_filter);
    } elseif ($raw_filter === 'all') {
        $targetFilter = 'all';
    } else {
        $targetFilter = intval($raw_filter);
    }
}

$unique_id = isset($_POST['unique_id']) ? intval(wp_unslash($_POST['unique_id'])) : wp_rand(1, 1000);

//--------------------------------------------------------------------------
// 2. Build the Custom Query Arguments
//--------------------------------------------------------------------------

// Base arguments for the query.
$custom_query_args = array(
    'post_type'      => $post_type,
    'post_status'    => 'publish',
    'posts_per_page' => (int) $blog_per_page_and_init_load,
    'orderby'        => $blog_order_by,
    'order'          => $blog_order,
);

// Only use post__not_in if we have posts to exclude, to save performance.
if (! empty($displayed_posts)) {
    // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn
    $custom_query_args['post__not_in'] = $displayed_posts;
}

// This is the array that will hold our taxonomy conditions.
$tax_query = array();

// Dynamically add the taxonomy query based on the active filter.
// This works for 'category', 'post_tag', or any custom taxonomy.
if ($targetFilter !== 'all' && !empty($blog_filtering)) {
    // If a specific filter is active (e.g., user clicked "Category A"), use it.
    $tax_query[] = array(
        'taxonomy'  => $blog_filtering,
        'field'     => 'term_id',
        'terms'     => is_array($targetFilter) ? $targetFilter : array($targetFilter), // Ensure terms are an array.
    );
} else if ($targetFilter === 'all' && !empty($blog_filtering) && !empty($selected_terms)) {
    // If filter is "All", fall back to the original terms selected in the shortcode.
    $tax_query[] = array(
        'taxonomy' => $blog_filtering,
        'field'    => 'term_id',
        'terms'    => explode(',', $selected_terms),
    );
}

// If we have built a tax_query, add it to the main query arguments.
if (!empty($tax_query)) {
    // Set the relation if you ever have more than one taxonomy condition.
    $tax_query['relation'] = 'AND';
    // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
    $custom_query_args['tax_query'] = $tax_query;
}


//--------------------------------------------------------------------------
// 3. Run The Query and Output The Content
//--------------------------------------------------------------------------

$custom_query = new WP_Query($custom_query_args);

if ($custom_query->have_posts()) :
    // Pass all the necessary variables from the original shortcode attributes to the template.

    // Corrected path to ensure the content template is found during AJAX requests.
    require(dirname(__FILE__) . '/blog-filter-content.php');

endif;

// Always exit correctly in WordPress AJAX handlers.
wp_die();
