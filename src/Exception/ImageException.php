<?php
declare(strict_types=1);

namespace App\Exception;

class ImageException extends \RuntimeException
{
    public function __construct(public array $errors = [])
    {
        parent::__construct('', 0, null);
    }
}