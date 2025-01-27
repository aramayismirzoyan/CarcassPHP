<?php

namespace Providers;

use App\Container\Container;
use App\Enums\ConfigsPaths;
use App\Helpers\Request;
use App\Helpers\Response;
use Exception;
use ReflectionClass;
use ReflectionMethod;

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

    private function handleRoutesWithParameters(): string
    {
        $route = $this->getRouteByUserId();

        if (!is_array($route)) {
            throw new Exception();
        }

        $uri = '/'.$route['path'];

        $config = $this->config['with_parameter'];

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
                return $this->bind($controller, $action);
            } else {
                return Response::sendNotFoundError();
            }
        } else {
            return Response::sendNotFoundError();
        }
    }

    private function bind($controller, $action): string
    {
        $id = 0;

        $route = $this->getRouteByUserId();

        if (is_array($route)) {
            $id = $route['id'];
        }

        try {
            $request = $this->container->get(Request::class);
        } catch (Exception $e) {
            return Response::sendServerError();
        }

        $reflector = new ReflectionClass($controller);

        $instance = $reflector->newInstanceArgs([$request]);

        $reflectionMethod = new ReflectionMethod($controller, $action);

        $parameters = $reflector->getMethod($action)->getParameters();

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
                try {
                    $arguments[] = $this->container->get($parameter->getType()->getName());
                } catch (Exception $e) {
                    return Response::sendServerError();
                }
            }
        }

        return $reflectionMethod->invokeArgs($instance, $arguments);
    }

    private function handleSimpleRoutes(): string
    {
        $requestUri = strtok($_SERVER["REQUEST_URI"], '?');

        $config = $this->config['simple'];

        if (array_key_exists($requestUri, $config)) {
            $controller = '\App\Controllers\\' . $config[$requestUri][0];
            $action = $config[$requestUri][1];
            $method = $config[$requestUri][2];

            try {
                $request = $this->container->get(Request::class);
            } catch (Exception $e) {
                return Response::sendServerError();
            }

            if ($this->hasAction($controller, $action) && $request->isValidType($method)) {
                return $this->bind($controller, $action);
            } else {
                return Response::sendNotFoundError();
            }
        } else {
            return Response::sendNotFoundError();
        }
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
