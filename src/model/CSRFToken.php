<?php
declare(strict_types=1);

namespace App\model;

class CSRFToken
{
    public static function generateToken()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(35));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateToken($token): bool
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}