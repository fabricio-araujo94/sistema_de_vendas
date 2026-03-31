<?php

namespace App\Middleware;

use Exception;

class CSRFMiddleware
{
    public static function getToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            // Generates 32 bytes of cryptographically secure data and converts it to hexadecimal.
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyRequest(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $submittedToken = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

            $sessionToken = $_SESSION['csrf_token'] ?? '';

            if (empty($submittedToken) || empty($sessionToken) || !hash_equals($sessionToken, $submittedToken)) {
                unset($_SESSION['csrf_token']);
                
                http_response_code(403);
                
                if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                    echo json_encode(['error' => 'CSRF token validation failed.']);
                } else {
                    die("<h3>403 Forbidden</h3><p>CSRF token validation failed. Please go back, refresh the page, and try again.</p>");
                }
                exit;
            }
        }
    }
}