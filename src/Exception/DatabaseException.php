<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

class DatabaseException extends Exception
{
    public function __construct($message = "Erreur de base de données")
    {
        parent::__construct($message);
    }
}
