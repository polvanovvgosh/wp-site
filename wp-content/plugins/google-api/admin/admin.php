<?php

$true_page = 'google-api-parameters';

function options()
{
    global $true_page;
    add_options_page('Google API', 'Google API', 'manage_options', $true_page, 'option_page');
}

add_action('admin_menu', 'options');


function option_page()
{
    global $true_page;
    ?>
    <div class="wrap">
    <h2><?php echo __('Google Api Options', 'googleapi'); ?></h2>
    <form method="post" enctype="multipart/form-data" action="options.php">
        <?php
        settings_fields('google_api_options');
        do_settings_sections($true_page);
        ?>
        <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>"/>
        </p>
    </form>
    </div><?php
}

function option_settings()
{
    global $true_page;

    register_setting('google_api_options', 'google_api_options');

    add_settings_section('true_section_1', __('Text fields', 'googleapi'), '', $true_page);

    $params = [
        'type' => 'text',
        'id' => 'folder',
        'desc' => __('Specify the name of the order folder. Example: "/orders/" ', 'googleapi'),
        'label_for' => 'folder'
    ];
    add_settings_field(
        'my_text_field',
        __('Folder name', 'googleapi'),
        'option_display_settings',
        $true_page,
        'true_section_1',
        $params
    );

    $params = [
        'type' => 'text',
        'id' => 'api_key',
        'desc' => __('', 'googleapi'),
        'label_for' => 'api_key'
    ];

    add_settings_field(
            'api_key',
        __('Google Api key', 'googleapi'),
        'option_display_settings',
        $true_page,
        'true_section_1',
        $params
    );

}

add_action('admin_init', 'option_settings');

function option_display_settings($args)
{
    extract($args);
    $option_name = 'google_api_options';

    $o = get_option($option_name);

    switch ($type) {
        case 'text':
            $o[$id] = esc_attr(stripslashes($o[$id]));
            echo "<input class='regular-text' type='text' id='$id' name='".$option_name."[$id]' value='$o[$id]' />";
            echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";
            break;
    }
}