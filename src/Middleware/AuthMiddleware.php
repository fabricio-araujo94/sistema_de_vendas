<?php

namespace App\Middleware;

use Exception;

class AuthMiddleware {
    public static function checkAuthentication(): void
    {
        if (empty($_SESSION['user_id'])) {
            if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'testing') {
                throw new Exception("Redirect: /login");
            }
            
            header('Location: /login');
            exit;
        }
    }

    public static function checkAdminRole(): void
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'testing') {
                throw new Exception("Redirect: /pos");
            }
            
            header('Location: /pos');
            exit;
        }
    }
}