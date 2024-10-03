<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Controller;
use App\Exception\AccessDeniedException;
use App\Exception\CommentNotFoundException;
use App\Exception\DatabaseException;
use App\Exception\UserNotFoundException;
use App\Model\Repository\CommentRepository;
use App\Security\AdminChecker;
use App\Service\UserProvider;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AdminController extends Controller
{
    private const string PENDING_STATUS = 'pending';

    private CommentRepository $commentRepository;

    private UserProvider $userProvider;

    private AdminChecker $adminChecker;

    public function __construct()
    {
        parent::__construct();
        $this->commentRepository = new CommentRepository();
        $this->userProvider = new UserProvider();
        $this->adminChecker = new AdminChecker();

    }
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function index(): void
    {
        try {
            $this->userProvider->getUser();
        } catch (UserNotFoundException) {
            header('location: /login');
            return;
        }

        try {
            $comments = $this->commentRepository->findByStatus(self::PENDING_STATUS);
            echo $this->twig->render('admin/index.html.twig', ['comments' => $comments]);
        } catch (\DateMalformedStringException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @throws \DateMalformedStringException
     * @throws DatabaseException
     */
    public function publishComment(int $id): void
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
            $comment->updatedAt = new \DateTimeImmutable();
            $comment->status = 'published';
            $this->commentRepository->update($comment, $id);

            header('Location: /admin');
            ob_end_flush();
            return;
        } catch (AccessDeniedException|CommentNotFoundException $e) {
            $validationErrors = $e->validationErrors;
            $comments = $this->commentRepository->findByPostId($comment?->blogPost->id);

            ob_end_clean();
        }
    }

    /**
     * @throws \DateMalformedStringException
     * @throws DatabaseException
     */
    public function deleteComment(int $id): void
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
                $validationErrors['comment'] = 'Le commentaire n\'a pas été trouvé';
                throw new CommentNotFoundException($validationErrors);
            }

            $this->adminChecker->isAdmin($user);
            $this->commentRepository->delete($id);

            header('Location: /admin');
            ob_end_flush();
            return;
        } catch (AccessDeniedException|CommentNotFoundException $e) {
            $validationErrors = $e->validationErrors;
            $comments = $this->commentRepository->findByPostId($comment?->blogPost->id);

            ob_end_clean();
        }
    }
}
