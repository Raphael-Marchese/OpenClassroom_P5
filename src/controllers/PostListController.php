<?php
declare(strict_types=1);

namespace App\controllers;

use App\model\PostRepository;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PostListController extends Controller
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function render(PostRepository $repository):void
    {
        $blogPosts = $repository->findAll();

        echo $this->twig->render('post/list.html.twig', ['blogPosts' => $blogPosts]);
    }
}