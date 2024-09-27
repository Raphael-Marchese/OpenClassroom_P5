<?php

namespace App\Controller;

use App\Exception\DatabaseException;
use App\Model\Repository\PostRepository;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class HomeController extends Controller
{
    private PostRepository $postRepository;

    public function __construct()
    {
        parent::__construct();
        $this->postRepository = new PostRepository();
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws DatabaseException
     * @throws \DateMalformedStringException
     */
    public function render(): void
    {
        try {
            $posts = $this->postRepository->findThreeLastPosts();

            echo $this->twig->render('homepage/homepage.html.twig', ['posts' => $posts]);
        } catch (DatabaseException $e) {
            $error = $e->getMessage();
            echo $this->twig->render('homepage/homepage.html.twig', ['error' => $error]);
        }
    }

}
