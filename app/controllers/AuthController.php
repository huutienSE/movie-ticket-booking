<?php

namespace app\controllers;

use app\core\Controller;
use app\services\AuthService;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->authService = new AuthService();
    }

    public function showLogin()
    {
        return $this->view('auth/login');
    }

    public function showRegister()
    {
        return $this->view('auth/register');
    }

    public function register()
    {
        try {
            $this->authService->register($_POST);

            header('Location: /login');
            exit;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function login()
    {
        try {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $this->authService->login($email, $password);

            $_SESSION['user'] = [
                'id' => $user['id'],
                'full_name' => $user['first_name'] . ' ' . $user['last_name'],
                'email' => $user['email'],
                'role' => $user['role']
            ];

            header('Location: /');
            exit;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function logout()
    {
        $this->authService->logout();

        header('Location: /login');
        exit;
    }
}