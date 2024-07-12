<?php
declare(strict_types=1);

namespace App\Controller\Post;

use App\Controller\Controller;
use App\Model\Repository\PostRepository;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class GetPostController extends Controller
{
    private PostRepository $postRepository;

    public function __construct()
    {
        parent::__construct();
        $this->postRepository = new PostRepository();
    }

    /**
     * Render the post list order by desc on post list page
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function getCollection():void
    {
        $posts = $this->postRepository->findAll();

        echo $this->twig->render('post/list.html.twig', ['posts' => $posts]);
    }

    /**
     * Render the single post page
     * @param int $id
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getPost(int $id):void
    {
        $post = $this->postRepository->findById($id);

        echo $this->twig->render('post/post.html.twig', ['post' => $post]);
    }
}