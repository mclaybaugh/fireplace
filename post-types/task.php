<?php
function fireplace_register_task_occurrence() {
	$labels = [
		"name" => "Task Tracking",
		"singular_name" => "Task Occurrence",
	];
	$args = [
		"label" => "Task Tracking",
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => false,
		"delete_with_user" => false,
		"exclude_from_search" => true,
		"capability_type" => "fireplace_task",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => [
			"slug" => "task_occurrence",
			"with_front" => true
		],
		"query_var" => true,
		"menu_icon" => 'dashicons-analytics',
		"supports" => [
			"title",
			"editor",
			"thumbnail",
			'custom-fields',
			'revisions'
		],
		"show_in_graphql" => false,
	];

	register_post_type( "fireplace_task_occ", $args );
}

add_action('init', 'fireplace_register_task_occurrence');

function fireplace_register_task() {
	$labels = [
		"name" => "Tasks",
		"singular_name" => "Task",
	];
	$args = [
		"label" => "Tasks",
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
		"rewrite" => [
			"slug" => "task",
			"with_front" => true
		],
		"query_var" => true,
		"menu_icon" => 'dashicons-yes',
		"supports" => [
			"title",
			"editor",
			"thumbnail",
			'custom-fields',
			'revisions'
		],
		"show_in_graphql" => false,
	];

	register_post_type( "fireplace_task", $args );
}
add_action('init', 'fireplace_register_task');

if (function_exists('acf_add_local_field_group')) {
	acf_add_local_field_group(array(
		'key' => 'group_621848f1394c6',
		'title' => 'Recurring Task Fields',
		'fields' => array(
			array(
				'key' => 'field_621848f920d8a',
				'label' => 'Date',
				'name' => 'start_date',
				'type' => 'date_picker',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'display_format' => 'm/d/Y',
				'return_format' => 'm/d/Y',
				'first_day' => 0,
			),
			array(
				'key' => 'field_62185104a47c9',
				'label' => 'Is Recurring',
				'name' => 'is_recurring',
				'type' => 'true_false',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'message' => '',
				'default_value' => 0,
				'ui' => 0,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
			array(
				'key' => 'field_6218509fce0b7',
				'label' => 'Frequency',
				'name' => 'frequency',
				'type' => 'number',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_62185104a47c9',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'min' => '',
				'max' => '',
				'step' => '',
			),
			array(
				'key' => 'field_621850b9ce0b8',
				'label' => 'Frequency Unit',
				'name' => 'frequency_unit',
				'type' => 'radio',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_62185104a47c9',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'choices' => array(
					'day' => 'Days',
					'week' => 'Weeks',
					'month' => 'Months',
					'year' => 'Years',
				),
				'allow_null' => 0,
				'other_choice' => 0,
				'default_value' => '',
				'layout' => 'vertical',
				'return_format' => 'value',
				'save_other_choice' => 0,
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'fireplace_task',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => true,
		'description' => '',
		'show_in_rest' => 1,
	));
	acf_add_local_field_group(array(
		'key' => 'group_621c36207174b',
		'title' => 'Task Occurance Fields',
		'fields' => array(
			array(
				'key' => 'field_621c362a60709',
				'label' => 'Task',
				'name' => 'task',
				'type' => 'post_object',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'post_type' => array(
					0 => 'fireplace_task',
				),
				'taxonomy' => '',
				'allow_null' => 0,
				'multiple' => 0,
				'return_format' => 'id',
				'ui' => 1,
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'fireplace_task_occ',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => true,
		'description' => '',
		'show_in_rest' => 1,
	));
}
