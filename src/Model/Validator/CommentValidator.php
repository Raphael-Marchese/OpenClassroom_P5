<?php

declare(strict_types=1);

namespace App\Model\Validator;

use App\Exception\CommentException;
use App\Model\Entity\Comment;

/**
 * @param Comment $data
 * @return array<string, string>
 */
class CommentValidator implements ValidatorInterface
{

    public function validate($data): void
    {
        $errors = [];

        if (empty($data->content)) {
            $errors['content'] = 'Votre commentaire est vide';
        }
        if (empty($data->author)) {
            $errors['author'] = 'Vous devez être connecté pour écrire un article';
        }

        if(count($errors) > 0)
        {
            throw new CommentException($errors);
        }

    }

    public function supports($object): bool
    {
        return $object instanceof Comment;
    }
}
