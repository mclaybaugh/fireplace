<?php
// Fonts
//  - headings
//  - content
//  - secondary
//  - etc

// Colors (default)
//  - text
//  - bg (default)
//  - highlight

add_action('customize_register', 'fireplace_customize_register');
function fireplace_customize_register($wp_customize)
{
    $wp_customize->add_section(
        'fireplace_section',
        [
            'title' => 'Fireplace Settings',
            'description' => 'magic',
        ]
    );
}
