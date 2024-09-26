<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Controller\Controller;
use App\Exception\DatabaseException;
use App\Model\CSRFToken;
use App\Model\Repository\CommentRepository;
use App\Model\Repository\PostRepository;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class GetPostController extends Controller
{
    private PostRepository $postRepository;

    private CommentRepository $commentRepository;

    private CSRFToken $token;


    public function __construct()
    {
        parent::__construct();
        $this->postRepository = new PostRepository();
        $this->commentRepository = new CommentRepository();
        $this->token = new CSRFToken();
    }

    /**
     * Render the post list order by desc on post list page
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws DatabaseException
     * @throws \DateMalformedStringException
     */
    public function getCollection(): void
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
     * @throws \DateMalformedStringException
     */
    public function getPost(int $id): void
    {
        $post = $this->postRepository->findById($id);
        $comments = $this->commentRepository->findByPostId($id);
        $csrfToken = $this->token->generateToken('commentPost');

        echo $this->twig->render('post/post.html.twig', ['post' => $post, 'comments' => $comments, 'csrf_token' => $csrfToken]);
    }
}
