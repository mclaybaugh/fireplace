<?php

add_shortcode('option-picker', 'fireplace_optionPicker');

function fireplace_optionPicker()
{
    $cats = get_terms([
        'taxonomy' => 'option_category',
        'hide_empty' => false,
    ]);
    // foreach category, get all matching posts
    ob_start();
    foreach ($cats as $cat) {
        $args = [
            'post_type' => 'fireplace_optionx',
            'posts_per_page' => -1,
            'tax_query' => [[
                'taxonomy' => 'option_category',
                'field' => 'id',
                'terms' => $cat->term_id,
            ]]
        ];
        $q = new WP_Query($args);
        $optionJson = json_encode($q->posts);
        $optionVarName = "options_" . $cat->term_id;
?>
        <section class="fireplace_optionPickerSection" data-options="<?php echo $optionVarName; ?>">
            <h2><?php echo $cat->name; ?></h2>
            <?php fireplace_btn('Get Option', 'fireplace_getOptionBtn'); ?>
            <p class="fireplace_showOption">?</p>
            <script>
                window.<?php echo $optionVarName; ?> = <?php echo $optionJson; ?>
            </script>
        </section>
<?php
    }
    $content = ob_get_clean();
    return $content;
}
