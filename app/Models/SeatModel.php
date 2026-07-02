<?php
namespace App\Models;

use App\Config\Database;

class SeatModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    /**
     * Lấy ghế theo phòng
     */
    public function getByRoom($room_id) {
        $sql = "SELECT s.*, st.name as seat_type_name, st.price as seat_type_price
                FROM seats s
                LEFT JOIN seat_types st ON s.seat_type_id = st.id
                WHERE s.room_id = ? AND s.is_active = 1
                ORDER BY s.seat_row, s.seat_number";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $room_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $seats = [];
        while ($row = $result->fetch_assoc()) {
            $seats[] = $row;
        }
        return $seats;
    }

    /**
     * Lấy ghế đã đặt theo suất chiếu
     */
    public function getBookedSeats($showtime_id) {
        $sql = "SELECT seat_id FROM tickets WHERE showtime_id = ? AND status != 'canceled'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $showtime_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $booked = [];
        while ($row = $result->fetch_assoc()) {
            $booked[] = $row['seat_id'];
        }
        return $booked;
    }

    /**
     * Lấy ghế theo danh sách ID
     */
    public function getByIds($seatIds) {
        if (empty($seatIds)) return [];
        
        $placeholders = implode(',', array_fill(0, count($seatIds), '?'));
        $sql = "SELECT s.*, st.name as seat_type_name, st.price as seat_type_price
                FROM seats s
                LEFT JOIN seat_types st ON s.seat_type_id = st.id
                WHERE s.id IN ($placeholders)";
        $stmt = $this->conn->prepare($sql);
        
        $types = str_repeat('i', count($seatIds));
        $stmt->bind_param($types, ...$seatIds);
        $stmt->execute();
        $result = $stmt->get_result();
        $seats = [];
        while ($row = $result->fetch_assoc()) {
            $seats[] = $row;
        }
        return $seats;
    }
}