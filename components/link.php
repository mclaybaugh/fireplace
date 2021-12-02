<?php

function fireplace_link($href, $text, $class = '', $target = '_self')
{
    ?>
    <a class="<?php echo $class; ?>"
    href="<?php echo $href; ?>"
    target="<?php echo $target; ?>">
        <?php echo $text; ?>
    </a>
    <?php
}