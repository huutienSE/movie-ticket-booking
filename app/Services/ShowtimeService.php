<?php
namespace App\Services;

use App\Models\ShowtimeModel;

class ShowtimeService {
    private $model;

    public function __construct() {
        $this->model = new ShowtimeModel();
    }

    public function getShowtimesByMovie($movieId, $date = null) {
        if ($movieId <= 0) return [];
        return $this->model->getShowtimesByMovie($movieId, $date);
    }

    public function getShowtimeDetails($showtimeId) {
        if ($showtimeId <= 0) return null;
        return $this->model->getShowtimeDetails($showtimeId);
    }
}
