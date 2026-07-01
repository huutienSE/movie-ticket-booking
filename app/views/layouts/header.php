<!-- http://localhost/movie-ticket-booking/app/views/layouts/header.php -->




<!-- /a
    index.php -< import a.data , import styles.css ,...........a 
    a.data
    styles.css

/b
    index.php
    styles.css
/c
    index.php
    styles.css
/d -->

<?php
// MÃ´ phá»ng session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Giáº£ láº­p dá»¯ liá»‡u máº£ng Ráº¡p phim náº¿u biáº¿n $theaters chÆ°a Ä‘Æ°á»£c Controller truyá»n vÃ o
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Ticket Booking</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css" />
    
    <link rel="stylesheet" href="/movie-ticket-booking/css/global.css">
    <link rel="stylesheet" href="/movie-ticket-booking/css/header.css">
    <link rel="stylesheet" href="/movie-ticket-booking/css/footer.css"> 
    <link rel="stylesheet" href="/movie-ticket-booking/css/home.css">
    <link rel="stylesheet" href="/movie-ticket-booking/css/movies.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
        <div class="header-top">
            <div class="logo">
                <a href="/"><img src="/movie-ticket-booking/images/logo.png" alt="Logo"></a>
            </div>
            
            <div class="header-top-right">
    <form class="search-box" action="/search" method="GET">
        <input type="text" name="keyword" placeholder="TÃ¬m phim...">
        <button type="submit" class="search-btn">
            <img src="/movie-ticket-booking/images/svg/search1.svg" alt="Search" class="search-icon">
        </button>
    </form>

    <div class="auth-section">
        <?php if (isset($_SESSION['user'])): ?>
            <div class="user-profile">
                <span>Hi, <?php echo htmlspecialchars($_SESSION['user']['name']); ?></span>
                <a href="/settings" title="CÃ i Ä‘áº·t tÃ i khoáº£n">
                    <img src="/movie-ticket-booking/images/svg/setting.svg" alt="Settings" class="setting-icon">
                </a>
            </div>
        <?php else: ?>
            <a href="/login" class="login-btn">
            ÄÄƒng nháº­p
            </a>
        <?php endif; ?>
    </div>
</div>
        </div>

        <nav class="header-bottom">
            <a href="/movies" class="nav-item">Phim</a>
            <a href="/huong-dan-dat-ve" class="nav-item">HÆ°á»›ng dáº«n Ä‘áº·t vÃ©</a>
        </nav>
        </div>
    </header>
    
    <main>
