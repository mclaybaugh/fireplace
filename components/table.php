<?php

function fireplace_table($headers, $rows, $footers = null, $colClasses = null)
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
            <?php for ($i = 0; $i < count($row); $i++) : ?>
                <td class="<?php echo $colClasses[$i]; ?>"><?php echo $row[$i]; ?></td>
            <?php endfor; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <?php if ($footers) : ?>
        <tfoot>
            <tr>
            <?php for ($i = 0; $i < count($footers); $i++) : ?>
                <td class="<?php echo $colClasses[$i]; ?>"><?php echo $footers[$i]; ?></td>
            <?php endfor; ?>
            </tr>
        </tfoot>
        <?php endif; ?>
    </table>
    <?php
}