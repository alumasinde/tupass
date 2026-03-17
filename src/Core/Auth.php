<?php

namespace App\Core;

class Auth
{
    public static function user()
    {
        return $_SESSION['user'] ?? null;
    }

    public static function id()
    {
        return $_SESSION['user']['id'] ?? null;
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function login(array $user)
    {
        session_regenerate_id(true);
        $_SESSION['user'] = $user;
    }

    public static function logout()
    {
        session_destroy();
    }
}
