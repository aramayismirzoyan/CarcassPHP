<?php

namespace Test\Helpers;

use App\Container\ContainerFactory;
use App\Helpers\Request;
use PHPUnit\Framework\TestCase;
use Providers\PDOProvider;
use Providers\RouteProvider;

class IntegrationTestCase extends TestCase
{
    public function getRequestMock($data)
    {
        $mock = $this->getMockBuilder(Request::class)
            ->onlyMethods(['getJsonData'])
            ->getMock();

        $mock->method('getJsonData')
            ->willReturn($data);

        return $mock;
    }

    private function setUrl(string $uri, string $method): void
    {
        $_SERVER["REQUEST_URI"] = $uri;
        $_SERVER['REQUEST_METHOD'] = $method;
    }

    public function setUp(): void
    {
        PDOProvider::create()->truncateTable('users');
    }

    public function tearDown(): void
    {
        PDOProvider::create()->truncateTable('users');
    }

    private function runAction($parameters): string
    {
        $request = $this->getRequestMock($parameters);

        $container = ContainerFactory::create();
        $container->set(Request::class, fn () => $request);

        $route = new RouteProvider($container);

        return $route->run();
    }

    public function get(string $uri, array $parameters = []): string
    {
        $this->setUrl($uri, 'GET');

        return $this->runAction($parameters);
    }

    public function post(string $uri, array $parameters = []): string
    {
        $this->setUrl($uri, 'POST');

        return $this->runAction($parameters);
    }

    public function patch(string $uri, array $parameters = []): string
    {
        $this->setUrl($uri, 'PATCH');

        return $this->runAction($parameters);
    }

    public function delete(string $uri, array $parameters = [])
    {
        $this->setUrl($uri, 'DELETE');

        return $this->runAction($parameters);
    }
}
