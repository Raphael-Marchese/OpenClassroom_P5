<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Controller\Controller;
use App\Exception\AccessDeniedException;
use App\Exception\UserNotFoundException;
use App\Model\Repository\PostRepository;
use App\Service\UserProvider;
use App\Security\AdminChecker;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class DeletePostController extends Controller
{
    private PostRepository $postRepository;

    private UserProvider $userProvider;

    private AdminChecker $adminChecker;

    public function __construct()
    {
        parent::__construct();
        $this->postRepository = new PostRepository();
        $this->userProvider = new UserProvider();
        $this->adminChecker = new AdminChecker();
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function deletePost($id): void
    {
        try {
            $user = $this->userProvider->getUser();
        } catch (UserNotFoundException) {
            header('location: /login');
            return;
        }

        try {
            $this->adminChecker->isAdmin($user);
            $this->postRepository->delete($id);
            echo $this->twig->render('homepage/homepage.html.twig');
            return;
        } catch (AccessDeniedException $e) {
            $validationErrors = $e->validationErrors;
            $post = $this->postRepository->findById($id);
            echo $this->twig->render('post/post.html.twig', ['post' => $post, 'errors' => $validationErrors]);
        }
    }
}
