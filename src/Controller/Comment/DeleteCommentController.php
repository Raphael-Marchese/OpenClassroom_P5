<?php

declare(strict_types=1);

namespace App\Controller\Comment;

use App\Controller\Controller;
use App\Exception\AccessDeniedException;
use App\Exception\UserNotFoundException;
use App\Model\Repository\CommentRepository;
use App\Model\Repository\PostRepository;
use App\Security\AdminChecker;
use App\Service\UserProvider;

class DeleteCommentController extends Controller
{

    private CommentRepository $commentRepository;

    private PostRepository $postRepository;

    private UserProvider $userProvider;

    private AdminChecker $adminChecker;

    public function __construct()
    {
        parent::__construct();
        $this->commentRepository = new CommentRepository();
        $this->userProvider = new UserProvider();
        $this->adminChecker = new AdminChecker();
        $this->postRepository = new PostRepository();
    }

    public function deleteComment($id): void
    {
        try {
            $user = $this->userProvider->getUser();
        } catch (UserNotFoundException) {
            header('location: /login');
            return;
        }

        $comment = $this->commentRepository->findById($id);

        if(!$comment){
            return;
        }

        if($user->role !== "ROLE_ADMIN" || $user->id !== $comment->author->id){
            $errors['access'] = "Vous n'avez pas les droits pour effectuer cette action";
            throw new AccessDeniedException($errors);
        }

        try {
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
