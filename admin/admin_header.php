<?php
/**
 * KIẾN THỨC PHP: Security & Include
 * Header này sẽ được gọi ở TẤT CẢ các trang trong thư mục admin/
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra quyền Admin: Phải đăng nhập và có role là 'admin'
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    // Nếu không phải admin, đẩy về trang login
    header("Location: ../login.php");
    exit;
}

// Lấy tên file hiện tại để highlight menu
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị hệ thống - Movie Booking</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Custom Admin CSS (Tông Đỏ Đen) -->
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar sẽ được include ngay sau thẻ div này -->
