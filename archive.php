<?php

$title = get_the_archive_title();
$description = get_the_archive_description();
fireplace_constrainedWidthPage(
	$title,
	'fireplace_archiveContent',
	'fireplace_archivePagination',
	$description
);

function fireplace_archiveContent()
{
	if (have_posts()) {
		while (have_posts()) {
			the_post();
			/*
			 * Include the Post-Type-specific template for the content.
			 * If you want to override this in a child theme, then include a file
			 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
			 */
			get_template_part('template-parts/content', get_post_type());
		}
	} else {
		get_template_part('template-parts/content', 'none');
	}
}

function fireplace_archivePagination()
{
	if (have_posts()) {
		the_posts_navigation();
	}
}