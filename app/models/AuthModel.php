<?php

namespace App\Models;

class AuthModel extends BaseModel
{
    protected string $table = 'users';

    // kiểm tra email tồn tại, và trả về danh sách user
    public function findByEmail(string $email): array|false
    {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE email = :email
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'email' => $email
        ]);

        return $stmt->fetch();
    }

    public function emailExists(string $email): bool
    {
        $sql = "
            SELECT id
            FROM {$this->table}
            WHERE email = :email
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'email' => $email
        ]);

        return $stmt->fetch() !== false;
    }

    public function phoneExists(string $phone): bool
    {
        $sql = "
            SELECT id
            FROM {$this->table}
            WHERE phone = :phone
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'phone' => $phone
        ]);

        return $stmt->fetch() !== false;
    }

    // public function createUser(array $data): bool
    // {
    //     $sql = "
    //         INSERT INTO users (
    //             first_name,
    //             last_name,
    //             email,
    //             password,
    //             phone,
    //             birth_date,
    //             role
    //         )
    //         VALUES (
    //             :first_name,
    //             :last_name,
    //             :email,
    //             :password,
    //             :phone,
    //             :birth_date,
    //             :role
    //         )
    //     ";

    //     $stmt = $this->db->prepare($sql);

    //     return $stmt->execute([
    //         'first_name' => $data['first_name'],
    //         'last_name' => $data['last_name'],
    //         'email' => $data['email'],
    //         'password' => $data['password'],
    //         'phone' => $data['phone'],
    //         'birth_date' => $data['birth_date'] ?? null,
    //         'role' => $data['role'] ?? 'user'
    //     ]);
    // }
}