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

// Get movie ID from URL
$movie_id = isset($_GET['movie_id']) ? intval($_GET['movie_id']) : 1;
$movie = isset($movies[$movie_id]) ? $movies[$movie_id] : $movies[1];

// Cinema data
$cinema = [
    'name' => 'CGV Hùng Vương Plaza',
    'address' => '126 Hồng Bàng, Quận 5, TP.HCM'
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

$selected_showtime = $showtimes[2]; // Default: 14:00
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chọn Suất Chiếu - <?php echo htmlspecialchars($movie['name']); ?></title>
    <link rel="stylesheet" href="/movie-ticket-booking/public/assets/css/booking.css">
</head>

<body>

<div class="booking-container">

    <!-- BREADCRUMB -->
    <div class="breadcrumb">
        <div class="step active-step">
            1. Chọn Suất Chiếu
        </div>
        <div class="step">
            2. Chọn Ghế
        </div>
        <div class="step">
            3. Xác Nhận
        </div>
        <div class="step">
            4. Thanh Toán
        </div>
    </div>

    <!-- MOVIE INFO -->
    <div class="movie-section">

        <div class="movie-poster">
            <img src="<?php echo htmlspecialchars($movie['image']); ?>" alt="<?php echo htmlspecialchars($movie['name']); ?>">
        </div>

        <div class="movie-info">

            <h1><?php echo htmlspecialchars($movie['name']); ?></h1>

            <div class="movie-meta">
                <span>⭐ <?php echo $movie['rating']; ?>/10</span>
                <span>⏱ <?php echo intdiv($movie['duration'], 60); ?>h <?php echo $movie['duration'] % 60; ?>m</span>
                <span>🎬 <?php echo htmlspecialchars($movie['genre']); ?></span>
                <span>🌐 <?php echo htmlspecialchars($movie['language']); ?></span>
            </div>

            <p class="movie-description">
                <?php echo htmlspecialchars($movie['description']); ?>
            </p>

            <div class="movie-detail">
                <p><strong>Khởi chiếu:</strong> <?php echo htmlspecialchars($movie['release_date']); ?></p>
                <p><strong>Đạo diễn:</strong> <?php echo htmlspecialchars($movie['director']); ?></p>
                <p><strong>Định dạng:</strong> <?php echo htmlspecialchars($movie['format']); ?></p>
            </div>

        </div>

    </div>

    <!-- DATE SELECTION -->
    <div class="card">

        <h2>Chọn Ngày Chiếu</h2>

        <div class="date-list">

            <button class="date-btn active" data-date="2026-06-23">
                <span>Thứ 2</span>
                <strong>23/06</strong>
            </button>

            <button class="date-btn" data-date="2026-06-24">
                <span>Thứ 3</span>
                <strong>24/06</strong>
            </button>

            <button class="date-btn" data-date="2026-06-25">
                <span>Thứ 4</span>
                <strong>25/06</strong>
            </button>

            <button class="date-btn" data-date="2026-06-26">
                <span>Thứ 5</span>
                <strong>26/06</strong>
            </button>

            <button class="date-btn" data-date="2026-06-27">
                <span>Thứ 6</span>
                <strong>27/06</strong>
            </button>

        </div>

    </div>

    <!-- CINEMA INFO -->
    <div class="card">

        <h2>Rạp Chiếu</h2>

        <div class="cinema-box">

            <h3>🎭 <?php echo htmlspecialchars($cinema['name']); ?></h3>

            <p>📍 <?php echo htmlspecialchars($cinema['address']); ?></p>

        </div>

    </div>

    <!-- SHOWTIME SELECTION -->
    <div class="card">

        <h2>Chọn Suất Chiếu</h2>

        <div class="showtime-list">

            <?php foreach($showtimes as $key => $showtime): ?>
            <button class="showtime-btn <?php echo ($key === 2) ? 'active-showtime' : ''; ?>" 
                    data-showtime="<?php echo htmlspecialchars($showtime['time']); ?>" 
                    data-room="<?php echo $showtime['room']; ?>" 
                    data-price="<?php echo $showtime['price']; ?>"
                    data-showtime-id="<?php echo $key; ?>"
                    onclick="selectShowtime(this, event)">
                <span><?php echo htmlspecialchars($showtime['time']); ?></span>
                <small>🎬 Phòng <?php echo $showtime['room']; ?></small>
                <small>💺 <?php echo $showtime['available']; ?> ghế trống</small>
            </button>
            <?php endforeach; ?>

        </div>

    </div>

    <!-- SUMMARY -->
    <div class="summary-card">

        <h2>Tóm Tắt Đặt Vé</h2>

        <div class="summary-item">
            <span>Phim</span>
            <strong><?php echo htmlspecialchars($movie['name']); ?></strong>
        </div>

        <div class="summary-item">
            <span>Ngày</span>
            <strong id="summary-date">23/06/2026</strong>
        </div>

        <div class="summary-item">
            <span>Suất Chiếu</span>
            <strong id="summary-time"><?php echo htmlspecialchars($selected_showtime['time']); ?></strong>
        </div>

        <div class="summary-item">
            <span>Phòng</span>
            <strong id="summary-room">Phòng <?php echo $selected_showtime['room']; ?></strong>
        </div>

        <div class="summary-item">
            <span>Giá Vé</span>
            <strong id="summary-price"><?php echo number_format($selected_showtime['price'], 0, '.', '.'); ?> VNĐ</strong>
        </div>

    </div>

    <!-- ACTION BUTTONS -->
    <div class="action-group">

        <button class="btn-back" onclick="history.back()">
            ← Quay Lại
        </button>

        <form action="/movie-ticket-booking/public/test_seat.php" method="GET" style="margin: 0;">
            <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">
            <input type="hidden" name="showtime_id" id="showtime_id_input" value="2">
            <button type="submit" class="btn-next">
                Tiếp Tục Chọn Ghế →
            </button>
        </form>

    </div>

</div>

<script>
// Handle date selection
document.querySelectorAll('.date-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.date-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const date = this.dataset.date;
        const [year, month, day] = date.split('-');
        document.getElementById('summary-date').textContent = day + '/' + month + '/' + year;
    });
});

// Handle showtime selection
function selectShowtime(element, event) {
    event.preventDefault();
    
    document.querySelectorAll('.showtime-btn').forEach(b => b.classList.remove('active-showtime'));
    element.classList.add('active-showtime');
    
    const time = element.dataset.showtime;
    const room = element.dataset.room;
    const price = element.dataset.price;
    const showtimeId = element.dataset.showtimeId;
    
    document.getElementById('summary-time').textContent = time;
    document.getElementById('summary-room').textContent = 'Phòng ' + room;
    document.getElementById('summary-price').textContent = parseInt(price).toLocaleString('vi-VN') + ' VNĐ';
    document.getElementById('showtime_id_input').value = showtimeId;
}
</script>

</body>

</html>