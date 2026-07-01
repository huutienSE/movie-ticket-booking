<?php
namespace App\Models;

use App\Config\Database;

class SeatModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function getSeatsByRoom($roomId) {
        $query = "
            SELECT s.*, st.name as type_name, st.price as base_price_extra
            FROM seats s
            JOIN seat_types st ON s.seat_type_id = st.id
            WHERE s.room_id = ? AND s.is_active = TRUE
            ORDER BY s.seat_row ASC, s.seat_number ASC
        ";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $roomId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $seats = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $seats[] = $row;
            }
        }
        return $seats;
    }

    public function getBookedSeats($showtimeId) {
        $query = "
            SELECT seat_id
            FROM tickets
            WHERE showtime_id = ? AND status IN ('booked', 'used')
        ";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $showtimeId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $bookedSeats = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $bookedSeats[] = $row['seat_id'];
            }
        }
        return $bookedSeats;
    }

    public function getError() {
        return mysqli_error($this->conn);
    }
}
