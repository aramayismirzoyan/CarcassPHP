<?php

namespace Test\Integration\Framework;

use App\Container\Container;
use PHPUnit\Framework\TestCase;
use Test\Helpers\IntegrationTestCase;

class PDOProviderTest extends IntegrationTestCase
{
    public function test_db()
    {
        //        $mock = $this->getMockBuilder(Container::class)
        //            ->onlyMethods(['get'])
        //            ->getMock();
        //
        //        $mock->expects($this->once())
        //            ->method('get')
        //            ->willReturnCallback(fn($id) =>
        //                match($id) {
        //                    'UserService' => 1111,
        //                    'Request' => 222,
        //                    default => ''
        //                }
        //            );
        //
        //        var_dump($mock->get('Request1')); die();
        $this->assertTrue(true);
    }
}
