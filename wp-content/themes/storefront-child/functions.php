<?php

add_action('after_setup_theme', 'alt_removes_action');

function alt_removes_action()
{
    remove_action('storefront_header', 'storefront_secondary_navigation', 30);

    remove_action('homepage', 'storefront_on_sale_products', 60);

}

add_action('woocommerce_before_main_content', 'art_move_storefront_secondary_navigation', 10);

add_filter('storefront_product_categories_args', 'art_homepage_product_categories', 20, 1);
function art_homepage_product_categories($arg)
{
    $arg['limit']   = 10;
    $arg['columns'] = 5;
    $arg['title']   = esc_attr__('Не робит', 'storefront');

    return $arg;
}


function alter_sf_products_per_page()
{
    return 8;
}

add_filter('storefront_products_per_page', 'alter_sf_products_per_page');

add_filter('loop_shop_per_page', 'new_loop_shop_per_page', 20);
function new_loop_shop_per_page($cols)
{
    // $cols contains the current number of products per page based on the value stored on Options -> Reading
    // Return the number of products you wanna show per page.
    $cols = 3;

    return $cols;
}

function art_move_storefront_secondary_navigation()
{
    echo '<div class="new_menu">';
    storefront_secondary_navigation();
    echo '</div>';
}

add_filter('storefront_register_nav_menus', 'art_added_menu_footer', 10, 1);

function art_added_menu_footer($menus)
{
    $menus['footer'] = 'Footer menu';

    return $menus;
}

add_action('storefront_before_footer', 'art_render_menu_footer');

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

add_filter( 'cron_schedules', 'alt_cron_interval');
function alt_cron_interval( $schedule ) {
    $schedule['every_3_min'] = array(
        'interval' => 180,
        'display' => 'Every three minutes'
    );
    return $schedule;
}

