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

    if (array_key_exists('actualBalance', $_POST)
    && wp_verify_nonce($_POST['updateTodaysBalance'], 'updateTodaysBalance')) {
        $addStatusMessage = fireplace_update_balance(
            $_POST['actualBalance'],
            $_POST['expectedBalance'],
            $_POST['updateDate']
        );
    }

    if (array_key_exists('genYear', $_POST)
    && wp_verify_nonce($_POST['generateTransactions'], 'generateTransactions')) {
        fireplace_generate_transactions($_POST['genYear'], $_POST['genMonth']);
    }

    $currentDatetime = current_datetime();
    // start date from URL or default
    if (array_key_exists('startYear', $_GET)
    && array_key_exists('startMonth', $_GET)) {
        $startYear = $_GET['startYear'];
        $startMonth = $_GET['startMonth'];
        $startDate = "$startYear-$startMonth-01 00:00:00";
    } else {
        $startDate = $currentDatetime->format('Y-m-01 00:00:00');
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
    $tableHeaders = [
        'Date',
        'Amount',
        'Balance',
        'Description',
    ];
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

    // @TODO
    // dont trust order.
    // make array of all days
    // make array of all transactions with details
    // foreach day, get matching transactions in order
    // fill in row data

    $query = new WP_Query($args);
    // var_dump($query->posts);
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
                $lastPrevDay = date('md', $timestamp);
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
        $lastDayOfMonth = date('md', $endTime);
        $dayDiff = $lastDayOfMonth - $lastPrevDay;
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

    // Get today's balance
    $today = $currentDatetime->format('Y-m-d');
    $todayBalance = false;
    foreach ($transactionRows as $row) {
        if ($row[0] === $today) {
            $todayBalance = $row[2];
        }
    }

    // Navigation data
    $fStartTime = date('F Y', $startTime);
    $fEndTime = date('F Y', $endTime);
    $previousTime = strtotime('-' . $atts['interval'] . ' days', $startTime);
    $pYear = date('Y', $previousTime);
    $pMonth = date('m', $previousTime);
    $nextTime = strtotime('+1 days', $endTime);
    $nYear = date('Y', $nextTime);
    $nMonth = date('m', $nextTime);

    ob_start();
    ?>
    <h2><?php echo $fStartTime; ?> to <?php echo $fEndTime; ?></h2>

    <?php if ($addStatusMessage) : ?>
    <p><?php echo $addStatusMessage; ?></p>
    <?php endif; ?>

    <a class="btn" href="?startYear=<?php echo $pYear; ?>&startMonth=<?php echo $pMonth; ?>">Previous period</a>
    <a class="btn" href="?startYear=<?php echo $nYear; ?>&startMonth=<?php echo $nMonth; ?>">Next period</a>

    <?php fireplace_table($tableHeaders, $transactionRows); ?>

    <h2>Actions</h2>

    <?php if ($todayBalance) : ?>
    <h3>Update Today</h3>
    <p>Today's expected balance: <?php echo $todayBalance; ?></p>
    <form method="post">
        <?php fireplace_input_number('Actual Balance', 'actualBalance', '0', null, '.01'); ?>
        <?php fireplace_submit_btn('Update'); ?>
        <input type="hidden" name="expectedBalance"
        value="<?php echo $todayBalance; ?>">
        <input type="hidden" name="updateDate"
        value="<?php echo $today; ?> 23:00:00">
        <?php wp_nonce_field('updateTodaysBalance', 'updateTodaysBalance') ?>
    </form>
    <?php endif; ?>

    <h3>Custom Range</h3>
    <form method="get">
        <?php fireplace_input_number_year('Start Year', 'startYear'); ?>
        <?php fireplace_select_month('Start Month', 'startMonth'); ?>
        <?php fireplace_input_number_year('End Year', 'endYear'); ?>
        <?php fireplace_select_month('End Month', 'endMonth'); ?>
        <?php fireplace_submit_btn(); ?>
    </form>

    <h3>Generate Transactions From Template</h3>
    <form method="post">
        <?php fireplace_input_number_year('Year', 'genYear'); ?>
        <?php fireplace_select_month('Month', 'genMonth'); ?>
        <?php fireplace_submit_btn('Generate'); ?>
        <?php wp_nonce_field('generateTransactions', 'generateTransactions') ?>
    </form>

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

function fireplace_update_balance($actual, $expected, $updateDate)
{
    $amount = $actual - $expected;
    if ($amount > 0) {
        $direction = 'in';
        $cats = ['income'];
    } else {
        $direction = 'out';
        $cats = ['variable'];
    }
    $transactionDetails = [
        'title' => 'Updated Today',
        'datetime' => $updateDate,
        'amount' => abs($amount),
        'direction' => $direction,
        'cats' => $cats,
        'is_template_transaction' => 0,
    ];
    $postId = fireplace_add_transaction($transactionDetails);
    if ($postId) {
        $addStatusMessage = 'Update transaction added successfully.';
    } else {
        $addStatusMessage = 'Update transaction failed.';
    }
    return $addStatusMessage;
}

function fireplace_generate_transactions($year, $month)
{
    // get all template transactions
    $args = [
        'post_type' => 'transaction',
        'posts_per_page' => -1,
        'meta_query' => [[
            'key' => 'is_template_transaction',
            'value' => 1,
        ]],
    ];
    $query = new WP_Query($args);
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            // foreach, add transaction with this month and year
            $query->the_post();
            $postId = get_the_ID();
            $title = str_replace('Private: ', '', get_the_title());
            $date = get_field('datetime', null, false);
            $dateTail = date('d H:i:s', strtotime($date));
            $newDate = "$year-$month-$dateTail";
            $amount = get_field('amount');
            $direction = get_field('direction');
            $cats = get_the_terms($postId, 'transaction_category');
            $cats = array_map(function ($term) {
                return $term->term_id;
            }, $cats);

            $transactionDetails = [
                'title' => $title,
                'datetime' => $newDate,
                'amount' => $amount,
                'direction' => $direction,
                'cats' => $cats,
                'is_template_transaction' => 0,
            ];
            $postId = fireplace_add_transaction($transactionDetails);
        }
        wp_reset_postdata();
    }
}
