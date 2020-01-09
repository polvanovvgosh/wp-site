<?php
/**
 * Plugin Name: Woocommerce report generator
 * Description: Allows you to generate reports in exel format and save them to Google disc
 * Author: Polvanov Igor
 * Version: 1.0
 */

add_action('get_report_data', 'get_orders_data');

register_activation_hook(__FILE__, 'set_cron_schedule');

/**
 * Runs a script daily
 */
function set_cron_schedule()
{
    if (!wp_next_scheduled('get_report_data')) {
        wp_schedule_event(time(), 'daily', 'get_report_data');
    }
}

/**
 * Get orders data from Db
 *
 * @return array|object|null
 */
function get_orders_data()
{
    global $wpdb;
    $results = $wpdb->get_results(
        'select * from wp_wc_order_stats 
        join wp_wc_customer_lookup 
        on wp_wc_order_stats.customer_id = wp_wc_customer_lookup.customer_id '
    );

    return $results;
}

/**
 * Save data from database in Xmlx file
 *
 * @throws \PhpOffice\PhpSpreadsheet\Exception
 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
 */
function save_report_to_file()
{
    $orders = get_orders_data();
    if (defined('CBXPHPSPREADSHEET_PLUGIN_NAME') && file_exists(CBXPHPSPREADSHEET_ROOT_PATH.'lib/vendor/autoload.php')) {

        require_once(CBXPHPSPREADSHEET_ROOT_PATH.'lib/vendor/autoload.php');

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    }

    foreach ($orders as $orderKey => $order) {
        $sheet = $spreadsheet->getActiveSheet();
        $key = $orderKey + 1;
        $sheet->setCellValue('A'.$key, $order->order_id );
        $sheet->setCellValue('B'.$key, $order->date_created);
        $sheet->setCellValue('C'.$key, $order->num_items_sold);
        $sheet->setCellValue('D'.$key, $order->net_total);
        $sheet->setCellValue('E'.$key, $order->user_id);
        $sheet->setCellValue('F'.$key, $order->username);
        $sheet->setCellValue('G'.$key, $order->first_name);
        $sheet->setCellValue('H'.$key, $order->last_name);
        $sheet->setCellValue('I'.$key, $order->email);
        $sheet->setCellValue('J'.$key, $order->country);
        $sheet->setCellValue('K'.$key, $order->postcode);
        $sheet->setCellValue('L'.$key, $order->city);
        $sheet->setCellValue('M'.$key, $order->state);
    }

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save( wp_get_upload_dir()['basedir'] . '/orders/'.date('c').'.xlsx');

}