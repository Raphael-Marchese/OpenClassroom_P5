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

        return $sanitizedData;
    }
}
