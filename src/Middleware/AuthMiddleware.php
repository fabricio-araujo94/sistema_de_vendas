<?php

namespace App\Middleware;

class AuthMiddleware {
    public static function checkAuthentication(): void {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['login_error'] = 'Access denied. Please log in to continue.';

            header("LOcation: /");
            exit;
        }
    }

    public static function checkAdminRole(): void {
        self::checkAuthentication();

        if ($_SESSION['user_role'] !== 'admin') {
            header("Location: /pos");
            exit;
        }
    }
}