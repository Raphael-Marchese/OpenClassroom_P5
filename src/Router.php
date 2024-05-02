<?php

namespace App;

use App\controllers\BlogPostList;
use App\controllers\Homepage;
use App\model\BlogPostRepository;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Router
{
    private array $routes;

    public function __construct()
    {
        $this->routes = [
            '/' => Homepage::class,
            '/list' => BlogPostList::class,
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

        /** @var Homepage | BlogPostList $controller */
        $controller = new $class();
        if($controller instanceof Homepage){
            $controller->render();
        } else {
            $repository = new BlogPostRepository();
            $controller->render($repository);
        }
    }
}
