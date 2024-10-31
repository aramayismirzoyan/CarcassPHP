<?php

use App\UserController;
use Providers\RouteProviders;

require './vendor/autoload.php';

$route = new RouteProviders();
$route->run();