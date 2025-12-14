<?php
// Kiểm tra session trước khi start để tránh lỗi nếu include nhiều lần
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';

// Hàm lấy danh mục (Chỉ khai báo nếu chưa tồn tại để tránh xung đột)
if (!function_exists('get_all_categories')) {
    function get_all_categories($pdo)
    {
        try {
            $sql = "SELECT id_danh_muc, ten_danh_muc FROM danh_muc ORDER BY ten_danh_muc ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}

$dsDanhMuc = get_all_categories($pdo);
?>
<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ĐIỆN THOẠI TRỰC TUYẾN</title>
    <link rel="stylesheet" href="assets/css/stylesTC.css">
</head>

<body>

    <header class="main-header">
        <div class="container header-inner">
            <div class="header-top">
                <a href="TrangChu.php" style="text-decoration:none">
                    <div class="logo">ĐIỆN THOẠI TRỰC TUYẾN</div>
                </a>
                
            </div>

            <div class="header-bottom">
                <div class="header-row">
                    <div class="header-bottom-border">
                        <div class="categories-short">
                            <div class="danh-container">
                                <button type="button" class="danh-muc" aria-haspopup="true" aria-expanded="false">☰ Danh mục</button>
                                <ul class="danh-menu">
                                    <?php foreach ($dsDanhMuc as $dm): ?>
                                        <li><a href="TimKiem.php?cat_id=<?= $dm['id_danh_muc'] ?>">
                                                <?= $dm['ten_danh_muc'] ?>
                                            </a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            </div>

                        <div class="search-wrap">
                            <form action="TimKiem.php" method="get" style="width: 500px;">
                                <input class="search" placeholder="Tìm kiếm" name="q" aria-label="Tìm kiếm" />
                                <button class="search-btn" aria-label="Tìm kiếm" type="submit">🔍</button>
                            </form>
                        </div>

                        <nav class="main-nav" aria-label="Main navigation">
                            <a href="SanPham.php">📱SẢN PHẨM</a>
                            <a href="GioHang.php">🛒GIỎ HÀNG</a>
                            <a id="accountLink" href="User.php">
                                <?php
                                if (isset($_SESSION['ho_ten'])) {
                                    echo "👤 Xin chào, " . $_SESSION['ho_ten'];
                                } else {
                                    echo "👤 TÀI KHOẢN";
                                }
                                ?>
                            </a>
                            <a href="logout.php">🚪</a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <script>
        (function() {
            document.querySelectorAll('.danh-container').forEach(dc => {
                const btn = dc.querySelector('.danh-muc');
                const menu = dc.querySelector('.danh-menu');
                if (!btn || !menu) return;
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    dc.classList.toggle('open');
                    btn.setAttribute('aria-expanded', dc.classList.contains('open'))
                });
                menu.addEventListener('click', (e) => e.stopPropagation());
            });
            document.addEventListener('click', () => document.querySelectorAll('.danh-container').forEach(dc => {
                dc.classList.remove('open');
                dc.querySelector('.danh-muc')?.setAttribute('aria-expanded', 'false');
            }));
        })();
    </script>