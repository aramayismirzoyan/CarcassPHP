<?php

namespace Test\Unit\Framework;

use App\Enums\ValidatorMessages;
use Messages\Handler;
use PHPUnit\Framework\TestCase;

class ValidatorMessagesTest extends TestCase
{
    public function test_getLength_method()
    {
        $length = 5;

        $message = ValidatorMessages::LENGTH->getLength($length);

        $string = Handler::get('validator')['length_start'] .
                ' ' . $length . ' ' .
                Handler::get('validator')['length_end_plural'];

        $this->assertEquals($message, $string);

        $message = ValidatorMessages::REQUIRED->getLength($length);

        $this->assertEquals('', $message);

        $length = 1;

        $message = ValidatorMessages::LENGTH->getLength($length);

        $string = Handler::get('validator')['length_start'] .
            ' ' . $length . ' ' .
            Handler::get('validator')['length_end'];

        $this->assertEquals($message, $string);
    }
}
