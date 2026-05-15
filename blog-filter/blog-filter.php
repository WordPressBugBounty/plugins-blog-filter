<?php
if (!defined('ABSPATH'))
	exit; // Exit if accessed directly
/**
Plugin Name: Blog Filter
Description: Blog Filter For WordPress Blog With Multiple Filters
Version: 1.8.0
Author: A WP Life
Author URI: http://awplife.com/
Text Domain: blog-filter
Domain Path: /languages
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 **/

if (!class_exists('Awl_Blog_Filter')) {

	class Awl_Blog_Filter
	{

		public function __construct()
		{
			$this->_constants();
			$this->_hooks();
		}

		protected function _constants()
		{
			//Plugin Version
			define('BF_PLUGIN_VER', '1.8.0');

			//Plugin Text Domain
			define('BF_TEXT_DOMAIN', 'blog-filter');

			//Plugin Name
			define('BF_PLUGIN_NAME', 'Blog Filter');

			//Plugin Slug
			define('BF_PLUGIN_SLUG', 'awl_blog_filter');

			//Plugin Directory Path
			define('BF_PLUGIN_DIR', plugin_dir_path(__FILE__));

			//Plugin Directory URL
			define('BF_PLUGIN_URL', plugin_dir_url(__FILE__));
		} // end of constructor function

		protected function _hooks()
		{

			//Load text domain
			add_action('plugins_loaded', array($this, 'load_textdomain'));

			//add menu item, change menu for multisite
			add_action('admin_menu', array($this, 'blog_filter_menu'), 101);

			// Added settings link on plugins page
			$bf_plugin_name = plugin_basename(__FILE__);
			add_filter("plugin_action_links_$bf_plugin_name", 'bf_plugins_page_settings_link');

			function bf_plugins_page_settings_link($links)
			{
				$bf_settings_link = '<a href="edit.php?page=blog-filter-settings-page">' . esc_html__('Settings', 'blog-filter') . '</a>';
				array_unshift($links, $bf_settings_link);
				return $links;
			}
			// Backward compatibility wrapper
			if (! function_exists('plugins_page_settings_link')) {
				function plugins_page_settings_link($links)
				{
					return bf_plugins_page_settings_link($links);
				}
			}

			function bfg_get_shortcode_defaults()
			{
				return array(
					// General & Post Type Settings
					'post_type' => 'post',
					'blog_direction' => 'ltr',
					'blog_fixed_grid' => 'no',
					'blog_template' => 'template1',

					// Columns
					'blog_col_large_desktops' => 'col-lg-4',
					'blog_col_desktops' => 'col-md-4',
					'blog_col_tablets' => 'col-sm-6',
					'blog_col_phones' => 'col-xs-12',

					// Image Settings
					'blog_image' => 'no',
					'blog_image_hover_effect' => 'none',
					'blog_image_quality' => 'large',

					// Title Settings
					'blog_title' => 'no',
					'blog_title_font_size' => 25,
					'blog_title_color' => '#000',
					'blog_title_below_image' => 'no',

					// Description Settings
					'blog_desc' => 'no',
					'blog_desc_characters' => '100',
					'blog_desc_font_size' => 12,
					'blog_desc_color' => '#606060',
					'blog_desc_box_color' => '#EDEEF0',
					'three_dots' => 'no',

					// Links and Display
					'link_on_date' => 'no',

					// Read More
					'blog_read_more' => 'no',
					'blog_read_more_text' => 'Read More',

					// Metadata Display
					'blog_date' => 'no',
					'blog_date_below_image' => 'no',
					'blog_author' => 'no',
					'blog_author_below_image' => 'no',
					'blog_categories' => 'no',
					'blog_tags' => 'no',

					// Pagination & Load
					'blog_pagination' => 'no',
					'blog_load_more' => 'no',
					'blog_pagination_loadmore_color' => '#58BBEE',
					'blog_per_page_and_init_load' => '12',
					'load_more_text' => 'Load More',
					'no_more_text' => 'No More Posts',

					// Filters
					'blog_filters' => 'no',
					'filter_post_count' => 'no',
					'blog_filter_all' => 'no',
					'blog_all_text' => 'All',
					'blog_first_filter_selected' => 'no',

					// Search
					'blog_search' => 'no',
					'blog_search_text' => 'Search',

					// Styling & Colors
					'blog_buttons_color' => '#58BBEE',

					// Taxonomy Filtering
					'blog_filtering' => 'blog_category',

					'selected_terms' => '',

					// Bootstrap
					'disable_bootstrap_css' => 'no',
					'disable_bootstrap_js' => 'no',

					// Custom CSS
					'custom_css' => '',
				);
				//return $defaults;
			}

			add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts_in_header'));

			add_action('wp_ajax_load_more', array(&$this, 'load_more_posts'));

			add_action('wp_ajax_nopriv_load_more', array(&$this, 'load_more_posts'));

			add_filter('wp_lazy_loading_enabled', '__return_false');

			add_action('wp_ajax_get_taxonomies_for_post_type', array(&$this, 'bfg_get_taxonomies_callback'));

			add_action('wp_ajax_get_terms_for_taxonomy', array(&$this, 'bfg_get_terms_for_taxonomy_callback'));
		}// end of hook function

		/**
		 * Returns an array of all default shortcode attributes.
		 * Centralized for easy maintenance.
		 */


		public function bfg_get_taxonomies_callback()
		{
			// First, check for security.
			if (!check_ajax_referer('bfg_admin_nonce', 'security', false)) {
				wp_send_json_error('Invalid security token.', 403);
				return;
			}

			if (!isset($_POST['post_type']) || empty($_POST['post_type'])) {
				wp_send_json_error('No post type specified.', 400);
				return;
			}

			$post_type = sanitize_text_field(wp_unslash($_POST['post_type']));

			// Get all public taxonomies associated with the post type.
			$taxonomies = get_object_taxonomies($post_type, 'objects');

			$options_html = '<option value="none">' . __('None', 'blog-filter') . '</option>';

			if (!empty($taxonomies)) {
				foreach ($taxonomies as $taxonomy) {
					// We only want public taxonomies that can be shown in a UI.
					if ($taxonomy->public && $taxonomy->show_ui) {
						$options_html .= '<option value="' . esc_attr($taxonomy->name) . '">' . esc_html($taxonomy->label) . ' (' . esc_html($taxonomy->name) . ')</option>';
					}
				}
			}

			// Check if we actually found any usable taxonomies
			if ($options_html === '<option value="none">' . __('None', 'blog-filter') . '</option>') {
				wp_send_json_success('<option value="none">' . __('No taxonomies found for this post type', 'blog-filter') . '</option>');
			} else {
				wp_send_json_success($options_html);
			}
		}

		public function bfg_get_terms_for_taxonomy_callback()
		{
			// Security check
			check_ajax_referer('bfg_admin_nonce', 'security');
			if (!current_user_can('manage_options')) {
				wp_send_json_error('Permission denied.', 403);
				return;
			}

			// --- Prepare data ---
			if (!isset($_POST['taxonomy']) || empty($_POST['taxonomy'])) {
				wp_send_json_error('No taxonomy specified.', 400);
				return;
			}
			$taxonomy_name = sanitize_text_field(wp_unslash($_POST['taxonomy']));
			$taxonomy_obj = get_taxonomy($taxonomy_name);
			if (!$taxonomy_obj) {
				wp_send_json_error('Invalid taxonomy.', 400);
				return;
			}
			$terms = get_terms(['taxonomy' => $taxonomy_name, 'hide_empty' => false]);
	$dropdown_html = '<option value="all">' . __('All', 'blog-filter') . '</option>';
			$table_html = '';

			// --- Build HTML for all three elements if terms exist ---
			if (!is_wp_error($terms) && !empty($terms)) {
				// Build Dropdown HTML
				foreach ($terms as $term) {
					$dropdown_html .= '<option value="' . esc_attr($term->term_id) . '">' . esc_html($term->name) . '</option>';
				}

				// Build Inclusion Table HTML
				ob_start();
?>
				<div class="bfg-overflow-auto" style="max-height: 415px;">
					<table class="bfg-w-full bfg-border-collapse bfg-border bfg-border-gray-300">
						<thead>
							<tr class="bfg-bg-gray-200">
								<th class="bfg-p-2 bfg-border bfg-border-gray-300"><?php esc_html_e('ID', 'blog-filter'); ?></th>
								<th class="bfg-p-2 bfg-border bfg-border-gray-300">
									<?php echo esc_html($taxonomy_obj->labels->singular_name); ?>
								</th>
								<th class="bfg-p-2 bfg-border bfg-border-gray-300"><?php esc_html_e('Post Count', 'blog-filter'); ?></th>
								<th class="bfg-p-2 bfg-border bfg-border-gray-300 bfg-text-center"></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($terms as $term): ?>
								<tr>
									<td class="bfg-p-2 bfg-border bfg-border-gray-300"><?php echo esc_html($term->term_id); ?></td>
									<td class="bfg-p-2 bfg-border bfg-border-gray-300"><?php echo esc_html($term->name); ?></td>
									<td class="bfg-p-2 bfg-border bfg-border-gray-300"><?php echo esc_html($term->count); ?></td>
									<td class="bfg-p-2 bfg-border bfg-border-gray-300 bfg-text-center">
										<input type="checkbox" class="bfg-term-checkbox" name="selected_terms[]"
											value="<?php echo esc_attr($term->term_id); ?>">
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					<p><b><?php esc_html_e('Note: Blog Filter Standard supports up to 4 taxonomy as filters', 'blog-filter'); ?></b></p>
				</div>
				<?php
				$table_html = ob_get_clean();
			} else {
				$table_html = '<p>' . __('No terms found for this taxonomy.', 'blog-filter') . '</p>';
			}

			// Send all three HTML strings back in a single JSON object
			wp_send_json_success([
				'dropdown' => $dropdown_html,
				'table' => $table_html,
			]);
		}

		public function load_more_posts()
		{

			if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'load_more_nonce')) {
				exit;
			}

			require(BF_PLUGIN_DIR . 'templates/blog-filter-ajax-get.php');
			die;
		}

		public function enqueue_scripts_in_header()
		{
			wp_enqueue_script('jquery');
		}

		public function load_textdomain()
		{
			// load_plugin_textdomain is no longer needed for plugins hosted on WordPress.org (since WP 4.6).
			// Translations are handled automatically. Keeping the method for backward compatibility.
		}

		public function blog_filter_menu()
		{
			$filter_settings_menu = add_submenu_page('edit.php', __('Blog Filters Settings', 'blog-filter'), __('Blog Filters Settings', 'blog-filter'), 'administrator', 'blog-filter-settings-page', array($this, 'awl_blog_filter_page'));
		}

		public function awl_blog_filter_page()
		{
			require_once('blog-filter-settings.php');
		}
	}

	// register bf scripts
	function bf_register_scripts()
	{

		//js
		wp_register_script('awl-bf-bootstrap-js', plugin_dir_url(__FILE__) . 'js/bootstrap.min.js', array('jquery'), BF_PLUGIN_VER, true);
		wp_register_script('awl-bf-filterizr-js', plugin_dir_url(__FILE__) . 'js/jquery.filterizr.js', array('jquery'), BF_PLUGIN_VER, false);

		// css
		wp_register_style('awl-bf-bootstrap-css', plugin_dir_url(__FILE__) . 'css/bootstrap.css', array(), BF_PLUGIN_VER);

		wp_register_style('awl-bf-filter-output-css', plugin_dir_url(__FILE__) . 'css/blog-filter-output.css', array(), BF_PLUGIN_VER);
		wp_register_style('awl-bf-hover-css', plugin_dir_url(__FILE__) . 'css/hover.css', array(), BF_PLUGIN_VER);

	}
	add_action('wp_enqueue_scripts', 'bf_register_scripts');

	// Backward compatibility wrapper for old function name
	if (! function_exists('awplife_bf_register_scripts')) {
		function awplife_bf_register_scripts()
		{
			bf_register_scripts();
		}
	}

	$bf_post_filter_object = new Awl_Blog_Filter();
	// Backward compatibility
	$pf_post_filter_object = $bf_post_filter_object;
	//Shortcode page
	require_once('blog-filter-shortcode.php');
} ?>