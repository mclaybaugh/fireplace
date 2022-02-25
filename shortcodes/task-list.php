<?php

add_shortcode('task-list', 'fireplace_taskList');

function fireplace_taskList($atts)
{
    // $atts = array_change_key_case((array) $atts, CASE_LOWER);
    // $atts = shortcode_atts(
    //     [
    //         'example' => '40',
    //     ], $atts
    // );

    if (!function_exists('get_field')) {
        return;
    }

    $rtasks = get_terms([
        'taxonomy' => 'fireplace_task',
        'hide_empty' => false,
    ]);

    foreach ($rtasks as $term) {
        $args = [
            'post_type' => 'fireplace_task_occ',
            'posts_per_page' => 1,
            'tax_query' => [
                'taxonomy' => 'fireplace_task',
                'field' => 'term_id',
                'terms' => $term->term_id,
            ],
        ];
        $q = new WP_Query($args);
        $mostRecent = false;
        if (count($q->posts) > 0) {
            $mostRecent = $q->posts[0];
        }
    }
    /* 
    load all recurring tasks
    for each task, get last time they were done (most recent post) and check
    against interval to see status

    statuses:
        Overdue X units
        Todo (normal)
        Done

    Actions:
    view task history (show all occurrences of task)
     - delete entries here

    mark complete
    - add notes optionally
    - add post with notes
    */
}