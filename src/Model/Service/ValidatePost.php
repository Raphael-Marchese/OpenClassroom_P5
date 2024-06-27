<?php
declare(strict_types=1);

namespace App\Model\Service;

use App\Entity\BlogPost;
use App\Exceptions\BlogPostCreationException;
use App\Model\Validator\ImageValidator;
use App\Model\Validator\PostValidator;

class ValidatePost
{
    private ImageValidator $imageValidator;
    private PostValidator $postValidator;
    public function __construct()
    {
        $this->imageValidator = new ImageValidator();
        $this->postValidator = new PostValidator();
    }

    public function validateData(BlogPost $post, array $fileData): void
        {
            $image = $fileData['image'];

            $validationErrors = $this->imageValidator->validate($image);
            if (count($validationErrors) > 0) {
                throw new BlogPostCreationException($validationErrors);
            }

            $validationErrors =  $this->postValidator->validate($post);
            if (count($validationErrors) > 0) {
                throw new BlogPostCreationException($validationErrors);
            }
        }
}