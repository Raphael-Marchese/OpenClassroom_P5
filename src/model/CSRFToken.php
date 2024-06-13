<?php
declare(strict_types=1);

namespace App\model;

class CSRFToken
{
    public static function generateToken(string $stringToHash): string
    {
        return hash('sha256', $stringToHash);
    }

    public static function validateToken(string $token, string $stringToValidate): array
    {
        $errors = [];
        $tokenToVerify = hash('sha256', $stringToValidate);
        if ($token !== $tokenToVerify) {
            $errors['csrf_token'] = 'Le token csrf n\'est pas valide';
        }

        return $errors;
    }
}