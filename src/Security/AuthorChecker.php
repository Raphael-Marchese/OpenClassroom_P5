<?php
declare(strict_types=1);

namespace App\Security;

use App\Exception\AccessDeniedException;
use App\Exception\UserNotFoundException;
use App\Service\UserProvider;

class AuthorChecker
{
    private UserProvider $userProvider;
    public function __construct()
    {
        $this->userProvider = new UserProvider();
    }

    /**
     * @throws UserNotFoundException
     * @throws AccessDeniedException
     */
    public function checkAuthor($data): void
    {
        $user = $this->userProvider->getUser();
        $errors = [];
        if ($data->author->id !== $user->id) {
            $errors['author'] = "Vous ne pouvez pas modifier un article dont vous n'êtes pas l'auteur";
            throw new AccessDeniedException($errors);

        }
    }
}
