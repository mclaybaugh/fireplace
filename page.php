<?php

fireplace_constrainedWidthPage(
	get_the_title(),
	'fireplace_pageContent'
);

function fireplace_pageContent()
{
	while (have_posts()) {
		the_post();
		get_template_part('template-parts/content', 'page');
	}
}
