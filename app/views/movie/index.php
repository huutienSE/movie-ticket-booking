<?php
require_once __DIR__ . '/../layouts/header.php'; // Äiá»u chá»‰nh Ä‘Æ°á»ng dáº«n header cho Ä‘Ãºng vá»›i thÆ° má»¥c cá»§a em

// 1. Káº¾T Ná»I DATABASE Báº°NG PDO
$host = '127.0.0.1';
$port = '3308';
$dbname = 'movie_ticket_booking';
$username = 'root';
$password = '123456'; 

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Lá»—i káº¿t ná»‘i CSDL: " . $e->getMessage());
}

// 2. HÃ€M TRUY Váº¤N Láº¤Y PHIM THEO TRáº NG THÃI
function getMoviesByStatus($pdo, $status) {
    $sql = "
        SELECT m.id, m.title, m.age_restriction, m.duration, m.country, 
               mi.image_url as poster,
               GROUP_CONCAT(g.name SEPARATOR ', ') as genres
        FROM movies m
        LEFT JOIN movie_images mi ON m.id = mi.movie_id
        LEFT JOIN movie_genre mg ON m.id = mg.movie_id
        LEFT JOIN genres g ON mg.genre_id = g.id
        WHERE m.status = :status AND m.is_active = 1
        GROUP BY m.id
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['status' => $status]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$nowShowingMovies = getMoviesByStatus($pdo, 'now_showing');
$comingSoonMovies = getMoviesByStatus($pdo, 'coming');

// HÃ m format nhÃ£n Ä‘á»™ tuá»•i vÃ  text mÃ´ táº£
function getAgeDescription($age) {
    if ($age == 0) {
        return ['label' => 'P', 'text' => 'P: Phim dÃ nh cho má»i lá»©a tuá»•i', 'color' => '#22c55e'];
    }
    return [
        'label' => 'T' . $age, 
        'text' => "T$age: Phim dÃ nh cho khÃ¡n giáº£ tá»« Ä‘á»§ $age tuá»•i trá»Ÿ lÃªn ($age+)", 
        'color' => ($age >= 18 ? '#ef4444' : '#eab308')
    ];
}
?>

<div class="movie-page-container">
    <div class="movie-tabs">
        <button class="tab-btn active" onclick="openTab('now_showing', this)">PHIM ÄANG CHIáº¾U</button>
        <button class="tab-btn" onclick="openTab('coming_soon', this)">PHIM Sáº®P CHIáº¾U</button>
    </div>

    <h1 class="page-main-title" id="main-title">PHIM ÄANG CHIáº¾U</h1>

    <div id="now_showing" class="tab-content active">
        <div class="movie-grid">
            <?php foreach ($nowShowingMovies as $movie): ?>
                <?php 
                    $ageDesc = getAgeDescription($movie['age_restriction']);
                    $posterUrl = !empty($movie['poster']) ? $movie['poster'] : 'https://via.placeholder.com/300x450?text=No+Image';
                ?>
                <div class="movie-list-card">
                    <div class="card-poster">
                        <img src="<?= htmlspecialchars($posterUrl) ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
                    </div>
                    <div class="card-info">
                        <h2><?= htmlspecialchars($movie['title']) ?> (<?= $ageDesc['label'] ?>)</h2>
                        <ul class="info-tags">
                            <li>
                                <img src="/movie-ticket-booking/images/svg/tag.svg" alt="Genre" class="icon-sm"> 
                                <?= htmlspecialchars($movie['genres'] ?? 'Äang cáº­p nháº­t') ?>
                            </li>
                            <li>
                                <img src="/movie-ticket-booking/images/svg/time.svg" alt="Duration" class="icon-sm"> 
                                <?= htmlspecialchars($movie['duration']) ?> phÃºt
                            </li>
                            <li>
                                <img src="/movie-ticket-booking/images/svg/world.svg" alt="Country" class="icon-sm"> 
                                <?= htmlspecialchars($movie['country']) ?>
                            </li>
                            <li class="age-warning" style="color: <?= $ageDesc['color'] ?>;">
                                <svg class="icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                <?= $ageDesc['text'] ?>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="coming_soon" class="tab-content">
        <div class="movie-grid">
            <?php foreach ($comingSoonMovies as $movie): ?>
                <?php 
                    $ageDesc = getAgeDescription($movie['age_restriction']);
                    $posterUrl = !empty($movie['poster']) ? $movie['poster'] : 'https://via.placeholder.com/300x450?text=No+Image';
                ?>
                <div class="movie-list-card">
                    <div class="card-poster">
                        <img src="<?= htmlspecialchars($posterUrl) ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
                    </div>
                    <div class="card-info">
                        <h2><?= htmlspecialchars($movie['title']) ?> (<?= $ageDesc['label'] ?>)</h2>
                        <ul class="info-tags">
                            <li>
                                <img src="/movie-ticket-booking/images/svg/tag.svg" alt="Genre" class="icon-sm"> 
                                <?= htmlspecialchars($movie['genres'] ?? 'Äang cáº­p nháº­t') ?>
                            </li>
                            <li>
                                <img src="/movie-ticket-booking/images/svg/time.svg" alt="Duration" class="icon-sm"> 
                                <?= htmlspecialchars($movie['duration']) ?> phÃºt
                            </li>
                            <li>
                                <img src="/movie-ticket-booking/images/svg/world.svg" alt="Country" class="icon-sm"> 
                                <?= htmlspecialchars($movie['country']) ?>
                            </li>
                            <img src="/movie-ticket-booking/images/svg/user.svg" alt="TagAge" class="icon-sm"> 
                                <?= htmlspecialchars($ageDesc['text']) ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
    // Script Ä‘á»ƒ chuyá»ƒn Tab
    function openTab(tabId, btnElement) {
        // 1. áº¨n táº¥t cáº£ tab content
        var contents = document.getElementsByClassName("tab-content");
        for (var i = 0; i < contents.length; i++) {
            contents[i].classList.remove("active");
        }
        // 2. XÃ³a class active á»Ÿ táº¥t cáº£ cÃ¡c nÃºt
        var btns = document.getElementsByClassName("tab-btn");
        for (var i = 0; i < btns.length; i++) {
            btns[i].classList.remove("active");
        }
        // 3. Hiá»ƒn thá»‹ tab Ä‘Æ°á»£c chá»n vÃ  Ä‘Ã¡nh dáº¥u nÃºt
        document.getElementById(tabId).classList.add("active");
        btnElement.classList.add("active");
        // 4. Äá»•i tiÃªu Ä‘á» chÃ­nh
        document.getElementById("main-title").innerText = btnElement.innerText;
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
