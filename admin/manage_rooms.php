<?php
require_once '../config.php';
require_once '../app/init.php';
require_once 'admin_header.php';
require_once 'admin_sidebar.php';

use App\Controllers\RoomController;

$controller = new RoomController();
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

$rooms_list = $controller->getAllRooms();
$theatres_list = $controller->getAllTheatres();
?>

<div class="container-fluid">
    <div class="admin-page-header d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="mb-0 text-white fw-bold">Quản lý phòng chiếu</h1>
            <p class="mb-0 mt-2 text-muted">Gán phòng cho từng rạp. Tên phòng phải duy nhất trong toàn hệ thống.</p>
        </div>
        <button type="button" class="btn btn-netflix-red" data-bs-toggle="modal" data-bs-target="#addRoomModal">
            <i class="bi bi-plus-lg me-1"></i> Thêm phòng
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
        <h5 class="mb-3 text-white"><i class="bi bi-door-open me-2"></i>Danh sách phòng chiếu</h5>
        <div class="table-responsive">
            <table class="table table-hover admin-table align-middle mb-0">
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="20%">Rạp</th>
                        <th width="15%">Tên phòng</th>
                        <th width="10%">Số ghế</th>
                        <th width="10%">Ghế thực tế</th>
                        <th width="10%">Trạng thái</th>
                        <th width="15%">Ngày tạo</th>
                        <th width="15%" class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rooms_list)): ?>
                        <?php foreach ($rooms_list as $room): ?>
                            <tr>
                                <td><?= $room['id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($room['theatre_name']) ?></strong>
                                    <br><span class="text-muted small"><?= htmlspecialchars($room['theatre_city'] ?? '') ?></span>
                                </td>
                                <td><?= htmlspecialchars($room['name']) ?></td>
                                <td><?= (int)$room['total_seats'] ?></td>
                                <td><?= (int)$room['seat_count'] ?></td>
                                <td>
                                    <?php if ($room['is_active']): ?>
                                        <span class="status-badge status-success">Hoạt động</span>
                                    <?php else: ?>
                                        <span class="status-badge status-secondary">Tạm ngưng</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($room['created_at'])) ?></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-info admin-icon-btn me-1 edit-room-btn"
                                            title="Sửa phòng"
                                            data-room='<?= htmlspecialchars(json_encode($room, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>'>
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="" method="POST" class="d-inline" onsubmit="return confirm('Xóa phòng này sẽ xóa toàn bộ ghế và suất chiếu liên quan. Bạn có chắc chắn?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $room['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger admin-icon-btn" title="Xóa phòng">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">
                                <div class="admin-empty d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-door-open"></i>
                                    <span>Chưa có phòng chiếu nào. Hãy thêm phòng đầu tiên.</span>
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
function renderRoomFormFields($prefix, $theatres_list, $isEdit = false) {
?>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Rạp chiếu <span class="text-danger">*</span></label>
            <select class="form-select" name="theatre_id" id="<?= $prefix ?>_theatre_id" required>
                <option value="">-- Chọn rạp --</option>
                <?php foreach ($theatres_list as $theatre): ?>
                    <option value="<?= $theatre['id'] ?>"><?= htmlspecialchars($theatre['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Tên phòng <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" id="<?= $prefix ?>_name" required placeholder="VD: Phòng 1">
        </div>
        <div class="col-md-6">
            <label class="form-label">Số ghế <span class="text-danger">*</span></label>
            <input type="number" class="form-control" name="total_seats" id="<?= $prefix ?>_total_seats" min="1" value="40" required>
        </div>
        <div class="col-md-6 d-flex align-items-end">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_active" id="<?= $prefix ?>_is_active" checked>
                <label class="form-check-label" for="<?= $prefix ?>_is_active">Phòng đang hoạt động</label>
            </div>
        </div>
    </div>
<?php } ?>

<div class="modal fade" id="addRoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-door-open me-2"></i>Thêm phòng chiếu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <?php renderRoomFormFields('add', $theatres_list); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-admin-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-netflix-red">Thêm mới</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editRoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Cập nhật phòng chiếu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_room_id">
                    <?php renderRoomFormFields('edit', $theatres_list, true); ?>
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
    const editModal = new bootstrap.Modal(document.getElementById('editRoomModal'));
    document.querySelectorAll('.edit-room-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const room = JSON.parse(this.getAttribute('data-room'));
            document.getElementById('edit_room_id').value = room.id;
            document.getElementById('edit_theatre_id').value = room.theatre_id;
            document.getElementById('edit_name').value = room.name || '';
            document.getElementById('edit_total_seats').value = room.total_seats || 0;
            document.getElementById('edit_is_active').checked = room.is_active == 1;
            editModal.show();
        });
    });
});
</script>

<?php require_once 'admin_footer.php'; ?>
