<?php
/**
 * Plugin Name: Money exchange rates
 * Description: Scraping money exchanges from nbk.kg
 * Version: 1.0
 */

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
        $title = apply_filters('widget_title', $instance['title']);
        echo $args['before_widget'];

        if (!empty($title)) {
            echo $args['before_title'].$title.$args['after_title'];
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.nbkr.kg');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $content = curl_exec($ch);
        curl_close($ch);

        preg_match("/<div.*id=\"sticker-exrates\".*>(.*|\n)*\s<\/div>/", $content,$match);
        $fields = explode('<td ',$match[0]);
        unset($fields[0]);

        $results = [];
        $i = 0;
        foreach ($fields as $field) {
            if (preg_match('/>(.*)</', $field, $finalMatch)) {
                $results[$i][] = $finalMatch[1];
                if (count($results[$i]) % 4 === 0) {
                    $i++;
                }
            }else{
                continue;
            }
        }

        echo '<table class="exchange-rates-table">';
        echo '<thead class="exchange-rates-table">';
        echo '<tr>';
        echo '<td>Валюта</td>';
        echo '<td>Сегодня</td>';
        echo '<td>Завтра</td>';
        echo '<td>Разница</td>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody   class="exchange-rates-table">';
        foreach ($results as $result) {
            echo '<tr>';
            echo '<td>'.$result[0].'</td>';
            echo '<td>'.$result[1].'</td>';
            echo '<td>'.$result[2].'</td>';
            echo '<td>'.$result[3].'</td>';
            echo '</tr>';
        }
        echo '<tbody>';
        echo '</table>';
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
