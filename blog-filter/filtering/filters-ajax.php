<?php if (! defined('ABSPATH')) exit; ?>
<script> <!---
jQuery(function() {
	//Simple filter controls
	jQuery('.filtr-controls-<?php echo esc_js($unique_id); ?>').click(function() {
	jQuery('.filtr-controls-<?php echo esc_js($unique_id); ?>').removeClass('active');
	jQuery(this).addClass('active');
	});
	//Multifilter controls
	jQuery('.multifilter li').click(function () {
		jQuery(this).toggleClass('active');
	});
	//Shuffle control
	jQuery('.shuffle-btn').click(function () {
		jQuery('.sort-btn').removeClass('active');
	});
	//Sort controls
	jQuery('.sort-btn').click(function () {
		jQuery('.sort-btn').removeClass('active');
		jQuery(this).addClass('active');
	});
});

	<?php if ($blog_fixed_grid == 'yes') { ?>
		document.addEventListener("DOMContentLoaded", function () {

			function AWL_setMaxHeight(className) {
				var elements = document.querySelectorAll(className);
				var maxHeight = 0;

				elements.forEach(function (element) {
					var height = element.offsetHeight;
					if (height > maxHeight) {
						maxHeight = height;
					}
				});

				elements.forEach(function (element) {
					element.style.height = maxHeight + 'px';
				});
			}

			<?php
			if ($blog_template == 'template1') { ?>

				AWL_setMaxHeight('.bf_title_box_1 ');
				AWL_setMaxHeight('.bf_title_box_2 ');

			<?php } ?>
		});
	<?php } ?>

	//Lazy load issue fix js
	setTimeout(function () {
		jQuery(".portfolio_thumbnail").each(function () {
			// console.log(jQuery(this).width() + "x" + jQuery(this).height())
			var h = jQuery(this).height();
			var w = jQuery(this).width();
			jQuery(this).height(h);
			jQuery(this).width(w);
			jQuery(this).resize();
		});
	}, 2500);

	jQuery(document).ready(function () {
		jQuery("a.page-numbers").each(function () {
			var $this = jQuery(this);
			var _href = $this.attr("href");
			$this.attr("href", _href + '');
		});

		// Animate loader off screen
		jQuery(".blog_loader").hide();
		jQuery(".bfg_theme_1").css("opacity", 1);
		//Filterizd Default options
		options<?php echo esc_js($unique_id); ?> = {
			callbacks: {
				onFilteringStart: function () { },
				onFilteringEnd: function () { },
				onShufflingStart: function () { },
				onShufflingEnd: function () { },
				onSortingStart: function () { },
				onSortingEnd: function () { }
			},
			controlsSelector: '.filtr-controls-<?php echo esc_js($unique_id); ?>',
			filter: 'all',
			filterOutCss: {
				top: '0px',
				left: '0px',
				opacity: 0.001,
				transform: ''
			},
			filterInCss: {
				top: '0px',
				left: '0px',
				opacity: 1,
				transform: ''
			},
			layout: 'sameWidth',
			
		selector: '.filtr-item',
			setupControls: false
		}
		var filterizd = jQuery('.bf_gallery_1-<?php echo esc_js($unique_id); ?>').filterizr(options<?php echo esc_js($unique_id); ?>);

		jQuery('.bf_gallery_1-<?php echo esc_js($unique_id); ?>').imagesLoaded(function () {
			// images have already loaded, instantiate Filterizr
			jQuery('.bf_gallery_1-<?php echo esc_js($unique_id); ?>').filterizr(options<?php echo esc_js($unique_id); ?>);
		});
	});

	//blog_pagination class add and active class add
	jQuery(document).ready(function () {
		// For init load scale
		jQuery('.filtr-item .post-box').addClass('lazyimg');

		jQuery("#filter-hide-button").on("click", function () {
			var Get_height = jQuery(".simplefilter").height();
			if (jQuery("#display-filter").hasClass("visible")) {
				jQuery("#display-filter").removeClass("visible");
				jQuery("#display-filter").height(0);
			} else {
				jQuery("#display-filter").addClass("visible");
				jQuery("#display-filter").height(Get_height + 20);
			}
		});

		jQuery("ul.page-numbers").addClass("blog_pagination mrgt-0");

		jQuery("#filter-all").click(function () {
			options<?php echo esc_js($unique_id); ?> = {
				animationDuration: 0.5,
				callbacks: {
					onFilteringStart: function () { },
					onFilteringEnd: function () { },
					onShufflingStart: function () { },
					onShufflingEnd: function () { },
					onSortingStart: function () { },
					onSortingEnd: function () { }
				},
				controlsSelector: '.filtr-controls-<?php echo esc_js($unique_id); ?>',
				filter: 'all',
				filterOutCss: {
					top: '0px',
					left: '0px',
					opacity: 0.001,
					transform: ''
				},
				filterInCss: {
					top: '0px',
					left: '0px',
					opacity: 1,
					transform: ''
				},
				layout: 'sameWidth',
				setupControls: false,
				selector: '.filtr-item',
			}
			var filterizd = jQuery('.bf_gallery_1-<?php echo esc_js($unique_id); ?>').filterizr(options<?php echo esc_js($unique_id); ?>);
			filterizd.filterizr('destroy');
			filterizd.filterizr(options<?php echo esc_js($unique_id); ?>).resize();
			jQuery('.filter-active').removeClass('filter-active');
			jQuery('#filter-all').addClass('filter-active');
		});

		// ==================================================

		<?php
		$termandcount = array();
		// Add the 'all' category count if available
		if (isset($total_post_incat)) {
			$termandcount['all'] = $total_post_incat;
		}
		// Populate counts for each individual term
		if (isset($terms) && is_array($terms)) {
			foreach ($terms as $term) {
				$termandcount[$term->term_id] = $term->count;
			}
		}
		?>
		var total_posts_in_filter = <?php echo wp_json_encode($termandcount); ?>;

		var initload = jQuery("#BlogFilterMain-<?php echo esc_js($unique_id); ?>").attr("data-initload");
		jQuery('.filtr-controls-<?php echo esc_js($unique_id); ?>').click(function () {
			var button = jQuery('#load-more-<?php echo esc_js($unique_id); ?>');
			var targetFilter = jQuery(this).data('filter');
			var filter_image_len = total_posts_in_filter[targetFilter];
			var loadedItems = jQuery('.filtr-item.' + targetFilter).length;
			//console.log(filter_image_len);
			//console.log(loadedItems);
			if (filter_image_len == loadedItems) {
				<?php if ($blog_load_more == "yes") { ?>
					button.find('span').text(<?php echo wp_json_encode($no_more_text); ?>);
					button.css('pointer-events', 'none');
					button.removeClass('active');
				<?php } ?>
			} else {
				<?php if ($blog_load_more == "yes") { ?>
					button.find('span').text(<?php echo wp_json_encode($load_more_text); ?>);
					button.css('pointer-events', 'auto');
					button.removeClass('active');
				<?php } ?>
				
			}

			
				let targetMultiFilter = jQuery('.filtr-controls-<?php echo esc_js($unique_id); ?>.active').map(function () {
					return parseInt(jQuery(this).data('filter'), 10);
				}).get();
			

			//if ( isAnyTargetFilterInDataCategory == false) {
			if (loadedItems < initload) {

				let taxonomyTargetFilter = {};

				// Iterate over each .dropdown element
				jQuery('.dropdown').each(function () {
					// Get the taxonomy data attribute value of the dropdown
					let taxonomy = jQuery(this).data('taxonomy');

					// Initialize the array for this taxonomy if it doesn't exist
					if (!taxonomyTargetFilter[taxonomy]) {
						taxonomyTargetFilter[taxonomy] = [];
					}

					// Find each input with the .filter-active class within this dropdown
					jQuery(this).find('input.filter-active').each(function () {
						// Get the value of the input
						let value = jQuery(this).val();

						// Add the value to the array for this taxonomy
						taxonomyTargetFilter[taxonomy].push(value);
					});
				});

				// console.log(targetMultiFilter);
				let targetFilter = targetMultiFilter;
				// load items if no items on page

				if (targetFilter === '' || (Array.isArray(targetFilter) && targetFilter.length === 0)) {
					targetFilter = 'all';
				}
				//console.log(targetFilter);

				var ajaxurl = '<?php echo esc_url(admin_url("admin-ajax.php")); ?>' + '?nocache=' + (new Date())
					.getTime();
				var nonce = '<?php echo esc_attr(wp_create_nonce("load_more_nonce")); ?>';
				var bfg_query_vars = <?php echo wp_json_encode($atts); ?>;
				var unique_id = <?php echo esc_js($unique_id); ?>;

				var displayed_posts = jQuery('.displayed_posts').map(function () {
					return jQuery(this).val();
				}).get();
				var button = jQuery('.load-scroll-block');
				data = {
					'action': 'load_more',
					'nonce': nonce,
					'bfg_query_vars': bfg_query_vars,
					'displayed_posts': displayed_posts,
					'targetFilter': targetFilter,
					'taxonomyTargetFilter': taxonomyTargetFilter,
					'unique_id': unique_id,
				};

				jQuery.ajax({
					///dataType : 'html',
					url: ajaxurl,
					data: data,
					type: 'POST',
					cache: false,
					beforeSend: function (xhr) {

						if (jQuery(".no-more-posts").hasClass("active")) {
							button.removeClass('active');
						} else {
							button.addClass('active');
						}
					},
					complete: function () { },
					success: function (data) {
						if (jQuery.trim(data) != '') {
							button.removeClass('active');
							options<?php echo esc_js($unique_id); ?> = {
								/*animationDuration: 0.5,*/
								callbacks: {
									onFilteringStart: function () { },
									onFilteringEnd: function () { },
									onShufflingStart: function () { },
									onShufflingEnd: function () { },
									onSortingStart: function () { },
									onSortingEnd: function () { }
								},
								controlsSelector: '.filtr-controls-<?php echo esc_js($unique_id); ?>',
								filter: targetFilter,
								filterOutCss: {
									top: '0px',
									left: '0px',
									opacity: 0.001,
									transform: ''
								},
								filterInCss: {
									top: '0px',
									left: '0px',
									opacity: 1,
									transform: ''
								},
								layout: 'sameWidth',
								
							selector: '.filtr-item',
								setupControls: false
							}

							var filterizd = jQuery('.bf_gallery_1-<?php echo esc_js($unique_id); ?>').filterizr(options<?php echo esc_js($unique_id); ?>);
							$node = jQuery(data);
							$node[0].firstElementChild.classList.add("loaded-block");

							filterizd.filterizr('insertItem', $node);
							filterizd.filterizr(options<?php echo esc_js($unique_id); ?>).resize();
							jQuery('.filtr-item .post-box').addClass('lazyimg');

							setTimeout(function () {
								jQuery('body, html').animate({ scrollTop: jQuery('.loaded-block').offset().top - 300 }, 500);
								jQuery('.post-box').removeClass("loaded-block");
							}, 1000);

							jQuery('.filtr-container').imagesLoaded(function () {
								// images have already loaded, instantiate Filterizr
								jQuery('.bf_gallery_1-<?php echo esc_js($unique_id); ?>').filterizr(options<?php echo esc_js($unique_id); ?>);
							});

						} else {
							button.removeClass('active');
						}
					}
				});
			}
		});
		<?php
		if ($blog_load_more == "yes") { ?>
			jQuery('#load-more-<?php echo esc_js($unique_id); ?>').click(function () {
				
					// Single Filter
					var targetFilter = jQuery('.filtr-control-<?php echo esc_js($unique_id); ?> li.active').map(function () { return jQuery(this).data('filter'); }).get();
				
				if (targetFilter[0] == "all" || targetFilter == '') { targetFilter = 'all'; }

				var ajaxurl = '<?php echo esc_url(admin_url("admin-ajax.php")); ?>';
				var nonce = '<?php echo esc_attr(wp_create_nonce("load_more_nonce")); ?>';
				var bfg_query_vars = <?php echo wp_json_encode($atts); ?>;
				var unique_id = <?php echo esc_js($unique_id); ?>;

				var displayed_posts = jQuery('.displayed_posts').map(function () { return jQuery(this).val(); }).get();
				var button = jQuery(this);
				data = {
					'action': 'load_more',
					'nonce': nonce,
					'bfg_query_vars': bfg_query_vars,
					'displayed_posts': displayed_posts,
					'targetFilter': targetFilter,
					'unique_id': unique_id,
				};
				jQuery.ajax({
					///dataType : 'html',
					url: ajaxurl,
					data: data,
					type: 'POST',
					cache: false,
					beforeSend: function (xhr) {
						//loading text
						button.find('span').html('..');
						button.addClass('active');
					},
					complete: function () { },
					success: function (data) {
						if (jQuery.trim(data) != '') {
							button.find('span').text(<?php echo wp_json_encode($load_more_text); ?>);
							button.removeClass('active');
							options<?php echo esc_js($unique_id); ?> = {
								/*animationDuration: 0.5,*/
								callbacks: {
									onFilteringStart: function () { },
									onFilteringEnd: function () { },
									onShufflingStart: function () { },
									onShufflingEnd: function () { },
									onSortingStart: function () { },
									onSortingEnd: function () { }
								},
								controlsSelector: '.filtr-controls-<?php echo esc_js($unique_id); ?>',
								filter: targetFilter,
								filterOutCss: {
									top: '0px',
									left: '0px',
									opacity: 0.001,
									transform: ''
								},
								filterInCss: {
									top: '0px',
									left: '0px',
									opacity: 1,
									transform: ''
								},
								layout: 'sameWidth',
								
								selector: '.filtr-item',
								setupControls: false
							}

							var filterizd = jQuery('.bf_gallery_1-<?php echo esc_js($unique_id); ?>').filterizr(options<?php echo esc_js($unique_id); ?>);
							var $nodes = jQuery(data).filter('.filtr-item');
							
							if ($nodes.length > 0) {
								$nodes.each(function() {
									var $singleNode = jQuery(this);
									filterizd.filterizr('insertItem', $singleNode);
								});
								
								// Add a marker to the first new item so we can scroll to it
								$nodes.first().find('.post-box').addClass('loaded-block');
								
								filterizd.filterizr(options<?php echo esc_js($unique_id); ?>).resize();
								jQuery('.filtr-item .post-box').addClass('lazyimg');
							}

							setTimeout(function () {
								jQuery('body, html').animate({ scrollTop: jQuery('.loaded-block').offset().top - 300 }, 500);
								jQuery('.post-box').removeClass("loaded-block");
							}, 1000);

							jQuery('.filtr-container').imagesLoaded(function () {
								// images have already loaded, instantiate Filterizr
								jQuery('.bf_gallery_1-<?php echo esc_js($unique_id); ?>').filterizr(options<?php echo esc_js($unique_id); ?>);
							});

						} else {
							//alert('No More Posts');
							button.find('span').text(<?php echo wp_json_encode($no_more_text); ?>);
							button.css('pointer-events', 'none');
							button.removeClass('active');
						}
					}
				});
			});
			<?php
		} ?>
	});

	
</script>