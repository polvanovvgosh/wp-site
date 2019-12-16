<?php
/**
 * Template Name: Cars Template
 */

get_header();

$cars = new WP_Query(['post_type' => 'car', 'posts_per_page' => -1]);

?>
    <div class="wrap">
        <?php
        if ($cars->have_posts()) :
            while ($cars->have_posts()) :
                $cars->the_post(); ?>
                <div class="list-item">
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <div class="car-photo">
                        <a href="<?php the_permalink(); ?>">
                            <img class="car-single-image" src="<?php the_field('car_photo'); ?>" alt="">
                        </a>
                    </div>
                    <p><?php the_field('car_cost'); ?></p>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>

    </div>

<?php
get_footer();