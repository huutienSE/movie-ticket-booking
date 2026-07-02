<?php
namespace App\Controllers;

use App\Services\AuthService;
use App\Services\UserService;

class AuthController {
    private $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $_SESSION['error_msg'] = "Vui lòng nhập đầy đủ thông tin!";
                header("Location: login.php");
                exit;
            }

            $result = $this->authService->login($email, $password);
            // chuyển hướng người dùng đến trang sau đăng nhập
            if ($result['status'] === 'success') {
                if ($result['role'] === 'admin') {
                    header("Location: admin/index.php");
                } else {
                    $redirect = trim($_POST['redirect'] ?? '');
                    if (!empty($redirect)) {
                        header("Location: " . $redirect);
                    } else {
                        header("Location: index.php");
                    }
                }
                exit;
            } else {
                $_SESSION['error_msg'] = $result['message'];
                header("Location: login.php");
                exit;
            }
        }
    }

    public function handleLogout() {
        $this->authService->logout();
        header("Location: index.php");
        exit;
    }

    public function handleRegister() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'first_name' => trim($_POST['first_name'] ?? ''),
                'last_name'  => trim($_POST['last_name']  ?? ''),
                'email'      => trim($_POST['email']      ?? ''),
                'phone'      => trim($_POST['phone']      ?? ''),
                'password'   => $_POST['password']        ?? '',
                'role'       => 'user',
            ];

            $confirmPassword = $_POST['confirm_password'] ?? '';
            if ($data['password'] !== $confirmPassword) {
                $_SESSION['error_msg'] = 'Mật khẩu nhập lại không khớp!';
                header('Location: registration.php');
                exit;
            }

            $userService = new UserService();
            $result = $userService->addUser($data);

            if ($result['status'] === 'success') {
                $_SESSION['success_msg'] = 'Đăng ký thành công! Vui lòng đăng nhập.';
                header('Location: login.php');
            } else {
                $_SESSION['error_msg'] = $result['message'];
                header('Location: registration.php');
            }
            exit;
        }
    }
}
