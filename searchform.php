<?php
use Timber\Timber;

$context = Timber::context();

Timber::render( 'partials/searchform.twig', $context );
?>
