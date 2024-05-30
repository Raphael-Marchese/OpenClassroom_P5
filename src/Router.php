<?php

namespace App;

use App\controllers\PostController;
use App\controllers\HomeController;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Router
{
    private array $routes;

    public function __construct()
    {
        $this->routes = [
            '/' => [HomeController::class, 'render'], // Route pour /
            '/list' => [PostController::class, 'getCollection'], // Route pour /list
            '/post/submit/create' => [PostController::class, 'submitCreate'], // Route pour /soumettre la création d'un post
            '/post/create' => [PostController::class, 'createPost'], // Route pour /post/create
            '/post/(\d+)' => [PostController::class, 'getPost'], // Route pour /post/id
            '/404' => Error404::class,
        ];
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws \ReflectionException
     */
    public function callController(string $path): void
    {
        foreach($this->routes as $route => $controller) {
            if ($path === $route) {

                $controllerClass =  $controller[0];
                $method = $controller[1];
                $controllerInstance = new $controllerClass();
                $controllerInstance->$method();
                return;
            }

            if (preg_match('#^' . $route . '$#', $path, $matches)) {
                $controllerClass = $controller[0];
                $method = $controller[1];
                $controllerInstance = new $controllerClass();
                $pathId = (int) $matches[1];
                $controllerInstance->$method($pathId);
                return;
            }
        }
        $error404Controller = new Error404();
        $error404Controller->render();
    }
}
