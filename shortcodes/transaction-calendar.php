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

    $startDate = date('Y-m-d 00:00:00');
    $endDate = date('Y-m-d 00:00:00', strtotime('+60 days'));
    $balance = fireplace_getBalanceUpTo(date('Y-m-d 00:00:00'));
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
            'compare' => '>',
            'value' => $startDate,
            'type' => 'DATETIME',
        ],[
            'field' => 'datetime',
            'compare' => '<',
            'value' => $endDate,
            'type' => 'DATETIME',
        ]],
    ];
    $query = new WP_Query($args);
    if ($query->have_posts()) {
        $total = 0;
        while ($query->have_posts()) {
            $query->the_post();
            $description = str_replace('Private: ', '', get_the_title());
            $date = get_field('datetime', null, false);
            $date = date('j', strtotime($date));
            $amount = get_field('amount');
            $total += $amount;
            $table['rows'][] = [$description, $date, $amount];
        }
        $table['total'] = $total;
        if ($cat->slug === 'income') {
            $income += $total;
        } else {
            $expenses += $total;
        }
        wp_reset_postdata();
    }
    ?>
    <table>
        <thead>
            <th>Date</th>
            <th>Amount</th>
            <th>Balance</th>
            <th>Description</th>
        </thead>
        <tbody>
            <tr>
                <td>2021-11-15</td>
                <td>-130.00</td>
                <td>900.00</td>
                <td>Did a thing</td>
            </tr>
        </tbody>
    </table>
    <?php
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