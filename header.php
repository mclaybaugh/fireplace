<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package fireplace
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
	<style>
	@import url('https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,700;1,400;1,700&family=Montserrat:ital,wght@0,100;0,200;0,400;0,900;1,100;1,200;1,400;1,900&display=swap');

	:root {
		--body-font: Lori, serif;
		--heading-font: Montserrat, sans-serif;
	}
	</style>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
	<a class="skip-link screen-reader-text" href="#primary">Skip to content</a>

	<header class="site-header">
		<div class="site-branding">
			<?php
			the_custom_logo();
			if (is_front_page() && is_home()) :
				?>
				<h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></h1>
				<?php
			else :
				?>
				<p class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></p>
				<?php
			endif;
			$fireplace_description = get_bloginfo( 'description', 'display' );
			if ( $fireplace_description || is_customize_preview() ) :
				?>
				<p class="site-description"><?php echo $fireplace_description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php endif; ?>
		</div><!-- .site-branding -->

		<nav id="site-navigation" class="main-navigation">
			<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Primary Menu', 'fireplace' ); ?></button>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'menu-1',
					'menu_id'        => 'primary-menu',
				)
			);
			?>
		</nav><!-- #site-navigation -->
	</header>
