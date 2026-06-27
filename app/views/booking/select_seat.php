<?php
// Movie data
$movies = [
    1 => [
        'id' => 1,
        'name' => 'Avengers: Endgame',
        'rating' => 8.9,
        'duration' => 181,
        'genre' => 'Hành động, Phiêu lưu',
        'language' => 'Phụ đề Việt',
        'release_date' => '26/04/2019',
        'director' => 'Anthony Russo, Joe Russo',
        'format' => 'IMAX 2D',
        'description' => 'Sau các sự kiện của Infinity War, các siêu anh hùng còn sống sót tập hợp lại để đảo ngược những mất mát do Thanos gây ra và cứu lấy vũ trụ.',
        'image' => '/movie-ticket-booking/public/assets/anh/Avengers_Endgame.jpg'
    ],
    2 => [
        'id' => 2,
        'name' => 'The Batman',
        'rating' => 7.8,
        'duration' => 176,
        'genre' => 'Hành động, Tội phạm',
        'language' => 'Phụ đề Việt',
        'release_date' => '04/03/2022',
        'director' => 'Matt Reeves',
        'format' => '2D',
        'description' => 'Trong năm thứ hai hoạt động, Batman khám phá những bí mật đen tối của Gotham City và nghi ngờ rằng tham nhũng liên kết tất cả những lĩnh vực của thành phố.',
        'image' => '/movie-ticket-booking/public/assets/anh/Batman.jpg'
    ],
    3 => [
        'id' => 3,
        'name' => 'Scream VI',
        'rating' => 6.5,
        'duration' => 123,
        'genre' => 'Kinh dí, Giật gân',
        'language' => 'Phụ đề Việt',
        'release_date' => '10/03/2023',
        'director' => 'Matt Bettinelli-Olpin',
        'format' => '2D',
        'description' => 'Ghostface quay trở lại và khủng bố nhóm bạn tại New York. Những vụ giết người rùng rợn khiến mọi người hoảng loạn.',
        'image' => '/movie-ticket-booking/public/assets/anh/scream.png'
    ]
];

// Showtimes
$showtimes = [
    ['time' => '09:00', 'room' => 1, 'available' => 45, 'price' => 75000],
    ['time' => '11:30', 'room' => 1, 'available' => 40, 'price' => 75000],
    ['time' => '14:00', 'room' => 2, 'available' => 56, 'price' => 90000],
    ['time' => '16:30', 'room' => 2, 'available' => 32, 'price' => 90000],
    ['time' => '19:00', 'room' => 3, 'available' => 18, 'price' => 110000],
    ['time' => '21:30', 'room' => 3, 'available' => 62, 'price' => 110000],
];

// Get parameters from URL
$movie_id = isset($_GET['movie_id']) ? intval($_GET['movie_id']) : 1;
$showtime_id = isset($_GET['showtime_id']) ? intval($_GET['showtime_id']) : 2;

$movie = isset($movies[$movie_id]) ? $movies[$movie_id] : $movies[1];
$showtime = isset($showtimes[$showtime_id]) ? $showtimes[$showtime_id] : $showtimes[2];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chọn Ghế Ngồi - <?php echo htmlspecialchars($movie['name']); ?></title>
   <link rel="stylesheet" href="/movie-ticket-booking/public/assets/css/seat.css">
</head>

<body>

<div class="seat-page">
    <div class="seat-container">

        <!-- BREADCRUMB -->
        <div class="breadcrumb">
            <div class="step">1. Chọn Suất Chiếu</div>
            <div class="step active-step">2. Chọn Ghế</div>
            <div class="step">3. Xác Nhận</div>
            <div class="step">4. Thanh Toán</div>
        </div>
        <!-- HEADER -->
        <div class="seat-header">
            <h1>Chọn Ghế Ngồi</h1>
            <p><?php echo htmlspecialchars($movie['name']); ?> | <?php echo htmlspecialchars($showtime['time']); ?> - Phòng <?php echo $showtime['room']; ?></p>
        </div>

        <!-- SCREEN -->
        <div class="screen">🎬 MÀN HÌNH 🎬</div>

        <!-- LEGEND -->
        <div class="seat-legend">
            <div class="legend-item">
                <div class="legend-box available"></div>
                <span>Ghế Trống</span>
            </div>
            <div class="legend-item">
                <div class="legend-box selected"></div>
                <span>Ghế Được Chọn</span>
            </div>

            <div class="legend-item">
                <div class="legend-box unavailable"></div>
                <span>Ghế Không Còn</span>
            </div>

        </div>

        <!-- SEAT MAP -->
        <div class="seat-map">
            <!-- Row A -->
            <button class="seat" data-seat="A1">A1</button>
            <button class="seat" data-seat="A2">A2</button>
            <button class="seat" data-seat="A3">A3</button>
            <button class="seat" data-seat="A4">A4</button>
            <button class="seat" data-seat="A5">A5</button>
            <button class="seat" data-seat="A6">A6</button>
            <button class="seat" data-seat="A7">A7</button>
            <button class="seat" data-seat="A8">A8</button>
            <button class="seat" data-seat="A9">A9</button>
            <button class="seat" data-seat="A10">A10</button>

            <!-- Row B -->
            <button class="seat" data-seat="B1">B1</button>
            <button class="seat" data-seat="B2">B2</button>
            <button class="seat" data-seat="B3">B3</button>
            <button class="seat unavailable" data-seat="B4">B4</button>
            <button class="seat unavailable" data-seat="B5">B5</button>
            <button class="seat" data-seat="B6">B6</button>
            <button class="seat" data-seat="B7">B7</button>
            <button class="seat" data-seat="B8">B8</button>
            <button class="seat" data-seat="B9">B9</button>
            <button class="seat" data-seat="B10">B10</button>

            <!-- Row C -->
            <button class="seat" data-seat="C1">C1</button>
            <button class="seat" data-seat="C2">C2</button>
            <button class="seat" data-seat="C3">C3</button>
            <button class="seat" data-seat="C4">C4</button>
            <button class="seat" data-seat="C5">C5</button>
            <button class="seat" data-seat="C6">C6</button>
            <button class="seat" data-seat="C7">C7</button>
            <button class="seat" data-seat="C8">C8</button>
            <button class="seat" data-seat="C9">C9</button>
            <button class="seat" data-seat="C10">C10</button>

            <!-- Row D -->
            <button class="seat" data-seat="D1">D1</button>
            <button class="seat" data-seat="D2">D2</button>
            <button class="seat unavailable" data-seat="D3">D3</button>
            <button class="seat" data-seat="D4">D4</button>
            <button class="seat" data-seat="D5">D5</button>
            <button class="seat" data-seat="D6">D6</button>
            <button class="seat" data-seat="D7">D7</button>
            <button class="seat unavailable" data-seat="D8">D8</button>
            <button class="seat" data-seat="D9">D9</button>
            <button class="seat" data-seat="D10">D10</button>

            <!-- Row E -->
            <button class="seat" data-seat="E1">E1</button>
            <button class="seat" data-seat="E2">E2</button>
            <button class="seat" data-seat="E3">E3</button>
            <button class="seat" data-seat="E4">E4</button>
            <button class="seat" data-seat="E5">E5</button>
            <button class="seat" data-seat="E6">E6</button>
            <button class="seat" data-seat="E7">E7</button>
            <button class="seat" data-seat="E8">E8</button>
            <button class="seat" data-seat="E9">E9</button>
            <button class="seat" data-seat="E10">E10</button>

            <!-- Row F -->
            <button class="seat" data-seat="F1">F1</button>
            <button class="seat" data-seat="F2">F2</button>
            <button class="seat" data-seat="F3">F3</button>
            <button class="seat" data-seat="F4">F4</button>
            <button class="seat" data-seat="F5">F5</button>
            <button class="seat" data-seat="F6">F6</button>
            <button class="seat" data-seat="F7">F7</button>
            <button class="seat" data-seat="F8">F8</button>
            <button class="seat" data-seat="F9">F9</button>
            <button class="seat" data-seat="F10">F10</button>
        </div>

        <!-- SELECTED SEATS INFO -->
        <div class="seat-info">
            <h3>Ghế Đã Chọn</h3>
            <div class="seat-list" id="selected-seats-list">
                <span style="color: #999;">Chưa chọn ghế nào</span>
            </div>
        </div>

        <!-- CONFIRMATION SECTION -->
        <div class="confirmation-section">
            <h3>Tóm Tắt Đặt Vé</h3>
            <div class="confirmation-item">
                <span>Phim</span>
                <strong><?php echo htmlspecialchars($movie['name']); ?></strong>
            </div>
            <div class="confirmation-item">
                <span>Suất Chiếu</span>
                <strong><?php echo htmlspecialchars($showtime['time']); ?> - Phòng <?php echo $showtime['room']; ?> | 23/06/2026</strong>
            </div>

            <div class="confirmation-item">
                <span>Số Vé</span>
                <strong id="seat-count">0</strong>
            </div>
            <div class="confirmation-item">
                <span>Giá Vé (1 vé)</span>
                <strong><?php echo number_format($showtime['price'], 0, '.', '.'); ?> VNĐ</strong>
            </div>

            <div class="confirmation-item">
                <span>Tổng Tiền</span>
                <strong id="total-price">0 VNĐ</strong>
            </div>

        </div>
        <!-- ACTION BUTTONS -->
        <div class="action-group">
            <button class="btn-back" onclick="history.back()">
                ← Quay Lại
            </button>
            <form action="confirm.php" method="POST" style="margin: 0;">
                <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">
                <input type="hidden" name="showtime_id" value="<?php echo $showtime_id; ?>">
                <input type="hidden" name="seat_ids" id="seat_ids">
                <button type="submit" class="btn-submit" id="confirm-btn" disabled>Tiếp Tục Xác Nhận →</button>
            </form>
        </div>
    </div>
</div>

<script>
const selectedSeats = new Set();
const pricePerSeat = <?php echo $showtime['price']; ?>;

// Handle seat selection
document.querySelectorAll('.seat:not(.unavailable)').forEach(seatBtn => {
    seatBtn.addEventListener('click', function() {
        const seatNum = this.dataset.seat;
        if (selectedSeats.has(seatNum)) {
            selectedSeats.delete(seatNum);
            this.classList.remove('selected');
        } else {
            selectedSeats.add(seatNum);
            this.classList.add('selected');
        }
        updateSummary();
    });
});

function updateSummary() {
    // Update selected seats list
    const seatsArray = Array.from(selectedSeats).sort();
    const seatsList = document.getElementById('selected-seats-list');
    
    if (seatsArray.length === 0) {
        seatsList.innerHTML = '<span style="color: #999;">Chưa chọn ghế nào</span>';
    } else {
        seatsList.textContent = seatsArray.join(', ');
    }
    // Update seat count and total price
    const seatCount = selectedSeats.size;
    const totalPrice = seatCount * pricePerSeat;
    
    document.getElementById('seat-count').textContent = seatCount;
    document.getElementById('total-price').textContent = totalPrice.toLocaleString('vi-VN') + ' VNĐ';
    document.getElementById('seat_ids').value = seatsArray.join(',');
    // Enable/disable confirm button
    const confirmBtn = document.getElementById('confirm-btn');
    confirmBtn.disabled = seatCount === 0;
}
</script>

</body>

</html>