<?php

namespace Test\Unit\Framework;

use App\Helpers\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function test_send_method()
    {
        $data = [
            'success' => true
        ];

        $result = Response::send($data);

        $this->assertEquals(json_encode($data), $result);

        $statusCode = http_response_code();

        $this->assertEquals(200, $statusCode);
    }

    public function test_sendValidatorError_method()
    {
        $data = [
            'success' => false
        ];

        $result = Response::sendValidatorError($data);

        $this->assertEquals(json_encode($data), $result);

        $statusCode = http_response_code();

        $this->assertEquals(422, $statusCode);
    }

    public function test_sendNotFoundError_method()
    {
        $data = [
            'success' => false,
            'result' => [
                'error' => "Not found"
            ]
        ];

        $result = Response::sendNotFoundError();

        $this->assertEquals(json_encode($data), $result);

        $statusCode = http_response_code();

        $this->assertEquals(404, $statusCode);
    }

    public function test_sendServerError_method()
    {
        $data = [
            'success' => false,
            'result' => [
                'error' => "Some problems with server"
            ]
        ];

        $result = Response::sendServerError();

        $this->assertEquals(json_encode($data), $result);

        $statusCode = http_response_code();

        $this->assertEquals(500, $statusCode);
    }
}
