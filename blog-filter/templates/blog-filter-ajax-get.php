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

// For convenience, extract attributes into local variables.
extract($atts);

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
