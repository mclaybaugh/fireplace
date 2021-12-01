<?php
/**
 * fireplace functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package fireplace
 */

if ( ! defined( 'FIREPLACE_VERSION' ) ) {
    // Replace the version number of the theme on each release.
    define( 'FIREPLACE_VERSION', '1.1.0' );
}

if ( ! function_exists( 'fireplace_setup' ) ) :
    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which
     * runs before the init hook. The init hook is too late for some features, such
     * as indicating support for post thumbnails.
     */
    function fireplace_setup() {
        // Add default posts and comments RSS feed links to head.
        add_theme_support( 'automatic-feed-links' );

        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support( 'title-tag' );

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support( 'post-thumbnails' );

        // This theme uses wp_nav_menu() in one location.
        register_nav_menus([
            'public-nav' => 'Public Navigation',
            'logged-in-nav' => 'Logged-In Navigation'
        ]);

        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support(
            'html5',
            array(
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
                'style',
                'script',
                'navigation-menus',
            )
        );

        // Set up the WordPress core custom background feature.
        // add_theme_support(
        //     'custom-background',
        //     apply_filters(
        //         'fireplace_custom_background_args',
        //         array(
        //             'default-color' => 'ffffff',
        //             'default-image' => '',
        //         )
        //     )
        // );

        // Add theme support for selective refresh for widgets.
        add_theme_support('customize-selective-refresh-widgets');

        /**
         * Add support for core custom logo.
         *
         * @link https://codex.wordpress.org/Theme_Logo
         */
        add_theme_support(
            'custom-logo',
            array(
                'height'      => 250,
                'width'       => 250,
                'flex-width'  => true,
                'flex-height' => true,
            )
        );
    }
endif;
add_action( 'after_setup_theme', 'fireplace_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function fireplace_content_width() {
    $GLOBALS['content_width'] = apply_filters( 'fireplace_content_width', 640 );
}
add_action( 'after_setup_theme', 'fireplace_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function fireplace_widgets_init() {
    register_sidebar(
        array(
            'name'          => esc_html__( 'Sidebar', 'fireplace' ),
            'id'            => 'sidebar-1',
            'description'   => esc_html__( 'Add widgets here.', 'fireplace' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );
}
add_action( 'widgets_init', 'fireplace_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function fireplace_scripts() {
    wp_enqueue_style( 'fireplace-style', get_stylesheet_uri(), array(), FIREPLACE_VERSION );
}
add_action( 'wp_enqueue_scripts', 'fireplace_scripts' );

$templateDir = get_template_directory();
//require $templateDir . '/inc/custom-header.php';
require $templateDir . '/inc/template-tags.php';
require $templateDir . '/inc/template-functions.php';
require $templateDir . '/inc/customizer.php';
// Post types
require $templateDir . '/post-types/idea.php';
require $templateDir . '/post-types/task.php';
require $templateDir . '/post-types/journal.php';
require $templateDir . '/post-types/transaction.php';
require $templateDir . '/shortcodes/transaction-calendar.php';
require $templateDir . '/shortcodes/transaction-template.php';
require $templateDir . '/components/input-number.php';
require $templateDir . '/components/input-number-year.php';
require $templateDir . '/components/link.php';
require $templateDir . '/components/select.php';
require $templateDir . '/components/select-month.php';
require $templateDir . '/components/submit.php';
require $templateDir . '/components/table.php';



/**
 * For private post types, prevent the "publish" status on save.
 */
add_filter('wp_insert_post_data', 'fireplace_private_post_types');
function fireplace_private_post_types($post)
{
    $private_types = [
        'journal',
        'idea',
        'task',
        'transaction',
    ];
    if (in_array($post['post_type'], $private_types)
    && $post['post_status'] == 'publish') {
        $post['post_status'] = 'private';
    }
    return $post;
}

add_action('wp_head', 'fireplace_outputInlineStyle');
function fireplace_outputInlineStyle()
{
    $fontImportUrl = get_theme_mod('fireplace_font_import_url', '');
    $bodyFont = get_theme_mod('fireplace_body_font', '');
    $headingFont = get_theme_mod('fireplace_heading_font', '');
    $backgroundColor = get_theme_mod('fireplace_background_color', '#ffffff');
    $textColor = get_theme_mod('fireplace_text_color', '#404040');
    $highlightColor = get_theme_mod('fireplace_highlight_color', '#3582c4');
    ?>
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
    <?php
}

function fireplace_constrainedWidthPage(
    $title,
    $contentFunction,
    $afterFunction = null,
    $description = '')
{
    get_header();
    ?>
    <main id="primary" class="site-main padding-2">
    <?php if ($title) : ?>
        <h1 class="pageTitle"><?php echo $title; ?></h1>
        <?php if ($description) : ?>
            <p><?php echo $description ?></p>
        <?php endif; ?>
        <hr>
    <?php endif; ?>
    <div class="constrained-width">
        <?php call_user_func($contentFunction); ?>
    </div>
    <?php if ($afterFunction) : ?>
        <?php call_user_func($afterFunction); ?>
    <?php endif; ?>
    </main>
    <?php
    get_sidebar();
    get_footer();
}