<?php

namespace App\Services;

class TestService
{
    public function get()
    {
        return json_encode([
            'success' => true,
            'method' => 'get'
        ]);
    }

    public function id()
    {
        return 'test_id';
    }
}
