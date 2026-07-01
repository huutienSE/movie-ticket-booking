<?php
namespace App\Models;

use App\Config\Database;

class ShowtimeModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function getShowtimesByMovie($movieId, $date = null) {
        $query = "
            SELECT s.*, r.name as room_name, t.name as theatre_name 
            FROM showtimes s
            JOIN rooms r ON s.room_id = r.id
            JOIN theatres t ON r.theatre_id = t.id
            WHERE s.movie_id = ? AND s.status = 'active'
        ";
        
        if ($date) {
            $query .= " AND s.show_date = ?";
        }
        $query .= " ORDER BY s.show_date ASC, s.start_time ASC";

        $stmt = mysqli_prepare($this->conn, $query);
        if ($date) {
            mysqli_stmt_bind_param($stmt, "is", $movieId, $date);
        } else {
            mysqli_stmt_bind_param($stmt, "i", $movieId);
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $showtimes = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $showtimes[] = $row;
            }
        }
        return $showtimes;
    }

    public function getShowtimeDetails($showtimeId) {
        $query = "
            SELECT s.*, m.title, m.poster, m.age_restriction, r.name as room_name, t.name as theatre_name, t.address
            FROM showtimes s
            JOIN movies m ON s.movie_id = m.id
            JOIN rooms r ON s.room_id = r.id
            JOIN theatres t ON r.theatre_id = t.id
            WHERE s.id = ?
        ";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $showtimeId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
        return null;
    }

    public function getError() {
        return mysqli_error($this->conn);
    }
}
