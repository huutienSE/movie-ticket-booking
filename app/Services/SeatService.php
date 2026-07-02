<?php
namespace App\Services;

use App\Models\SeatModel;

class SeatService {
    private $seatModel;

    public function __construct() {
        $this->seatModel = new SeatModel();
    }

    /**
     * Lấy danh sách ghế theo phòng
     */
    public function getSeatsByRoom($room_id) {
        if ($room_id <= 0) return [];
        return $this->seatModel->getByRoom($room_id);
    }

    /**
     * Lấy ghế đã đặt theo suất chiếu
     */
    public function getBookedSeats($showtime_id) {
        if ($showtime_id <= 0) return [];
        return $this->seatModel->getBookedSeats($showtime_id);
    }

    /**
     * Lấy sơ đồ ghế (có trạng thái booked/available)
     */
    public function getSeatMap($showtime_id, $room_id) {
        if ($showtime_id <= 0 || $room_id <= 0) return [];
        
        $allSeats = $this->getSeatsByRoom($room_id);
        $bookedSeats = $this->getBookedSeats($showtime_id);

        $seatMap = [];
        foreach ($allSeats as $seat) {
            $seat['status'] = in_array($seat['id'], $bookedSeats) ? 'booked' : 'available';
            $seatMap[] = $seat;
        }

        return $seatMap;
    }
    
    /**
     * Lấy thông tin ghế theo danh sách ID
     */
    public function getSeatsByIds($seatIds) {
        if (empty($seatIds)) return [];
        return $this->seatModel->getByIds($seatIds);
    }
}