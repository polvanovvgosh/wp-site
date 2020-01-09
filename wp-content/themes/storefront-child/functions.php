<?php

add_action('after_setup_theme', 'alt_removes_action');

/**
 * Delete Actions
 *
 */
function alt_removes_action()
{
    remove_action('storefront_header', 'storefront_secondary_navigation', 30);
    remove_action('homepage', 'storefront_on_sale_products', 60);
}

/**
 * Add key to google map api
 *
 * @param $api
 *
 * @return mixed
 */
function my_acf_google_map_api( $api ){

    $api['key'] = 'AIzaSyC728jq3e_oIgK3SWWathYg__zGsoTY8EQ';

    return $api;

}

add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');

add_filter('storefront_product_categories_args', 'art_homepage_product_categories', 20, 1);
function art_homepage_product_categories($arg)
{
    $arg['limit']   = 10;
    $arg['columns'] = 5;
    $arg['title']   = esc_attr__('Не робит', 'storefront');

    return $arg;
}

/**
 * Changes quantity of products on page
 *
 * @return int
 */
function alter_sf_products_per_page()
{
    return 8;
}

add_filter('storefront_products_per_page', 'alter_sf_products_per_page');

add_filter('loop_shop_per_page', 'new_loop_shop_per_page', 20);
/**
 *
 * Changes quantity product columns
 *
 * @param $cols
 *
 * @return int
 */
function new_loop_shop_per_page($cols)
{
    $cols = 3;

    return $cols;
}

/**
 * Move secondary navigation
 */
function art_move_storefront_secondary_navigation()
{
    echo '<div class="new_menu">';
    storefront_secondary_navigation();
    echo '</div>';
}

add_filter('storefront_register_nav_menus', 'art_added_menu_footer', 10, 1);
/**
 * Add Menu in Menus list
 *
 * @param $menus
 *
 * @return mixed
 */
function art_added_menu_footer($menus)
{
    $menus['footer'] = 'Footer menu';

    return $menus;
}

add_action('storefront_before_footer', 'art_render_menu_footer');
/**
 * Add menu before footer
 */
function art_render_menu_footer()
{
    if (has_nav_menu('footer')) {
        ?>
        <nav class="footer-navigation" role="navigation">
            <?php
            wp_nav_menu(
                [
                    'theme_location' => 'footer',
                    'fallback_cb'    => '',
                ]
            );
            ?>
        </nav><!-- #site-navigation -->
        <?php
    }
}

add_action('widgets_init', 'register_my_widgets');
/**
 * Registration custom sidebars
 *
 */
function register_my_widgets()
{
    register_sidebar(
        [
            'name'          => 'Before footer sidebar',
            'id'            => "sidebar-above-footer",
            'description'   => 'Display above footer',
            'class'         => 'before-footer-sidebar',
            'before_widget' => '<div id="video-widget-before-footer" class="widget footer-before">',
            'after_widget'  => "</div>",
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => "</h2>",
        ]
    );
    register_sidebar(
        [
            'name'          => 'Before content sidebar',
            'id'            => "sidebar-top-sidebar",
            'description'   => 'Display above content',
            'class'         => 'before-content-sidebar',
            'before_widget' => '<div id="above-content-sidebar" class="widget above-content">',
            'after_widget'  => "</div>",
            'before_title'  => '<h2 class="top-widget-title">',
            'after_title'   => "</h2>",
        ]
    );
}

add_filter('cron_schedules', 'alt_cron_interval');
/**
 * Create a custom schedule for cron
 *
 * @param $schedule
 *
 * @return mixed
 */
function alt_cron_interval($schedule)
{
    $schedule['every_3_min'] = [
        'interval' => 180,
        'display'  => 'Every three minutes',
    ];

    return $schedule;
}

add_action('admin_enqueue_scripts', 'atl_adds_custom_styles');
/**
 * Add custom styles in admin panel
 *
 */
function atl_adds_custom_styles()
{
    wp_register_style('admin-style', get_stylesheet_directory_uri().'/admin-style.css');
    wp_enqueue_style('admin-style', get_stylesheet_directory_uri().'/admin-style.css');
}

add_action( 'wp_enqueue_scripts', 'alt_scripts_basic' );

/**
 * Add custom js scripts
 */
function alt_scripts_basic ()
{
    wp_register_script('custom-script', get_stylesheet_directory_uri().'/js/custom-script.js');
    wp_enqueue_script('custom-script', get_stylesheet_directory_uri().'/js/custom-script.js');
}
