<?php
/**
 * from start date to end date, show every day's balance
 * and show each transaction on a new row as well
 */

add_shortcode('transaction-template', 'fireplace_transactionTemplate');

function fireplace_transactionTemplate($atts)
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

    $categories = get_terms([
        'taxonomy' => 'transaction_category',
        'hide_empty' => false,
    ]);
    $categoryTables = [];
    $income = 0;
    $expenses = 0;
    $netIncome = 0;
    foreach ($categories as $cat) {
        $table = [
            'name' => $cat->name,
            'rows' => [],
        ];
        $args = [
            'post_type' => 'transaction',
            'posts_per_page' => -1,
            'tax_query' => [[
                'taxonomy' => 'transaction_category',
                'field' => 'term_id',
                'terms' => $cat->term_id,
            ]],
            'meta_query' => [[
                'key' => 'is_template_transaction',
                'value' => 1,
            ]],
        ];
        $query = new WP_Query($args);
        if ($query->have_posts()) {
            $total = 0;
            while ($query->have_posts()) {
                $query->the_post();
                $title = str_replace('Private: ', '', get_the_title());
                $editLink = get_edit_post_link();
                $description = '<a href="' . $editLink . '">' . $title . '</a>';
                $date = get_field('datetime', null, false);
                $date = date('j', strtotime($date));
                $amount = get_field('amount');
                $total += $amount;
                $table['rows'][] = [$description, $date, $amount];
            }
            $table['total'] = $total;
            $table['footers'] = [
                'Total',
                '',
                $total,
            ];
            if ($cat->slug === 'income') {
                $income += $total;
            } else {
                $expenses += $total;
            }
            wp_reset_postdata();
        }
        $categoryTables[] = $table;
    }
    $netIncome = $income - $expenses;
    $summaryHeaders = [
        'Description',
        'Amount',
    ];
    $summaryRows = [
        ['Income', $income],
        ['Expenses', $expenses],
        ['Net Income', $netIncome],
    ];

    $categoryTableHeaders = [
        'Description',
        'Day of Month',
        'Amount',
    ];

    // View
    ?>
    <?php foreach ($categoryTables as $table) : ?>
    <h2><?php echo $table['name']; ?></h2>
    <?php fireplace_table($categoryTableHeaders, $table['rows'], $table['footers']); ?>
    <?php endforeach; ?>

    <h2>Summary</h2>
    <?php fireplace_table($summaryHeaders, $summaryRows); ?>
    <?php
}