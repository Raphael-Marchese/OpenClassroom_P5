<?php

declare(strict_types=1);

namespace App\Model\Contact;

class Contact
{
    public function __construct(
        public string $email,
        public string $message,
        public ?string $firstName,
        public ?string $lastName,
        public ?string $subject
    ) {
    }
}
