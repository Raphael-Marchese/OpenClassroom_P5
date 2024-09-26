<?php

declare(strict_types=1);

namespace App\Controller\Post;

use App\Controller\Controller;
use App\Exception\AccessDeniedException;
use App\Exception\BlogPostException;
use App\Exception\CSRFTokenException;
use App\Exception\DatabaseException;
use App\Exception\ImageException;
use App\Exception\UserNotFoundException;
use App\Model\CSRFToken;
use App\Model\Repository\PostRepository;
use App\Service\FormSanitizer;
use App\Service\ImageFactory;
use App\Service\PostExtractor;
use App\Service\UserProvider;
use App\Model\Validator\ValidatorFactory;
use App\Security\AuthorChecker;
use DateTime;
use Exception;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class EditPostController extends Controller
{
    private PostRepository $postRepository;

    private UserProvider $userProvider;

    private FormSanitizer $sanitizer;

    private PostExtractor $postExtractor;

    private AuthorChecker $authorChecker;

    private ImageFactory $imageFactory;

    private CSRFToken $token;

    public function __construct()
    {
        parent::__construct();
        $this->postRepository = new PostRepository();
        $this->userProvider = new UserProvider();
        $this->sanitizer = new FormSanitizer();
        $this->postExtractor = new PostExtractor();
        $this->authorChecker = new AuthorChecker();
        $this->imageFactory = new ImageFactory();
        $this->token = new CSRFToken();
    }

    /**
     * @throws SyntaxError
     * @throws UserNotFoundException
     * @throws RuntimeError
     * @throws LoaderError
     * @throws \DateMalformedStringException
     */
    public function postEditForm(int $id): void
    {
        $post = $this->postRepository->findById($id);
        if ($post === null) {
            return;
        }

        try {
            $this->userProvider->getUser();
        } catch (UserNotFoundException) {
            header('location: /login');
            return;
        }

        try {
            $this->authorChecker->checkAuthor($post);
        } catch (AccessDeniedException $e) {
            $errors = $e->validationErrors;
            echo $this->twig->render('post/post.html.twig', ['post' => $post, 'errors' => $errors]);
        }

        $csrfToken = $this->token->generateToken('editPost');

        echo $this->twig->render('post/edit.html.twig', ['post' => $post, 'csrf_token' => $csrfToken]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function postEdit(int $id): void
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

        $post = null;

        try {
            $token = $_POST['csrf_token'];
            $this->token->validateToken($token, $csrfCheck);

            $sanitizedData = $this->sanitizer->sanitize($_POST);

            $image = $this->imageFactory->createImage($_FILES['image']);
            $post = $this->postExtractor->extractBlogPost($user, $sanitizedData, $image);

            ValidatorFactory::validate($image);
            ValidatorFactory::validate($post);

            $post->updatedAt = new DateTime();
            $oldPost = $this->postRepository->findById($id);

            if ($_FILES['image']['size'] === 0) {
                $post->image = $oldPost?->image;
            }

            $this->authorChecker->checkAuthor($post);

            $this->postRepository->update($post, $id);

            header(sprintf('Location: /post/%s', $id));
            ob_end_flush();
            return;
        } catch (ImageException|BlogPostException|AccessDeniedException $e) {
            $validationErrors = $e->validationErrors;

            ob_end_clean();

            echo $this->twig->render('post/edit.html.twig', [
                'errors' => $validationErrors,
                'formData' => [
                    'title' => $post->title,
                    'chapo' => $post->chapo,
                    'content' => $post->content,
                ]
            ]);
        } catch (Exception|DatabaseException $e) {
            $error = $e->getMessage();
            echo $this->twig->render('post/edit.html.twig', [
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
