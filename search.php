<?php
$title = 'Search Results for: <span>' . get_search_query() . '</span>';
fireplace_constrainedWidthPage(
	$title,
	function() {
	if (have_posts()) {
		while (have_posts()) {
			the_post();
			get_template_part( 'template-parts/content', 'search' );
		}
		the_posts_navigation();
	} else {
		get_template_part( 'template-parts/content', 'none' );
	}
});
