<?php
declare(strict_types=1);

namespace App\Model\Validator;

use App\Entity\User;
use App\Model\Service\UserProvider;

/**
 * @implements ValidatorInterface<User>
 */
class AdminValidator implements ValidatorInterface
{
    #[\Override]
    public function validate($data): array
    {
        $errors = [];
        if ($data->role !== 'ROLE_ADMIN') {
            $errors['admin'] = "Vous ne pouvez pas supprimer d'articles si vous n'êtes pas administrateur de ce site";
        }
        return $errors;
    }
}