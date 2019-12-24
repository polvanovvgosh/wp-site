<?php
/**
 * Plugin Name: Woocommerce report generator
 * Description: Allows you to generate reports in exel format and save them to Google disc
 * Author: Polvanov Igor
 * Version: 1.0
 */

add_action('get_report_data', 'get_orders_data');

register_activation_hook(__FILE__, 'set_cron_schedule');

function set_cron_schedule()
{
    if (!wp_next_scheduled('get_report_data')) {
        wp_schedule_event(time(), 'daily', 'get_report_data');
    }
}

add_action('storefront_before_footer', 'get_orders_data');

function get_orders_data()
{
    global $wpdb;

    $results = $wpdb->get_results(
        'select * from wp_wc_order_stats 
        join wp_wc_customer_lookup 
        on wp_wc_order_stats.customer_id = wp_wc_customer_lookup.customer_id '
    );

    var_dump($results);
    return $results;

}