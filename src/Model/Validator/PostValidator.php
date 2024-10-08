<?php
declare(strict_types=1);

namespace App\Model\Validator;

use App\Model\Entity\BlogPost;
use App\Exception\BlogPostException;
use Override;

/**
 * @param BlogPost $data
 * @return array<string, string>
 */
class PostValidator implements ValidatorInterface
{
    /**
     * @throws BlogPostException
     */
    #[Override]
    public function validate($data): void
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
            $errors['author'] = 'Vous devez être connecté pour écrire un commentaire';
        }
        if(count($errors) > 0)
        {
            throw new BlogPostException($errors);
        }
    }

    public function supports($object): bool
    {
        return $object instanceof BlogPost;
    }
}