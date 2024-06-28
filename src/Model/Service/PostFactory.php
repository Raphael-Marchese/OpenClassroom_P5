<?php
declare(strict_types=1);

namespace App\Model\Service;

use App\Entity\BlogPost;
use App\Entity\User;
use App\Model\Repository\UserRepository;
use DateTime;

class PostFactory
{
    private UserRepository $userRepository;
    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function createBlogPost(array $postData): BlogPost
    {
        $title = $postData['title'] ?? null ;
        $chapo = $postData['chapo'] ?? null ;
        $content = $postData['content'] ?? null ;
        $image = $postData['image'] ?? null ;
        $status = $postData['status'] ?? null ;
        $createdAt = $postData['created_at'] ? new DateTime($postData['created_at']) : null;
        $updatedAt = $postData['updated_at'] ? new DateTime($postData['updated_at']) : null;
        $user = $postData['author'] ?? null;
        if(is_int($user))
        {
            $user = $this->userRepository->findById($user);
        }

        return new BlogPost(title: $title, chapo: $chapo, content: $content, status: $status, createdAt: $createdAt, author: $user, image: $image, updatedAt: $updatedAt);
    }
}