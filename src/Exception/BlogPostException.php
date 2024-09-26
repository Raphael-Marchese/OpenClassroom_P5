<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class BlogPostException extends Exception
{
    public function __construct(public array $validationErrors = [])
    {
        parent::__construct('', 0, null);
    }
}
