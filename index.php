<?php

use App\UserController;
use Providers\RouteProviders;

ini_set('display_errors', 1);
require './vendor/autoload.php';

$route = new RouteProviders();
$route->run();