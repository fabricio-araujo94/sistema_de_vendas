<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, string $controllerAction): void
    {
        $this->addRoute("GET", $path, $controllerAction);
    }

    public function post(string $path, string $controllerAction): void
    {
        $this->addRoute("POST", $path, $controllerAction);
    }

    public function addRoute(
        string $method,
        string $path,
        string $controllerAction,
    ): void {
        $this->routes[$method][$path] = $controllerAction;
    }

    public function dispatch(string $uri, string $method): void
    {
        $parsedUri = parse_url($uri, PHP_URL_PATH);

        if (isset($this->routes[$method][$parsedUri])) {
            $action = $this->routes[$method][$parsedUri];

            [$controllerName, $methodName] = explode("@", $action);

            $controllerClass = "\\App\\Controller\\" . $controllerName;

            if (class_exists($controllerClass)) {
                $controllerInstance = new $controllerClass();

                if (method_exists($controllerInstance, $methodName)) {
                    $controllerInstance->$methodName();
                    return;
                }
            }
        }

        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
        echo "<p>The request URL {$parsedUri} was not found on this server.</p>";
    }
}
