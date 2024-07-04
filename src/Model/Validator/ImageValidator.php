<?php
declare(strict_types=1);

namespace App\Model\Validator;

use App\Exception\ImageException;
use App\Model\File\File;

/**
 * @param File $data
 * @return array<string, string>
 */
class ImageValidator implements ValidatorInterface
{
    #[\Override]
    public function validate($data): void
    {
        $errors = [];

        if(!$data) {
            return;
        }

        if ($data->size === 0)
        {
            return;
        }

        if ($data->size > 1000000) {
            $errors['image'] = "L'envoi n'a pas pu être effectué, erreur ou image trop volumineuse (max 10 MO)";
        }

        $fileInfo = pathinfo($data->name);
        $extension = strtolower($fileInfo['extension']);
        $allowedExtensions = ['jpg', 'jpeg', 'gif', 'png'];
        if (!in_array($extension, $allowedExtensions)) {
            $errors['image'] = "L'envoi n'a pas pu être effectué, l'extension {$extension} n'est pas autorisée";
        }

        $path = dirname(__DIR__,3) . '/public/assets/images/';
        if (!is_dir($path)) {
            $errors['image'] = "L'envoi n'a pas pu être effectué, le dossier uploads est manquant";
        }

        if (count($errors) > 0) {
            throw new ImageException($errors);
        }

        move_uploaded_file($data->tmpName, $path . basename($data->name));
    }

    public function supports($object): bool
    {
        return $object instanceof File;
    }
}
