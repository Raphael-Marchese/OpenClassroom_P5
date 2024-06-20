<?php
declare(strict_types=1);

namespace App\controllers;

use App\entity\User;
use App\entity\BlogPost;
use App\Exceptions\AccessDeniedException;
use App\Exceptions\BlogPostCreationException;
use App\model\repository\PostRepository;
use App\model\repository\UserRepository;
use App\model\validator\FormValidator;
use App\model\validator\ImageValidator;
use App\model\validator\PostValidator;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PostController extends Controller
{

    private PostRepository $repository;
    private UserRepository $userRepository;
    public function __construct()
    {
        parent::__construct();
        $this->repository = new PostRepository();
        $this->userRepository = new UserRepository();
    }

    /**
     * Render the post list order by desc on post list page
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function getCollection():void
    {
        $posts = $this->repository->findAll();

        echo $this->twig->render('post/list.html.twig', ['posts' => $posts]);
    }

    /**
     * Renter the single post page
     * @param PostRepository $repository
     * @param int $id
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getPost(int $id):void
    {
        $post = $this->repository->findById($id);

        echo $this->twig->render('post/post.html.twig', ['post' => $post]);
    }

    public function createPost():void
    {
        echo $this->twig->render('post/create.html.twig');
    }

    public function submitCreate():void
    {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        try {
            $user = $this->getUser();
        } catch (AccessDeniedException) {
            header('location: /login');
            return;
        }

        $post = $this->extractBlogPost($user);

        try {
            $this->validateData($post);
            $this->repository->save($post);

            header(sprintf('location: /post/%s', $post->id));
            return;

        } catch (BlogPostCreationException $e) {
            $validationErrors = $e->validationErrors;

            echo $this->twig->render('post/create.html.twig', [
                'errors' => $validationErrors,
                'formData' => [
                    'title' => $post->title,
                    'chapo' => $post->chapo,
                    'content' => $post->content,
                ]
            ]);
        }
    }

    private function getUser(): User
    {
        $userId = $_SESSION['LOGGED_USER']['user_id'] ?? null ;
        $user = $this->userRepository->findById($userId);

        if (null === $user) {
            throw new AccessDeniedException();
        }

        return $user;
    }

    private function validateData(BlogPost $post)
    {
        $image = $_FILES['image'];

        $validationErrors = ImageValidator::validate($image);
        if (count($validationErrors) > 0) {
            throw new BlogPostCreationException($validationErrors);
        }

        $validationErrors = PostValidator::validate($post);
        if (count($validationErrors) > 0) {
            throw new BlogPostCreationException($validationErrors);
        }
    }

    private function extractBlogPost(User $user): BlogPost
    {
        $sanitizedData = FormValidator::sanitize($_POST);
        $image = $_FILES['image'];

        $title = $sanitizedData['title'] ?? null ;
        $chapo = $sanitizedData['chapo'] ?? null ;
        $content = $sanitizedData['content'] ?? null ;
        $image = $image['size'] !== 0 ? basename($image['name']) : null;
        $status = $sanitizedData['submitButton'] ?? null;
        $createdAt = new \DateTimeImmutable();

        return new BlogPost(title: $title, chapo: $chapo, content: $content, createdAt: $createdAt, image: $image, status: $status, author: $user);
    }
}