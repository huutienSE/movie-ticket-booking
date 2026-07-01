<?php
namespace App\Models;

use App\Config\Database;

class ReviewModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function getReviewsByMovie($movieId) {
        $query = "
            SELECT r.*, u.first_name, u.last_name 
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.movie_id = ?
            ORDER BY r.created_at DESC
        ";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $movieId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $reviews = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $reviews[] = $row;
            }
        }
        return $reviews;
    }

    public function insertReview($userId, $movieId, $rating, $comment) {
        $stmt = mysqli_prepare($this->conn, "INSERT INTO reviews (user_id, movie_id, rating, comment) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iiis", $userId, $movieId, $rating, $comment);
        return mysqli_stmt_execute($stmt);
    }

    public function getError() {
        return mysqli_error($this->conn);
    }
}
