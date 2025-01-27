<?php

namespace App\Container;

use App\Helpers\Request;
use App\Repositories\UserRepository;
use App\Services\TestService;
use App\Services\UserService;
use Providers\PDOProvider;

class ContainerFactory
{
    public static function create(): Container
    {
        $container = new Container();

        $container->set(UserService::class, function ($container) {
            $connection = $container->get(PDOProvider::class);
            ;
            $repository = new UserRepository($connection);
            return new UserService($repository);
        });

        $container->set(Request::class, function () {
            return new Request();
        });

        $container->set(TestService::class, function () {
            return new TestService();
        });

        $container->set(UserRepository::class, function ($container) {
            $connection = $container->get(PDOProvider::class);
            return new UserRepository($connection);
        });

        $container->set(PDOProvider::class, function () {
            return PDOProvider::create();
        });

        return $container;
    }
}
