# Project Progress

## 2026-06-27: Restructure MVC Framework
- Đã xóa các thư mục dư thừa không phù hợp (app/Console, app/Events, resources).
- Tạo kiến trúc Core MVC (Router, App, Database singleton).
- Tổ chức lại cấu trúc thư mục controllers (admin, user).
- Tạo cấu hình cơ bản (config/app.php, config/database.php, .env.example).
- Thiết lập helper xử lý URL (url_helper.php).
- Sửa lại BaseController và HomeController để tuân theo chuẩn MVC mới.
- Chuyển logic routing sang sử dụng Router class.
- Sửa đường dẫn tĩnh trong view từ `/MOVIE-TICKET-BOOKING/` sang chữ thường.

*File này sẽ được cập nhật thêm sau mỗi lần hoàn thành các module mới.*
