<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

global $wp_query;

// --- START: NEW DYNAMIC LOGIC ---

// 1. Set the taxonomy name directly from the shortcode attribute ($blog_filtering).
$taxonomy_name = (!empty($blog_filtering) && $blog_filtering !== 'blog_category') ? $blog_filtering : 'category';

// 2. Prepare arguments to get only the terms selected in the shortcode.
// This now uses the generic 'selected_terms' attribute.

$selected_terms_raw = !empty($selected_terms) ? array_map('intval', array_map('trim', explode(',', $selected_terms))) : array();
$selected_terms_array = !empty($selected_terms_raw) ? array_slice($selected_terms_raw, 0, 4) : array();

$term_args = array(
    'taxonomy'   => $taxonomy_name,
    'hide_empty' => true,
    'number'     => 4,
);
// If specific terms are selected in the shortcode, only include those.
if (!empty($selected_terms_array)) {
    $term_args['include'] = $selected_terms_array;
}

$terms = get_terms($term_args);

if ($terms && !is_wp_error($terms)) :

    // 3. Dynamically count total posts for the "All" filter using a tax_query.
    $all_post_count = '';
    if ($filter_post_count == "yes") {
        $count_query_args = array(
            'post_type'      => $post_type,
            'posts_per_page' => 1, // We only need found_posts
            'fields'         => 'ids',
            'no_found_rows'  => false, // Must be false to get found_posts
        );
        // If specific terms are selected, count posts within them.
        if (!empty($selected_terms_array)) {
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
            $count_query_args['tax_query'] = array(
                array(
                    'taxonomy'         => $taxonomy_name,
                    'field'            => 'term_id',
                    'terms'            => $selected_terms_array,
                    'include_children' => false,
                ),
            );
        }
        $query_for_count = new WP_Query($count_query_args);
        $total_post_incat = $query_for_count->found_posts;
        $all_post_count = ' (' . $total_post_incat . ')';
        wp_reset_postdata(); // Reset post data after custom query.
    }
    // --- END: NEW DYNAMIC LOGIC ---

    // The rest of this file is your original HTML structure, which now works with the dynamic $terms variable.
    if ($blog_filters == "yes") { ?>

        <div class="text-center">
            <ul class="simplefilter filtr-control-<?php echo esc_attr($unique_id); ?>">
                <?php
                if ($blog_filter_all == "yes") { ?>
                    <li id="all" class="snip0047 snip0047-<?php echo esc_attr($unique_id); ?> active filtr-controls-<?php echo esc_attr($unique_id); ?>" data-filter="all"><span style="pointer-events: none;"><?php echo esc_html($blog_all_text);
                                                                                                                                                                                                                echo esc_html($all_post_count); ?></span><i class="bf-icon-check" style="pointer-events: none;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></i></li>
                <?php
                }
                foreach ($terms as $term) {
                    $single_filter_post_count = ($filter_post_count == "yes") ? ' (' . $term->count . ')' : '';  ?>
                    <li id="<?php echo esc_attr($term->term_id); ?>" class="filtr-controls-<?php echo esc_attr($unique_id); ?> snip0047 snip0047-<?php echo esc_attr($unique_id); ?>" value="<?php echo esc_attr($term->term_id); ?>" data-filter="<?php echo esc_attr($term->term_id); ?>"><span style="pointer-events: none;"><?php echo esc_html($term->name);
                                                                                                                                                                                                                                                                                                                                echo esc_html($single_filter_post_count); ?></span><i class="bf-icon-check" style="pointer-events: none;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></i></li>
                <?php
                } ?>
            </ul>
        </div>
        <?php if ($blog_search == "yes") { ?>
            <div class="blog_search_div text-center">
                <input type="text" class="filtr-controls-<?php echo esc_attr($unique_id); ?> searchTerm" name="blog_search" placeholder="<?php echo esc_attr($blog_search_text); ?>" data-search>
            </div>
<?php }
    }
endif;
?>