<?php
declare(strict_types=1);

namespace App\Controller\Post;

use App\Controller\Controller;
use App\Exception\AccessDeniedException;
use App\Exception\BlogPostException;
use App\Exception\CSRFTokenException;
use App\Exception\ImageException;
use App\Exception\UserNotFoundException;
use App\Model\CSRFToken;
use App\Model\Repository\PostRepository;
use App\Security\AdminChecker;
use App\Service\FormSanitizer;
use App\Service\ImageFactory;
use App\Service\PostExtractor;
use App\Service\UserProvider;
use App\Model\Validator\ValidatorFactory;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class CreatePostController extends Controller
{
    private PostRepository $postRepository;

    private UserProvider $userProvider;

    private PostExtractor $postExtractor;

    private ImageFactory $imageFactory;

    private FormSanitizer $formSanitizer;

    private CSRFToken $token;

    private AdminChecker $adminChecker;

    public function __construct()
    {
        parent::__construct();
        $this->postRepository = new PostRepository();
        $this->userProvider =  new UserProvider();
        $this->postExtractor = new PostExtractor();
        $this->imageFactory = new ImageFactory();
        $this->formSanitizer = new FormSanitizer();
        $this->token = new CSRFToken();
        $this->adminChecker = new AdminChecker();
    }

    public function createPostForm():void
    {
        try {
            $user = $this->userProvider->getUser();
        } catch (UserNotFoundException) {
            header('location: /login');
            return;
        }

        try {
            $this->adminChecker->checkAdmin($user);
        } catch (AccessDeniedException $e) {
            $errors = $e->validationErrors;
            echo $this->twig->render('homepage/homepage.html.twig', [
                'errors' => $errors,
            ]);
        }

        $csrfToken = $this->token->generateToken('createPost');

        echo $this->twig->render('post/create.html.twig', ['csrf_token' => $csrfToken]);
    }

    public function createPost():void
    {
        $csrfCheck = 'createPost';
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

            $sanitizedData = $this->formSanitizer->sanitize($_POST);
            $token =$sanitizedData['csrf_token'];

            $this->token->validateToken($token, $csrfCheck);
            ValidatorFactory::validate($image);

            $post = $this->postExtractor->extractBlogPost($user, $_POST, $image);

            ValidatorFactory::validate($post);

            $this->postRepository->create($post);

            header(sprintf('location: /post/%s', $post->id));
            return;

        } catch (ImageException | CSRFTokenException | BlogPostException $e) {
            $errors = $e->validationErrors;

            echo $this->twig->render('post/create.html.twig', [
                'errors' => $errors,
                'formData' => [
                    'title' => $post->title,
                    'chapo' => $post->chapo,
                    'content' => $post->content,
                ]
            ]);
        }catch (\Exception $e) {
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
