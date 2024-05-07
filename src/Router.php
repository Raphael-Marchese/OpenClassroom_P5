<?php

namespace App;

use App\controllers\PostListController;
use App\controllers\HomeController;
use App\model\PostRepository;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Router
{
    private array $routes;

    public function __construct()
    {
        $this->routes = [
            '/' => HomeController::class,
            '/list' => PostListController::class,
            '/404' => Error404::class,
        ];
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function callController(string $path): void
    {
        $class = $this->routes[$path] ?? $this->routes['/404'];

        /** @var HomeController | PostListController $controller */
        $controller = new $class();
        if($controller instanceof HomeController){
            $controller->render();
        } else {
            $repository = new PostRepository();
            $controller->render($repository);
        }
    }
}
