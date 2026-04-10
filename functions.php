<?php

/**
 * Functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 * @link https://github.com/timber/starter-theme
 */

namespace App;

use Timber\Timber;
use App\Classes\StarterSite;
use App\Classes\GallaidesignTheme;

// Load Composer dependencies.
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/classes/timber.class.php';
require_once __DIR__ . '/classes/theme.class.php';

Timber::init();

new StarterSite();
new GallaidesignTheme();

if( function_exists('acf_add_options_page') ) {
    acf_add_options_page(array(
        'page_title'    => 'Téma beállítások',
        'menu_title'    => 'Téma beállítások',
        'menu_slug'     => 'theme-general-settings',
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));

    acf_add_options_sub_page(array(
        'page_title'    => 'Főoldal beállítások',
        'menu_title'    => 'Főoldal beállítások',
		'menu_slug'     => 'theme-index-settings',
        'parent_slug'   => 'theme-general-settings',
    ));
}

// Woocommerce support
function theme_add_woocommerce_support()
{
	add_theme_support( 'woocommerce', [
		'single_image_width' => 480,
	] );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}

add_action('after_setup_theme', 'App\\theme_add_woocommerce_support');

function timber_set_product($post)
{
    global $product;

    if (is_woocommerce()) {
        $product = wc_get_product($post->ID);
    }
}

add_action( 'woocommerce_before_add_to_cart_quantity', 'App\\qty_front_add_cart' );
 
function qty_front_add_cart() {
	echo '<div class="qty">Mennyiség: </div>'; 
}

function custom_my_account_menu_items( $items ) {
    unset($items['downloads']);
    return $items;
}
add_filter( 'woocommerce_account_menu_items', 'App\\custom_my_account_menu_items' );

// In functions.php - this is the most reliable approach
add_filter('posts_clauses', function($clauses, $query) {
    if (!is_admin() && $query->is_main_query() && (is_product_category() || is_shop())) {
        global $wpdb;

        if (strpos($clauses['join'], 'stock_meta') === false) {
            $clauses['join'] .= " LEFT JOIN {$wpdb->postmeta} AS stock_meta 
                ON ({$wpdb->posts}.ID = stock_meta.post_id 
                AND stock_meta.meta_key = '_stock_status')";
        }

        $existing_orderby = !empty($clauses['orderby']) ? ', ' . $clauses['orderby'] : '';
        $clauses['orderby'] = "CASE 
            WHEN stock_meta.meta_value = 'instock' THEN 0 
            ELSE 1 
        END ASC" . $existing_orderby;
    }

    return $clauses;
}, 999, 2);

// Woocoomerce - Show 25 products per page
add_filter('loop_shop_per_page', function($cols) {
    return 25;
}, 20);

// ============================================================
// MY ACCOUNT — FAVOURITES TAB
// ============================================================

/**
 * Register the new tab in My Account menu.
 */
add_filter( 'woocommerce_account_menu_items', function( $items ) {
    $new = [];
    foreach ( $items as $key => $label ) {
        $new[ $key ] = $label;
        if ( $key === 'dashboard' ) {
            $new['favourites'] = __( 'Kedvencek', 'fishingfanatic-v2-theme' );
        }
    }
    return $new;
});

/**
 * Register the endpoint.
 */
add_action( 'init', function() {
    add_rewrite_endpoint( 'favourites', EP_ROOT | EP_PAGES );
});

/**
 * Render the favourites tab content.
 */
add_action( 'woocommerce_account_favourites_endpoint', function() {
    $user_id = get_current_user_id();
    $fav_ids = get_user_meta( $user_id, '_favourites', true );

    if ( empty( $fav_ids ) || ! is_array( $fav_ids ) ) {
        echo '<p>' . __( 'Még egy terméket sem raktál be a kedvencek közé.', 'fishingfanatic-v2-theme' ) . '</p>';
        return;
    }

    $query = new \WP_Query([
        'post_type'      => 'product',
        'post__in'       => $fav_ids,
        'orderby'        => 'post__in',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ]);

    if ( ! $query->have_posts() ) {
        echo '<p>' . __( 'No products found.', 'fishingfanatic-v2-theme' ) . '</p>';
        return;
    }

    echo '<h2>' . __( 'Kedvencek', 'fishingfanatic-v2-theme' ) . '</h2>';
    echo '<ul class="ff-favourites-list">';

    while ( $query->have_posts() ) {
        $query->the_post();
        $product = wc_get_product( get_the_ID() );
        if ( ! $product ) continue;

        echo '<li class="ff-fav-item">';
        echo '<a href="' . get_permalink() . '">';
        echo get_the_post_thumbnail( get_the_ID(), 'thumbnail' );
        echo '<span>' . get_the_title() . '</span>';
        echo '</a>';
        echo '<span class="ff-fav-price">' . $product->get_price_html() . '</span>';
        echo '<button class="fav-btn active ff-remove-fav" data-product-id="' . get_the_ID() . '" aria-label="Eltávolítás a kedvencek közül">';
        echo '&#10005; Eltávolítás';
        echo '</button>';
        echo '</li>';
    }

    wp_reset_postdata();
    echo '</ul>';
});

add_filter( 'posts_search', 'App\\search_by_sku_and_title', 10, 2 );
function search_by_sku_and_title( $search, $wp_query ) {
    global $wpdb;

    if ( ! is_admin() && $wp_query->is_search() && ! empty( $search ) ) {

        $search_term = $wp_query->query_vars['s'];

        $search .= " OR EXISTS (
            SELECT 1 FROM {$wpdb->postmeta}
            WHERE post_id = {$wpdb->posts}.ID
            AND meta_key = '_sku'
            AND meta_value LIKE '%" . esc_sql( $search_term ) . "%'
        )";
    }

    return $search;
}