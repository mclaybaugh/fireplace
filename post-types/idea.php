<?php
function fireplace_register_idea() {
    /**
     * Post Type: Ideas.
     */

    $labels = [
        "name" => __( "Ideas", "twentytwentyone" ),
        "singular_name" => __( "Idea", "twentytwentyone" ),
    ];

    $args = [
        "label" => __( "Ideas", "twentytwentyone" ),
        "labels" => $labels,
        "description" => "",
        "public" => true,
        "publicly_queryable" => true,
        "show_ui" => true,
        "show_in_rest" => true,
        "rest_base" => "",
        "rest_controller_class" => "WP_REST_Posts_Controller",
        "has_archive" => 'ideas',
        "show_in_menu" => true,
        "show_in_nav_menus" => false,
        "delete_with_user" => false,
        "exclude_from_search" => true,
        "capability_type" => "post",
        "map_meta_cap" => true,
        "hierarchical" => false,
        "rewrite" => false,
        "query_var" => true,
        "menu_icon" => 'dashicons-lightbulb',
        "supports" => [ "title", "editor", "thumbnail" ],
        "show_in_graphql" => false,
    ];

    register_post_type( "idea", $args );
}

add_action('init', 'fireplace_register_idea');
