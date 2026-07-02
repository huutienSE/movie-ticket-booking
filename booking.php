<?php
/**
 *booking.php : đăt vé phim
 * Flow: Chọn ghế -> Chọn phương thức thanh toán -> Xác nhận đặt vé
 */
$pageCSS = ['css/booking.css']; // Load CSS riêng cho trang booking
require_once 'header.php';
require_once 'app/init.php';

use App\Controllers\ShowtimeController;
use App\Controllers\SeatController;
use App\Controllers\BookingController;

// lấy ID suất chiếu từ URL
$showtimeId = (int)($_GET['showtime_id'] ?? 0);
if ($showtimeId <= 0) { // kiểm tra id
    echo "<div style='padding:40px;text-align:center;color:#fff;'>Suất chiếu không hợp lệ.</div>";
    require_once 'footer.php';
    exit;
}

$showtimeController = new ShowtimeController();
$seatController = new SeatController();
$bookingController = new BookingController();

// Xử lý form đặt vé trước
$bookingResult = $bookingController->handleRequest();
if ($bookingResult && $bookingResult['status'] === 'success') {
    echo "<script>window.location.href = 'booking_history.php';</script>";
    exit;
}

// lấy thông tin suất chiếu từ database
$showtime = $showtimeController->getShowtimeDetails($showtimeId);
if (!$showtime) {
    echo "<div style='padding:40px;text-align:center;color:#fff;'>Không tìm thấy thông tin suất chiếu.</div>";
    require_once 'footer.php';
    exit;
}

// lấy sơ đồ ghế của phòng chiếu
$seatMap = $seatController->getSeatMap($showtimeId, $showtime['room_id']);
?>

<div class="booking-page">
    <div class="booking-container">

        <!-- ===== TOP BAR: Tiêu đề + Nút quay lại ===== -->
        <div class="booking-top-bar">
            <div class="booking-title">
                <span class="eyebrow">🎫 Đặt Vé</span>
                <h1>Chọn ghế & thanh toán</h1>
            </div>
            <a href="movie_details.php?id=<?php echo $showtime['movie_id']; ?>" class="back-link-top">
                ← Quay lại chi tiết phim
            </a>
        </div>

        <!-- Header: Poster + Thông tin phim -->
        <div class="booking-header">
            <div class="movie-poster">
                <img src="<?php echo htmlspecialchars($showtime['poster'] ?? 'images/movies/default.jpg'); ?>" 
                     alt="<?php echo htmlspecialchars($showtime['movie_title'] ?? 'Phim'); ?>"
                     onerror="this.src='images/movies/default.jpg'">
                <?php if (!empty($showtime['rating'])): ?>
                <div class="movie-rating">⭐ <?php echo number_format($showtime['rating'], 1); ?></div>
                <?php endif; ?>
            </div>
            <div class="movie-info">
                <h1><?php echo htmlspecialchars($showtime['movie_title'] ?? 'Phim'); ?></h1>
                <div class="movie-meta">
                    <span>⏱ <?php echo $showtime['duration'] ?? 'Đang cập nhật'; ?> phút</span>
                    <span>🌍 <?php echo htmlspecialchars($showtime['country'] ?? 'Đang cập nhật'); ?></span>
                    <span>🔞 <?php echo $showtime['age_restriction'] ?? 'Đang cập nhật'; ?>+</span>
                </div>
            </div>
        </div>

        <!-- Thông tin suất chiếu -->
        <div class="showtime-info">
            <h2>🎬 Thông tin suất chiếu</h2>
            <div class="info-grid">
                <div class="info-item">
                    <span class="label">🏢 Rạp</span>
                    <span class="value"><?php echo htmlspecialchars($showtime['theatre_name'] ?? 'Đang cập nhật'); ?></span>
                </div>
                <div class="info-item">
                    <span class="label">🚪 Phòng</span>
                    <span class="value"><?php echo htmlspecialchars($showtime['room_name'] ?? 'Đang cập nhật'); ?></span>
                </div>
                <div class="info-item">
                    <span class="label">📅 Ngày</span>
                    <span class="value"><?php echo date('d/m/Y', strtotime($showtime['show_date'] ?? 'now')); ?></span>
                </div>
                <div class="info-item">
                    <span class="label">⏰ Giờ</span>
                    <span class="value">
                        <?php 
                        if (!empty($showtime['start_time']) && !empty($showtime['end_time'])) {
                            echo date('H:i', strtotime($showtime['start_time'])) . ' - ' . date('H:i', strtotime($showtime['end_time']));
                        } else {
                            echo 'Đang cập nhật';
                        }
                        ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="label">💰 Giá vé</span>
                    <span class="value price"><?php echo number_format($showtime['base_price'] ?? 0); ?>đ</span>
                </div>
            </div>
        </div>

        <?php if (isset($bookingResult) && $bookingResult['status'] === 'error'): ?>
            <div class="alert error">
                ❌ <?php echo htmlspecialchars($bookingResult['message']); ?>
            </div>
        <?php endif; ?>

        <?php if (!isset($_SESSION['user'])): ?>
            <div class="alert error">
                🔐 Vui lòng <a href="login.php" style="color:#facc15; text-decoration:underline;">đăng nhập</a> để đặt vé.
            </div>
        <?php else: ?>

            <!-- FORM đặt vé -->
            <form method="POST" action="" class="booking-form">
                <input type="hidden" name="action" value="book_ticket">
                <input type="hidden" name="showtime_id" value="<?php echo $showtimeId; ?>">

                <!-- Chọn ghế -->
                <div class="seat-selection">
                    <h2>💺 Chọn ghế</h2>
                    
                    <!-- Legend -->
                    <div class="legend">
                        <span class="legend-item">
                            <span class="legend-swatch selected"></span> Đã chọn
                        </span>
                        <span class="legend-item">
                            <span class="legend-swatch" style="background:rgba(255,255,255,0.1);border-color:rgba(255,255,255,0.2);"></span> Còn trống
                        </span>
                        <span class="legend-item">
                            <span class="legend-swatch unavailable"></span> Đã đặt
                        </span>
                        <span class="legend-item">
                            <span class="legend-swatch vip"></span> VIP
                        </span>
                    </div>

                    <!-- Screen : màn hình-->
                    <div class="screen">🎬 MÀN HÌNH</div>

                    <!-- Seat Grid: ghế -->
                    <div class="seat-grid">
                        <?php
                        $basePrice = $showtime['base_price'] ?? 0;
                        if (empty($seatMap)) {
                            $rows = ['A', 'B', 'C', 'D', 'E'];
                            $statuses = ['available', 'available', 'booked', 'available', 'available'];
                            foreach ($rows as $row) {
                                for ($i = 1; $i <= 8; $i++) {
                                    $seatMap[] = [
                                        'id' => rand(1, 100),
                                        'seat_row' => $row,
                                        'seat_number' => $i,
                                        'status' => $statuses[array_rand($statuses)],
                                        'base_price_extra' => rand(0, 30000)
                                    ];
                                }
                            }
                        }
                        // duyêt từ ghế hiển thị
                        foreach ($seatMap as $seat):
                            $isBooked = ($seat['status'] === 'booked');
                            $extraPrice = (int)($seat['base_price_extra'] ?? 0);
                            $totalPrice = $basePrice + $extraPrice;
                            $seatLabel = $seat['seat_row'] . $seat['seat_number'];
                            $isVip = ($extraPrice > 20000);
                        ?>
                            <label class="seat-option <?php echo $isBooked ? 'disabled' : ''; ?> <?php echo $isVip ? 'vip' : ''; ?>">
                                <input type="checkbox" 
                                       name="seats[]" 
                                       value="<?php echo $seat['id']; ?>" 
                                       <?php echo $isBooked ? 'disabled' : ''; ?>
                                       data-price="<?php echo $totalPrice; ?>"
                                       onchange="updateTotal()">
                                <span class="seat-label"><?php echo $seatLabel; ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Thanh toán -->
                <div class="payment-section">
                    <h2>💳 Thanh toán</h2>
                    
                    <!-- Tổng tiền -->
                    <div class="total-box">
                        <span class="total-label">Tổng tiền</span>
                        <span class="total-amount" id="totalDisplay">0đ</span>
                        <span class="seat-count">(🎫 <span id="seatCount">0</span> ghế)</span>
                    </div>

                    <!-- Phương thức thanh toán -->
                    <div class="payment-methods">
                        <label class="payment-option active" onclick="selectPayment(this)">
                            <input type="radio" name="payment_method" value="cash" checked>
                            <span class="payment-icon">💵</span>
                            <span class="payment-name">Tiền mặt</span>
                        </label>
                        <label class="payment-option" onclick="selectPayment(this)">
                            <input type="radio" name="payment_method" value="momo">
                            <span class="payment-icon">📱</span>
                            <span class="payment-name">MoMo</span>
                        </label>
                        <label class="payment-option" onclick="selectPayment(this)">
                            <input type="radio" name="payment_method" value="vnpay">
                            <span class="payment-icon">🏦</span>
                            <span class="payment-name">VNPay</span>
                        </label>
                    </div>

                    <button type="submit" class="btn-confirm">
                        ✅ XÁC NHẬN ĐẶT VÉ
                    </button>
                </div>
            </form>
        <?php endif; ?>

    </div>
</div>

<!-- JS-Xử lý -->
<script>
function updateTotal() { // lấy tất cả ghế chọn
    const checkboxes = document.querySelectorAll('input[name="seats[]"]:checked');
    let total = 0;
    checkboxes.forEach(cb => { // tính tổng tiền từ ghế
        total += parseInt(cb.dataset.price) || 0;
    });
    // cập nhật hiển thị
    document.getElementById('totalDisplay').textContent = total.toLocaleString() + 'đ';
    document.getElementById('seatCount').textContent = checkboxes.length;
}
//  Xử lý chọn phương thức thanh toán
function selectPayment(element) {
    document.querySelectorAll('.payment-option').forEach(opt => {
        opt.classList.remove('active');
    });
    element.classList.add('active');
    const radio = element.querySelector('input[type="radio"]');
    if (radio) {
        radio.checked = true;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateTotal();
    
    document.querySelectorAll('.seat-option input[type="checkbox"]').forEach(cb => {
        cb.addEventListener('change', function() {
            this.closest('.seat-option').classList.toggle('selected', this.checked);
        });
    });
});
</script>

<?php require_once 'footer.php'; ?>