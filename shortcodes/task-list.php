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

    $taskData = [];
    $currentTimestamp = current_time('timestamp');
    $currentDateTime = new DateTimeImmutable($currentTimestamp);
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
        $taskData[] = fireplace_getTaskStatus($term, $mostRecent, $currentDateTime);
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

function fireplace_getTaskStatus(
    $task,
    $mostRecentOcc,
    DateTimeImmutable $currentDateTime)
{
    $startDate = get_field('start_date', $task, false);
    $startDateTime = new DateTimeImmutable($startDate);

    if (!$mostRecentOcc) {
        $dueDate = $startDateTime->format('Y-m-d');
        $status = [
            'completed' => false,
            'due_date' => $dueDate,
        ];
    } elseif (!get_field('is_recurring', $task)) {
        $status = [
            'completed' => true,
            'due_date' => false,
        ];
    } else {
        $freqNum = get_field('frequency', $task);
        $freqUnit = get_field('frequency_unit', $task);
        // get prev and next due date
        // (start date, start date + freq)
        // (loop until date is future, then use future and one before)

        // if occurence > prev due date AND <= next due date
        // then complete
        // else not complete, due next due date

        // @TODO
        // add "is_archived" field to tasks to stop them from loading on task list
        // add archive listing to show paged tasks
        // add template to view task history for single task
        // UPDATE 2022-02-26
        // change task to post type, and then add in custom taxomonies
        // for task categories/tags
        // Add priority field or tax
    }

    return $status;
}