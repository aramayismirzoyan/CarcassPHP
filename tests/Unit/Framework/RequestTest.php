<?php

namespace Test\Unit\Framework;

use App\Helpers\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function test_getParam_method()
    {
        $data = [
            'name' => 'John'
        ];

        $request = new Request();

        $param = $request->getParam($data, 'name');

        $this->assertEquals($data['name'], $param);

        $param = $request->getParam($data, 'age');

        $this->assertEquals('', $param);
    }

    public function test_isValidType_method()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $result = (new Request())->isValidType('GET');

        $this->assertTrue($result);
    }
}
