<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Entity\BlogPost;
use App\Model\Entity\User;
use App\Model\File\File;
use DateTimeImmutable;

class PostExtractor
{

    /**
     * @throws \Exception
     */
    public function extractBlogPost(User $user, array $postData, File $file): BlogPost
    {
        $title = $postData['title'] ?? null;
        $chapo = $postData['chapo'] ?? null;
        $content = $postData['content'] ?? null;
        $image = $file->name !== '' ? basename($file->name) : null;
        $status = $postData['submitButton'] ?? null;
        $createdAt = new DateTimeImmutable();

        return new BlogPost(
            title: $title,
            chapo: $chapo,
            content: $content,
            status: $status,
            createdAt: $createdAt,
            author: $user,
            image: $image
        );
    }
}
