<?php get_header();?>

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

<div class="car-comments-form">
<?php comments_template(); ?>
</div>
<?php get_footer();?>
