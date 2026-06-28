<?php
namespace App\Service;

use App\Models\AuthModel;

class AuthService{
    private AuthModel $authModel;

    public function __construct()
    {
        $this->authModel = new AuthModel();
    }

    //register
    public function register(array $data): bool
    {
        if (empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) || empty($data['password']) || empty($data['confirm_password']) || empty($data['phone'])
        ) {
            throw new \Exception('Vui lòng nhập đầy đủ thông tin.');
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Email không hợp lệ.');
        }

        if ($data['password'] !== $data['confirm_password']) {
            throw new \Exception('Mật khẩu xác nhận không khớp.');
        }

        if (strlen($data['password']) < 6) {
            throw new \Exception('Mật khẩu phải có ít nhất 6 ký tự.');
        }

        if ($this->authModel->emailExists($data['email'])) {
            throw new \Exception('Email đã tồn tại.');
        }

        if ($this->authModel->phoneExists($data['phone'])) {
            throw new \Exception('Số điện thoại đã tồn tại.');
        }

        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

        $userData = [
            'first_name' => trim($data['first_name']),
            'last_name' => trim($data['last_name']),
            'email' => trim($data['email']),
            'password' => $passwordHash,
            'phone' => trim($data['phone']),
            'birth_date' => $data['birth_date'] ?? null,
            'role' => 'USER'
        ];

        $userId = $this->authModel->create($userData);

        return (int) $userId > 0;
    }

    //login
    public function login(string $email, string $password): array
    {
        $user = $this->authModel->findByEmail($email);

        if (!$user) {
            throw new \Exception('Email hoặc mật khẩu không đúng.');
        }

        if (!password_verify($password, $user['password'])) {
            throw new \Exception('Email hoặc mật khẩu không đúng.');
        }

        return $user;
    }
}
