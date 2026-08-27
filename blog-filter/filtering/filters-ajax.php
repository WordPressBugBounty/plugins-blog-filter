<?php if (! defined('ABSPATH')) exit; ?>
<script type="text/javascript">
(function($) {
	$(document).ready(function() {
		var uniqueId = <?php echo wp_json_encode((string)$unique_id); ?>;
		var containerSelector = '.bf_gallery_1-' + uniqueId;
		var controlsSelector = '.filtr-controls-' + uniqueId;
		var $container = $(containerSelector);

		function revealGallery() {
			$('.blog_loader-' + uniqueId).fadeOut(250, function() {
				$(this).hide();
			});
			$container.css('opacity', 1);
			$('.bfg_theme_1').css('opacity', 1);
		}

		// Filterizr options
		var filterOptions = {
			callbacks: {
				onFilteringStart: function () { },
				onFilteringEnd: function () { },
				onShufflingStart: function () { },
				onShufflingEnd: function () { },
				onSortingStart: function () { },
				onSortingEnd: function () { }
			},
			controlsSelector: controlsSelector,
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
			setupControls: true
		};

		var filterizd = null;
		if ($container.length && typeof $container.filterizr === 'function') {
			filterizd = $container.filterizr(filterOptions);
			if (typeof $container.imagesLoaded === 'function') {
				$container.imagesLoaded(function() {
					if (filterizd && typeof filterizd.filterizr === 'function') {
						filterizd.filterizr(filterOptions);
					}
					revealGallery();
				});
			} else {
				revealGallery();
			}
		} else {
			revealGallery();
		}

		// Fallback timeout to ensure posts are always displayed
		setTimeout(function() {
			revealGallery();
		}, 800);

		// Active class handling for filter buttons
		$(controlsSelector).on('click', function() {
			$(controlsSelector).removeClass('active');
			$(this).addClass('active');
		});

		// Multifilter controls
		$('.multifilter li').on('click', function() {
			$(this).toggleClass('active');
		});

		// Search handling
		$('input.searchTerm').on('keyup input', function() {
			if (filterizd && typeof filterizd.filterizr === 'function') {
				filterizd.filterizr('search', $(this).val());
			}
		});

		<?php if ($blog_fixed_grid == 'yes' && $blog_template == 'template1') { ?>
		function AWL_setMaxHeight(className) {
			var elements = document.querySelectorAll(className);
			var maxHeight = 0;
			elements.forEach(function(el) {
				if (el.offsetHeight > maxHeight) {
					maxHeight = el.offsetHeight;
				}
			});
			elements.forEach(function(el) {
				el.style.height = maxHeight + 'px';
			});
		}
		AWL_setMaxHeight('.bf_title_box_1');
		AWL_setMaxHeight('.bf_title_box_2');
		<?php } ?>

		// Load More AJAX Button Handling
		<?php if ($blog_load_more == 'yes') { ?>
		$('#load-more-' + uniqueId).on('click', function(e) {
			e.preventDefault();
			var button = $(this);
			var targetFilter = $('.filtr-control-' + uniqueId + ' li.active').map(function() {
				return $(this).data('filter');
			}).get();

			if (!targetFilter.length || targetFilter[0] === 'all' || targetFilter === '') {
				targetFilter = 'all';
			}

			var ajaxurl = <?php echo wp_json_encode(admin_url('admin-ajax.php')); ?>;
			var nonce = <?php echo wp_json_encode(wp_create_nonce('load_more_nonce')); ?>;
			var bfg_query_vars = <?php echo wp_json_encode($atts, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES); ?>;
			var loadMoreText = <?php echo wp_json_encode($load_more_text); ?>;
			var noMoreText = <?php echo wp_json_encode($no_more_text); ?>;

			var displayed_posts = $('.displayed_posts').map(function() {
				return $(this).val();
			}).get();

			var postData = {
				action: 'load_more',
				nonce: nonce,
				bfg_query_vars: bfg_query_vars,
				displayed_posts: displayed_posts,
				targetFilter: targetFilter,
				unique_id: uniqueId
			};

			button.find('span').text('...');
			button.addClass('active');

			$.ajax({
				url: ajaxurl,
				data: postData,
				type: 'POST',
				cache: false,
				success: function(data) {
					var trimmedData = (typeof data === 'string') ? data.trim() : '';
					if (trimmedData !== '') {
						button.find('span').text(loadMoreText);
						button.removeClass('active');

						var $nodes = $(trimmedData).filter('.filtr-item');
						if ($nodes.length > 0 && filterizd) {
							$nodes.each(function() {
								filterizd.filterizr('insertItem', $(this));
							});
							$nodes.first().find('.post-box').addClass('loaded-block');
							filterizd.filterizr(filterOptions).resize();
							$('.filtr-item .post-box').addClass('lazyimg');

							setTimeout(function() {
								if ($('.loaded-block').length) {
									$('body, html').animate({
										scrollTop: $('.loaded-block').offset().top - 300
									}, 500);
									$('.post-box').removeClass('loaded-block');
								}
							}, 1000);

							if (typeof $container.imagesLoaded === 'function') {
								$container.imagesLoaded(function() {
									filterizd.filterizr(filterOptions);
								});
							}
						}
					} else {
						button.find('span').text(noMoreText);
						button.css('pointer-events', 'none');
						button.removeClass('active');
					}
				},
				error: function() {
					button.find('span').text(noMoreText);
					button.removeClass('active');
				}
			});
		});
		<?php } ?>
	});
})(jQuery);
</script>