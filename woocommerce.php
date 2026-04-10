<?php 
if (!class_exists('Timber')) {
    echo 'Timber not activated. Make sure you activate the plugin in <a href="/wp-admin/plugins.php#timber">/wp-admin/plugins.php</a>';

    return;
}

$context = Timber::context();
$context['sidebar'] = Timber::get_widgets('shop-sidebar');

if (is_singular('product')) {
    $context['post'] = Timber::get_post();
    $product = wc_get_product($context['post']->ID);
    $context['product'] = $product;

    // Get related products
    $related_limit = wc_get_loop_prop('columns');
    // Get related product IDs
    $related_ids = wc_get_related_products($context['post']->ID, $related_limit);

    // Filter out out-of-stock products
    $related_ids = array_filter($related_ids, function($product_id) {
        $product = wc_get_product($product_id);
        return $product && $product->is_in_stock();
    });

    // Get Timber posts
    $context['related_products'] = Timber::get_posts($related_ids)->to_array();

    // Restore the context and loop back to the main query loop.
    wp_reset_postdata();

    Timber::render('views/woo/single-product.twig', $context);
} else {
    $context = Timber::context();
    $posts = Timber::get_posts();
    $context['products'] = $posts;

    if (is_product_category()) {
        $queried_object = get_queried_object();
        $term_id = $queried_object->term_id;
        $context['category'] = get_term($term_id, 'product_cat');
        $context['title'] = single_term_title('', false);
    }

    Timber::render('views/woo/archive.twig', $context);
}
?>
