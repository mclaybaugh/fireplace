<?php
/**
 * from start date to end date, show every day's balance
 * and show each transaction on a new row as well
 */

add_shortcode('transaction-calendar', 'fireplace_transactionCalendar');

function fireplace_transactionCalendar($atts)
{
    $atts = array_change_key_case((array) $atts, CASE_LOWER);
    $atts = shortcode_atts(
        [
            'interval' => '40',
        ], $atts
    );

    if (!function_exists('get_field')) {
        return;
    }

    // start date from URL or default
    if (array_key_exists('startYear', $_GET)
    && array_key_exists('startMonth', $_GET)) {
        $startYear = $_GET['startYear'];
        $startMonth = $_GET['startMonth'];
        $startDate = "$startYear-$startMonth-01 00:00:00";
    } else {
        $startDate = date('Y-m-01 00:00:00');
    }
    $startTime = strtotime($startDate);

    // end date from URL or default
    if (array_key_exists('endYear', $_GET)
    && array_key_exists('endMonth', $_GET)) {
        $endYear = $_GET['endYear'];
        $endMonth = $_GET['endMonth'];
        $monthStart = strtotime("$endYear-$endMonth-01 00:00:00");
        $numDays = date('t', $monthStart);
        $endDate = "$endYear-$endMonth-$numDays 00:00:00";
    } else {
        $nextMonth = strtotime('+' . $atts['interval'] . ' days', $startTime);
        $endDate = date('Y-m-t 00:00:00', $nextMonth);
    }
    $endTime = strtotime($endDate);

    // declare variables
    $balance = 0;
    $transactionRows = [];
    $isFirst = true;
    $previousDay = false;
    $previousTimestamp = false;

    // run query
    $args = [
        'post_type' => 'transaction',
        'posts_per_page' => -1,
        'meta_key' => 'datetime',
        'orderby' => 'meta_value_datetime',
        'order' => 'ASC',
        'meta_query' => [[
            'key' => 'is_template_transaction',
            'value' => 0,
        ],[
            'key' => 'datetime',
            'compare' => '<=',
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
            print_r($date);

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
            if ($previousTimestamp) {
                $startFill = strtotime('+1 day', $previousTimestamp);
            } else {
                $startFill = $startTime;
            }
            $transactionRows = addMissingDays(
                $transactionRows,
                $balance,
                $startFill,
                $endTime
            );
        } 
        wp_reset_postdata();
    }

    ob_start();
    ?>
    <h2><?php echo date('F Y', $startTime); ?>
    to <?php echo date('F Y', $endTime); ?></h2>
    <?php
    $previousTime = strtotime('-' . $atts['interval'] . ' days', $startTime);
    $pYear = date('Y', $previousTime);
    $pMonth = date('m', $previousTime);
    $nextTime = strtotime('+1 days', $endTime);
    $nYear = date('Y', $nextTime);
    $nMonth = date('m', $nextTime);
    ?>
    <a class="btn" href="?startYear=<?php echo $pYear; ?>&startMonth=<?php echo $pMonth; ?>">Previous period</a>
    <a class="btn" href="?startYear=<?php echo $nYear; ?>&startMonth=<?php echo $nMonth; ?>">Next period</a>
    <form method="get">
        <input type="number" maxlength="4" name="startYear" required="required">
        <select name="startMonth" required="required">
            <option value="01">January</option>
            <option value="02">February</option>
            <option value="03">March</option>
            <option value="04">April</option>
            <option value="05">May</option>
            <option value="06">June</option>
            <option value="07">July</option>
            <option value="08">August</option>
            <option value="09">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
        </select>
        <input type="number" maxlength="4" name="endYear" required="required">
        <select name="endMonth" required="required">
            <option value="01">January</option>
            <option value="02">February</option>
            <option value="03">March</option>
            <option value="04">April</option>
            <option value="05">May</option>
            <option value="06">June</option>
            <option value="07">July</option>
            <option value="08">August</option>
            <option value="09">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
        </select>
        <input type="submit" value="submit">
    </form>
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