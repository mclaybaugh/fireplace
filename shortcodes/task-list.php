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

    $taskArgs = [
        'post_type' => 'fireplace_task',
        'posts_per_page' => -1,
    ];
    $taskQuery = new WP_Query($taskArgs);
    $tasks = $taskQuery->posts;

    $taskData = [];
    $currentTimestamp = current_time('timestamp');
    $currentDateTime = DateTimeImmutable::createFromFormat('U', $currentTimestamp);
    foreach ($tasks as $task) {
        $args = [
            'post_type' => 'fireplace_task_occ',
            'posts_per_page' => 1,
            'meta_query' => [[
                'key' => 'task',
                'compare' => '=',
                'value' => $task->ID,
            ]],
        ];
        $q = new WP_Query($args);
        $mostRecent = false;
        if (count($q->posts) > 0) {
            $mostRecent = $q->posts[0];
        }
        $taskData[] = fireplace_getTaskStatus($task, $mostRecent, $currentDateTime);
    }
    $headers = [
        'Completed',
        'Task Name',
        'Due Date',
    ];

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

    // @TODO
    // add "is_archived" field to tasks to stop them from loading on task list
    // add archive listing to show paged tasks
    // add template to view task history for single task
    */
    ob_start();
    fireplace_table($headers, $taskData);
    $content = ob_get_clean();
    return $content;
}

function fireplace_getTaskStatus(
    $task,
    $mostRecentOcc,
    DateTimeImmutable $currentDateTime)
{
    $startDate = get_field('start_date', $task, false);
    $startDateTime = new DateTimeImmutable($startDate);
    $isRecurring = get_field('is_recurring', $task);
    $title = get_the_title($task);

    if (!$mostRecentOcc) {
        $completed = false;
        $nextDueDate = $startDateTime->format('Y-m-d');
    } elseif (!$isRecurring) {
        $completed = true;
        $nextDueDate = false;
    } else {
        // Recurring task that has priors
        $freqNum = get_field('frequency', $task);
        $freqUnit = get_field('frequency_unit', $task);

        // get prev and next due date
        $freq = DateInterval::createFromDateString("$freqNum $freqUnit");
        $prevDueDate = $startDateTime;
        $nextDueDate = $prevDueDate->add($freq);
        while ($nextDueDate < $currentDateTime) {
            $oldDueDate = $prevDueDate;
            $prevDueDate = $nextDueDate;
            $nextDueDate = $prevDueDate->add($freq);
        }
        // don't use most recent, use the previous two. These are named
        // poorly I know
        $nextDueDate = $prevDueDate;
        $prevDueDate = $oldDueDate;


        $rawOccDate = $mostRecentOcc->post_date;
        $recentDateTime = new DateTimeImmutable($rawOccDate);
        if ($recentDateTime > $prevDueDate
        && $recentDateTime <= $nextDueDate) {
            $completed = true;
        } else {
            $completed = false;
        }
        $nextDueDate = $nextDueDate->format('Y-m-d');
    }

    return [
        $completed,
        $title,
        $nextDueDate,
    ];
}
