<?php

function fireplace_register_optionx()
{

    /**
     * Post Type: Option To Picks.
     */

    $labels = [
        "name" => "Option To Pick",
        "singular_name" => "Option Entry",
    ];

    $args = [
        "label" => "Option To Pick",
        "labels" => $labels,
        "description" => "",
        "public" => true,
        "publicly_queryable" => true,
        "show_ui" => true,
        "show_in_rest" => true,
        "rest_base" => "",
        "rest_controller_class" => "WP_REST_Posts_Controller",
        "has_archive" => 'options-to-pick',
        "show_in_menu" => true,
        "show_in_nav_menus" => false,
        "delete_with_user" => false,
        "exclude_from_search" => true,
        "capability_type" => "post",
        "map_meta_cap" => true,
        "hierarchical" => false,
        "rewrite" => ["slug" => "option", "with_front" => true],
        "query_var" => true,
        "menu_icon" => 'dashicons-welcome-write-blog',
        "supports" => ["title", "editor", "thumbnail"],
        "show_in_graphql" => false,
    ];

    register_post_type("fireplace_optionx", $args);
}

add_action('init', 'fireplace_register_optionx');

function fireplace_register_option_category()
{
    /**
     * Taxonomy: Option Categories.
     */

    $labels = [
        "name" => __("Option Categories", "fireplace"),
        "singular_name" => __("Option Category", "fireplace"),
    ];


    $args = [
        "label" => __("Option Categories", "fireplace"),
        "labels" => $labels,
        "public" => true,
        "publicly_queryable" => true,
        "hierarchical" => true,
        "show_ui" => true,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "query_var" => true,
        "rewrite" => ['slug' => 'option_category', 'with_front' => true,],
        "show_admin_column" => true,
        "show_in_rest" => true,
        "show_tagcloud" => false,
        "rest_base" => "option_category",
        "rest_controller_class" => "WP_REST_Terms_Controller",
        "show_in_quick_edit" => true,
        "show_in_graphql" => false,
    ];
    register_taxonomy("option_category", ["fireplace_optionx"], $args);
}
add_action('init', 'fireplace_register_option_category');
