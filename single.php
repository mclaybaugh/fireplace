<?php
get_header();
?>
<main id="primary" class="site-main padding-2">
	<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	
	<div class="constrained-width">
		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content', get_post_type() );
			?>
			</div>
			<?php
			the_post_navigation(
				array(
					'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'fireplace' ) . '</span> <span class="nav-title">%title</span>',
					'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'fireplace' ) . '</span> <span class="nav-title">%title</span>',
				)
			);
		endwhile; // End of the loop.
		?>
</main><!-- #main -->

<?php
get_sidebar();
get_footer();
