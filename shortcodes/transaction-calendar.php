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
    $previousTimestamp = false;
    $prevYearMonthDay = false;
    global $todaysDate;
    $todaysDate = $currentDatetime->format('Y-m-d');
    global $todayBalance;
    $todayBalance = false;

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
    // Add datetime to post fields to avoid repeated calls later.
    $sortedPosts = array_map(function ($x) {
        $x->datetime = get_field('datetime', $x->ID);
        $x->timestamp = strtotime($x->datetime);
        return $x;
    }, $query->posts);

    // Sort by timestamp, ascending
    usort($sortedPosts, function ($a, $b) {
        return $a->timestamp - $b->timestamp;
    });

    // echo "<pre>";
    // var_dump($sortedPosts);
    // echo "</pre>";

    foreach ($sortedPosts as $transaction) {
        $amount = get_field('amount', $transaction->ID);
        $direction = get_field('direction', $transaction->ID);

        // if in range, get details
        $timestamp = $transaction->timestamp;
        if ($timestamp >= $startTime
        && $timestamp <= $endTime) {
            $description = str_replace('Private: ', '', get_the_title($transaction->ID));
            $editLink = get_edit_post_link($transaction->ID);
            $dateFormatted = date('Y-m-d', $timestamp);
            $dayOfMonth = date('j', $timestamp);
            $yearMonthDay = date('Ymd', $timestamp);
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
            if ($prevYearMonthDay) {
                $dayDiff = $yearMonthDay - $prevYearMonthDay;
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
            $dateDisplay = $dateFormatted;
            if ($prevYearMonthDay === $yearMonthDay) {
                $dateDisplay = '';
            }
            $previousTimestamp = $timestamp;
            $prevYearMonthDay = date('Ymd', $timestamp);

            // Update balance
            if ($direction === 'in') {
                $balance += $amount;
            } else {
                $balance -= $amount;
            }

            if ($dateFormatted === $todaysDate) {
                $todayBalance = $balance;
            }

            $transactionRows[] = [
                $dateDisplay,
                $directionSymbol . fireplace_format_currency($amount),
                fireplace_format_currency($balance),
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
    $lastDayOfMonth = date('Ymt', $endTime);
    $dayDiff = $lastDayOfMonth - $prevYearMonthDay;
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

    $colClasses = [
        'align-left',
        'align-right',
        'align-right',
        'align-left',
    ];

    // Navigation data
    $fStartTime = date('F Y', $startTime);
    $fEndTime = date('F Y', $endTime);
    $previousTime = strtotime('-' . $atts['interval'] . ' days', $startTime);
    $pYear = date('Y', $previousTime);
    $pMonth = date('m', $previousTime);
    $pFormatted = date('M Y', $previousTime);
    $nextTime = strtotime('+1 days', $endTime);
    $nYear = date('Y', $nextTime);
    $nMonth = date('m', $nextTime);
    $nFormated = date('M Y', $nextTime);

    $previousHref = "?startYear=$pYear&startMonth=$pMonth";
    $previousText = "< $pFormatted";
    $nextHref = "?startYear=$nYear&startMonth=$nMonth";
    $nextText = "$nFormated >";

    ob_start();
    ?>
    <h2><?php echo $fStartTime; ?> to <?php echo $fEndTime; ?></h2>

    <?php if ($addStatusMessage) : ?>
    <p><?php echo $addStatusMessage; ?></p>
    <?php endif; ?>

    <div class="flex justify-space-between flex-wrap pb1">
        <?php fireplace_link_btn($previousHref, $previousText); ?>
        <?php fireplace_link_btn($nextHref, $nextText); ?>
    </div>
    <?php fireplace_table($tableHeaders, $transactionRows, null, $colClasses); ?>

    <h2>Actions</h2>

    <?php if ($todayBalance) : ?>
    <h3>Update Today</h3>
    <p>Today's expected balance: <?php echo fireplace_format_currency($todayBalance); ?></p>
    <form method="post">
        <?php fireplace_input_number('Actual Balance', 'actualBalance', '0', null, '.01'); ?>
        <?php fireplace_submit_btn('Update'); ?>
        <input type="hidden" name="expectedBalance"
        value="<?php echo $todayBalance; ?>">
        <input type="hidden" name="updateDate"
        value="<?php echo $todaysDate; ?> 23:00:00">
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
    // echo "<pre>";
    // var_dump(date('Y-m-d', $startTime));
    // var_dump(date('Y-m-d', $endTime));
    // echo "</pre>";
    $endDate = date('Y-m-d', strtotime('+1 day', $endTime));
    global $todaysDate;
    for ($time = $startTime; date('Y-m-d', $time) !== $endDate; $time = strtotime('+1 day', $time)) {
        $dateFormatted = date('Y-m-d', $time);
        if ($dateFormatted === $todaysDate) {
            global $todayBalance;
            $todayBalance = $balance;
        }
        $transactionRows[] = [
            $dateFormatted,
            '-',
            fireplace_format_currency($balance),
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
        $addStatusMessage = 'Update transaction added successfully: ' . fireplace_format_currency($amount);
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