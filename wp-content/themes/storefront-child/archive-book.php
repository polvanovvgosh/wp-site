<?php
/**
 * Template Name: Books Template
 */
get_header();

$books = new WP_Query(['post_type' => 'book', 'posts_per_page' => -1]);

?>
<div class="wrap">
    <?php
    if ($books->have_posts()) :
        while ($books->have_posts()) :
            $books->the_post(); ?>
            <div class="list-item">
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <div class="">
                    <a href="<?php the_permalink(); ?>">
                        <img class="book-single-image" src="<?php the_field('book_cover'); ?>" alt="">
                    </a>
                </div>
                <p>Цена: <?php the_field('book_cost'); ?></p>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>

<?php get_footer();?>
