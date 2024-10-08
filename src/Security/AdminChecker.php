<?php

declare(strict_types=1);

namespace App\Security;

use App\Exception\AccessDeniedException;

class AdminChecker
{
    /**
     * @throws AccessDeniedException
     */
    public function isAdmin($data): void
    {
        $errors = [];
        if ($data->role !== 'ROLE_ADMIN') {
            $errors['admin'] = "Seuls les administrateurs ont accès à cette fonctionnalité";
            throw new AccessDeniedException($errors);
        }
    }
}
