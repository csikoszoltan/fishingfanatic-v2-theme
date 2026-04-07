<?php

/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 */

namespace App;

use Timber\Timber;

$templates = array('posts/archive.twig', 'index/index.twig');

$title = 'Archive';
if (is_day()) {
	$title = 'Archive: ' . get_the_date('D M Y');
} elseif (is_month()) {
	$title = 'Archive: ' . get_the_date('M Y');
} elseif (is_year()) {
	$title = 'Archive: ' . get_the_date('Y');
} elseif (is_tag()) {
	$title = single_tag_title('', false);
} elseif (is_category()) {
	$title = single_cat_title( '', false );
	
	$context['category_post'] = Timber::get_posts([
	    'post_type'         =>  'post',
	    'post_status'       =>	'publish',
	    'cat'				=>	get_query_var( 'cat' ),
	    'posts_per_page'    =>  -1,
	    'order'             =>  'DESC',
	    'orderby'           =>	'date',
	]);
	array_unshift( $templates, 'posts/category.twig' );
} elseif (is_post_type_archive()) {
	$title = post_type_archive_title('', false);
	array_unshift($templates, 'posts/archive-' . get_post_type() . '.twig');
}

$context = Timber::context([
	'title' => $title,
]);

Timber::render($templates, $context);
