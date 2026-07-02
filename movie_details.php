<?php
/*movie_detail.php- CHI TIẾT PHIM*/

$pageCSS = ['css/movie.css'];
require_once 'header.php';
require_once 'app/init.php';
// Import các Controller cần dùng
use App\Controllers\MovieController;
use App\Controllers\ShowtimeController;
use App\Controllers\ReviewController;

// lấy ID phim từ URL
$movieId = (int)($_GET['id'] ?? 0);
if ($movieId <= 0) {
    echo "<p style='padding:40px;text-align:center;color:#fff;'>ID phim không hợp lệ.</p>";
    require_once 'footer.php';
    exit;
}

$movieController = new MovieController();
$showtimeController = new ShowtimeController();
$reviewController = new ReviewController();

// Xử lý gửi review
$reviewResult = $reviewController->handleRequest();

// Lấy thông tin phim
$movie = $movieController->getMovieById($movieId);
if (!$movie) {
    echo "<p style='padding:40px;text-align:center;color:#fff;'>Không tìm thấy phim.</p>";
    require_once 'footer.php';
    exit;
}

// Lấy lịch chiếu và review
$showtimes = $showtimeController->getShowtimesByMovie($movieId);
$reviews = $reviewController->getReviewsByMovie($movieId);
?>

<div class="movie-details-page">
    <div class="movie-details-container">

        <!-- Nút quay lại -->
        <a href="index.php" class="btn-back">← Quay lại trang chủ</a>

        <!-- ===== THÔNG TIN PHIM ===== -->
        <div class="movie-detail-card">
            <div class="movie-detail-poster">
                <?php
                // ===== XỬ LÝ ẢNH =====
                // Xác định đường dẫn ảnh
                $imagePath = 'images/movies/default.jpg';
                
                // Kiểm tra các nguồn ảnh
                if (!empty($movie['images'])) {
                    $imagePath = $movie['images'];
                } elseif (!empty($movie['poster'])) {
                    $imagePath = $movie['poster'];
                }
                
                // Kiểm tra file ảnh có tồn tại không
                if (!file_exists($imagePath)) {
                    $imagePath = 'images/movies/default.jpg';
                }
                ?>
                <img src="<?php echo htmlspecialchars($imagePath); ?>"
                     alt="<?php echo htmlspecialchars($movie['title'] ?? 'Phim'); ?>"
                     onerror="this.src='images/movies/default.jpg'; this.onerror=null;">
                     <!-- Hiển thị rating nếu có -->
                <?php if (!empty($movie['rating'])): ?>
                <div class="movie-detail-rating">⭐ <?php echo number_format($movie['rating'], 1); ?>/10</div>
                <?php endif; ?>
            </div>
                      <!-- Thông tin phim -->
            <div class="movie-detail-info">
                <h1><?php echo htmlspecialchars($movie['title'] ?? 'Không có tiêu đề'); ?></h1>
                    <!-- Thể loại phim -->
                <?php if (!empty($movie['genre_names'])): ?>
                <div class="movie-detail-genre"><?php echo htmlspecialchars($movie['genre_names']); ?></div>
                <?php endif; ?>

                <div class="movie-detail-meta">
                    <span>⏱ <?php echo $movie['duration'] ?? 'Đang cập nhật'; ?> phút</span>
                    <span>🌍 <?php echo htmlspecialchars($movie['country'] ?? 'Đang cập nhật'); ?></span>
                    <span>🔞 <?php echo $movie['age_restriction'] ?? 'Đang cập nhật'; ?>+</span>
                </div>

                <p class="movie-detail-description"><?php echo htmlspecialchars($movie['description'] ?? 'Chưa có mô tả'); ?></p>

                <div class="movie-detail-grid">
                    <p><strong>Đạo diễn:</strong> <?php echo htmlspecialchars($movie['director'] ?? 'Đang cập nhật'); ?></p>
                    <p><strong>Diễn viên:</strong> <?php echo htmlspecialchars($movie['cast'] ?? 'Đang cập nhật'); ?></p>
                    <p><strong>Thể loại:</strong> <?php echo htmlspecialchars($movie['genre_names'] ?? 'Đang cập nhật'); ?></p>
                    <p><strong>Quốc gia:</strong> <?php echo htmlspecialchars($movie['country'] ?? 'Đang cập nhật'); ?></p>
                    <p><strong>Thời lượng:</strong> <?php echo $movie['duration'] ?? 'Đang cập nhật'; ?> phút</p>
                    <p><strong>Giới hạn tuổi:</strong> <?php echo $movie['age_restriction'] ?? 'Đang cập nhật'; ?>+</p>
                </div>
            </div>
        </div>

        <!-- ===== LỊCH CHIẾU ===== -->
        <div class="showtimes-section">
            <h2 class="section-title">🎬 Lịch Chiếu <span>Showtimes</span></h2>

            <?php if (empty($showtimes)): ?>
                <p class="no-showtimes">😅 Hiện chưa có lịch chiếu cho phim này.</p>
            <?php else: ?>
                <div class="showtime-grid">
                    <?php foreach ($showtimes as $st): ?>
                    <div class="showtime-card">
                        <div class="showtime-date">📅 <?php echo date('d/m/Y', strtotime($st['show_date'])); ?></div>
                        <div class="showtime-time"><?php echo date('H:i', strtotime($st['start_time'])); ?> - <?php echo date('H:i', strtotime($st['end_time'])); ?></div>
                        <div class="showtime-room">  <!-- Tên rạp và phòng -->
                            🏢 <?php echo htmlspecialchars($st['theatre_name'] ?? 'Rạp'); ?> - <?php echo htmlspecialchars($st['room_name']); ?>
                        </div>
                          <!-- Giá vé -->
                        <div class="showtime-price">💵 <?php echo number_format($st['base_price']); ?>đ</div> 
                        <a href="booking.php?showtime_id=<?php echo $st['id']; ?>" class="btn-book">
                            Chọn Ghế & Đặt Vé
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- ===== ĐÁNH GIÁ & BÌNH LUẬN ===== -->
        <div class="showtimes-section" style="margin-top: 30px;">
            <h2 class="section-title">💬 Đánh Giá & Bình Luận</h2>

            <?php if (isset($reviewResult)): ?>
                <div style="padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;
                    background: <?php echo $reviewResult['status'] === 'success' ? 'rgba(34,197,94,0.15)' : 'rgba(239,68,68,0.15)'; ?>;
                    color: <?php echo $reviewResult['status'] === 'success' ? '#22c55e' : '#ef4444'; ?>;
                    border: 1px solid <?php echo $reviewResult['status'] === 'success' ? 'rgba(34,197,94,0.3)' : 'rgba(239,68,68,0.3)'; ?>;">
                    <?php echo $reviewResult['message']; ?>
                </div>
            <?php endif; ?>

            <!-- Form viết bình luận -->
            <div style="background: rgba(15,27,46,0.6); padding: 24px; border-radius: 14px; margin-bottom: 28px; border: 1px solid rgba(255,255,255,0.06);">
                <h3 style="color:#fff; margin-bottom:16px;">✏️ Viết bình luận của bạn</h3>
                <?php if (isset($_SESSION['user'])): ?>
                    <form method="POST" action="" style="display:flex; flex-direction:column; gap:14px;">
                        <input type="hidden" name="action" value="add_review">
                        <input type="hidden" name="movie_id" value="<?php echo $movieId; ?>">

                        <div>
                            <label style="color:#bfc9d8; font-size:14px; display:block; margin-bottom:6px;">⭐ Số sao (1-5):</label>
                            <select name="rating" style="background:#0f1b2e; color:#fff; border:1px solid rgba(255,255,255,0.12); padding:10px 16px; border-radius:8px; font-size:15px; width:160px;">
                                <option value="5">⭐⭐⭐⭐⭐ 5 Sao</option>
                                <option value="4">⭐⭐⭐⭐ 4 Sao</option>
                                <option value="3">⭐⭐⭐ 3 Sao</option>
                                <option value="2">⭐⭐ 2 Sao</option>
                                <option value="1">⭐ 1 Sao</option>
                            </select>
                        </div>
                        <!-- Nhập bình luận -->
                        <div>
                            <label style="color:#bfc9d8; font-size:14px; display:block; margin-bottom:6px;">💬 Bình luận:</label>
                            <textarea name="comment" rows="4" required placeholder="Nhập cảm nhận của bạn về bộ phim..."
                                style="width:100%; background:#0f1b2e; color:#fff; border:1px solid rgba(255,255,255,0.12);
                                       padding:12px 16px; border-radius:8px; font-size:15px; resize:vertical; font-family:inherit;"></textarea>
                        </div>
                         <!-- Nút gửi -->
                        <button type="submit" style="align-self:flex-start; padding:12px 32px; background:linear-gradient(135deg,#ef4444,#dc2626);
                            color:#fff; border:none; border-radius:8px; font-size:15px; font-weight:600; cursor:pointer;
                            transition:all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            Gửi đánh giá
                        </button>
                    </form>
                <?php else: ?>
                    <p style="color:#bfc9d8;">Vui lòng <a href="login.php" style="color:#ef4444; font-weight:600;">đăng nhập</a> để viết bình luận.</p>
                <?php endif; ?>
            </div>

            <!-- Danh sách review -->
            <?php if (empty($reviews)): ?>
                <p class="no-showtimes">Chưa có đánh giá nào. Hãy là người đầu tiên!</p>
            <?php else: ?>
                <?php foreach ($reviews as $rev): ?>
                <div style="background:rgba(15,27,46,0.5); border-radius:12px; padding:18px 22px; margin-bottom:14px;
                            border: 1px solid rgba(255,255,255,0.05);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; flex-wrap:wrap; gap:8px;">
                        <strong style="color:#fff;"><?php echo htmlspecialchars($rev['first_name'] . ' ' . $rev['last_name']); ?></strong>
                        <span style="color:#facc15; font-size:18px;"><?php echo str_repeat('⭐', (int)$rev['rating']); ?></span>
                    </div>
                    <p style="color:#c6d1e0; font-style:italic; margin-bottom:6px;">"<?php echo htmlspecialchars($rev['comment']); ?>"</p>
                    <small style="color:#6b7280;"><?php echo date('d/m/Y H:i', strtotime($rev['created_at'])); ?></small>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php require_once 'footer.php'; ?>