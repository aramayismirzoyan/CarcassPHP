<?php

require 'test_routes.php';

return [
    'simple' => [
        '/create' => ['UserController', 'create', 'POST'],
        '/get' => ['UserController', 'get', 'GET'],
        '/delete' => ['UserController', 'delete', 'DELETE'],
        ...$testConfig['simple']
    ],
    'with_parameter' => [
        '/get' => ['UserController', 'show', 'GET'],
        '/update' => ['UserController', 'update', 'PATCH'],
        '/delete' => ['UserController', 'deleteById', 'DELETE'],
        ...$testConfig['with_parameter']
    ]
];
