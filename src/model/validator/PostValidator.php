<?php
declare(strict_types=1);

namespace App\model\validator;

use App\entity\BlogPost;

class PostValidator implements ValidatorInterface
{

    public static function validate($data): array
    {
        $errors = [];

        if (!$data instanceof BlogPost) {
            return [];
        }

        if (empty($data->title)) {
            $errors['title'] = 'Renseigner un titre est obligatoire';
        }

        if (empty($data->chapo)) {
            $errors['chapo'] = 'Le chapo est obligatoire. (phrase d\'accroche de l\'article';
        }
        if (empty($data->content)) {
            $errors['content'] = 'L\'article doit avoir un contenu';
        }

        return $errors;    }
}