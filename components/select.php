<?php

function fireplace_select($label, $name, $options, $isRequired = true)
{
    ?>
    <label class="display-block"><?php echo $label; ?>
        <select name="<?php echo $name; ?>" class="fireplace__select"
        <?php if ($isRequired): ?> required="required" <?php endif; ?>>
            <?php foreach ($options as $value => $text) : ?>
            <option value="<?php echo $value; ?>">
                <?php echo $text; ?>
            </option>
            <?php endforeach; ?>
        </select>
    </label>
    <?php
}
