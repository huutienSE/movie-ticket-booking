# 📖 Developer Guide - Hướng Dẫn Phát Triển

> Tài liệu này giúp các thành viên trong nhóm hiểu cách làm việc với kiến trúc MVC đã được setup sẵn.
> Hãy đọc kỹ trước khi bắt đầu code.

---

## 📁 Cấu Trúc Project

```txt
movie-ticket-booking/
│
├── app/
│   ├── controllers/
│   │   ├── BaseController.php      ← Controller gốc, TẤT CẢ controller phải kế thừa
│   │   ├── HomeController.php      ← Controller trang chủ (ví dụ mẫu)
│   │   ├── admin/                  ← Controllers cho Admin Site
│   │   └── user/                   ← Controllers cho User Site
│   │
│   ├── models/
│   │   └── BaseModel.php           ← Model gốc, TẤT CẢ model phải kế thừa
│   │
│   ├── services/                   ← Business logic phức tạp (nếu cần)
│   │
│   ├── helpers/
│   │   └── url_helper.php          ← Hàm tiện ích: asset(), url(), base_url(), redirect()
│   │
│   └── views/
│       ├── home/                   ← Views cho trang chủ
│       ├── booking/                ← Views cho luồng đặt vé (Thịnh)
│       ├── layouts/                ← Layout chung User Site (header.php, footer.php)
│       ├── admin/                  ← Views cho Admin Site
│       │   └── layouts/            ← Layout riêng Admin Site
│       └── errors/                 ← Trang lỗi (404.php, ...)
│
├── config/
│   ├── app.php                     ← Cấu hình app (base URL, tên app, debug)
│   └── database.php                ← Cấu hình database (host, dbname, user, pass)
│
├── core/                           ← ⚠️ KHÔNG ĐƯỢC SỬA các file trong đây
│   ├── App.php                     ← Khởi chạy ứng dụng
│   ├── Router.php                  ← Hệ thống routing
│   └── Database.php                ← Kết nối database (Singleton)
│
├── Database/
│   └── BookingTicketDatabase.sql   ← SQL script tạo database
│
├── public/
│   ├── index.php                   ← Entry point (KHÔNG SỬA)
│   ├── .htaccess                   ← URL rewriting (KHÔNG SỬA)
│   └── assets/
│       ├── css/                    ← File CSS
│       ├── js/                     ← File JavaScript
│       ├── img/                    ← Hình ảnh banner
│       ├── movieImage/             ← Hình ảnh phim
│       └── svg/                    ← Icon SVG
│
├── routes/
│   └── web.php                     ← Đăng ký tất cả routes tại đây
│
├── docs/                           ← Tài liệu
├── .env.example                    ← Mẫu file cấu hình môi trường
└── .gitignore

```

---

## ⚠️ Quy Tắc Quan Trọng

### 🔴 KHÔNG ĐƯỢC sửa:
| File | Lý do |
|------|-------|
| `core/App.php`, `core/Router.php`, `core/Database.php` | Core framework |
| `public/index.php`, `public/.htaccess` | Entry point + URL rewriting |
| `app/controllers/BaseController.php` | Base class chung |
| `app/models/BaseModel.php` | Base class chung |

### 🟢 Chỉ CẦN làm việc với:
| Bạn cần | File/Thư mục |
|---------|-------------|
| Tạo Controller mới | `app/controllers/user/` hoặc `app/controllers/admin/` |
| Tạo Model mới | `app/models/` |
| Tạo View mới | `app/views/` (theo module) |
| Đăng ký route | `routes/web.php` |
| Thêm CSS/JS | `public/assets/css/` hoặc `public/assets/js/` |
| Thêm hình ảnh | `public/assets/img/` hoặc `public/assets/movieImage/` |

---

## 🚀 Hướng Dẫn Tạo Module Mới

### Bước 1: Tạo Model (`app/models/TênModel.php`)
```php
<?php
require_once __DIR__ . '/BaseModel.php';

class MovieModel extends BaseModel
{
    protected $table = 'movies'; // Tên bảng trong database
}
```

### Bước 2: Tạo Controller (`app/controllers/user/` hoặc `admin/`)
```php
<?php
// File: app/controllers/user/MovieController.php
require_once __DIR__ . '/../BaseController.php';
require_once __DIR__ . '/../../models/MovieModel.php';

class MovieController extends BaseController
{
    private $movieModel;

    public function __construct()
    {
        $this->movieModel = new MovieModel();
    }

    public function index()
    {
        $movies = $this->movieModel->findAll();
        $this->view('user/movies/index', ['movies' => $movies]);
    }
}
```

> ⚠️ Controller trong `admin/` phải thêm prefix `Admin`: `AdminMovieController`

### Bước 3: Tạo View (`app/views/user/tên-module/`)
```php
<!-- File: app/views/user/movies/index.php -->
<div class="container">
    <?php foreach ($movies as $movie): ?>
        <h3><?= htmlspecialchars($movie['title']) ?></h3>
    <?php endforeach; ?>
</div>
```

### Bước 4: Đăng ký Route (`routes/web.php`)
```php
// Thêm vào khu vực USER ROUTES hoặc ADMIN ROUTES
$router->get('/movies', 'MovieController', 'index');
$router->post('/admin/movies/store', 'AdminMovieController', 'store');
```

---

## 🔀 Tránh Conflict Git

| Thành viên | Module | Files |
|-----------|--------|-------|
| Nhân | Movie, Genre | `models/MovieModel.php`, `controllers/user/MovieController.php`, `views/user/movies/` |
| Tiến | Room, Seat, Showtime | `models/RoomModel.php`, `controllers/user/ShowtimeController.php`, `views/user/showtimes/` |
| Quỳnh Anh | User Frontend | `views/user/`, `public/assets/css/` |
| Thịnh | Booking Frontend | `views/booking/`, `public/assets/css/movie.css, showtime.css, seat.css` |
| Hữu Tiền | Auth, Booking Backend | `models/UserModel.php`, `controllers/user/AuthController.php` |

### Quy tắc route:
- Mỗi người thêm route ở **block riêng** (có comment tên)
- **KHÔNG xóa route của người khác**
- Route User: `/movies`, `/showtimes`...
- Route Admin: `/admin/movies`, `/admin/showtimes`...

---

## 📋 Checklist

- [ ] Tạo Model (kế thừa `BaseModel`, khai báo `$table`)
- [ ] Tạo Controller (kế thừa `BaseController`)
- [ ] Tạo View
- [ ] Đăng ký route trong `routes/web.php`
- [ ] Test trên trình duyệt
- [ ] Commit và push lên branch riêng
