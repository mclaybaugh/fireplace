<?php
/**
 * from start date to end date, show every day's balance
 * and show each transaction on a new row as well
 */

add_shortcode('transaction-template', 'fireplace_transactionTemplate');

function fireplace_transactionTemplate($atts)
{
    $atts = array_change_key_case((array) $atts, CASE_LOWER);
    $atts = shortcode_atts(
        [
            'title' => '',
        ], $atts
    );

    // get all cats
    $categories = get_terms([
        'taxonomy' => 'transaction_category',
        'hide_empty' => false,
    ]);
    print_r($categories);
    // foreach cat, get template transactions sorted by date of month
    // calculate summary table
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