<?php
declare(strict_types=1);

namespace App\Controller\Post;

use App\Controller\Controller;
use App\Exception\BlogPostException;
use App\Exception\ImageException;
use App\Exception\UserNotFoundException;
use App\Model\Repository\PostRepository;
use App\Model\Service\ImageFactory;
use App\Model\Service\PostExtractor;
use App\Model\Service\UserProvider;
use App\Model\Validator\PostValidator;
use App\Model\Validator\ValidatorFactory;

class CreatePostController extends Controller
{
    private PostRepository $postRepository;
    private UserProvider $userProvider;
    private PostExtractor $postExtractor;
    private PostValidator $validatePost;
    private ImageFactory $imageFactory;

    public function __construct()
    {
        parent::__construct();
        $this->postRepository = new PostRepository();
        $this->userProvider =  new UserProvider();
        $this->postExtractor = new PostExtractor();
        $this->validatePost = new PostValidator();
        $this->imageFactory = new ImageFactory();
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
        } catch (UserNotFoundException) {
            header('location: /login');
            return;
        }

        try {
            $image = $this->imageFactory->createImage($_FILES['image']);
            ValidatorFactory::validate($image);

            $post = $this->postExtractor->extractBlogPost($user, $_POST, $image);

            ValidatorFactory::validate($post);

            $this->postRepository->create($post);

            header(sprintf('location: /post/%s', $post->id));
            return;

        } catch (ImageException $e) {
            $errors = $e->validationErrors;

            echo $this->twig->render('post/create.html.twig', [
                'errors' => $errors,
                'formData' => [
                    'title' => $post->title,
                    'chapo' => $post->chapo,
                    'content' => $post->content,
                ]
            ]);
        } catch (BlogPostException $e) {
            $errors = $e->validationErrors;

            echo $this->twig->render('post/create.html.twig', [
                'errors' => $errors,
                'formData' => [
                    'title' => $post->title,
                    'chapo' => $post->chapo,
                    'content' => $post->content,
                ]
            ]);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            echo $this->twig->render('post/create.html.twig', [
                'otherError' => $error,
                'formData' => [
                    'title' => $post->title,
                    'chapo' => $post->chapo,
                    'content' => $post->content,
                ]
            ]);
        }
    }

}
