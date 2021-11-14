<?php

fireplace_constrainedWidthPage(
	get_the_title(),
	function() {
    while (have_posts()) {
		the_post();
		get_template_part( 'template-parts/content', get_post_type() );
	}
}, function() {
	the_post_navigation(
		array(
			'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'fireplace' ) . '</span> <span class="nav-title">%title</span>',
			'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'fireplace' ) . '</span> <span class="nav-title">%title</span>',
		)
	);
});
