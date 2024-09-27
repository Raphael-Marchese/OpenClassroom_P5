<?php

declare(strict_types=1);

namespace App\Controller\Comment;

use App\Controller\Controller;
use App\Exception\AccessDeniedException;
use App\Exception\CommentException;
use App\Exception\CommentNotFoundException;
use App\Exception\CSRFTokenException;
use App\Exception\DatabaseException;
use App\Exception\UserNotFoundException;
use App\Model\CSRFToken;
use App\Model\Repository\CommentRepository;
use App\Model\Repository\PostRepository;
use App\Model\Validator\ValidatorFactory;
use App\Security\AdminChecker;
use App\Security\AuthorChecker;
use App\Service\CommentExtractor;
use App\Service\FormSanitizer;
use App\Service\UserProvider;
use DateTime;
use Exception;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class EditCommentController extends Controller
{

    private CommentRepository $commentRepository;

    private UserProvider $userProvider;

    private AuthorChecker $authorChecker;

    private AdminChecker $adminChecker;

    private PostRepository $postRepository;

    private CSRFToken $token;

    private FormSanitizer $sanitizer;

    private CommentExtractor $commentExtractor;

    public function __construct()
    {
        parent::__construct();
        $this->commentRepository = new CommentRepository();
        $this->userProvider = new UserProvider();
        $this->authorChecker = new AuthorChecker();
        $this->postRepository = new PostRepository();
        $this->token = new CSRFToken();
        $this->sanitizer = new FormSanitizer();
        $this->commentExtractor = new CommentExtractor();
        $this->adminChecker = new AdminChecker();
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws UserNotFoundException
     * @throws \DateMalformedStringException
     */
    public function commentEditForm($id): void
    {
        $editedComment = $this->commentRepository->findById($id);
        if ($editedComment === null) {
            return;
        }

        try {
            $this->userProvider->getUser();
        } catch (UserNotFoundException) {
            header('location: /login');
            return;
        }

        try {
            $this->authorChecker->checkAuthor($editedComment);
        } catch (AccessDeniedException $e) {
            $errors = $e->validationErrors;
            echo $this->twig->render('post/post.html.twig', ['post' => $editedComment->blogPost, 'errors' => $errors]);
        }

        $csrfToken = $this->token->generateToken('editPost');
        $commentEdit = 'editComment';


        echo $this->twig->render(
            'post/post.html.twig',
            [
                'post' => $editedComment->blogPost,
                'csrf_token' => $csrfToken,
                'editedComment' => $editedComment,
                'commentEdit' => $commentEdit
            ]
        );
    }

    /**
     * @throws RuntimeError
     * @throws LoaderError
     * @throws SyntaxError
     * @throws \DateMalformedStringException
     */
    public function commentEdit(int $id): void
    {
        $csrfCheck = 'editPost';

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
            $validationErrors = $e->validationErrors;

            echo $this->twig->render('post/post.html.twig', [
                'errors' => $validationErrors,
            ]);
        }

        $post = null;

        try {
            $sanitizedData = $this->sanitizer->sanitize($_POST);

            $post = $this->postRepository->findById((int)$sanitizedData['post_id']);

            if ($post === null) {
                throw new CommentException(['otherError' => 'Erreur lors de la création du commentaire']);
            }

            $comment = $this->commentExtractor->extractComment($sanitizedData, $user, $post);

            if ($comment === null) {
                $validationErrors['otherError'] = 'Le commentaire concerné par la modification n\'a pas été trouvé';
                throw new CommentNotFoundException($validationErrors);
            }

            ValidatorFactory::validate($comment);

            $comment->updatedAt = new DateTime();

            $this->authorChecker->checkAuthor($comment);

            $this->commentRepository->update($comment, $id);

            header(sprintf('Location: /post/%s', $post?->id));
            ob_end_flush();
            return;
        } catch (AccessDeniedException|CommentException|CommentNotFoundException $e) {
            $validationErrors = $e->validationErrors;

            $comments = $this->commentRepository->findByPostId($post->id);

            ob_end_clean();

            echo $this->twig->render(
                'post/post.html.twig',
                ['post' => $post, 'comments' => $comments, 'csrf_token' => $token, 'errors' => $validationErrors,]
            );
        } catch (Exception|DatabaseException $e) {
            $comments = $this->commentRepository->findByPostId($post->id);

            $error = $e->getMessage();
            echo $this->twig->render(
                'post/post.html.twig',
                ['post' => $post, 'comments' => $comments, 'csrf_token' => $token, 'errors' => $error,]
            );
        }
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function commentStatusEdit($id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        try {
            $user = $this->userProvider->getUser();
        } catch (UserNotFoundException) {
            header('location: /login');
            return;
        }

        $comment = $this->commentRepository->findById($id);

        try {
            if ($comment === null) {
                $validationErrors['comment'] = 'Le commentaire n\'a pas été trouvé';
                throw new CommentNotFoundException($validationErrors);
            }

            $this->adminChecker->isAdmin($user);
            $comment->updatedAt = new DateTime();
            $comment->status = 'published';
            $this->commentRepository->update($comment, (int)$id);

            header(sprintf('Location: /post/%s', $comment?->blogPost->id));
            ob_end_flush();
            return;
        } catch (AccessDeniedException|CommentNotFoundException $e) {
            $validationErrors = $e->validationErrors;
            $comments = $this->commentRepository->findByPostId($comment?->blogPost->id);

            ob_end_clean();

            try {
                echo $this->twig->render(
                    'post/post.html.twig',
                    ['post' => $comment?->blogPost, 'comments' => $comments, 'errors' => $validationErrors,]
                );
            } catch (LoaderError | RuntimeError | SyntaxError $e) {
                echo $e->getMessage();
            }

        } catch (Exception|DatabaseException $e) {
            $error = $e->getMessage();
            $comments = $this->commentRepository->findByPostId($comment?->blogPost->id);

            try {
                echo $this->twig->render(
                    'post/post.html.twig',
                    ['post' => $comment?->blogPost, 'comments' => $comments, 'errors' => $error,]
                );
            } catch (LoaderError | RuntimeError | SyntaxError $e) {
                echo $e->getMessage();
            }
        }
    }
}
