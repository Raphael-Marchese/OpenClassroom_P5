<?php
declare(strict_types=1);

namespace App\model\validator;

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

        if (empty($data->username)) {
            $errors['username'] = 'Renseigner un pseudo est obligatoire';
        }

        if (empty($data->email)) {
            $errors['email'] = 'L\'email est obligatoire.';
        } elseif (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Ceci n\'est pas une adresse email valide.';
        }

        if (empty($data->password)) {
            $errors['password'] = 'Le mot de passe est obligatoire.';
        }

        return $errors;
    }
}