<?php
if (! defined('ABSPATH')) exit; // Exit if accessed directly
?>
<style>
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .portfolio_thumbnail {
		border-radius: 0;
		display: block;
		height: auto;
		line-height: 1.42857;
		height: 100% !important;
		width: 100% !important;
		float: left;
	}

	/* thumb spacing */

	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-xs-1,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-sm-1,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-md-1,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-lg-1,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-xs-2,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-sm-2,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-md-2,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-lg-2,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-xs-3,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-sm-3,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-md-3,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-lg-3,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-xs-4,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-sm-4,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-md-4,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-lg-4,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-xs-5,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-sm-5,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-md-5,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-lg-5,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-xs-6,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-sm-6,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-md-6,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-lg-6,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-xs-7,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-sm-7,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-md-7,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-lg-7,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-xs-8,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-sm-8,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-md-8,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-lg-8,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-xs-9,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-sm-9,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-md-9,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-lg-9,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-xs-10,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-sm-10,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-md-10,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-lg-10,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-xs-11,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-sm-11,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-md-11,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-lg-11,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-xs-12,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-sm-12,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-md-12,
	.bf_gallery_1-<?php echo esc_attr($unique_id); ?> .col-lg-12 {
		padding-right: 5px !important;
		padding-left: 5px !important;
		padding-bottom: 5px !important;
		padding-top: 5px !important;
	}

	.snip0047-<?php echo esc_attr($unique_id); ?> {
		background-color: <?php echo esc_attr($blog_buttons_color); ?> !important;
	}

	.snip0047-<?php echo esc_attr($unique_id); ?>:focus {
		background-color: <?php echo esc_attr($blog_buttons_color); ?> !important;
	}

	.snip0047-<?php echo esc_attr($unique_id); ?>:active {
		background-color: <?php echo esc_attr($blog_buttons_color); ?> !important;
	}

	.blog_pagination-<?php echo esc_attr($unique_id); ?> span {
		background: <?php echo esc_attr($blog_pagination_loadmore_color); ?> !important;
		border: 1px solid #eaeaea;
		display: inline-block;
		text-align: center;
		color: #FFFFFF;
		padding: 4px 12px;
		border-radius: 5px;
	}

	.blog_pagination-<?php echo esc_attr($unique_id); ?> span:hover {
		background: <?php echo esc_attr($blog_pagination_loadmore_color); ?> !important;
		color: #ffffff;
	}

	.blog_pagination-<?php echo esc_attr($unique_id); ?> a {
		border: 1px solid <?php echo esc_attr($blog_pagination_loadmore_color); ?> !important;
		display: inline-block;
		text-align: center;
		color: <?php echo esc_attr($blog_pagination_loadmore_color); ?> !important;
		padding: 4px 12px;
		border-radius: 5px;
		transition: 0.7s;
	}

	.blog_pagination-<?php echo esc_attr($unique_id); ?> a:hover,
	.blog_pagination-<?php echo esc_attr($unique_id); ?> a:focus {
		background: <?php echo esc_attr($blog_pagination_loadmore_color); ?> !important;
		color: #FFFFFF !important;
		text-decoration: none;
	}

	figure.snip1228-<?php echo esc_attr($unique_id); ?> figcaption i {
		background-color: <?php echo esc_attr($blog_buttons_color); ?> !important;
	}

	.blog_loader-<?php echo esc_attr($unique_id); ?> {
		border-top: 5px solid <?php echo esc_attr($blog_buttons_color); ?> !important;
	}

	.filtr-container {
		transition-property: height, width;
	}

	.filtr-container,
	.filtr-container .filtr-item {
		-webkit-transition-duration: 0.8s;
		-moz-transition-duration: 0.8s;
		-ms-transition-duration: 0.8s;
		-o-transition-duration: 0.8s;
		transition-duration: 0.8s;

	}

	.filtr-item {
		-webkit-transition-property: transform, opacity, height, width;
		-moz-transition-property: transform, opacity, height, width;
		-ms-transition-property: transform, opacity, height, width;
		-o-transition-property: transform, opacity, height, width;
		transition-property: transform, opacity, height, width;
		-webkit-transition-timing-function: ease-out;
		-moz-transition-timing-function: ease-out;
		transition-timing-function: ease-out;
	}

	.filtr-item .post-box {
		transform: scale3d(1, 1, 1);
		transition: transform 0.5s ease-out, opacity 0.5s ease-out;
	}

	.filtr-item .lazyimg {
		transition-property: transform, opacity;
		transition-duration: 0.5s;
		transform: scale3d(1, 1, 1);
		transition-timing-function: ease-out;
	}

	#load-more-<?php echo esc_attr($unique_id); ?> {
		transition-delay: 0.8s;
		text-align: center;
		margin: 0 auto;
		background-color: <?php echo esc_attr($blog_pagination_loadmore_color); ?> !important;
	}

	#load-more-<?php echo esc_attr($unique_id); ?>:focus {
		box-shadow: none !important
	}

	.lds-ellipsis-<?php echo esc_attr($unique_id); ?> div {
		position: absolute;
		top: 33px;
		width: 13px;
		height: 13px;
		border-radius: 50%;
		background: <?php echo esc_attr($blog_pagination_loadmore_color); ?> !important;
		animation-timing-function: cubic-bezier(0, 1, 1, 0);
	}

	/* Image to background */
	<?php if ($blog_fixed_grid == 'yes') { ?> .fit-in-content {
		display: block;
		height: 250px;
		background-repeat: no-repeat !important;
		background-size: cover !important;
		background-position: center !important;
	}

	<?php } ?>@media (min-width: 1025px) {
		.snip0047-<?php echo esc_attr($unique_id); ?>:hover span {
			-webkit-transform: translate3d(-20px, 0px, 0px);
			transform: translate3d(-20px, 0px, 0px);
			opacity: 1;
		}

		.snip0047-<?php echo esc_attr($unique_id); ?>:hover i {
			opacity: 1;
			-webkit-transition-delay: 0.15s;
			transition-delay: 0.15s;
		}

		.snip0047-<?php echo esc_attr($unique_id); ?>:hover:before {
			width: 38px;
			-webkit-transition-delay: 0s;
			transition-delay: 0s;
			border-radius: 5px;
		}

	}

	<?php
	if ($blog_direction == "rtl") { ?> .blog_filter_main {
		direction: rtl;
	}

	<?php
	} ?>.snip0047-<?php echo esc_attr($unique_id); ?> .active span {
		-webkit-transform: translate3d(-20px, 0px, 0px);
		transform: translate3d(-20px, 0px, 0px);
		opacity: 1;
	}

	.snip0047-<?php echo esc_attr($unique_id); ?> .active i {
		opacity: 1;
		-webkit-transition-delay: 0.15s;
		transition-delay: 0.15s;
	}

	.snip0047-<?php echo esc_attr($unique_id); ?> .active:before {
		width: 38px;
		-webkit-transition-delay: 0s;
		transition-delay: 0s;
		border-radius: 5px;
	}

	/* title box css*/

	.blog_title:hover {
		color: <?php echo esc_attr($blog_buttons_color); ?> !important;
	}

	.bf_thumb_box_1-<?php echo esc_attr($unique_id); ?> {
		padding: inherit;
		background-color: <?php echo esc_attr($blog_desc_box_color); ?>;
		border: 1px solid;
		border-color: rgba(<?php echo intval($r); ?>, <?php echo intval($g); ?>, <?php echo intval($b); ?>);
	}

	<?php
	if ($blog_title_below_image == "no" || $blog_date_below_image == "no" || $blog_author_below_image == "no") { ?> .bf_title_box_1-<?php echo esc_attr($unique_id); ?> {
		padding-top: 5px;
		padding-bottom: 10px;
		padding-left: 8px;
		padding-right: 8px;
	}

	<?php } ?> .bf_title_box_2-<?php echo esc_attr($unique_id); ?> {
		padding-top: 10px;
		padding-bottom: 10px;
		padding-left: 8px;
		padding-right: 8px;
	}

	.bf_title_1-<?php echo esc_attr($unique_id); ?> {
		float: left;
		margin-top: 15px;
		margin-bottom: 15px;
		font-size: <?php echo intval($blog_title_font_size); ?>px !important;
		color: <?php echo esc_attr($blog_title_color); ?> !important;
		font-weight: bold !important;
		width: 100%;
	}

	.bf_desc_1-<?php echo esc_attr($unique_id); ?> {
		font-size: <?php echo intval($blog_desc_font_size); ?>px;
		color: <?php echo esc_attr($blog_desc_color); ?>;
		margin: 10px 1px;
	}

	.blog_metaInfo>span {
		color: <?php echo esc_attr($blog_desc_color); ?>;
	}


	<?php echo esc_html(wp_strip_all_tags($custom_css)); ?>
</style>