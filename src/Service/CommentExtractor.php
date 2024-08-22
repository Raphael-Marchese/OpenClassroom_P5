<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Entity\BlogPost;
use App\Model\Entity\Comment;
use App\Model\Entity\User;

class CommentExtractor
{
    public function extractComment(array $postData, User $user, BlogPost $post): Comment
    {
        $content = $postData['content'];
        $createdAt = new \DateTimeImmutable();
        $updatedAt = new \DateTimeImmutable();
        $status = 'pending';

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
