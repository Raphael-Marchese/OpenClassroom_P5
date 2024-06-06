<?php
declare(strict_types=1);

namespace App\model\validator;

use App\model\CSRFToken;

class FormValidator implements ValidatorInterface
{

    /**
     * @throws \Exception
     */
    public static function validate($data): array
    {
        $sanitizedData = [];

        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitizedData[$key] = htmlspecialchars($value);
            } else {
                $sanitizedData[$key] = $value;
            }
        }
        if ($sanitizedData['csrf_token'] && !CSRFToken::validateToken($sanitizedData['csrf_token']))
        {
            throw new \RuntimeException('Invalid CSRF token');
        }

        return $sanitizedData;
    }
}