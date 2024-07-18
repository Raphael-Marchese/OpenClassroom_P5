<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Entity\User;

class UserExtractor
{
    public function extractUser ($sanitizedData): User
    {
        $username = $sanitizedData['username'] ?? null ;
        $firstName = $sanitizedData['firstName'] ?? null ;
        $lastName = $sanitizedData['lastName'] ?? null ;
        $email = $sanitizedData['email'] ?? null ;
        $plainPassword = $sanitizedData['password'] ?? null ;

        $password = $plainPassword ? password_hash($plainPassword, PASSWORD_DEFAULT) : $plainPassword;

        return new User(firstName: $firstName, lastName: $lastName, username: $username, email: $email, password: $password, role: "['ROLE_USER']");
    }
}