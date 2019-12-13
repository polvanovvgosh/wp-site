<?php
/**
 * Plugin Name: Money exchange rates
 * Description: Scraping money exchanges from nbk.kg
 * Version: 1.0
 */

require_once(__DIR__.'/get-current-rates.php');

register_activation_hook(__FILE__, 'exchange_rates_install');
register_activation_hook(__FILE__, 'setCronSchedule');

function setCronSchedule()
{
    if (!wp_next_scheduled('scraping_rates_hook')) {
        wp_schedule_event(time(), 'twicedaily', 'scraping_rates_hook');
    }
}

global $rates_db_version;
$rates_db_version = "1.0";

function exchange_rates_install()
{
    global $wpdb;
    global $rates_db_version;

    $table_name = $wpdb->prefix."exchange_rates";
    if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {

        $sql = "CREATE TABLE ".$table_name." (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  currency VARCHAR(10) NOT NULL,
	  first_rate VARCHAR(10) NOT NULL,
	  second_rate VARCHAR(10) NOT NULL,
	  difference VARCHAR(10) NOT NULL,
	  UNIQUE KEY id (id)
	);";

        require_once(ABSPATH.'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        add_option("exchange_db_version", $rates_db_version);
    }
}

function exchange_rates_widget()
{
    register_widget('exchange_widget');
}

add_action('widgets_init', 'exchange_rates_widget');

class exchange_widget extends WP_Widget
{

    function __construct()
    {
        parent::__construct(
            'exchange_widget',
            __('Exchange rates widget', 'exchange_widget_domain'),
            ['description' => __('Gets money exchange rates every day', 'exchange_widget_domain'),]
        );
    }

    public function widget($args, $instance)
    {
        global $wpdb;
        $title = apply_filters('widget_title', $instance['title']);
        echo $args['before_widget'];

        if (!empty($title)) {
            echo $args['before_title'].$title.$args['after_title'];
        }

        $rates = $wpdb->get_results('select * from wp_exchange_rates order by id desc limit 4');

        $first_rate  = date('j.n.Y', mktime(0, 0, 0, date("m"), date("d"), date("Y")));
        $second_rate = date('j.n.Y', mktime(0, 0, 0, date("m"), date("d") + 1, date("Y")));
        if ((int)(date('j') % 2)) {
            $first_rate  = date('j.n.Y', mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
            $second_rate = date('j.n.Y', mktime(0, 0, 0, date("m"), date("d"), date("Y")));
        }

        echo '<table class="exchange-rates-table">'.
            '<thead class="exchange-rates-table">'.
            '<tr>'.
            '<td>Валюта</td>'.
            '<td>'.$first_rate.'</td>'.
            '<td>'.$second_rate.'</td>'.
            '<td>Разница</td>'.
            '</tr>'.
            '</thead>'.
            '<tbody   class="exchange-rates-table">';
        foreach ($rates as $rate) {
            echo '<tr>'.
                '<td>'.$rate->currency.'</td>'.
                '<td>'.$rate->first_rate.'</td>'.
                '<td>'.$rate->second_rate.'</td>'.
                '<td>'.$rate->difference.'</td>'.
                '</tr>';
        }
        echo '<tbody>'.
            '</table>';

        echo $args['after_widget'];
    }

    public function form($instance)
    {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('Заголовок!', 'exchange_widget_domain');
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>"/>
        </p>
        <?php
    }


    public function update($new_instance, $old_instance)
    {
        $instance          = [];
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';

        return $instance;
    }
}

?>
