<?php
declare(strict_types=1);

namespace App\Model\Validator;

use App\Model\Entity\User;
use App\Exception\UserException;

/**
 * @param User $data
 * @return array<string, string>
 */
class UserValidator implements ValidatorInterface
{
    #[\Override]
    public function validate($data): void
    {
        $errors = [];

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


        if (count($errors) > 0) {
            throw new UserException($errors);
        }
    }

    public function supports($object): bool
    {
        return $object instanceof User;
    }
}
