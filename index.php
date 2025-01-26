<?php

use App\Container\ContainerFactory;
use Providers\RouteProvider;

require './vendor/autoload.php';

$container = ContainerFactory::create();
$route = new RouteProvider($container);
$route->run();
