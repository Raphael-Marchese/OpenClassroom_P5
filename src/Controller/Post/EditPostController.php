<?php
declare(strict_types=1);

namespace App\Controller\Post;

use App\Controller\Controller;
use App\Exception\AccessDeniedException;
use App\Exception\BlogPostException;
use App\Exception\ImageException;
use App\Exception\UserNotFoundException;
use App\Model\Repository\PostRepository;
use App\Model\Service\FormSanitizer;
use App\Model\Service\ImageFactory;
use App\Model\Service\PostExtractor;
use App\Model\Service\UserProvider;
use App\Model\Validator\PostValidator;
use App\Model\Validator\ValidatorFactory;
use App\Security\AuthorChecker;

class EditPostController extends Controller
{
    private PostRepository $postRepository;
    private UserProvider $userProvider;
    private FormSanitizer $sanitizer;
    private PostExtractor $postExtractor;
    private AuthorChecker $authorChecker;
    private ValidatorFactory $validatorFactory;
    private ImageFactory $imageFactory;
    public function __construct()
    {
        parent::__construct();
        $this->postRepository = new PostRepository();
        $this->userProvider = new UserProvider();
        $this->validatorFactory = new ValidatorFactory();
        $this->sanitizer = new FormSanitizer();
        $this->postExtractor = new PostExtractor();
        $this->authorChecker = new AuthorChecker();
        $this->imageFactory = new ImageFactory();
    }

    public function postEditForm($id): void
    {
        $post = $this->postRepository->findById($id);
        if($post === null) {
            return;
        }

        try {
            $user = $this->userProvider->getUser();
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

        echo $this->twig->render('post/edit.html.twig', ['post' => $post]);
    }

    public function postEdit(): void
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

        $sanitizedData = $this->sanitizer->sanitize($_POST);

        $id = $sanitizedData['id'];

        try {
            $image = $this->imageFactory->createImage($_FILES['image']);
            ValidatorFactory::validate($image);

            $post = $this->postExtractor->extractBlogPost($user, $_POST, $image);

            ValidatorFactory::validate($post);

            $post->updatedAt = new \DateTime();
            $oldPost = $this->postRepository->findById((int)$id);

            if($_FILES['image']['size'] === 0)
            {
                $post->image = $oldPost?->image;
            }

            if($user->id !== $post->author->id)
            {
                throw new AccessDeniedException();
            }

            $this->postRepository->update($post, (int) $id);

            header(sprintf('location: /post/%s', $id));
            return;

        } catch (BlogPostException $e) {
            $validationErrors = $e->validationErrors;

            echo $this->twig->render('post/edit.html.twig', [
                'errors' => $validationErrors,
                'formData' => [
                    'title' => $post->title,
                    'chapo' => $post->chapo,
                    'content' => $post->content,
                ]
            ]);
        } catch (AccessDeniedException $e) {
            $validationErrors = $e->validationErrors;

            echo $this->twig->render('post/edit.html.twig', [
                'errors' => $validationErrors,
                'formData' => [
                    'title' => $post->title,
                    'chapo' => $post->chapo,
                    'content' => $post->content,
                ]
            ]);
        } catch (ImageException $e) {
            $errors = $e->validationErrors;

            echo $this->twig->render('post/edit.html.twig', [
                'errors' => $errors,
                'formData' => [
                    'title' => $post->title,
                    'chapo' => $post->chapo,
                    'content' => $post->content,
                ]
            ]);
        } catch (\Exception $e) {
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