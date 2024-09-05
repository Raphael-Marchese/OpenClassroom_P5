<?php

declare(strict_types=1);

namespace App\Controller\Comment;

use App\Controller\Controller;
use App\Exception\AccessDeniedException;
use App\Exception\CommentException;
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


        echo $this->twig->render('post/post.html.twig', ['post' => $editedComment->blogPost, 'csrf_token' => $csrfToken, 'editedComment' => $editedComment, 'commentEdit' => $commentEdit]);
    }
    public function commentEdit(): void
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

        $sanitizedData = $this->sanitizer->sanitize($_POST);

        $id = $sanitizedData['comment_id'];

        try {
            $token = $sanitizedData['csrf_token'];

            $this->token->validateToken($token, $csrfCheck);
        } catch (CSRFTokenException $e) {
            $validationErrors = $e->validationErrors;

            echo $this->twig->render('post/post.html.twig', [
                'errors' => $validationErrors,
            ]);
        }

        $post = $this->postRepository->findById((int)$sanitizedData['post_id']);

        $comment = $this->commentExtractor->extractComment($sanitizedData, $user, $post);

        $comments = $this->commentRepository->findByPostId($post?->id);


        try {
            ValidatorFactory::validate($comment);

            $comment->updatedAt = new \DateTime();

            $this->authorChecker->checkAuthor($comment);

            $this->commentRepository->update($comment, (int)$id);

            header(sprintf('Location: /post/%s', $post?->id));
            ob_end_flush();
            return;
        } catch (AccessDeniedException | CommentException $e) {
            $validationErrors = $e->validationErrors;

            ob_end_clean();

            echo $this->twig->render('post/post.html.twig', ['post' => $post, 'comments' => $comments, 'csrf_token' => $token,'errors' => $validationErrors, ]);
        } catch (\Exception|DatabaseException $e) {
            $error = $e->getMessage();
            echo $this->twig->render('post/post.html.twig', ['post' => $post, 'comments' => $comments, 'csrf_token' => $token,'errors' => $error, ]);

        }
    }

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

        $comments = $this->commentRepository->findByPostId($comment?->blogPost->id);

        try {
            if(!$comment) {
                $validationErrors['comment'] = 'Le commentaire n\'a pas été trouvé';
                throw new CommentException($validationErrors);
            }

            $this->adminChecker->isAdmin($user);
            $comment->updatedAt = new \DateTime();
            $comment->status = 'published';
            $this->commentRepository->update($comment, (int)$id);

            header(sprintf('Location: /post/%s', $comment?->blogPost->id));
            ob_end_flush();
            return;
        } catch (AccessDeniedException | CommentException $e) {
            $validationErrors = $e->validationErrors;

            ob_end_clean();

            echo $this->twig->render('post/post.html.twig', ['post' => $comment?->blogPost, 'comments' => $comments, 'errors' => $validationErrors, ]);
        } catch (\Exception|DatabaseException $e) {
            $error = $e->getMessage();
            echo $this->twig->render('post/post.html.twig', ['post' => $comment?->blogPost, 'comments' => $comments, 'errors' => $error, ]);

        }
    }
}
