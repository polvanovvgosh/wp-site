<?php
/*
 * Template Name: home-page
 * */
get_header();
?>
    <div>
        <?php
        if (have_posts()) {
            the_post();
            if (function_exists('dynamic_sidebar')) {
                dynamic_sidebar('sidebar-top-sidebar');
            }
            the_content();
            if (function_exists('dynamic_sidebar')) {
                dynamic_sidebar('sidebar-above-footer');
            }

        }
        ?>
    </div>

<?php
get_footer();
?>