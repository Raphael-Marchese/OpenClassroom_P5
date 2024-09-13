<?php

namespace App;

use App\Controller\Comment\CreateCommentController;
use App\Controller\Comment\DeleteCommentController;
use App\Controller\Comment\EditCommentController;
use App\Controller\Contact\ContactController;
use App\Controller\HomeController;
use App\Controller\Post\CreatePostController;
use App\Controller\Post\DeletePostController;
use App\Controller\Post\EditPostController;
use App\Controller\Post\GetPostController;
use App\Controller\User\LoginController;
use App\Controller\User\RegisterController;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Router
{
    private array $routes;

    public function __construct()
    {
        $this->routes = [
            '/' => [HomeController::class, 'render'],
            // Route pour /
            '/list' => [GetPostController::class, 'getCollection'],
            // Route pour /list
            '/post/create' => [CreatePostController::class, 'createPostForm'],
            // Route pour /post/create
            '/post/create/submit' => [CreatePostController::class, 'createPost'],
            // Route pour /soumettre la création d'un post
            '/post/(\d+)' => [GetPostController::class, 'getPost'],
            // Route pour /post/id
            '/post/(\d+)/edit' => [EditPostController::class, 'postEditForm'],
            '/post/(\d+)/edit/submit' => [EditPostController::class, 'postEdit'],
            '/post/(\d+)/delete' => [DeletePostController::class, 'deletePost'],
            '/register' => [RegisterController::class, 'register'],
            '/register/submit' => [RegisterController::class, 'submitRegister'],
            '/login' => [LoginController::class, 'login'],
            '/login/submit' => [LoginController::class, 'submitLogin'],
            '/logout' => [LoginController::class, 'logout'],
            '/comment/create/submit' => [CreateCommentController::class, 'createComment'],
            '/comment/(\d+)/delete' => [DeleteCommentController::class, 'deleteComment'],
            '/comment/(\d+)/edit' => [EditCommentController::class, 'commentEditForm'],
            '/comment/(\d+)/edit/submit' => [EditCommentController::class, 'commentEdit'],
            '/comment/(\d+)/status/edit' => [EditCommentController::class, 'commentStatusEdit'],
            '/contact' => [ContactController::class, 'contact'],
            '/contact/submit' => [ContactController::class, 'submitContact'],
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
        foreach ($this->routes as $route => $controller) {
            if ($path === $route) {
                $controllerClass = $controller[0];
                $method = $controller[1];
                $controllerInstance = new $controllerClass();
                $controllerInstance->$method();
                return;
            }

            if (preg_match('#^' . $route . '$#', $path, $matches)) {
                $controllerClass = $controller[0];
                $method = $controller[1];
                $controllerInstance = new $controllerClass();
                $pathId = (int)$matches[1];
                $controllerInstance->$method($pathId);
                return;
            }
        }
        $error404Controller = new Error404();
        $error404Controller->render();
    }
}
