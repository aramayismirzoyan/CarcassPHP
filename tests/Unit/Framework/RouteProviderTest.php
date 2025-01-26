<?php

namespace Test\Unit\Framework;

use App\Container\Container;
use App\Container\ContainerFactory;
use App\Helpers\Request;
use App\Services\TestService;
use App\Services\UserService;
use PHPUnit\Framework\TestCase;
use Providers\RouteProvider;

class RouteProviderTest extends TestCase
{
    public function test_simple_routs_get_request()
    {
        $_SERVER["REQUEST_URI"] = '/test_get';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $requestMock = $this->getMockBuilder(Request::class)
            ->onlyMethods(['getJsonData'])
            ->getMock();

        $requestMock->method('getJsonData')
            ->willReturn([]);


        $containerMock = $this->getMockBuilder(Container::class)
            ->onlyMethods(['get'])
            ->getMock();

        $containerMock->method('get')
            ->willReturnCallback(
                fn ($id) =>
                match($id) {
                    UserService::class => 1111,
                    Request::class => $requestMock,
                    default => ''
                }
            );

        $route = new RouteProvider($containerMock);
        $route->run();

        $this->expectOutputString(json_encode([
            'success' => true
        ]));
    }

    public function test_simple_routs_container_binding_in_the_method()
    {
        $_SERVER["REQUEST_URI"] = '/test_check';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $container = ContainerFactory::create();

        $route = new RouteProvider($container);
        $route->run();

        $this->expectOutputString(json_encode([
            'success' => true,
            'method' => 'get'
        ]));
    }

    public function test_routs_with_parameters_container_binding_in_the_method()
    {
        $_SERVER["REQUEST_URI"] = '/test_get/1';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $container = ContainerFactory::create();

        $route = new RouteProvider($container);
        $route->run();

        $this->expectOutputString(json_encode([
            'id' => 1,
            'service' => $container->get(TestService::class)->id()
        ]));
    }

    public function test_routs_with_parameters_container_binding_in_the_method_reverse_parameters()
    {
        $_SERVER["REQUEST_URI"] = '/test_get_reverse/1';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $container = ContainerFactory::create();

        $route = new RouteProvider($container);
        $route->run();

        $this->expectOutputString(json_encode([
            'id' => 1,
            'service' => $container->get(TestService::class)->id()
        ]));
    }

    public function test_routs_with_parameters_container_binding_in_the_method_when_parameter_has_not_type_hint()
    {
        $_SERVER["REQUEST_URI"] = '/test_without_ype_hint/1';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $container = ContainerFactory::create();

        $route = new RouteProvider($container);
        $route->run();

        $this->expectOutputString(json_encode([
            'id' => '1',
            'service' => $container->get(TestService::class)->id()
        ]));
    }
}
