<?php
function fireplace_register_transaction() {
	/**
	 * Post Type: Tasks.
	 */

	$labels = [
		"name" => "Transactions",
		"singular_name" => "Transaction",
	];

	$args = [
		"label" => "Transactions",
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => 'transactions',
		"show_in_menu" => true,
		"show_in_nav_menus" => false,
		"delete_with_user" => false,
		"exclude_from_search" => true,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => [ "slug" => "transaction", "with_front" => true ],
		"query_var" => true,
		"menu_icon" => 'dashicons-money-alt',
		"supports" => [ "title" ],
		"show_in_graphql" => false,
	];

	register_post_type("transaction", $args);
}

add_action('init', 'fireplace_register_transaction');

function fireplace_register_transaction_category() {
	/**
	 * Taxonomy: Transaction Categories.
	 */

	$labels = [
		"name" => __( "Transaction Categories", "fireplace" ),
		"singular_name" => __( "Transaction Category", "fireplace" ),
	];

	
	$args = [
		"label" => __( "Transaction Categories", "fireplace" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => false,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => [ 'slug' => 'transaction_category', 'with_front' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => true,
		"show_tagcloud" => false,
		"rest_base" => "transaction_category",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit" => false,
		"show_in_graphql" => false,
	];
	register_taxonomy( "transaction_category", [ "transaction" ], $args );
}
add_action('init', 'fireplace_register_transaction_category');

if( function_exists('acf_add_local_field_group') ):
    acf_add_local_field_group(array(
        'key' => 'group_618f2b1913a5b',
        'title' => 'Transaction Fields',
        'fields' => array(
            array(
                'key' => 'field_618f2b261d45c',
                'label' => 'Datetime',
                'name' => 'datetime',
                'type' => 'date_time_picker',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'display_format' => 'm/d/Y g:i a',
                'return_format' => 'm/d/Y g:i a',
                'first_day' => 0,
            ),
            array(
                'key' => 'field_618f2bc01d45d',
                'label' => 'Amount',
                'name' => 'amount',
                'type' => 'number',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
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
                'step' => '.01',
            ),
            array(
                'key' => 'field_618f2beb1d45e',
                'label' => 'Balance',
                'name' => 'balance',
                'type' => 'number',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
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
                'step' => '.01',
            ),
            array(
                'key' => 'field_618f2ca01f7f7',
                'label' => 'Is Template Transaction',
                'name' => 'is_template_transaction',
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
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'transaction',
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
        'show_in_rest' => 0,
    ));
endif;