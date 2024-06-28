<?php
declare(strict_types=1);

namespace App\Model\Service;

use App\Entity\BlogPost;
use App\Entity\User;
use App\Exceptions\AccessDeniedException;
use App\Exceptions\BlogPostCreationException;
use App\Model\Validator\AdminValidator;
use App\Model\Validator\ImageValidator;
use App\Model\Validator\PostValidator;

class ValidateUser
{
    private AdminValidator $adminValidator;

    public function __construct()
    {
        $this->adminValidator = new AdminValidator();
    }

    public function validateRole(User $user): void
    {
        $validationErrors = $this->adminValidator->validate($user);
        if (count($validationErrors) > 0) {
            throw new AccessDeniedException($validationErrors);
        }

    }
}