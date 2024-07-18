<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Entity\BlogPost;
use App\Model\Entity\User;
use App\Model\File\File;

class PostExtractor
{
    private FormSanitizer $formSanitizer;

    public function __construct()
    {
        $this->formSanitizer = new FormSanitizer();
    }

    public function extractBlogPost(User $user, array $postData, File $file): BlogPost
    {
        $sanitizedData = $this->formSanitizer->sanitize($postData);

        $title = $sanitizedData['title'] ?? null;
        $chapo = $sanitizedData['chapo'] ?? null;
        $content = $sanitizedData['content'] ?? null;
        $image = $file->name !== '' ? basename($file->name) : null;
        $status = $sanitizedData['submitButton'] ?? null;
        $createdAt = new \DateTimeImmutable();

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
