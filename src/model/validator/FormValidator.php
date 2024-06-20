<?php
declare(strict_types=1);

namespace App\model\validator;

use App\model\CSRFToken;

class FormValidator
{

    /**
     * @throws \Exception
     */
    public static function sanitize($data): array
    {
        $sanitizedData = [];

        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitizedData[$key] = htmlspecialchars($value);
            } else {
                $sanitizedData[$key] = $value;
            }
        }

        return $sanitizedData;
    }
}
