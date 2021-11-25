<?php

function fireplace_input_number($name, $min, $max, $step, $isRequired = true)
{
    ?>
    <input type="number" name="<?php echo $name; ?>"
    <?php if ($min): ?> min="<?php echo $min; ?>" <?php endif; ?>
    <?php if ($max): ?> max="<?php echo $max; ?>" <?php endif; ?>
    <?php if ($step): ?> step="<?php echo $step; ?>" <?php endif; ?>
    <?php if ($isRequired): ?> required="required" <?php endif; ?>>
    <?php
}