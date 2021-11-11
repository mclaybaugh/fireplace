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
        'fireplace_settings',
        [
            'title' => 'Fireplace Settings',
        ]
    );

    $settings = [[
        'name' => 'background_color',
        'type' => 'color',
        'section' => 'fireplace_settings',
        'label' => 'Background Color',
        'default' => '#ffffff',
    ],[
        'name' => 'text_color',
        'type' => 'color',
        'section' => 'fireplace_settings',
        'label' => 'Text Color',
        'default' => '#404040',
    ],[
        'name' => 'highlight_color',
        'type' => 'color',
        'section' => 'fireplace_settings',
        'label' => 'Highlight Color',
        'default' => '#3582c4',
    ],[
        'name' => 'font_import_url',
        'type' => 'text',
        'section' => 'fireplace_settings',
        'label' => 'Font Import URL',
        'default' => '',
    ],[
        'name' => 'heading_font',
        'type' => 'text',
        'section' => 'fireplace_settings',
        'label' => 'Heading Font',
        'default' => '',
    ],[
        'name' => 'body_font',
        'type' => 'text',
        'section' => 'fireplace_settings',
        'label' => 'Body Font',
        'default' => '',
    ]];

    fireplace_addSettingsAndControls($wp_customize, $settings);
}

function fireplace_addSettingsAndControls($wp_customize, $settings)
{
    foreach ($settings as $setting) {
        $settingId = 'fireplace_' . $setting['name'];
        $controlId = 'fireplace_' . $setting['name'] . '_control';

        $wp_customize->add_setting(
            $settingId,
            [
                'default' => $setting['default'],
            ]
        );

        if ($setting['type'] === 'color') {
            $colorControl = new WP_Customize_Color_Control(
                $wp_customize,
                $controlId,
                [
                    'label' => $setting['label'],
                    'section' => $setting['section'],
                    'settings' => $settingId,
                ]
            );
            $wp_customize->add_control($colorControl);
        } else {
            $wp_customize->add_control(
                $controlId,
                [
                    'label' => $setting['label'],
                    'section' => $setting['section'],
                    'settings' => $settingId,
                ]
            );
        }
    }
}
