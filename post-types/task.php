<?php
function fireplace_register_task() {

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
		"capability_type" => "fireplace_task",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => [ "slug" => "task", "with_front" => true ],
		"query_var" => true,
		"menu_icon" => 'dashicons-yes',
		"supports" => [ "title", "editor", "thumbnail", 'custom-fields', 'revisions' ],
		"show_in_graphql" => false,
	];

	register_post_type( "task", $args );
}

add_action( 'init', 'fireplace_register_task' );

function fireplace_register_tax_recurring_task() {
	$labels = [
		"name" => __( "Recurring Tasks", "fireplace" ),
		"singular_name" => __( "Recurring Task", "fireplace" ),
	];
	$args = [
		"label" => __( "Recurring Tasks", "fireplace" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => false,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => false,
		"query_var" => true,
		"rewrite" => [ 'slug' => 'recurring_task', 'with_front' => true, ],
		"show_admin_column" => true,
		"show_in_rest" => true,
		"show_tagcloud" => false,
		"rest_base" => "recurring_task",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit" => false,
		"show_in_graphql" => false,
	];
	register_taxonomy( "recurring_task", [ "task" ], $args );
}
add_action( 'init', 'fireplace_register_tax_recurring_task' );
