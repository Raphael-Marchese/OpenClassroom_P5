<?php
declare(strict_types=1);

namespace App\Model\Validator;

use App\Model\CSRFToken;

class FormSanitizer
{

    /**
     * @throws \Exception
     */
    public function sanitize($data): array
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
