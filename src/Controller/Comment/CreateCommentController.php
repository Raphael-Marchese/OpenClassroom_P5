<?php

declare(strict_types=1);

namespace App\Controller\Comment;

use App\Controller\Controller;
use App\Exception\CommentException;
use App\Exception\CSRFTokenException;
use App\Exception\DatabaseException;
use App\Exception\UserNotFoundException;
use App\Model\CSRFToken;
use App\Model\Repository\CommentRepository;
use App\Model\Repository\PostRepository;
use App\Service\CommentExtractor;
use App\Service\FormSanitizer;
use App\Service\UserProvider;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class CreateCommentController extends Controller
{

    private UserProvider $userProvider;

    private FormSanitizer $formSanitizer;

    private CSRFToken $token;

    private CommentExtractor $commentExtractor;

    private PostRepository $postRepository;

    private CommentRepository $commentRepository;


    public function __construct()
    {
        parent::__construct();
        $this->userProvider = new UserProvider();
        $this->formSanitizer = new FormSanitizer();
        $this->token = new CSRFToken();
        $this->commentExtractor = new CommentExtractor();
        $this->postRepository = new PostRepository();
        $this->commentRepository = new CommentRepository();
    }

    /**
     * @throws RuntimeError
     * @throws LoaderError
     * @throws SyntaxError
     */
    public function createComment(): void
    {
        $csrfCheck = 'commentPost';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        try {
            $user = $this->userProvider->getUser();
        } catch (UserNotFoundException) {
            header('location: /login');
            return;
        }

        try {
            $token = $_POST['csrf_token'];
            $this->token->validateToken($token, $csrfCheck);
        } catch (CSRFTokenException $e) {
            $errors = $e->validationErrors;
            $sanitizedData = $this->formSanitizer->sanitize($_POST);
            $post = $this->postRepository->findById((int)$sanitizedData['post_id']);
            echo $this->twig->render('post/post.html.twig', ['post' => $post, 'errors' => $errors]);
        }

        $post = null;

        try {
            $sanitizedData = $this->formSanitizer->sanitize($_POST);

            $post = $this->postRepository->findById((int)$sanitizedData['post_id']);

            if ($post === null) {
                throw new CommentException(['otherError' => 'Erreur lors de la création du commentaire']);
            }

            $comment = $this->commentExtractor->extractComment($sanitizedData, $user, $post);

            $this->commentRepository->create($comment);

            header(sprintf('location: /post/%s', $post->id));
            return;
        } catch (CommentException $e) {
            $errors = $e->validationErrors;
            echo $this->twig->render('post/post.html.twig', ['post' => $post, 'errors' => $errors]);
        } catch (DatabaseException $e) {
            $error = $e->getMessage();
            echo $this->twig->render('post/post.html.twig', ['post' => $post, 'error' => $error]);
        }
    }
}
