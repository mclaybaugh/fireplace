<?php

function fireplace_select($name, $options, $isRequired = true)
{
    ?>
    <select name="<?php echo $name; ?>"
    <?php if ($isRequired): ?> required="required" <?php endif; ?>>
        <?php foreach ($options as $value => $text) : ?>
        <option value="<?php echo $value; ?>">
            <?php echo $text; ?>
        </option>
        <?php endforeach; ?>
    </select>
    <?php
}
