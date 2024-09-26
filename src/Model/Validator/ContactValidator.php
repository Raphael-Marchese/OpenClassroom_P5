<?php

declare(strict_types=1);

namespace App\Model\Validator;

use App\Exception\BlogPostException;
use App\Exception\ContactException;
use App\Model\Contact\Contact;

class ContactValidator implements ValidatorInterface
{

    /**
     * @throws ContactException
     */
    public function validate($data): void
    {
        $errors = [];

        if (empty($data->email)) {
            $errors['email'] = 'Renseigner un email valide est obligatoire';
        }

        if (empty($data->message)) {
            $errors['message'] = 'Le message doit obligatoirement avoir un contenu';
        }

        if (count($errors) > 0) {
            throw new ContactException($errors);
        }
    }

    public function supports($object): bool
    {
        return $object instanceof Contact;
    }
}
