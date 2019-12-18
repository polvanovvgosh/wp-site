<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

global $post;

$short_description = apply_filters('woocommerce_short_description', $post->post_excerpt);

if (!$short_description) {
    return;
}

?>
<div class="woocommerce-product-details__short-description">
    <?php echo mb_strimwidth($short_description, 0, 140, "..."); // WPCS: XSS ok. ?>
</div>
