<?php
declare(strict_types=1);

namespace App\Exceptions;

class BlogPostUpdateException extends \RuntimeException
{
    public function __construct(public array $validationErrors = [])
    {
        parent::__construct('', 0, null);
    }
}