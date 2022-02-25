<?php

function fireplace_register_journal() {

/**
 * Post Type: Journals.
 */

$labels = [
    "name" => __( "Journal", "twentytwentyone" ),
    "singular_name" => __( "Journal Entry", "twentytwentyone" ),
];

$args = [
    "label" => __( "Journal", "twentytwentyone" ),
    "labels" => $labels,
    "description" => "",
    "public" => true,
    "publicly_queryable" => true,
    "show_ui" => true,
    "show_in_rest" => true,
    "rest_base" => "",
    "rest_controller_class" => "WP_REST_Posts_Controller",
    "has_archive" => 'journals',
    "show_in_menu" => true,
    "show_in_nav_menus" => false,
    "delete_with_user" => false,
    "exclude_from_search" => true,
    "capability_type" => "post",
    "map_meta_cap" => true,
    "hierarchical" => false,
    "rewrite" => [ "slug" => "journal", "with_front" => true ],
    "query_var" => true,
    "menu_icon" => 'dashicons-welcome-write-blog',
    "supports" => [ "title", "editor", "thumbnail" ],
    "show_in_graphql" => false,
];

register_post_type( "fireplace_journal", $args );
}

add_action( 'init', 'fireplace_register_journal' );
