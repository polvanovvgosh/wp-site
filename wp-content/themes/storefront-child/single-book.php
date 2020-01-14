<?php get_header();
global $post;
?>
<div><img class="book-cover" src="<?php the_field('book_cover'); ?>" alt=""></div>
<div>
    <p class="book-field"><?php echo __('Author:'); ?>  <?php the_field('book_author'); ?></p>
    <p class="book-field"><?php echo __('Pages:'); ?> <?php the_field('boot_pages'); ?></p>
    <p class="book-field"><?php echo __('Comment'); ?>: <?php the_field('book_cost'); ?> руб.</p>
</div>


<div class="car-comments-form">
    <?php comments_template(); ?>
</div>
<?php
if (have_posts()) {
    the_post();
    the_content();
}
?>

<?php get_footer(); ?>
