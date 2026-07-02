<?php
namespace App\Controllers;

use App\Services\ShowtimeService;

class ShowtimeController {
    private $showtimeService;

    public function __construct() {
        $this->showtimeService = new ShowtimeService();
    }

    public function getShowtimesByMovie($movie_id) {
        return $this->showtimeService->getShowtimesByMovie($movie_id);
    }

    public function getShowtimeById($id) {
        return $this->showtimeService->getShowtimeById($id);
    }

    public function getShowtimeDetails($id) {
        return $this->showtimeService->getShowtimeDetails($id);
    }
}