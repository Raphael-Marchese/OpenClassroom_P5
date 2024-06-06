<?php
declare(strict_types=1);

namespace App\model;

use App\entity\User;

class UserValidator implements ValidatorInterface
{
    /**
     * @param $data
     * @return array<string, string>
     */
    public static function validate($data): array
    {
        $errors = [];

        if (!$data instanceof User) {
            return [];
        }

        if (empty($user->username)) {
            $errors['username'] = 'Renseigner un pseudo est obligatoire';
        }

        if (empty($user->email)) {
            $errors['email'] = 'L\'email est obligatoire.';
        } elseif (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Ceci n\'est pas une adresse email valide.';
        }

        if (empty($user->password)) {
            $errors['password'] = 'Le mot de passe est obligatoire.';
        }

        return $errors;
    }
}