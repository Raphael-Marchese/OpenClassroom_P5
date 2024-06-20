<?php
declare(strict_types=1);

namespace App\model\validator;

class ImageValidator implements ValidatorInterface
{
    public static function validate($data): array
    {
        $errors = [];

        if ($data['size'] > 1000000) {
            $errors['image'] = "L'envoi n'a pas pu être effectué, erreur ou image trop volumineuse (max 10 MO)";
        }

        $extension = $data['extension'];
        $allowedExtensions = ['jpg', 'jpeg', 'gif', 'png'];
        if (!in_array($extension, $allowedExtensions)) {
            $errors['image'] = "L'envoi n'a pas pu être effectué, l'extension {$extension} n'est pas autorisée";
        }

        $path = __DIR__ . '/public/assets/images/';
        if (!is_dir($path)) {
            $errors['image'] = "L'envoi n'a pas pu être effectué, le dossier uploads est manquant";
        }

        return $errors;
    }
}