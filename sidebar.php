<?php

use Timber\Site;
use Timber\Timber;

$context = Timber::context();
$site = new Site();

Timber::render( array( 'partials/sidebar.twig' ), $context );
