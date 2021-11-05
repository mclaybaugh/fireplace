<?php
get_header();
?>

	<main id="primary" class="site-main">
		<div class="constrained-width padding-2">
			<?php
			if (have_posts()): 
				while (have_posts()):
					the_post();
					/*
					* Include the Post-Type-specific template for the content.
					* If you want to override this in a child theme, then include a file
					* called content-___.php (where ___ is the Post Type name) and that will be used instead.
					*/
					get_template_part( 'template-parts/content', get_post_type() );
				endwhile;
				the_posts_navigation();
			else :
				get_template_part( 'template-parts/content', 'none' );
			endif;
			?>
		</div>
	</main><!-- #main -->
<?php
get_sidebar();
get_footer();
