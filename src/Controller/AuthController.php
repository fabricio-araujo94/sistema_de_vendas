<?php

namespace App\Controller;

use App\DAO\UserDAO;

class AuthController extends BaseController {
    public function loginForm(): void {
        if(isset($_SESSION['user_id'])) {
            $this->redirect("/pos");
        }

        $error = $_SESSION["login_error"] ?? null;
        unset($_SESSION['login_error']);

        $this->render('auth/login', [
            'pageTitle' => 'System Login',
            'error' => $error
        ]);
    }

    public function authenticate(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/');
        }

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            $_SESSION['login_error'] = 'Please fill in all fields.';
            $this->redirect('/');
        }

        $userDAO = new UserDAO();
        $user = $userDAO->findByEmail($email);

        if ($user && password_verify($password, $user->getPassword())) {
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['user_name'] = $user->getName();
            $_SESSION['user_role'] = $user->getRole();

            session_regenerate_id(true);

            $this->redirect("/pos");
        } else {
            $_SESSION['login_error'] = 'Invalid email or password.';
            $this->redirect('/');
        }
    }

    public function logout(): void {
        $_SESSION = [];
        session_destroy();
        $this->redirect('/');
    }
}