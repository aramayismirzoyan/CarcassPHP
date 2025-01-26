<?php

namespace App\Controllers;

use App\Helpers\Request;
use App\Services\TestService;

class TestController
{
    public function __construct(private readonly Request $request)
    {
    }

    public function get()
    {
        return json_encode([
            'success' => true
        ]);
    }

    public function check(TestService $service, int $id)
    {
        return $service->get();
    }

    public function show(TestService $service, int $id)
    {
        return json_encode([
            'id' => $id,
            'service' => $service->id()
        ]);
    }

    public function showReverse(int $testId, TestService $service)
    {
        return json_encode([
            'id' => $testId,
            'service' => $service->id()
        ]);
    }

    public function showWithoutTypeHint($testId, TestService $service)
    {
        return json_encode([
            'id' => $testId,
            'service' => $service->id()
        ]);
    }
}
