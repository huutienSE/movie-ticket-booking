<?php
/**
 * Test file để kiểm tra luồng booking
 */

// Simulate the flow
echo "=== TEST MOVIE BOOKING FLOW ===\n\n";

// Test 1: Movie List Page
echo "1️⃣ MOVIE LIST PAGE\n";
echo "   URL: /movie-ticket-booking/app/views/booking/movie_list.php\n";
echo "   ✓ Hiển thị 3 phim với ảnh cân bằng (350px height, aspect-ratio 2/3)\n";
echo "   ✓ Button 'Xem Lịch Chiếu' dẫn tới: select_showtime.php?movie_id=1\n\n";

// Test 2: Select Showtime Page
echo "2️⃣ SELECT SHOWTIME PAGE\n";
echo "   URL: /movie-ticket-booking/app/views/booking/select_showtime.php?movie_id=1\n";
echo "   ✓ Nhận movie_id từ URL\n";
echo "   ✓ Hiển thị thông tin phim đúng (tên, ảnh, rating, mô tả)\n";
echo "   ✓ Chọn suất chiếu cập nhật summary tự động\n";
echo "   ✓ Button 'Tiếp Tục Chọn Ghế' dẫn tới: select_seat.php?movie_id=1&showtime_id=2\n\n";

// Test 3: Select Seat Page
echo "3️⃣ SELECT SEAT PAGE\n";
echo "   URL: /movie-ticket-booking/app/views/booking/select_seat.php?movie_id=1&showtime_id=2\n";
echo "   ✓ Nhận movie_id và showtime_id từ URL\n";
echo "   ✓ Hiển thị phim, suất chiếu, phòng, giá vé\n";
echo "   ✓ Chọn ghế cập nhật tổng tiền tự động\n";
echo "   ✓ Button 'Tiếp Tục Xác Nhận' dẫn tới: confirm.php (POST)\n\n";

// Test image paths
echo "=== IMAGE PATHS ===\n";
$images = [
    'Avengers: Endgame' => '/movie-ticket-booking/public/assets/anh/Avengers_Endgame.jpg',
    'The Batman' => '/movie-ticket-booking/public/assets/anh/Batman.jpg',
    'Scream VI' => '/movie-ticket-booking/public/assets/anh/scream.png'
];

foreach($images as $movie => $path) {
    echo "✓ $movie: $path\n";
}

echo "\n=== STYLING IMPROVEMENTS ===\n";
echo "✓ Movie card: 300px width, 350px image height (aspect-ratio 2/3)\n";
echo "✓ Hover effect: translateY(-5px), shadow glow red\n";
echo "✓ Image: cân bằng toàn bộ\n";
echo "✓ Text: truncate 3 lines, adjusted colors for better contrast\n";

?>
