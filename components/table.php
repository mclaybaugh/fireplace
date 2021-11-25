<?php

function fireplace_table($headers, $rows, $footers = null)
{
    ?>
    <table>
        <thead>
            <?php foreach ($headers as $header) : ?>
            <th><?php echo $header; ?></th>
            <?php endforeach; ?>
        </thead>
        <tbody>
        <?php foreach ($rows as $row) : ?>
            <tr>
            <?php foreach ($row as $data) : ?>
                <td><?php echo $data; ?></td>
            <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <?php if ($footers) : ?>
        <tfoot>
            <tr>
                <?php foreach ($footers as $footer) : ?>
                <td><?php echo $footer; ?></td>
                <?php endforeach; ?>
            </tr>
        </tfoot>
        <?php endif; ?>
    </table>
    <?php
}