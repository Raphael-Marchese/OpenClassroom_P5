<?php

declare(strict_types=1);

namespace App\Security;

use App\Exception\AccessDeniedException;
use App\Exception\UserNotFoundException;
use App\Service\UserProvider;

class AuthorisationChecker
{
    private UserProvider $userProvider;

    private AuthorChecker $authorChecker;

    private AdminChecker $adminChecker;

    public function __construct()
    {
        $this->userProvider = new UserProvider();
        $this->authorChecker = new AuthorChecker();
        $this->adminChecker = new AdminChecker();
    }

    /**
     * @throws UserNotFoundException
     * @throws AccessDeniedException
     */
    public function checkAuthorisation($data): void
    {
        $user = $this->userProvider->getUser();
        $this->adminChecker->isAdmin($user);
        $this->authorChecker->checkAuthor($user);
    }
}