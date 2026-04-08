<?php

/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 */

use Timber\Timber;

$templates = array('index/index.twig');

if (is_home()) {
	array_unshift( $templates, 'index/front-page.twig', 'index/home.twig' );
}

$popular_items = Timber::get_posts([
	'posts_per_page' => 6,
	'post_type' => 'product',
	'post_status' => 'publish',
	'ignore_sticky_posts' => 1,
	'meta_key' => 'total_sales',
	'orderby' => 'meta_value_num',
	'order' => 'DESC',
	'meta_query' => [
        [
            'key'     => '_stock_status',
            'value'   => 'instock',
            'compare' => '='
        ]
    ]
]);

$latest_items = Timber::get_posts([
	'posts_per_page' => 20,
	'post_type' => 'product',
	'post_status' => 'publish',
	'ignore_sticky_posts' => 1,
	'order' => 'DESC',
]);

$mr_fisher = Timber::get_posts([
	'posts_per_page' => 20,
	'post_type' => 'product',
	'post_status' => 'publish',
	'ignore_sticky_posts' => 1,
	'order' => 'DESC',
	'tax_query' => [
		[
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => ['mr-fisher-ajanlasaval'],
			'operator' => 'IN',
		],
	],
]);

$context = Timber::context([
	'szolgaltatasok' => get_field('szolgaltatasok', 'options'),
	'bannerek' => get_field('bannerek', 'options'),
	'kiemelt_markaink' => get_field('kiemelt_markaink', 'options'),
	'rolunk_hatterkep' => get_field('rolunk_hatterkep', 'options'),
	'rolunk_szoveg' => get_field('rolunk_szoveg', 'options'),
	'nepszeru_termekek_banner' => get_field('nepszeru_termekek_banner', 'options'),
	'legujabb_termekeink_hatterkep' => get_field('legujabb_termekeink_hatterkep', 'options'),
	'popular_items' => $popular_items->to_array(),
	'latest_items' => $latest_items->to_array(),
	'index_slider' => get_field('slider', 'options'),
	'mr_fisher' => $mr_fisher->to_array(),
]);

Timber::render($templates, $context);
