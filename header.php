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
$fontImportUrl = get_theme_mod('fireplace_font_import_url', '');
$bodyFont = get_theme_mod('fireplace_body_font', '');
$headingFont = get_theme_mod('fireplace_heading_font', '');
$backgroundColor = get_theme_mod('fireplace_background_color', '#ffffff');
$textColor = get_theme_mod('fireplace_text_color', '#404040');
$highlightColor = get_theme_mod('fireplace_highlight_color', '#3582c4');
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
	<style>
	<?php if ($fontImportUrl) : ?>
	@import url('<?php echo $fontImportUrl; ?>');
	<?php endif; ?>

	:root {
		<?php if ($bodyFont) : ?>
		--body-font: <?php echo $bodyFont; ?>;
		<?php endif; ?>

		<?php if ($headingFont) : ?>
		--heading-font: <?php echo $headingFont; ?>;
		<?php endif; ?>

		<?php if ($backgroundColor) : ?>
		--background-color: <?php echo $backgroundColor; ?>;
		<?php endif; ?>

		<?php if ($textColor) : ?>
		--text-color: <?php echo $textColor; ?>;
		<?php endif; ?>

		<?php if ($highlightColor) : ?>
		--highlight-color: <?php echo $highlightColor; ?>;
		<?php endif; ?>
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
			wp_nav_menu([
				'theme_location' => 'menu-1',
				'menu_class' => 'list-style-none margin-none padding-none flex',
			]);
			?>
		</nav><!-- #site-navigation -->
	</header>
