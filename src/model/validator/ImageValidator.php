<?php
declare(strict_types=1);

namespace App\model\validator;

class ImageValidator implements ValidatorInterface
{
    public static function validate($data): array
    {
        $errors = [];

        if(!$data) {
            return $errors;
        }

        if($data['size'] === 0 ) {
            return $errors;
        }

        if ($data['size'] > 1000000) {
            $errors['image'] = "L'envoi n'a pas pu être effectué, erreur ou image trop volumineuse (max 10 MO)";
        }

        $fileInfo = pathinfo($data['name']);
        $extension = strtolower($fileInfo['extension']);
        $allowedExtensions = ['jpg', 'jpeg', 'gif', 'png'];
        if (!in_array($extension, $allowedExtensions)) {
            $errors['image'] = "L'envoi n'a pas pu être effectué, l'extension {$extension} n'est pas autorisée";
        }

        $path = dirname(__DIR__,3) . '/public/assets/images/';
        if (!is_dir($path)) {
            $errors['image'] = "L'envoi n'a pas pu être effectué, le dossier uploads est manquant";
        }

        if (empty($errors)) {
            move_uploaded_file($data['tmp_name'], $path . basename($data['name']));
            return $errors;
        }
        return $errors;
    }
}