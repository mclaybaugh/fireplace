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
	<?php wp_head(); ?>
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
			<div class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></div>
			<?php
		endif;
		$fireplace_description = get_bloginfo( 'description', 'display' );
		if ( $fireplace_description || is_customize_preview() ) :
			?>
			<p class="site-description"><?php echo $fireplace_description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
		<?php endif; ?>
	</div><!-- .site-branding -->

	<nav id="site-navigation" class="mainNav">
		<?php
		if (current_user_can('administrator')) {
			$themeLocation = 'logged-in-nav';
		} else {
			$themeLocation = 'public-nav';
		}
		wp_nav_menu([
			'theme_location' => $themeLocation,
			'menu_class' => 'list-style-none margin-none padding-none flex',
		]);
		?>
	</nav><!-- #site-navigation -->
</header>
