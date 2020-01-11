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
 * Get items data from Db
 *
 * @return array|object|null
 */
function get_items_data()
{
    global $wpdb;
    return $wpdb->get_results(
        'SELECT product_id, product_net_revenue , COUNT(product_id) as products_sum, product_net_revenue * COUNT(product_id) as net_sum 
        FROM wp_wc_order_product_lookup
        GROUP BY product_id'
    );
}

add_action('after_setup_theme', 'save_report_to_file');

/**
 * Save data from database in Xmlx file
 *
 * @throws \PhpOffice\PhpSpreadsheet\Exception
 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
 */
function save_report_to_file()
{
    $orders = get_orders_data();
    $items = get_items_data();
    if (defined('CBXPHPSPREADSHEET_PLUGIN_NAME') && file_exists(
            CBXPHPSPREADSHEET_ROOT_PATH.'lib/vendor/autoload.php'
        )) {

        require_once(CBXPHPSPREADSHEET_ROOT_PATH.'lib/vendor/autoload.php');

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    }

    foreach ($orders as $orderKey => $order) {

        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();
        $key   = $orderKey + 1;

        $sheet->setCellValue('A'.$key, $order->order_id);
        $sheet->setCellValue('B'.$key, $order->date_created);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->setCellValue('C'.$key, $order->num_items_sold);
        $sheet->setCellValue('D'.$key, $order->net_total);
        $sheet->setCellValue('E'.$key, $order->first_name);
        $sheet->setCellValue('F'.$key, $order->last_name);
        $sheet->setCellValue('G'.$key, $order->email);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->setCellValue('H'.$key, $order->country);
        $sheet->setCellValue('I'.$key, $order->postcode);
    }

    $spreadsheet->createSheet();
    foreach ($items as $itemKey => $item) {
        $spreadsheet->setActiveSheetIndex(1);
        $sheet = $spreadsheet->getActiveSheet();

        $key = $itemKey + 1;
        $sheet->setCellValue('A'.$key, $item->product_id);
        $sheet->setCellValue('B'.$key, $item->product_net_revenue);
        $sheet->setCellValue('C'.$key, $item->products_sum);
        $sheet->setCellValue('D'.$key, $item->net_sum);
    }
        $cell = $key + 1;
        $sheet->setCellValue('D'.$cell, '=SUM(D1:D'.$key.')');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save(wp_get_upload_dir()['basedir'].'/orders/'.date('c').'.xlsx');
}

