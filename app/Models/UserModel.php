<?php

namespace App\Models;

use App\Config\Database;

class UserModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function findByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";

        $stmt = mysqli_prepare($this->conn, $sql);

        if (!$stmt) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_assoc($result) ?: null;
    }

    public function emailExists($email)
    {
        return $this->findByEmail($email) !== null;
    }

    public function phoneExists($phone)
    {
        $sql = "SELECT id FROM users WHERE phone = ? LIMIT 1";

        $stmt = mysqli_prepare($this->conn, $sql);

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param($stmt, "s", $phone);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_assoc($result) !== null;
    }

    public function createUser($data)
    {
        $sql = "INSERT INTO users (
                    first_name,
                    last_name,
                    email,
                    password,
                    phone,
                    birth_date,
                    role
                ) VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($this->conn, $sql);

        if (!$stmt) {
            return false;
        }

        mysqli_stmt_bind_param(
            $stmt,
            "sssssss",
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['password'],
            $data['phone'],
            $data['birth_date'],
            $data['role']
        );

        return mysqli_stmt_execute($stmt);
    }

    public function getError()
    {
        return mysqli_error($this->conn);
    }
}