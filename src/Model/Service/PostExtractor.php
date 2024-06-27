<?php
declare(strict_types=1);

namespace App\Model\Service;

use App\Entity\BlogPost;
use App\Entity\User;
use App\Model\Validator\FormSanitizer;

class PostExtractor
{
    private FormSanitizer $formSanitizer;

    public function __construct()
    {
        $this->formSanitizer = new FormSanitizer();
    }
    public function extractBlogPost(User $user, array $postData, array $fileData): BlogPost
    {
        $sanitizedData = FormSanitizer::sanitize($postData);
        $image = $fileData['image'];

        $title = $sanitizedData['title'] ?? null ;
        $chapo = $sanitizedData['chapo'] ?? null ;
        $content = $sanitizedData['content'] ?? null ;
        $image = $image['size'] !== 0 ? basename($image['name']) : null;
        $status = $sanitizedData['submitButton'] ?? null;
        $createdAt = new \DateTimeImmutable();

        return new BlogPost(title: $title, chapo: $chapo, content: $content, status: $status, createdAt: $createdAt, author: $user, image: $image);
    }
}