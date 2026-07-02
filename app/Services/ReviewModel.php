<?php
namespace App\Models;

use App\Config\Database;

class ReviewModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    // ✅ THÊM PHƯƠNG THỨC NÀY
    public function getByMovie($movie_id) {
        $sql = "SELECT r.*, u.first_name, u.last_name 
                FROM reviews r
                LEFT JOIN users u ON r.user_id = u.id
                WHERE r.movie_id = ?
                ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $movie_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $reviews = [];
        while ($row = $result->fetch_assoc()) {
            $reviews[] = $row;
        }
        return $reviews;
    }

    // ✅ THÊM PHƯƠNG THỨC NÀY
    public function create($userId, $movieId, $rating, $comment) {
        $sql = "INSERT INTO reviews (user_id, movie_id, rating, comment, created_at) 
                VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiis", $userId, $movieId, $rating, $comment);
        return $stmt->execute();
    }
}