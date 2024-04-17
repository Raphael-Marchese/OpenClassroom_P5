<?php
declare(strict_types=1);

namespace App\controllers;

use App\model\BlogPostRepository;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class BlogPostList extends Controller
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function render(BlogPostRepository $repository):void
    {
        $blogPosts = $repository->findAll();

        echo $this->twig->render('blogPost/list.html.twig', ['blogPosts' => $blogPosts]);
    }
}