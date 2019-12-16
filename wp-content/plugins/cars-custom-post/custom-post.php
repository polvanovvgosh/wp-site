<?php
/**
 * Plugin Name: Custom post plugin
 * Description: Adds the ability to create custom pages
 * Author: Polvanov Igor
 */

add_action('init', 'alt_custom_post_cars');

function alt_custom_post_cars()
{
    $labels = [
        'name'               => __('Cars'),
        'singular_name'      => __('Cars'),
        'add_new'            => __('Add New Car'),
        'add_new_item'       => __('Add New Car'),
        'edit_item'          => __('Edit Car'),
        'new_item'           => __('New Car'),
        'all_items'          => __('All Cars'),
        'view_item'          => __('View Car'),
        'search_items'       => __('Search Cars'),
        'featured_image'     => 'Photo',
        'set_featured_image' => 'Add Photo',
    ];

    $args = [
        'labels'            => $labels,
        'description'       => 'Holds your car\'s specific data',
        'public'            => true,
        'menu_position'     => 5,
        'supports'          => [
            'title',
            'editor',
            'thumbnail',
            'excerpt',
            'comments',
            'custom-fields',
            'page-attributes',
        ],
        'has_archive'       => true,
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'query_var'         => 'car',
        'menu_icon'         => 'dashicons-carrot',
    ];


    register_post_type('car', $args);
}

add_action('init', 'alt_custom_post_books');

function alt_custom_post_books()
{
    $labels = [
        'name'               => __('Books'),
        'singular_name'      => __('Books'),
        'add_new'            => __('Add New Book'),
        'add_new_item'       => __('Add New Book'),
        'edit_item'          => __('Edit Book'),
        'new_item'           => __('New Book'),
        'all_items'          => __('All Books'),
        'view_item'          => __('View Book'),
        'search_items'       => __('Search Book'),
        'featured_image'     => 'Cover',
        'set_featured_image' => 'Add Cover',
    ];

    $args = [
        'labels'            => $labels,
        'description'       => 'Holds your book\'s specific data',
        'public'            => true,
        'menu_position'     => 6,
        'supports'          => [
            'title',
            'editor',
            'thumbnail',
            'excerpt',
            'comments',
            'custom-fields',
            'page-attributes',
        ],
        'has_archive'       => true,
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'query_var'         => 'book',
        'menu_icon'         => 'dashicons-book-alt',
    ];


    register_post_type('book', $args);
}