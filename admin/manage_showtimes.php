<?php
require_once '../config.php';
require_once '../app/init.php';
require_once 'admin_header.php';
require_once 'admin_sidebar.php';

use App\Controllers\ShowtimeController;

$controller = new ShowtimeController();
$actionResult = $controller->handleRequest();

$success_msg = '';
$error_msg = '';

if ($actionResult) {
    if ($actionResult['status'] === 'success') {
        $success_msg = $actionResult['message'];
    } else {
        $error_msg = $actionResult['message'];
    }
}

$showtimes_list = $controller->getAllShowtimes();
$movies_list = $controller->getAllMovies();
$rooms_list = $controller->getAllRooms();
?>

<div class="container-fluid">
    <div class="admin-page-header d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="mb-0 text-white fw-bold">Quản lý lịch chiếu</h1>
            <p class="mb-0 mt-2 text-muted">Gán suất chiếu cho phim và phòng. Giờ kết thúc có thể tự tính từ thời lượng phim nếu để trống.</p>
        </div>
        <button type="button" class="btn btn-netflix-red" data-bs-toggle="modal" data-bs-target="#addShowtimeModal">
            <i class="bi bi-plus-lg me-1"></i> Thêm suất chiếu
        </button>
    </div>

    <?php if ($success_msg): ?>
        <div class="alert admin-alert admin-alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i> <?= htmlspecialchars($success_msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert admin-alert admin-alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i> <?= htmlspecialchars($error_msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    <?php endif; ?>

    <div class="admin-card">
        <h5 class="mb-3 text-white"><i class="bi bi-calendar-event me-2"></i>Danh sách suất chiếu</h5>
        <div class="table-responsive">
            <table class="table table-hover admin-table align-middle mb-0">
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="18%">Phim</th>
                        <th width="18%">Rạp / Phòng</th>
                        <th width="10%">Ngày</th>
                        <th width="10%">Bắt đầu</th>
                        <th width="10%">Kết thúc</th>
                        <th width="10%">Giá vé</th>
                        <th width="9%">Trạng thái</th>
                        <th width="10%" class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($showtimes_list)): ?>
                        <?php foreach ($showtimes_list as $showtime): ?>
                            <tr>
                                <td><?= $showtime['id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($showtime['movie_title']) ?></strong>
                                    <br><span class="text-muted small"><?= (int)$showtime['movie_duration'] ?> phút</span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($showtime['theatre_name']) ?></strong>
                                    <br><span class="text-muted"><?= htmlspecialchars($showtime['room_name']) ?></span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($showtime['show_date'])) ?></td>
                                <td><?= date('H:i', strtotime($showtime['start_time'])) ?></td>
                                <td><?= date('H:i', strtotime($showtime['end_time'])) ?></td>
                                <td><?= number_format((float)$showtime['base_price'], 0, ',', '.') ?> đ</td>
                                <td>
                                    <?php if ($showtime['status'] === 'active'): ?>
                                        <span class="status-badge status-success">Đang mở</span>
                                    <?php else: ?>
                                        <span class="status-badge status-secondary">Đã hủy</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-info admin-icon-btn me-1 edit-showtime-btn"
                                            title="Sửa suất chiếu"
                                            data-showtime='<?= htmlspecialchars(json_encode($showtime, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>'>
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="" method="POST" class="d-inline" onsubmit="return confirm('Xóa suất chiếu này? Vé đã đặt liên quan cũng sẽ bị ảnh hưởng.');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $showtime['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger admin-icon-btn" title="Xóa suất chiếu">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">
                                <div class="admin-empty d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-calendar-event"></i>
                                    <span>Chưa có suất chiếu nào. Hãy thêm suất chiếu đầu tiên.</span>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
function renderShowtimeFormFields($prefix, $movies_list, $rooms_list) {
?>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Phim <span class="text-danger">*</span></label>
            <select class="form-select" name="movie_id" id="<?= $prefix ?>_movie_id" required>
                <option value="">-- Chọn phim --</option>
                <?php foreach ($movies_list as $movie): ?>
                    <option value="<?= $movie['id'] ?>" data-duration="<?= (int)$movie['duration'] ?>">
                        <?= htmlspecialchars($movie['title']) ?> (<?= (int)$movie['duration'] ?> phút)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Phòng chiếu <span class="text-danger">*</span></label>
            <select class="form-select" name="room_id" id="<?= $prefix ?>_room_id" required>
                <option value="">-- Chọn phòng --</option>
                <?php foreach ($rooms_list as $room): ?>
                    <option value="<?= $room['id'] ?>"><?= htmlspecialchars($room['theatre_name'] . ' - ' . $room['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Ngày chiếu <span class="text-danger">*</span></label>
            <input type="date" class="form-control" name="show_date" id="<?= $prefix ?>_show_date" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Giờ bắt đầu <span class="text-danger">*</span></label>
            <input type="time" class="form-control" name="start_time" id="<?= $prefix ?>_start_time" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Giờ kết thúc</label>
            <input type="time" class="form-control" name="end_time" id="<?= $prefix ?>_end_time">
            <div class="form-text">Để trống để tự tính từ thời lượng phim.</div>
        </div>
        <div class="col-md-6">
            <label class="form-label">Giá vé cơ bản (VNĐ) <span class="text-danger">*</span></label>
            <input type="number" class="form-control" name="base_price" id="<?= $prefix ?>_base_price" min="1000" step="1000" value="80000" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Trạng thái</label>
            <select class="form-select" name="status" id="<?= $prefix ?>_status">
                <option value="active">Đang mở</option>
                <option value="canceled">Đã hủy</option>
            </select>
        </div>
    </div>
<?php } ?>

<div class="modal fade" id="addShowtimeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-calendar-event me-2"></i>Thêm suất chiếu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <?php renderShowtimeFormFields('add', $movies_list, $rooms_list); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-admin-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-netflix-red">Thêm mới</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editShowtimeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Cập nhật suất chiếu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_showtime_id">
                    <?php renderShowtimeFormFields('edit', $movies_list, $rooms_list); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-admin-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-netflix-red">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editModal = new bootstrap.Modal(document.getElementById('editShowtimeModal'));
    document.querySelectorAll('.edit-showtime-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const showtime = JSON.parse(this.getAttribute('data-showtime'));
            document.getElementById('edit_showtime_id').value = showtime.id;
            document.getElementById('edit_movie_id').value = showtime.movie_id;
            document.getElementById('edit_room_id').value = showtime.room_id;
            document.getElementById('edit_show_date').value = showtime.show_date;
            document.getElementById('edit_start_time').value = showtime.start_time ? showtime.start_time.substring(0, 5) : '';
            document.getElementById('edit_end_time').value = showtime.end_time ? showtime.end_time.substring(0, 5) : '';
            document.getElementById('edit_base_price').value = showtime.base_price;
            document.getElementById('edit_status').value = showtime.status;
            editModal.show();
        });
    });
});
</script>

<?php require_once 'admin_footer.php'; ?>
