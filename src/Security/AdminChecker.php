<?php
declare(strict_types=1);

namespace App\Security;

use App\Exception\AccessDeniedException;

class AdminChecker
{
    public function checkAdmin($data): void
    {
        $errors = [];
        if ($data->role !== 'ROLE_ADMIN') {
            $errors['admin'] = "Vous ne pouvez pas supprimer d'articles si vous n'êtes pas administrateur de ce site";
            throw new AccessDeniedException($errors);
        }
    }
}