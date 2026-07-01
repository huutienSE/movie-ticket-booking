<?php
namespace App\Services;

use App\Models\ReviewModel;

class ReviewService {
    private $model;

    public function __construct() {
        $this->model = new ReviewModel();
    }

    public function getReviewsByMovie($movieId) {
        if ($movieId <= 0) return [];
        return $this->model->getReviewsByMovie($movieId);
    }

    public function addReview($userId, $movieId, $rating, $comment) {
        if ($rating < 1 || $rating > 5) {
            return ['status' => 'error', 'message' => 'Đánh giá không hợp lệ (1-5 sao).'];
        }
        
        if (empty(trim($comment))) {
            return ['status' => 'error', 'message' => 'Bình luận không được để trống.'];
        }

        if ($this->model->insertReview($userId, $movieId, $rating, $comment)) {
            return ['status' => 'success', 'message' => 'Thêm bình luận thành công!'];
        }
        return ['status' => 'error', 'message' => 'Lỗi khi thêm bình luận: ' . $this->model->getError()];
    }
}
