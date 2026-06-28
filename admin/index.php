<?php
require_once '../config.php';
require_once 'admin_header.php';
require_once 'admin_sidebar.php';

/**
 * KIẾN THỨC PHP: Thực thi nhiều câu truy vấn để lấy thống kê
 */

// 1. Tổng số phim
$result_movies = mysqli_query($conn, "SELECT COUNT(*) as count FROM movies");
$total_movies = mysqli_fetch_assoc($result_movies)['count'];

// 2. Tổng số khách hàng
$result_users = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role='user'");
$total_users = mysqli_fetch_assoc($result_users)['count'];

// 3. Tổng số vé đã đặt (bookings)
$result_bookings = mysqli_query($conn, "SELECT COUNT(*) as count FROM bookings");
$total_bookings = mysqli_fetch_assoc($result_bookings)['count'];

// 4. Tổng doanh thu (những booking đã thanh toán)
$result_revenue = mysqli_query($conn, "SELECT SUM(total_price) as total FROM bookings WHERE status='paid'");
$total_revenue = mysqli_fetch_assoc($result_revenue)['total'] ?? 0;

// 5. Lấy 5 booking mới nhất
$query_recent_bookings = "
    SELECT b.*, u.first_name, u.last_name 
    FROM bookings b 
    JOIN users u ON b.user_id = u.id 
    ORDER BY b.created_at DESC LIMIT 5
";
$recent_bookings = mysqli_query($conn, $query_recent_bookings);

// 6. Lấy 5 phim đang chiếu
$query_now_showing = "SELECT * FROM movies WHERE status = 'now_showing' AND is_active = 1 LIMIT 5";
$now_showing = mysqli_query($conn, $query_now_showing);
?>

<div class="container-fluid">
    <h2 class="mb-4 fw-bold">Dashboard Tổng Quan</h2>

    <!-- Hàng chứa 4 thẻ thống kê -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card stat-card bg-primary text-white h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="card-title text-uppercase mb-2">Tổng số Phim</h6>
                        <h2 class="mb-0"><?= number_format($total_movies) ?></h2>
                    </div>
                    <div class="icon-box bg-white text-primary">
                        <i class="bi bi-film"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card bg-success text-white h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="card-title text-uppercase mb-2">Người Dùng</h6>
                        <h2 class="mb-0"><?= number_format($total_users) ?></h2>
                    </div>
                    <div class="icon-box bg-white text-success">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card bg-warning text-dark h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="card-title text-uppercase mb-2">Lượt Đặt Vé</h6>
                        <h2 class="mb-0"><?= number_format($total_bookings) ?></h2>
                    </div>
                    <div class="icon-box bg-white text-warning">
                        <i class="bi bi-ticket-perforated"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card bg-netflix-red text-white h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="card-title text-uppercase mb-2">Doanh Thu</h6>
                        <h2 class="mb-0"><?= number_format($total_revenue, 0, ',', '.') ?>đ</h2>
                    </div>
                    <div class="icon-box bg-white text-netflix-red">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Bảng Đặt vé gần đây -->
        <div class="col-md-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-netflix-red"></i> Đặt vé gần đây</h5>
                    <a href="manage_booking.php" class="btn btn-sm btn-outline-secondary">Xem tất cả</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã Đặt</th>
                                    <th>Khách Hàng</th>
                                    <th>Tổng Tiền</th>
                                    <th>Trạng Thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($recent_bookings && mysqli_num_rows($recent_bookings) > 0): ?>
                                    <?php while ($booking = mysqli_fetch_assoc($recent_bookings)): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($booking['booking_code']) ?></strong></td>
                                            <td><?= htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']) ?></td>
                                            <td><?= number_format($booking['total_price'], 0, ',', '.') ?>đ</td>
                                            <td>
                                                <?php if ($booking['status'] == 'paid'): ?>
                                                    <span class="badge bg-success">Đã thanh toán</span>
                                                <?php elseif ($booking['status'] == 'pending'): ?>
                                                    <span class="badge bg-warning text-dark">Chờ thanh toán</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Đã hủy</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center text-muted py-3">Chưa có dữ liệu đặt vé</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bảng Phim đang chiếu -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-play-circle me-2 text-netflix-red"></i> Phim đang chiếu</h5>
                    <a href="manage_movies.php" class="btn btn-sm btn-outline-secondary">Xem tất cả</a>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php if ($now_showing && mysqli_num_rows($now_showing) > 0): ?>
                            <?php while ($movie = mysqli_fetch_assoc($now_showing)): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                    <div class="d-flex align-items-center">
                                        <img src="../<?= htmlspecialchars($movie['images'] ?: 'images/movies/default.jpg') ?>" 
                                             class="rounded" style="width: 40px; height: 60px; object-fit: cover;" alt="<?= htmlspecialchars($movie['title']) ?>">
                                        <div class="ms-3">
                                            <h6 class="mb-1 fw-bold"><?= htmlspecialchars($movie['title']) ?></h6>
                                            <small class="text-muted"><i class="bi bi-clock me-1"></i><?= $movie['duration'] ?> phút</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-netflix-red rounded-pill">T1<?= $movie['age_restriction'] ?></span>
                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li class="list-group-item text-center text-muted py-3">Không có phim đang chiếu</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>