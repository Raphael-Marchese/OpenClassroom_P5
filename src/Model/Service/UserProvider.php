<?php
declare(strict_types=1);

namespace App\Model\Service;

use App\Entity\User;
use App\Exception\UserNotFoundException;
use App\Model\Repository\UserRepository;

class UserProvider
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function getUser(): User
    {
        $userId = $_SESSION['LOGGED_USER']['user_id'] ?? null;
        $user = $this->userRepository->findById($userId);

        if (null === $user) {
            $errors['userNotFound'] = 'User not found';
            throw new UserNotFoundException($errors);
        }

        return $user;
    }
}
