<?php
function cptui_register_my_cpts_task() {

	/**
	 * Post Type: Tasks.
	 */

	$labels = [
		"name" => __( "Tasks", "twentytwentyone" ),
		"singular_name" => __( "Task", "twentytwentyone" ),
	];

	$args = [
		"label" => __( "Tasks", "twentytwentyone" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => 'tasks',
		"show_in_menu" => true,
		"show_in_nav_menus" => false,
		"delete_with_user" => false,
		"exclude_from_search" => true,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => [ "slug" => "task", "with_front" => true ],
		"query_var" => true,
		"menu_icon" => 'dashicons-yes',
		"supports" => [ "title", "editor", "thumbnail" ],
		"show_in_graphql" => false,
	];

	register_post_type( "task", $args );
}

add_action( 'init', 'cptui_register_my_cpts_task' );
