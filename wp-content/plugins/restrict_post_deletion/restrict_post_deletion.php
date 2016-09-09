<?php
/*
Plugin Name:  Delete only empty car
Version: 0.1
Author: andrukevich
*/



function is_product_has_orders($post_ID){
    global $wpdb;

    $res = $wpdb->get_var(
        $wpdb->prepare(
            " SELECT  order_post.ID
    FROM wp_woocommerce_order_itemmeta AS oim
    INNER JOIN wp_woocommerce_order_items AS oi ON (oi.order_item_id = oim.order_item_id)
    INNER JOIN  wp_posts AS order_post  ON (order_post.ID = oi.order_id)
       WHERE  oim.meta_value = %d  AND meta_key = '_product_id'
    AND order_post.post_status IN ('wc-pending', 'wc-processing', 'wc-on-hold')",
            $post_ID
        )
    );

    return (bool)$res;
}


function restrict_post_deletion(){

    global $post;

    if($post->post_type == 'product' && is_product_has_orders($post->ID)) {


        echo "<h1>Remove impossible . The machine is in the order</h1>>";
        exit;
    }
}


add_action('wp_trash_post', 'restrict_post_deletion');
add_action('delete_post', 'restrict_post_deletion');

