<?php get_header();?>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC728jq3e_oIgK3SWWathYg__zGsoTY8EQ"></script>

<div><img class="car-photo" src="<?php the_field('car_photo'); ?>" alt=""></div>
<div id="visual characteristics">
    <p class="car-field">Цвет: <?php the_field('car_color'); ?></p>
    <p class="car-field">Двери: <?php the_field('number_of_car_doors'); ?></p>
</div>

<div id="specifications">
    <p class="car-field">Обьем двигателя: <?php the_field('car_engine_volume'); ?></p>
    <p class="car-field">Трансмиссия: <?php the_field('car_transmission'); ?></p>
    <p class="car-field">Мощность: <?php the_field('car_power'); ?></p>
    <p class="car-field">Цена: <?php the_field('car_cost'); ?></p>
</div>

<?php
global $post;
$location = get_field('car_google_maps', $post->ID);
if( $location ): ?>
    <div class="acf-map" data-zoom="16">
        <div class="marker" data-lat="<?php echo esc_attr($location['lat']); ?>" data-lng="<?php echo esc_attr($location['lng']); ?>"></div>
    </div>
<?php endif; ?>

<div class="car-comments-form">
<?php comments_template(); ?>
</div>

<?php get_footer();?>
