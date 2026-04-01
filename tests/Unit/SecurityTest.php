<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Middleware\AuthMiddleware;
use App\Middleware\CSRFMiddleware;
use Exception;

class SecurityTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
        $_POST = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_ENV['APP_ENV'] = 'testing'; 
    }

    public function test_csrf_generates_a_valid_token(): void
    {
        $token = CSRFMiddleware::getToken();

        $this->assertNotEmpty($token);
        $this->assertEquals(64, strlen($token), "Token should be 64 characters long (hexadecimal).");
        $this->assertEquals($token, $_SESSION['csrf_token'], "Token should be stored in the session.");
    }

    public function test_csrf_allows_post_request_with_valid_token(): void
    {
        // Arrange
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $validToken = 'a1b2c3d4e5f6g7h8i9j0';
        $_SESSION['csrf_token'] = $validToken;
        $_POST['csrf_token'] = $validToken;

        CSRFMiddleware::verifyRequest();
        $this->assertTrue(true);
    }

    public function test_csrf_blocks_post_request_with_invalid_token(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SESSION['csrf_token'] = 'valid_token_123';
        $_POST['csrf_token'] = 'invalid_hacker_token';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("CSRF validation failed");

        CSRFMiddleware::verifyRequest();
    }

    public function test_auth_blocks_unauthenticated_users(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Redirect: /login");

        AuthMiddleware::checkAuthentication();
    }

    public function test_rbac_allows_admin_access(): void
    {
        $_SESSION['user_id'] = 1;
        $_SESSION['user_role'] = 'admin';

        AuthMiddleware::checkAdminRole();
        $this->assertTrue(true);
    }

    public function test_rbac_blocks_seller_from_admin_areas(): void
    {
        $_SESSION['user_id'] = 2;
        $_SESSION['user_role'] = 'seller';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Redirect: /pos");

        AuthMiddleware::checkAdminRole();
    }
}