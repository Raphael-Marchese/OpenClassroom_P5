<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\AccessDeniedException;
use App\Exceptions\BlogPostCreationException;
use App\Exceptions\BlogPostUpdateException;
use App\Model\Repository\PostRepository;
use App\Model\Service\PostExtractor;
use App\Model\Service\UserProvider;
use App\Model\Service\ValidatePost;
use App\Model\Validator\FormSanitizer;

class EditPostController extends Controller
{
    private PostRepository $postRepository;
    private UserProvider $userProvider;
    private ValidatePost $validatePost;
    private FormSanitizer $sanitizer;
    private PostExtractor $postExtractor;
    public function __construct()
    {
        parent::__construct();
        $this->postRepository = new PostRepository();
        $this->userProvider = new UserProvider();
        $this->validatePost = new ValidatePost();
        $this->sanitizer = new FormSanitizer();
        $this->postExtractor = new PostExtractor();
    }

    public function postEditForm($id): void
    {
        $post = $this->postRepository->findById($id);
        if($post === null) {
            return;
        }
        $user = $this->userProvider->getUser();

        if($user->id !== $post->author->id)
        {
            throw new AccessDeniedException();
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
        } catch (AccessDeniedException) {
            header('location: /login');
            return;
        }

        $sanitizedData = $this->sanitizer->sanitize($_POST);

        $id = $sanitizedData['id'];

        $post = $this->postExtractor->extractBlogPost($user, $_POST, $_FILES);

        try {
            $this->validatePost->validateData($post, $_FILES);
            $post->updatedAt = new \DateTime();
            if($_FILES['image']['size'] === 0)
            {
                $oldPost = $this->postRepository->findById((int)$id);

                $post->image = $oldPost?->image;
            }
            $this->postRepository->update($post, $id);

            header(sprintf('location: /post/%s', $id));
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