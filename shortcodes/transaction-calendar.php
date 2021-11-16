<?php
/**
 * from start date to end date, show every day's balance
 * and show each transaction on a new row as well
 */

add_shortcode('transaction-calendar', 'fireplace_transactionCalendar');

function fireplace_transactionCalendar($atts)
{
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