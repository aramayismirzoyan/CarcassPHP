<?php


$testConfig =  [
    'simple' => [
        '/test_get' => ['TestController', 'get', 'GET'],
        '/test_check' => ['TestController', 'check', 'GET'],
    ],

    'with_parameter' => [
        '/test_get' => ['TestController', 'show', 'GET'],
        '/test_get_reverse' => ['TestController', 'showReverse', 'GET'],
        '/test_without_ype_hint' => ['TestController', 'showWithoutTypeHint', 'GET'],
    ]
];
