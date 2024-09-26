<?php

declare(strict_types=1);

namespace App\Model;

use App\Exception\CSRFTokenException;
use Exception;

class CSRFToken extends Exception
{
    public function generateToken(string $stringToHash): string
    {
        return hash('sha256', $stringToHash);
    }

    /**
     * @throws CSRFTokenException
     */
    public function validateToken(string $token, string $stringToValidate): array
    {
        $errors = [];
        $tokenToVerify = hash('sha256', $stringToValidate);
        if ($token !== $tokenToVerify) {
            $errors['csrf_token'] = 'Le token csrf n\'est pas valide';
            throw new CSRFTokenException($errors);
        }

        return $errors;
    }
}
