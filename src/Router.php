<?php

namespace App;

use App\controllers\PostController;
use App\controllers\HomeController;
use App\controllers\UserController;
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
            '/post/(\d+)' => [PostController::class, 'getPost'], // Route pour /post/id
            '/register' => [UserController::class, 'register'],
            '/register/submit' => [UserController::class, 'submitRegister'],
            '/login' => [UserController::class, 'login'],
            '/login/submit' => [UserController::class, 'submitLogin'],
            '/logout' => [UserController::class, 'logout'],
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
