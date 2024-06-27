<?php
declare(strict_types=1);

namespace App\Model\Validator;

use App\Entity\BlogPost;

/**
 * @implements ValidatorInterface<BlogPost>
 */
class PostValidator implements ValidatorInterface
{
    #[\Override]
    public function validate($data): array
    {
        $errors = [];

        if (empty($data->title)) {
            $errors['title'] = 'Renseigner un titre est obligatoire';
        }

        if (empty($data->chapo)) {
            $errors['chapo'] = 'Le chapo est obligatoire. (phrase d\'accroche de l\'article';
        }
        if (empty($data->content)) {
            $errors['content'] = 'L\'article doit avoir un contenu';
        }

        if (empty($data->author)) {
            $errors['author'] = 'Vous devez être connecté pour écrire un article';
        }

        return $errors;
    }
}