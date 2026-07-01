<?php
namespace App\Services;

use App\Models\SeatModel;

class SeatService {
    private $model;

    public function __construct() {
        $this->model = new SeatModel();
    }

    public function getSeatMap($showtimeId, $roomId) {
        if ($showtimeId <= 0 || $roomId <= 0) return [];
        
        $allSeats = $this->model->getSeatsByRoom($roomId);
        $bookedSeats = $this->model->getBookedSeats($showtimeId);

        $seatMap = [];
        foreach ($allSeats as $seat) {
            $seat['status'] = in_array($seat['id'], $bookedSeats) ? 'booked' : 'available';
            $seatMap[] = $seat;
        }

        return $seatMap;
    }
}
