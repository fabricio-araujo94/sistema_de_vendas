<?php

namespace App\Controller;

use App\Middleware\AuthMiddleware;
use App\DAO\UserDAO;

class UserController extends BaseController
{
    private UserDAO $userDAO;

    public function __construct()
    {
        AuthMiddleware::checkAdminRole();
        $this->userDAO = new UserDAO();
    }

    public function index(): void
    {
        $users = $this->userDAO->findAll();

        $this->render('users/index', [
            'pageTitle' => 'User Management',
            'users'     => $users
        ]);
    }

    public function create(): void
    {
        $this->render('users/create', [
            'pageTitle' => 'Add New User'
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/users');
        }

        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? ''; 
        $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_SPECIAL_CHARS);

        if (!$name || !$email || strlen($password) < 6 || !in_array($role, ['admin', 'seller'])) {
            die("Invalid data. Passwords must be at least 6 characters.");
        }

        if ($this->userDAO->insert($name, $email, $password, $role)) {
            $this->redirect('/users');
        } else {
            $this->redirect('/users/create');
        }
    }
}