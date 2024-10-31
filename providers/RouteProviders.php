<?php

namespace Providers;

use App\Helpers\Request;
use App\Helpers\Response;
use App\UserController;
use Exception;

class RouteProviders
{
    private array $config;
    public function __construct()
    {
        $this->config = include('./config/routes.php');
    }

    private function hasAction($controller, $action): bool
    {
        return class_exists($controller) && method_exists($controller, $action);
    }

    private function getRouteByUserId()
    {
        $request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if (preg_match("#^/(\w+)/(\d+)$#", $request, $matches)) {
            $route = $matches[1];
            $user_id = $matches[2];

            return [
                'path' => $matches[1],
                'user_id' => $matches[2]
            ];
        } else {
            return false;
        }
    }

    private function handleRoutesWIthParameters(): bool
    {
        $route = $this->getRouteByUserId();

        if(is_array($route)) {

            $uri = '/'.$route['path'];

            $config = $this->config['with_parameter'];

            if(array_key_exists($uri, $config)) {
                $controller = 'App\\' . $config[$uri][0];
                $action = $config[$uri][1];
                $method = $config[$uri][2];

                if($this->hasAction($controller, $action) && Request::isValidType($method)) {
                    (new UserController())->$action($route['user_id']);
                } else {
                    Response::sendNotFoundError();
                }
            } else {
                Response::sendNotFoundError();
            }

            return true;
        }

        return false;
    }

    public function run(): void
    {
        $requestUri = strtok($_SERVER["REQUEST_URI"], '?');

        if($this->handleRoutesWIthParameters()) {
            return;
        }

        $config = $this->config['simple'];

        if(array_key_exists($requestUri, $config)) {
                $controller = 'App\\' . $config[$requestUri][0];
                $action = $config[$requestUri][1];
                $method = $config[$requestUri][2];

                if($this->hasAction($controller, $action) && Request::isValidType($method)) {
                    (new $controller)->$action();
                } else {
                    Response::sendNotFoundError();
                }
        } else {
            Response::sendNotFoundError();
        }
    }
}