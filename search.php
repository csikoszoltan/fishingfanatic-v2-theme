<?php

/**
 * Search results page
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 */

use Timber\Timber;

$templates = array( 'partials/search.twig', 'posts/archive.twig', 'index/index.twig' );

$context = Timber::context([
   'title' => 'Keresési eredmények a következőre ' . get_search_query(),
   'posts' => Timber::get_posts()
]);

Timber::render($templates, $context);
