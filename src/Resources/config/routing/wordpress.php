<?php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$collection = new RouteCollection();
$collection->add(
    'qce_wordpress_catch_all',
    new Route('/{path<.*>}', ['_controller' => 'qce_wordpress.wordpress.controller']),
    -10,
);

return $collection;
