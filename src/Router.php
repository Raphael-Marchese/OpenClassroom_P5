<?php

namespace App;



use App\controllers\Homepage;

class Router
{
    private array $routes;

    public function __construct()
    {
        $this->routes = [
            '/' => Homepage::class,
            '/bar' => Bar::class,
            '/404' => Error404::class,
        ];
    }

    public function callController(string $path): void
    {
        $class = $this->routes[$path] ?? $this->routes['/404'];

        /** @var Homepage $controller */
        $controller = new $class();
        $controller->render();
    }
}