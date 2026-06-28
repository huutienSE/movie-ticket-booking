<?php
require_once '../config.php';
require_once 'admin_header.php';
require_once 'admin_sidebar.php';

$success_msg = '';
$error_msg = '';

// --- XỬ LÝ POST REQUEST (Thêm, Sửa, Xóa) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'add') {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);

        if (!empty($name)) {
            // Kiểm tra trùng tên
            $check_stmt = mysqli_prepare($conn, "SELECT id FROM genres WHERE name = ?");
            mysqli_stmt_bind_param($check_stmt, "s", $name);
            mysqli_stmt_execute($check_stmt);
            $check_result = mysqli_stmt_get_result($check_stmt);

            if (mysqli_num_rows($check_result) > 0) {
                $error_msg = "Tên thể loại '$name' đã tồn tại!";
            } else {
                $stmt = mysqli_prepare($conn, "INSERT INTO genres (name, description) VALUES (?, ?)");
                mysqli_stmt_bind_param($stmt, "ss", $name, $description);
                if (mysqli_stmt_execute($stmt)) {
                    $success_msg = "Thêm thể loại thành công!";
                } else {
                    $error_msg = "Lỗi khi thêm: " . mysqli_error($conn);
                }
            }
        } else {
            $error_msg = "Tên thể loại không được để trống!";
        }
    } 
    elseif ($action === 'edit') {
        $id = (int)$_POST['id'];
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);

        if (!empty($name) && $id > 0) {
            // Kiểm tra trùng tên với id khác
            $check_stmt = mysqli_prepare($conn, "SELECT id FROM genres WHERE name = ? AND id != ?");
            mysqli_stmt_bind_param($check_stmt, "si", $name, $id);
            mysqli_stmt_execute($check_stmt);
            $check_result = mysqli_stmt_get_result($check_stmt);

            if (mysqli_num_rows($check_result) > 0) {
                $error_msg = "Tên thể loại '$name' đã tồn tại ở mục khác!";
            } else {
                $stmt = mysqli_prepare($conn, "UPDATE genres SET name = ?, description = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "ssi", $name, $description, $id);
                if (mysqli_stmt_execute($stmt)) {
                    $success_msg = "Cập nhật thể loại thành công!";
                } else {
                    $error_msg = "Lỗi khi cập nhật: " . mysqli_error($conn);
                }
            }
        } else {
            $error_msg = "Dữ liệu không hợp lệ!";
        }
    } 
    elseif ($action === 'delete') {
        $id = (int)$_POST['id'];
        if ($id > 0) {
            $stmt = mysqli_prepare($conn, "DELETE FROM genres WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            if (mysqli_stmt_execute($stmt)) {
                $success_msg = "Xóa thể loại thành công!";
            } else {
                $error_msg = "Lỗi khi xóa: " . mysqli_error($conn);
            }
        }
    }
}

// Lấy danh sách genres
$query = "SELECT * FROM genres ORDER BY id DESC";
$genres_result = mysqli_query($conn, $query);
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Quản lý Thể loại Phim</h2>
        <button type="button" class="btn btn-netflix-red" data-bs-toggle="modal" data-bs-target="#addGenreModal">
            <i class="bi bi-plus-circle me-1"></i> Thêm Thể loại mới
        </button>
    </div>

    <!-- Hiển thị thông báo -->
    <?php if ($success_msg): ?>
        <div class="alert alert-success alert-dismissible fade show bg-success text-white border-0" role="alert">
            <i class="bi bi-check-circle me-2"></i> <?= htmlspecialchars($success_msg) ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-danger alert-dismissible fade show bg-danger text-white border-0" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i> <?= htmlspecialchars($error_msg) ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Bảng danh sách thể loại -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="20%">Tên Thể loại</th>
                            <th width="40%">Mô tả</th>
                            <th width="20%">Ngày tạo</th>
                            <th width="15%" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($genres_result && mysqli_num_rows($genres_result) > 0): ?>
                            <?php while ($genre = mysqli_fetch_assoc($genres_result)): ?>
                                <tr>
                                    <td><?= $genre['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($genre['name']) ?></strong></td>
                                    <td><?= htmlspecialchars($genre['description'] ?? 'Không có mô tả') ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($genre['created_at'])) ?></td>
                                    <td class="text-center">
                                        <!-- Nút Sửa: Truyền dữ liệu vào attribute để JS xử lý -->
                                        <button class="btn btn-sm btn-outline-info me-1 edit-genre-btn"
                                                data-id="<?= $genre['id'] ?>"
                                                data-name="<?= htmlspecialchars($genre['name']) ?>"
                                                data-desc="<?= htmlspecialchars($genre['description'] ?? '') ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        
                                        <!-- Form Xóa nhỏ gọn -->
                                        <form action="" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thể loại này? Phim thuộc thể loại này sẽ bị gỡ thẻ.');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $genre['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">Chưa có thể loại nào. Hãy thêm mới!</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm Thể Loại -->
<div class="modal fade" id="addGenreModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm Thể Loại Mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label">Tên thể loại <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required placeholder="VD: Hành động, Hài hước...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả (Tùy chọn)</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Mô tả ngắn gọn về thể loại này..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-netflix-red">Thêm mới</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sửa Thể Loại -->
<div class="modal fade" id="editGenreModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Cập Nhật Thể Loại</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label class="form-label">Tên thể loại <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="edit_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả (Tùy chọn)</label>
                        <textarea class="form-control" name="description" id="edit_desc" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-info text-white">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Javascript xử lý mở modal sửa và điền dữ liệu -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const editBtns = document.querySelectorAll('.edit-genre-btn');
    const editModal = new bootstrap.Modal(document.getElementById('editGenreModal'));
    
    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Lấy dữ liệu từ data attributes
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const desc = this.getAttribute('data-desc');
            
            // Điền vào form sửa
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_desc').value = desc;
            
            // Mở modal
            editModal.show();
        });
    });
});
</script>

<?php require_once 'admin_footer.php'; ?>