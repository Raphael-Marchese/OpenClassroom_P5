<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Entity\Comment;
use App\Model\Repository\PostRepository;
use App\Model\Repository\UserRepository;
use DateTime;

class CommentFactory
{
    private UserRepository $userRepository;

    private PostRepository $postRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->postRepository = new PostRepository();
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function createComment(array $postData): Comment
    {
        $content = $postData['content'];
        $createdAt = $postData['created_at'] ? new DateTime($postData['created_at']) : null;
        $updatedAt = $postData['updated_at'] ? new DateTime($postData['updated_at']) : null;
        $status = $postData['status'] ?? null;
        $post = $postData['post'] ?? null;
        $user = $postData['author'] ?? null;
        if (is_int($user)) {
            $user = $this->userRepository->findById($user);
        }

        if(is_int($post)) {
            $post = $this->postRepository->findById($post);
        }

        return new Comment(
            content: $content,
            createdAt: $createdAt,
            updatedAt: $updatedAt,
            status: $status,
            author: $user,
            blogPost: $post
        );
    }
}