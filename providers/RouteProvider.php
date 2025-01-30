<?php

namespace Providers;

use App\Container\Container;
use App\Enums\ConfigsPaths;
use App\Exceptions\ContainerException;
use App\Helpers\Request;
use App\Helpers\Response;
use Exception;
use ReflectionClass;
use ReflectionException;

class RouteProvider
{
    private array $config;

    public function __construct(private readonly Container $container)
    {
        $this->config = include(ConfigsPaths::ROUTES->get());
    }

    private function hasAction($controller, $action): bool
    {
        return class_exists($controller) && method_exists($controller, $action);
    }

    private function getRouteByUserId()
    {
        $request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if (preg_match("#^/(\w+)/(\d+)$#", $request, $matches)) {
            return [
                'path' => $matches[1],
                'id' => $matches[2]
            ];
        } else {
            return false;
        }
    }

    private function runAction($uri, $config, $id = false): string
    {
        if (array_key_exists($uri, $config)) {
            $controller = '\App\Controllers\\' . $config[$uri][0];
            $action = $config[$uri][1];
            $method = $config[$uri][2];

            try {
                $request = $this->container->get(Request::class);
            } catch (Exception $e) {
                return Response::sendServerError();
            }

            if ($this->hasAction($controller, $action) && $request->isValidType($method)) {
                return $this->bind($controller, $action, $id);
            } else {
                return Response::sendNotFoundError();
            }
        } else {
            return Response::sendNotFoundError();
        }
    }

    private function handleRoutesWithParameters(): string
    {
        $route = $this->getRouteByUserId();

        if (!is_array($route)) {
            throw new Exception('This route hasn\'t parameters');
        }

        $uri = '/'.$route['path'];

        $config = $this->config['with_parameter'];

        return $this->runAction($uri, $config, $route['id']);
    }

    private function createControllerReflection($controller): ReflectionClass
    {
        try {
            $reflector = new ReflectionClass($controller);
        } catch (Exception $e) {
            throw new Exception('The class is not fount');
        }

        return $reflector;
    }

    private function invokeMethodArgs(ReflectionClass $reflector, $arguments, $action)
    {
        try {
            $request = $this->container->get(Request::class);
            $instance = $reflector->newInstanceArgs([$request]);
            return $reflector->getMethod($action)->invokeArgs($instance, $arguments);
        } catch (ContainerException $e) {
            throw new Exception('Request is not resolved by dependency container');
        } catch (ReflectionException $e) {
            throw new Exception($e);
        }
    }

    private function createInvocableArguments($parameters, $id): array
    {
        $id = $id ? $id : 0;

        $arguments = [];

        foreach ($parameters as $parameter) {
            $hintType = $parameter->getType()?->getName() ?? 'int';

            $isSimpleParameter = $parameter->getType()?->isBuiltin() ?? true;

            if ($isSimpleParameter) {
                $arguments[] = match ($hintType) {
                    'int' => $id,
                    default => ''
                };
            } else {
                $classname = $parameter->getType()->getName();
                try {
                    $arguments[] = $this->container->get($classname);
                } catch (Exception $e) {
                    throw new Exception("$classname is not resolved by dependency container");
                }
            }
        }

        return $arguments;
    }

    private function bind($controller, $action, $id): string
    {
        try {
            $reflector = $this->createControllerReflection($controller);
            $parameters = $reflector->getMethod($action)->getParameters();
            $arguments = $this->createInvocableArguments($parameters, $id);
            return $this->invokeMethodArgs($reflector, $arguments, $action);
        } catch (Exception $e) {
            return Response::sendServerError();
        }
    }

    private function handleSimpleRoutes(): string
    {
        $requestUri = strtok($_SERVER["REQUEST_URI"], '?');

        $config = $this->config['simple'];

        return $this->runAction($requestUri, $config);
    }

    public function run(): string
    {
        try {
            return $this->handleRoutesWithParameters();
        } catch (Exception $e) {
            try {
                return $this->handleSimpleRoutes();
            } catch (Exception $e) {
                return Response::sendServerError();
            }
        }
    }
}
