<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Entity\User;
use App\Exceptions\AccessDeniedException;
use App\Exceptions\BlogPostCreationException;
use App\Model\Repository\PostRepository;
use App\Model\Service\PostExtractor;
use App\Model\Service\UserProvider;
use App\Model\Service\ValidatePost;
use App\Model\Validator\PostValidator;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PostController extends Controller
{

    private PostRepository $postRepository;
    private UserProvider $userProvider;
    private PostExtractor $postExtractor;
    private ValidatePost $validatePost;

    public function __construct()
    {
        parent::__construct();
        $this->postRepository = new PostRepository();
        $this->userProvider =  new UserProvider();
        $this->postExtractor = new PostExtractor();
        $this->validatePost = new ValidatePost();
    }

    /**
     * Render the post list order by desc on post list page
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function getCollection():void
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
     */
    public function getPost(int $id):void
    {
        $post = $this->postRepository->findById($id);

        echo $this->twig->render('post/post.html.twig', ['post' => $post]);
    }

    public function createPostForm():void
    {
        echo $this->twig->render('post/create.html.twig');
    }

    public function createPost():void
    {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        try {
            $user = $this->userProvider->getUser();
        } catch (AccessDeniedException) {
            header('location: /login');
            return;
        }

        $post = $this->postExtractor->extractBlogPost($user, $_POST, $_FILES);

        try {
            $this->validatePost->validateData($post, $_FILES);
            $this->postRepository->save($post);

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

}