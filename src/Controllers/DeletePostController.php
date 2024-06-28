<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\AccessDeniedException;
use App\Model\Repository\PostRepository;
use App\Model\Repository\UserRepository;
use App\Model\Service\UserProvider;
use App\Model\Service\ValidateUser;
use App\Model\Validator\AdminValidator;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class DeletePostController extends Controller
{
    private PostRepository $postRepository;
    private UserProvider $userProvider;
    private ValidateUser $validateUser;
    public function __construct()
    {
        parent::__construct();
        $this->postRepository = new PostRepository();
        $this->userProvider = new UserProvider();
        $this->validateUser = new ValidateUser();
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function deletePost($id): void
    {
        $user = $this->userProvider->getUser();

        try{
            $this->validateUser->validateRole($user);
            $this->postRepository->delete($id);
            echo $this->twig->render('homepage/homepage.html.twig');
            return;

        }catch (AccessDeniedException $e){
            $validationErrors = $e->validationErrors;
            $post = $this->postRepository->findById($id);
            echo $this->twig->render('post/post.html.twig', ['post' => $post, 'errors' => $validationErrors]);
        }
    }
}