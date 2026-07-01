<?php
namespace App\Controllers;

use App\Services\ShowtimeService;

class ShowtimeController {
    private $showtimeService;

    public function __construct() {
        $this->showtimeService = new ShowtimeService();
    }

    public function getShowtimesByMovie($movieId, $date = null) {
        return $this->showtimeService->getShowtimesByMovie($movieId, $date);
    }

    public function getShowtimeDetails($showtimeId) {
        return $this->showtimeService->getShowtimeDetails($showtimeId);
    }
}
