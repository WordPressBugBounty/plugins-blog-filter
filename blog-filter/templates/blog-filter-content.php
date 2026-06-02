<?php
if (!defined('ABSPATH'))
	exit; // Exit if accessed directly 

if ($custom_query->have_posts()):
	$abc = 0;
	// In Free version, we always use the unified 'Posts per page' setting.
	$blog_load = (int) $blog_per_page_and_init_load;

	while ($abc < $blog_load && $custom_query->have_posts()):
		$custom_query->the_post();
		//while ( $custom_query->have_posts()) : $custom_query->the_post();
		$post_id = get_the_ID();

		//Categories Fetch
		global $post;

		// Initialize variables to store the term data.
		$keys = '';
		$lightbox_keys = '';
		$filter_value_name = ''; // Used for the data-sort attribute

		// Get the terms for the current post using the dynamic taxonomy name from the shortcode.
		// The $blog_filtering variable should hold 'category', 'post_tag', or your custom taxonomy name.
		$terms = get_the_terms($post->ID, $blog_filtering);

		// Check if any terms were found and it's not an error.
		if ($terms && !is_wp_error($terms)) {
			$prefix = '';
			$prefix2 = '';
			foreach ($terms as $filter_value) {
				// Build the comma-separated list of term IDs for data-category.
				$keys .= $prefix . $filter_value->term_id;
				$prefix = ', ';

				// Build the space-separated list of term IDs for the lightbox class.
				$lightbox_keys .= $prefix2 . $filter_value->term_id;
				$prefix2 = ' bfg-lightbox-';
			}
			// Set the sort value to the name of the first term.
			$filter_value_name = $terms[0]->name;
		}
		// --- END: NEW DYNAMIC CODE ---

		// Excerpt/Description Fetch Fallback for CPTs and Page Builders (e.g. Divi)
		$bf_excerpt = get_the_excerpt();
		if (empty($bf_excerpt)) {
			$bf_excerpt = get_the_content();
			
			// Check if Divi Builder is active on this post
			$is_divi = false;
			if (function_exists('et_pb_is_pagebuilder_used') && et_pb_is_pagebuilder_used(get_the_ID())) {
				$is_divi = true;
			} elseif (get_post_meta(get_the_ID(), '_et_pb_use_builder', true) === 'on') {
				$is_divi = true;
			}

			if ($is_divi) {
				// Let Divi evaluate its shortcodes and modules first to generate the actual content
				if (function_exists('et_builder_render_layout')) {
					$bf_excerpt = et_builder_render_layout($bf_excerpt);
				} else {
					$bf_excerpt = do_shortcode($bf_excerpt);
				}
				// If dynamic content tokens remain, pass through 'the_content' filter to resolve them
				if (strpos($bf_excerpt, '@ET-DC@') !== false) {
					$bf_excerpt = apply_filters('the_content', $bf_excerpt);
				}
			} else {
				// Standard shortcode stripping via regex for non-Divi builders
				$bf_excerpt = preg_replace('/\[.*?\]/', '', $bf_excerpt);
			}
		}
		// Clean up any leftover Divi dynamic content tokens to prevent raw Base64 leak
		$bf_excerpt = preg_replace('/@ET-DC@.*?@/', '', $bf_excerpt);
		$bf_excerpt = wp_strip_all_tags(strip_shortcodes($bf_excerpt));
?>
		<div style="opacity:0;" id="bf_<?php echo esc_attr(get_the_ID()); ?>" data-category="<?php echo esc_attr($keys); ?>"
			data-sort="<?php echo esc_attr($filter_value_name); ?>"
			class="<?php echo esc_attr(str_replace(",", "", $keys)); ?> pfg_theme_1 filtr-item filtr_item_1 single_one <?php echo esc_attr($blog_col_large_desktops); ?> <?php echo esc_attr($blog_col_desktops); ?> <?php echo esc_attr($blog_col_tablets); ?> <?php echo esc_attr($blog_col_phones); ?>">
			<?php
			// ------------ ********** -----------------//
			// ------------ TEMPLATE 1 -----------------//
			// ------------ ********** -----------------//
			if ($blog_template == 'template1') { ?>
				<div
					class="post-box bf_thumb_box_1-<?php echo esc_attr($unique_id); ?> ">
					<div class="bf_title_box_1-<?php echo esc_attr($unique_id); ?> bf_title_box_1 fit-text-main" maxlength="20">

						<?php
						if ($blog_title_below_image == "no") {
							if ($blog_title == "yes") { ?>
								<h2 class="bf_title_1-<?php echo esc_attr($unique_id); ?> blog_title_1 fit-text"><?php the_title(); ?></h2>
							<?php
							}
						}
						if ($blog_date_below_image == "no") {
							if ($blog_date == "yes") {
								$day = get_the_date('d');
								$month = get_the_date('m');
								$year = get_the_date('Y'); ?>
								<div class="blog_metaInfo">
									<?php
									if ($link_on_date == "yes") { ?>
										<span class="blog_date"><i class="bf-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg></i> <a class="blog_date_a"
												href="<?php echo esc_url(get_day_link($year, $month, $day)); ?>"><?php the_time('j F, Y'); ?></a>
										</span>
									<?php
									} else { ?>
										<span class="blog_date"><i class="bf-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg></i> <?php the_time('j F, Y'); ?> </span>
									<?php
									} ?>
								</div>
							<?php
							}
						}
						if ($blog_author_below_image == "no") {
							if ($blog_author == "yes") { ?>
								<div class="blog_metaInfo">
									<span class="blog_author"><i class="bf-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></i> <?php esc_html_e('By', 'blog-filter') ?> <?php the_author(); ?> </span>
								</div>
						<?php
							}
						} ?>
					</div>

					<?php // Image Content 
					include(BF_PLUGIN_DIR . "templates/blog-img-content.php"); ?>

					<div class="bf_title_box_2-<?php echo esc_attr($unique_id); ?> bf_title_box_2 fit-text-main">
						<?php
						if ($blog_title_below_image == "yes") {
							if ($blog_title == "yes") { ?>
								<h2 class="bf_title_1-<?php echo esc_attr($unique_id); ?> blog_title_1 fit-text"><?php the_title(); ?></h2>
							<?php
							}
						}
						if ($blog_date_below_image == "yes") {
							if ($blog_date == "yes") {
								$day = get_the_date('d');
								$month = get_the_date('m');
								$year = get_the_date('Y'); ?>
								<div class="blog_metaInfo">
									<?php
									if ($link_on_date == "yes") { ?>
										<span class="blog_date"><i class="bf-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg></i> <a class="blog_date_a"
												href="<?php echo esc_url(get_day_link($year, $month, $day)); ?>"><?php the_time('j F, Y'); ?></a>
										</span>
									<?php
									} else { ?>
										<span class="blog_date"><i class="bf-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg></i> <?php the_time('j F, Y'); ?> </span>
									<?php
									} ?>
								</div>
							<?php
							}
						}
						if ($blog_author_below_image == "yes") {
							if ($blog_author == "yes") { ?>
								<div class="blog_metaInfo">
									<span class="blog_author"><i class="bf-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></i> <?php esc_html_e('By', 'blog-filter') ?> <?php the_author(); ?> </span>
								</div>
							<?php
							}
						}
						if ($blog_categories == "yes") { ?>
							<div class="blog_metaInfo">
								<span class="blog_cat"><i class=""><img class="blog_cat_icon" src="<?php echo esc_url(BF_PLUGIN_URL) ?>img/cat.png"></i>
									<?php
									$categories = get_the_category();
									$separator = ", ";
									$output = '';
									if ($categories) {
										foreach ($categories as $category) {
											$output .= esc_html($category->cat_name) . $separator;
										}
										echo esc_html(trim($output, $separator));
									} ?>
								</span>
							</div><!-- end meta -->
						<?php
						}
						if ($blog_desc == "yes") { ?>
							<div class="bf_desc_1-<?php echo esc_attr($unique_id); ?> blog_desc fit-text">
								<?php
								if ($three_dots == "yes") {
									echo esc_html(substr($bf_excerpt, 0, (int)$blog_desc_characters)) . '...';
								} else {
									echo esc_html(substr($bf_excerpt, 0, (int)$blog_desc_characters));
								} ?>
							</div>
						<?php
						}
						if ($blog_tags == "yes") { ?>
							<div class="blog_metaInfo">
								<?php
								if (get_the_tags()) { ?>
									<span class="blog_tag"><i class=""><img class="blog_tag_icon"
												src="<?php echo esc_url(BF_PLUGIN_URL) ?>img/tag.png"></i> <?php $post_tags = get_the_tags();
																											$separator = ', ';
																											$output = '';
																											if (!empty($post_tags)) {
																												foreach ($post_tags as $tag) {
																													$output .= esc_html($tag->name) . $separator;
																												}
																											}
																											echo esc_html(trim($output, $separator)); ?>
									</span>
								<?php
								} ?>
							</div>
						<?php
						}
						if ($blog_read_more == "yes") { ?>
							<div class="bf_read_more_div_1">
								<a id="blog_read_more" class="snip0047 snip0047-<?php echo esc_attr($unique_id); ?> bf_read_more_1"
									href="<?php echo esc_url(get_permalink()); ?>"><span><?php echo esc_html($blog_read_more_text); ?></span><i
										class="bf-icon-readmore"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg></i></a>
							</div>
						<?php
						} ?>
					</div>
				</div>
			<?php
			}
			// ------------ / TEMPLATE 1 End -----------------//
			?>
			<input type="hidden" value="<?php echo esc_attr(get_the_ID()); ?>" class="displayed_posts">
		</div>
<?php
		$abc++;
	endwhile;
	// Reset Post Data
	wp_reset_postdata();
endif; ?>