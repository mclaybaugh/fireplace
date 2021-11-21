<?php
/**
 * from start date to end date, show every day's balance
 * and show each transaction on a new row as well
 */

add_shortcode('transaction-calendar', 'fireplace_transactionCalendar');

function fireplace_transactionCalendar($atts)
{
    // $atts = array_change_key_case((array) $atts, CASE_LOWER);
    // $atts = shortcode_atts(
    //     [
    //         'title' => '',
    //     ], $atts
    // );

    if (!function_exists('get_field')) {
        return;
    }

    $startDate = date('Y-m-01 00:00:00');
    $startTime = strtotime($startDate);
    $endTime = strtotime('+60 days', $startTime);
    $endDate = date('Y-m-d 00:00:00', $endTime);
    $balance = 0;
    $transactionRows = [];
    $isFirst = true;
    $previousDay = false;
    $previousTimestamp = false;
    $args = [
        'post_type' => 'transaction',
        'posts_per_page' => -1,
        'meta_key' => 'datetime',
        'orderby' => 'meta_value_datetime',
        'order' => 'ASC',
        'meta_query' => [[
            'field' => 'is_template_transaction',
            'compare' => '=',
            'value' => 0,
        ],[
            'field' => 'datetime',
            'compare' => '<',
            'value' => $endDate,
            'type' => 'DATETIME',
        ]],
    ];
    $query = new WP_Query($args);
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $amount = get_field('amount');
            $direction = get_field('direction');

            // if in range, get details
            $date = get_field('datetime', null, false);
            $timestamp = strtotime($date);
            if ($timestamp >= $startTime
            && $timestamp <= $endTime) {
                $description = str_replace('Private: ', '', get_the_title());
                $editLink = get_edit_post_link();
                $dateFormatted = date('Y-m-d', $timestamp);
                $dayOfMonth = date('j', $timestamp);
                $directionSymbol = '-';
                if ($direction === 'in') {
                    $directionSymbol = '';
                }

                if ($isFirst) {
                    if ($dayOfMonth > 1) {
                        $transactionRows = addMissingDays(
                            $transactionRows,
                            $balance,
                            $startTime,
                            strtotime('-1 day', $timestamp)
                        );
                    }
                    $isFirst = false;
                }

                // if days before this one, add them
                if ($previousDay) {
                    $dayDiff = $dayOfMonth - $previousDay;
                    if ($dayDiff > 1) {
                        $transactionRows = addMissingDays(
                            $transactionRows,
                            $balance,
                            strtotime('+1 day', $previousTimestamp),
                            strtotime('-1 day', $timestamp)
                        );
                    }
                }

                // don't show date if repeat
                if ($previousDay === $dayOfMonth) {
                    $dateFormatted = '';
                }
                $previousDay = $dayOfMonth;
                $previousTimestamp = $timestamp;

                // Update balance
                if ($direction === 'in') {
                    $balance += $amount;
                } else {
                    $balance -= $amount;
                }

                $transactionRows[] = [
                    $dateFormatted,
                    $directionSymbol . $amount,
                    $balance,
                    "<a href=\"$editLink\">$description</a>",
                ];
            } else {
                // Update balance
                if ($direction === 'in') {
                    $balance += $amount;
                } else {
                    $balance -= $amount;
                }
            }
        }
        // if last transaction not on last day of month, postfill days
        $lastDayOfMonth = date('j', $endTime);
        $dayDiff = $lastDayOfMonth - $previousDay;
        if ($dayDiff > 1) {
            $transactionRows = addMissingDays(
                $transactionRows,
                $balance,
                strtotime('+1 day', $previousTimestamp),
                $endTime
            );
        }
        wp_reset_postdata();
    }

    ob_start();
    ?>
    <table>
        <thead>
            <th>Date</th>
            <th>Amount</th>
            <th>Balance</th>
            <th>Description</th>
        </thead>
        <tbody>
        <?php foreach ($transactionRows as $row) : ?>
            <tr>
            <?php foreach ($row as $item) : ?>
                <td><?php echo $item; ?></td>
            <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php
    $content = ob_get_clean();
    return $content;
}

function fireplace_getBalanceUpTo($date)
{
    $args = [
        'post_type' => 'transaction',
        'posts_per_page' => -1,
        'meta_query' => [[
            'field' => 'is_template_transaction',
            'compare' => '=',
            'value' => 0,
        ],[
            'field' => 'datetime',
            'compare' => '<',
            'value' => $date,
            'type' => 'DATETIME',
        ]],
    ];
    $balance = 0;
    $query = new WP_Query($args);
    if ($query->have_posts()) {
        $total = 0;
        while ($query->have_posts()) {
            $query->the_post();
            $amount = get_field('amount');
            $balance += $amount;
        }
        wp_reset_postdata();
    }
    return $total;
}

function addMissingDays($transactionRows, $balance, $startTime, $endTime)
{
    $endDate = date('Y-m-d', strtotime('+1 day', $endTime));
    for ($time = $startTime; date('Y-m-d', $time) !== $endDate; $time = strtotime('+1 day', $time)) {
        $transactionRows[] = [
            date('Y-m-d', $time),
            '-',
            $balance,
            '-',
        ];
    }
    return $transactionRows;
}