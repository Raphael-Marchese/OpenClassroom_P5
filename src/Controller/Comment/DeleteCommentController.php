<?php

declare(strict_types=1);

namespace App\Controller\Comment;

use App\Controller\Controller;
use App\Exception\AccessDeniedException;
use App\Exception\CommentNotFoundException;
use App\Exception\DatabaseException;
use App\Exception\UserNotFoundException;
use App\Model\Repository\CommentRepository;
use App\Model\Repository\PostRepository;
use App\Security\AdminChecker;
use App\Security\AuthorisationChecker;
use App\Service\UserProvider;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class DeleteCommentController extends Controller
{

    private CommentRepository $commentRepository;

    private PostRepository $postRepository;

    private UserProvider $userProvider;

    private AuthorisationChecker $authorisationChecker;

    public function __construct()
    {
        parent::__construct();
        $this->commentRepository = new CommentRepository();
        $this->userProvider = new UserProvider();
        $this->postRepository = new PostRepository();
        $this->authorisationChecker = new AuthorisationChecker();
    }

    /**
     * @throws SyntaxError
     * @throws UserNotFoundException
     * @throws RuntimeError
     * @throws LoaderError
     * @throws DatabaseException
     * @throws \DateMalformedStringException
     */
    public function deleteComment($id): void
    {
        try {
            $user = $this->userProvider->getUser();
        } catch (UserNotFoundException) {
            header('location: /login');
            return;
        }

        $comment = $this->commentRepository->findById($id);

        try {
            if ($comment === null) {
                $validationErrors['comment'] = 'Commentaire non trouvé';
                throw new CommentNotFoundException($validationErrors);
            }
        } catch (CommentNotFoundException $e) {
            $validationErrors = $e->validationErrors;
            $post = $this->postRepository->findById($id);
            echo $this->twig->render('post/post.html.twig', ['post' => $post, 'errors' => $validationErrors]);
        }

        try {
            $this->authorisationChecker->checkAuthorisation($user);
            $this->commentRepository->delete($id);
            header(sprintf('location: /post/%s', $comment->blogPost->id));
            return;
        } catch (AccessDeniedException $e) {
            $validationErrors = $e->validationErrors;
            $post = $this->postRepository->findById($id);
            echo $this->twig->render('post/post.html.twig', ['post' => $post, 'errors' => $validationErrors]);
        }
    }
}
