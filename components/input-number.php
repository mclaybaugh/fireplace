<?php

function fireplace_input_number($label, $name, $min, $max, $step, $isRequired = true)
{
    ?>
    <label><?php echo $label; ?>
        <input type="number" name="<?php echo $name; ?>"
        <?php if ($min): ?> min="<?php echo $min; ?>" <?php endif; ?>
        <?php if ($max): ?> max="<?php echo $max; ?>" <?php endif; ?>
        <?php if ($step): ?> step="<?php echo $step; ?>" <?php endif; ?>
        <?php if ($isRequired): ?> required="required" <?php endif; ?>>
    </label>
    <?php
}