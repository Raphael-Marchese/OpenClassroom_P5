<?php
declare(strict_types=1);

namespace App\entity;

class User
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $username,
        public string $email,
        public string $password,
        public string $role
    ) {}

    public readonly int $id;

    public function setId(int $id): void
    {
        if (!isset($this->id)) {
            $this->id = $id;
        }
    }
}
