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
│   ├── App.php                     ← Bootstrap ứng dụng
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
│       ├── img/                    ← Hình ảnh
│       └── svg/                    ← Icon SVG
│
├── routes/
│   └── web.php                     ← Đăng ký tất cả routes tại đây
│
├── docs/                           ← Tài liệu
│   ├── developer-guide.md          ← File này
│   └── progress.md                 ← Nhật ký tiến độ
│
├── .env.example                    ← Mẫu file cấu hình môi trường
└── .gitignore
```

---

## ⚠️ Quy Tắc Quan Trọng — ĐỌC TRƯỚC KHI CODE

### 🔴 KHÔNG ĐƯỢC sửa các file sau (trừ khi Team Leader cho phép):

| File | Lý do |
|------|-------|
| `core/App.php` | Bootstrap ứng dụng |
| `core/Router.php` | Hệ thống routing |
| `core/Database.php` | Kết nối database |
| `public/index.php` | Entry point duy nhất |
| `public/.htaccess` | URL rewriting |
| `app/controllers/BaseController.php` | Base class chung |
| `app/models/BaseModel.php` | Base class chung |

### 🟢 Chỉ CẦN làm việc với:

| Bạn cần | File/Thư mục |
|---------|-------------|
| Tạo Controller mới | `app/controllers/user/` hoặc `app/controllers/admin/` |
| Tạo Model mới | `app/models/` |
| Tạo View mới | `app/views/` (theo module) |
| Đăng ký route | `routes/web.php` (thêm dòng mới, KHÔNG xóa route người khác) |
| Thêm CSS | `public/assets/css/` |
| Thêm JS | `public/assets/js/` |
| Thêm hình ảnh | `public/assets/img/` hoặc `public/assets/svg/` |

---

## 🚀 Hướng Dẫn Từng Bước

### 1. Tạo Model Mới

Model đại diện cho một bảng trong database. Đặt file trong `app/models/`.

**Ví dụ: Tạo `MovieModel.php`**

```php
<?php
// File: app/models/MovieModel.php

require_once __DIR__ . '/BaseModel.php';

class MovieModel extends BaseModel
{
    // Tên bảng trong database — BẮT BUỘC phải khai báo
    protected $table = 'movies';

    // Thêm các method riêng nếu cần
    public function findByGenre($genreId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE genre_id = :genre_id"
        );
        $stmt->execute(['genre_id' => $genreId]);
        return $stmt->fetchAll();
    }

    public function search($keyword)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE title LIKE :keyword"
        );
        $stmt->execute(['keyword' => '%' . $keyword . '%']);
        return $stmt->fetchAll();
    }
}
```

**BaseModel đã có sẵn các method sau (không cần viết lại):**

| Method | Mô tả | Ví dụ |
|--------|--------|-------|
| `findAll()` | Lấy tất cả bản ghi | `$this->model->findAll()` |
| `findById($id)` | Tìm theo ID | `$this->model->findById(5)` |
| `delete($id)` | Xóa theo ID | `$this->model->delete(5)` |

---

### 2. Tạo Controller Mới

Controller xử lý request và trả về response. Đặt file theo phân hệ:
- **User Site** → `app/controllers/user/`
- **Admin Site** → `app/controllers/admin/`

**Ví dụ: Tạo `MovieController.php` cho User Site**

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

    // GET /movies — Danh sách phim
    public function index()
    {
        $movies = $this->movieModel->findAll();
        $this->view('user/movies/index', [
            'title' => 'Danh Sách Phim',
            'movies' => $movies
        ]);
    }

    // GET /movies/show?id=1 — Chi tiết phim
    public function show()
    {
        $id = $_GET['id'] ?? null;
        $movie = $this->movieModel->findById($id);

        if (!$movie) {
            http_response_code(404);
            require_once __DIR__ . '/../../views/errors/404.php';
            return;
        }

        $this->view('user/movies/show', [
            'title' => $movie['title'],
            'movie' => $movie
        ]);
    }
}
```

**Ví dụ: Tạo `MovieController.php` cho Admin Site**

```php
<?php
// File: app/controllers/admin/MovieController.php

require_once __DIR__ . '/../BaseController.php';
require_once __DIR__ . '/../../models/MovieModel.php';

class AdminMovieController extends BaseController
{
    private $movieModel;

    public function __construct()
    {
        $this->movieModel = new MovieModel();
    }

    // GET /admin/movies — Quản lý phim
    public function index()
    {
        $movies = $this->movieModel->findAll();
        // Sử dụng layout 'admin' (tham số thứ 3)
        $this->view('admin/movies/index', [
            'title' => 'Quản Lý Phim',
            'movies' => $movies
        ], 'admin');
    }
}
```

> **⚠️ LƯU Ý VỀ ĐẶT TÊN CLASS:**
> - Controller trong `user/` → đặt tên bình thường: `MovieController`, `ShowtimeController`
> - Controller trong `admin/` → **thêm prefix `Admin`**: `AdminMovieController`, `AdminShowtimeController`
> - Lý do: PHP không cho phép 2 class trùng tên trong cùng một request

---

### 3. Tạo View Mới

View là file PHP chứa HTML. Tổ chức theo module:

```txt
app/views/
├── user/                       ← Views cho User Site
│   └── movies/
│       ├── index.php           ← Danh sách phim
│       └── show.php            ← Chi tiết phim
├── admin/                      ← Views cho Admin Site
│   └── movies/
│       ├── index.php           ← Danh sách phim (admin)
│       ├── create.php          ← Form thêm phim
│       └── edit.php            ← Form sửa phim
└── ...
```

**Ví dụ: `app/views/user/movies/index.php`**

```php
<div class="container" style="padding: 40px 20px;">
    <h2>Phim Đang Chiếu</h2>
    <div class="movie-grid">
        <?php foreach ($movies as $movie): ?>
            <div class="movie-card">
                <img src="/movie-ticket-booking/public/assets/img/<?= $movie['poster'] ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
                <h3><?= htmlspecialchars($movie['title']) ?></h3>
                <a href="/movie-ticket-booking/public/movies/show?id=<?= $movie['id'] ?>">Xem Chi Tiết</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
```

> **📌 Lưu ý:**
> - View chỉ chứa HTML + hiển thị dữ liệu. KHÔNG viết logic xử lý phức tạp trong view.
> - Dữ liệu được truyền từ Controller qua `$this->view('path', $data)` — các key trong `$data` sẽ trở thành biến trong view (ví dụ: `$movies`, `$title`).
> - Header và Footer được tự động include bởi `BaseController`.

---

### 4. Đăng Ký Route

Mở file `routes/web.php` và thêm route mới.

```php
<?php
// File: routes/web.php

$router = $this->router;

// ---------- USER ROUTES ----------
$router->get('/', 'HomeController', 'index');
$router->get('/home', 'HomeController', 'index');

// Movie routes (User) — Nhân thêm
$router->get('/movies', 'MovieController', 'index');
$router->get('/movies/show', 'MovieController', 'show');

// Showtime routes (User) — Tiến thêm
$router->get('/showtimes', 'ShowtimeController', 'index');

// ---------- ADMIN ROUTES ----------
// Movie routes (Admin) — Nhân thêm
$router->get('/admin/movies', 'AdminMovieController', 'index');
$router->get('/admin/movies/create', 'AdminMovieController', 'create');
$router->post('/admin/movies/store', 'AdminMovieController', 'store');
```

> **⚠️ QUY TẮC ROUTE:**
> - Mỗi người **thêm route ở khu vực riêng** (có comment tên người thêm)
> - **KHÔNG xóa hoặc sửa route của người khác**
> - Route User bắt đầu bằng `/` (ví dụ: `/movies`)
> - Route Admin bắt đầu bằng `/admin/` (ví dụ: `/admin/movies`)
> - Cú pháp: `$router->get('/đường-dẫn', 'TênController', 'tênMethod');`
> - Hỗ trợ 2 HTTP method: `$router->get()` và `$router->post()`

---

### 5. Sử Dụng Helper Functions

Các hàm tiện ích có sẵn, dùng được ở mọi nơi (controller, view):

| Hàm | Mô tả | Kết quả |
|-----|--------|---------|
| `base_url()` | URL gốc của app | `http://localhost/movie-ticket-booking/public` |
| `url('movies')` | Tạo URL đầy đủ | `http://localhost/movie-ticket-booking/public/movies` |
| `asset('css/style.css')` | Tạo URL tới file asset | `http://localhost/movie-ticket-booking/public/assets/css/style.css` |
| `redirect('movies')` | Chuyển hướng trang | Redirect tới `/movies` |

---

### 6. Quy Trình BaseController — `$this->view()` Hoạt Động Như Thế Nào?

Khi bạn gọi:

```php
$this->view('user/movies/index', ['movies' => $movies, 'title' => 'Phim'], 'user');
```

BaseController sẽ tự động:

```
1. extract($data)         →  Biến $movies và $title có sẵn trong view
2. require header.php     →  Load layout header (user hoặc admin)
3. require view file      →  Load app/views/user/movies/index.php
4. require footer.php     →  Load layout footer
```

**Tham số thứ 3 — Layout:**
- `'user'` (mặc định) → dùng `app/views/layouts/header.php` + `footer.php`
- `'admin'` → dùng `app/views/admin/layouts/header.php` + `footer.php`

---

## 🔀 Tránh Conflict Git

### Mỗi thành viên chỉ tạo/sửa file trong phạm vi module của mình:

| Thành viên | Module | Files tạo mới |
|-----------|--------|--------------|
| Nhân | Movie, Genre | `models/MovieModel.php`, `models/GenreModel.php`, `controllers/user/MovieController.php`, `controllers/admin/AdminMovieController.php`, `views/user/movies/`, `views/admin/movies/` |
| Tiến | Room, Seat, Showtime | `models/RoomModel.php`, `models/SeatModel.php`, `models/ShowtimeModel.php`, `controllers/user/ShowtimeController.php`, `views/user/showtimes/` |
| Quỳnh Anh | User Frontend | `views/user/` (các trang UI), `public/assets/css/` |
| Thịnh | Booking Frontend | `views/user/booking/`, `public/assets/css/booking.css` |
| Hữu Tiền | Auth, Booking Backend | `models/UserModel.php`, `models/BookingModel.php`, `controllers/user/AuthController.php`, `controllers/user/BookingController.php` |

### File dễ conflict nhất: `routes/web.php`

**Cách giảm conflict:**
1. Mỗi người thêm route vào **block riêng**, có comment tên
2. Mỗi block cách nhau 1 dòng trống
3. Khi merge, nếu conflict → giữ lại TẤT CẢ routes của mọi người

---

## 📋 Checklist Khi Tạo Module Mới

Khi bạn bắt đầu code một module mới, hãy làm theo thứ tự:

- [ ] **Bước 1**: Tạo Model (`app/models/TênModel.php`) — kế thừa `BaseModel`, khai báo `$table`
- [ ] **Bước 2**: Tạo Controller (`app/controllers/user/` hoặc `admin/`) — kế thừa `BaseController`
- [ ] **Bước 3**: Tạo thư mục View (`app/views/user/tên-module/` hoặc `admin/tên-module/`)
- [ ] **Bước 4**: Tạo các file View (`.php`) trong thư mục vừa tạo
- [ ] **Bước 5**: Đăng ký route trong `routes/web.php`
- [ ] **Bước 6**: Tạo CSS riêng cho module nếu cần (`public/assets/css/tên-module.css`)
- [ ] **Bước 7**: Test trên trình duyệt
- [ ] **Bước 8**: Commit và push lên branch riêng

---

## ❓ FAQ

**Q: Tại sao controller Admin phải đặt tên khác (AdminMovieController)?**
> Vì PHP không cho phép 2 class cùng tên. Nếu cả `user/MovieController.php` và `admin/MovieController.php` đều có class `MovieController` → sẽ bị lỗi.

**Q: Làm sao kết nối database?**
> Đã được xử lý tự động. Chỉ cần kế thừa `BaseModel` và dùng `$this->db` để query. Cấu hình database ở `config/database.php`.

**Q: Tôi cần thêm một helper function mới?**
> Tạo file mới trong `app/helpers/` (ví dụ: `string_helper.php`), rồi báo Team Leader để thêm vào `App.php`.

**Q: CSS/JS mới đặt ở đâu?**
> `public/assets/css/` cho CSS, `public/assets/js/` cho JavaScript. Sau đó include vào view hoặc layout.

**Q: Tôi muốn thêm middleware (kiểm tra đăng nhập)?**
> Tạm thời kiểm tra `$_SESSION['user']` trong controller. Sau này Team Leader sẽ thêm middleware system.

**Q: Tôi không biết route nào đã được đăng ký?**
> Mở file `routes/web.php` để xem toàn bộ routes.
