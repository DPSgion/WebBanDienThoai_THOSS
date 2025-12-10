<?php
session_start();
include 'config/config.php'; 

/**
 * Hàm lấy danh sách tất cả Danh mục
 * @param PDO $pdo
 * @return array
 */
function get_all_categories($pdo)
{
  try {
    $sql = "SELECT id_danh_muc, ten_danh_muc FROM danh_muc ORDER BY ten_danh_muc ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    // Log lỗi
    return [];
  }
}

$romFilter   = $_GET['rom']   ?? '';
$osFilter    = $_GET['os']    ?? '';
$priceFilter = $_GET['price'] ?? '';
$colorFilter = $_GET['color'] ?? '';

$sqlAll = "SELECT 
    sp.id_san_pham,
    sp.ten_san_pham,
    MIN(bt.gia) AS gia,
    (
        SELECT duong_dan_anh 
        FROM anh_san_pham 
        WHERE id_san_pham = sp.id_san_pham 
        LIMIT 1
    ) AS hinh_anh
FROM san_pham sp
LEFT JOIN bien_the bt ON sp.id_san_pham = bt.id_san_pham
WHERE 1 ";


// Lọc ROM
if (!empty($romFilter)) {
  $sqlAll .= " AND bt.rom = :rom ";
}

// Lọc OS (KHÔNG dùng param)
if (!empty($osFilter)) {
  if ($osFilter === 'iOS') {
    $sqlAll .= " AND sp.os LIKE 'iOS%'";
  } elseif ($osFilter === 'Android') {
    $sqlAll .= " AND sp.os LIKE 'Android%'";
  }
}

// Lọc màu sắc
if (!empty($colorFilter)) {
  $sqlAll .= " AND bt.mau = :color ";
}

$sqlAll .= " GROUP BY sp.id_san_pham ";

// Lọc giá
if ($priceFilter == "low_high") {
  $sqlAll .= " ORDER BY gia ASC ";
}
if ($priceFilter == "high_low") {
  $sqlAll .= " ORDER BY gia DESC ";
}

$stmt = $pdo->prepare($sqlAll);

// Bind đúng tham số nào có trong SQL
if (!empty($romFilter)) {
  $stmt->bindParam(':rom', $romFilter);
}

if (!empty($colorFilter)) {
  $stmt->bindParam(':color', $colorFilter);
}

// KHÔNG bindParam(':os') vì SQL không có :os !!!!

$stmt->execute();
$allProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);



// --- THỰC THI CHÍNH ---

// Lấy danh sách danh mục
$categories = get_all_categories($pdo);


// Lấy thông tin người dùng cho Header
$user_name = isset($_SESSION['ho_ten']) ? $_SESSION['ho_ten'] : 'TÀI KHOẢN';
$account_link = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true ? 'logout.php' : 'login.php';
$account_text = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true ? '👤XIN CHÀO, ' . htmlspecialchars($user_name) : '👤TÀI KHOẢN';

?>
<!doctype html>
<html lang="vi">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Tìm kiếm & Lọc — ĐIỆN THOẠI TRỰC TUYẾN</title>
  <link rel="stylesheet" href="assets/css/stylesTC.css">
  <link rel="stylesheet" href="assets/css/stylesSanPham.css">
  <style>
    .filter-menu li a {
      text-decoration: none;
      color: inherit;
      /* Giữ nguyên màu chữ như li */
      display: block;
      /* Giúp hover toàn dòng */
    }

    .filter-menu a.active {
      font-weight: bold;
      color: #ff3b30;
    }

    .filter-menu a.active {
      background: #f0f0f0;
      border-radius: 6px;
      color: #d00;
    }
  </style>
</head>

<body>
  <!-- MAIN HEADER / NAV -->
  <header class="main-header">
    <div class="container header-row">
      <div class="logo-left">
        <div class="logo">ĐIỆN THOẠI TRỰC TUYẾN</div>
      </div>
      <div class="search-center">
        <form action="TimKiem.php" method="GET">
          <input class="search-input" placeholder="Tìm kiếm sản phẩm" />
          <button class="search-btn" aria-label="Tìm kiếm">🔍</button>
        </form>
      </div>
      <div class="icons-right">
        <a href="TrangChu.php" class="icon-btn cart" aria-label="Trang chủ">🏠 </a>
        <a href="GioHang.php" class="icon-btn cart" aria-label="Giỏ hàng">🛒 </span></a>
        <a id="accountLink" href="User.php">👤</a>
        <a href="logout.php" class="icon-btn cart">🚪</a>
        <div class="danh-container">
          <button type="button" class="danh-muc" aria-haspopup="true" aria-expanded="false">☰ Danh mục</button>
          <ul class="danh-menu" role="menu">
            <?php foreach ($categories as $cat): ?>
              <li><a href="TimKiem.php?cat_id=<?php echo htmlspecialchars($cat['id_danh_muc']); ?>" class="danh-link"><?php echo htmlspecialchars($cat['ten_danh_muc']); ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
  </header>


  <main class="container search-page">
    <div class="filter-bar">
      <div class="filter-item">
        <button class="filter-btn">Bộ nhớ (ROM) <span class="arrow">▾</span></button>
        <ul class="filter-menu">
          <li><a href="SanPham.php?rom=&os=<?= $_GET['os'] ?? '' ?>&price=<?= $_GET['price'] ?? '' ?>&color=<?= $_GET['color'] ?? '' ?>">All</a></li>
          <li><a class="<?= (($_GET['rom'] ?? '') == '32GB') ? 'active' : '' ?>" href="SanPham.php?rom=32GB&os=<?= $_GET['os'] ?? '' ?>&price=<?= $_GET['price'] ?? '' ?>&color=<?= $_GET['color'] ?? '' ?>">32GB</a></li>
          <li><a class="<?= (($_GET['rom'] ?? '') == '64GB') ? 'active' : '' ?>" href="SanPham.php?rom=64GB&os=<?= $_GET['os'] ?? '' ?>&price=<?= $_GET['price'] ?? '' ?>&color=<?= $_GET['color'] ?? '' ?>">64GB</a></li>
          <li><a class="<?= (($_GET['rom'] ?? '') == '128GB') ? 'active' : '' ?>" href="SanPham.php?rom=128GB&os=<?= $_GET['os'] ?? '' ?>&price=<?= $_GET['price'] ?? '' ?>&color=<?= $_GET['color'] ?? '' ?>">128GB</a></li>
          <li><a class="<?= (($_GET['rom'] ?? '') == '256GB') ? 'active' : '' ?>" href="SanPham.php?rom=256GB&os=<?= $_GET['os'] ?? '' ?>&price=<?= $_GET['price'] ?? '' ?>&color=<?= $_GET['color'] ?? '' ?>">256GB</a></li>
          <li><a class="<?= (($_GET['rom'] ?? '') == '512GB') ? 'active' : '' ?>" href="SanPham.php?rom=512GB&os=<?= $_GET['os'] ?? '' ?>&price=<?= $_GET['price'] ?? '' ?>&color=<?= $_GET['color'] ?? '' ?>">512GB</a></li>
        </ul>
      </div>
      <div class="filter-item">
        <button class="filter-btn">Hệ điều hành <span class="arrow">▾</span></button>
        <ul class="filter-menu">
          <li><a href="SanPham.php?os=&rom=<?= $_GET['rom'] ?? '' ?>&price=<?= $_GET['price'] ?? '' ?>&color=<?= $_GET['color'] ?? '' ?>">All</a></li>
          <li><a class="<?= (($_GET['os'] ?? '') == 'iOS') ? 'active' : '' ?>" href="SanPham.php?os=iOS&rom=<?= $_GET['rom'] ?? '' ?>&price=<?= $_GET['price'] ?? '' ?>&color=<?= $_GET['color'] ?? '' ?>">iOS</a></li>
          <li><a class="<?= (($_GET['os'] ?? '') == 'Android') ? 'active' : '' ?>" href="SanPham.php?os=Android&rom=<?= $_GET['rom'] ?? '' ?>&price=<?= $_GET['price'] ?? '' ?>&color=<?= $_GET['color'] ?? '' ?>">Android</a></li>
        </ul>
      </div>
      <div class="filter-item">
        <button class="filter-btn">Giá <span class="arrow">▾</span></button>
        <ul class="filter-menu">
          <li><a href="SanPham.php?price=&rom=<?= $_GET['rom'] ?? '' ?>&os=<?= $_GET['os'] ?? '' ?>&color=<?= $_GET['color'] ?? '' ?>">Mặc định</a></li>
          <li><a class="<?= (($_GET['price'] ?? '') == 'low_high') ? 'active' : '' ?>" href="SanPham.php?price=low_high&rom=<?= $_GET['rom'] ?? '' ?>&os=<?= $_GET['os'] ?? '' ?>&color=<?= $_GET['color'] ?? '' ?>">Giá thấp → cao</a></li>
          <li><a class="<?= (($_GET['price'] ?? '') == 'high_low') ? 'active' : '' ?>" href="SanPham.php?price=high_low&rom=<?= $_GET['rom'] ?? '' ?>&os=<?= $_GET['os'] ?? '' ?>&color=<?= $_GET['color'] ?? '' ?>">Giá cao → thấp</a></li>
        </ul>
      </div>

      <div class="filter-item">
        <button class="filter-btn">Màu sắc <span class="arrow">▾</span></button>
        <ul class="filter-menu">
          <li><a href="SanPham.php?color=&rom=<?= $_GET['rom'] ?? '' ?>&os=<?= $_GET['os'] ?? '' ?>&price=<?= $_GET['price'] ?? '' ?>">Tất cả</a></li>
          <li><a class="<?= (($_GET['color'] ?? '') == 'Orange') ? 'active' : '' ?>" href="SanPham.php?color=Orange&rom=<?= $_GET['rom'] ?? '' ?>&os=<?= $_GET['os'] ?? '' ?>&price=<?= $_GET['price'] ?? '' ?>">Cam</a></li>
          <li><a class="<?= (($_GET['color'] ?? '') == 'Red') ? 'active' : '' ?>" href="SanPham.php?color=Red&rom=<?= $_GET['rom'] ?? '' ?>&os=<?= $_GET['os'] ?? '' ?>&price=<?= $_GET['price'] ?? '' ?>">Đỏ</a></li>
          <li><a class="<?= (($_GET['color'] ?? '') == 'White') ? 'active' : '' ?>" href="SanPham.php?color=White&rom=<?= $_GET['rom'] ?? '' ?>&os=<?= $_GET['os'] ?? '' ?>&price=<?= $_GET['price'] ?? '' ?>">Trắng</a></li>
          <li><a class="<?= (($_GET['color'] ?? '') == 'Black') ? 'active' : '' ?>" href="SanPham.php?color=Black&rom=<?= $_GET['rom'] ?? '' ?>&os=<?= $_GET['os'] ?? '' ?>&price=<?= $_GET['price'] ?? '' ?>">Đen</a></li>
        </ul>
      </div>
    </div>

    <!-- iPhone chính hãng -->
    <section class="section-grid">
      <div class="section-header">
        <h2>Tất cả sản phẩm</h2>
      </div>
      <div class="products-grid">
        <?php foreach ($allProducts as $p): ?>
          <div class="product-card">
            <div class="label">Trả góp 0%</div>
            <button class="fav">♡</button>

            <a href="ChiTietSanPham.php?id=<?= $p['id_san_pham'] ?>" aria-label="Xem chi tiết <?= $p['ten_san_pham'] ?>">
              <img src="<?= $p['hinh_anh'] ?>" alt="<?= $p['ten_san_pham'] ?>" class="prod-img">
              <div class="prod-name"><?= $p['ten_san_pham'] ?></div>
            </a>

            <?php if ($p['gia'] === null): ?>
              <div class="prod-prices">
                <div class="sale">Liên hệ</div>
              </div>
            <?php else: ?>
              <div class="prod-prices">
                <div class="sale"><?= number_format($p['gia'], 0, ',', '.') ?>đ</div>
                <div class="orig"><?= number_format($p['gia'] * 1.08, 0, ',', '.') ?>đ</div>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>

      </div>

      <div class="see-more">
        <button class="btn see">XEM THÊM →</button>
      </div>
    </section>
  </main>
  <!-- Footer -->
  <footer class="site-footer">
    <div class="container footer-grid">
      <div class="col">
        <h4>ĐIỆN THOẠI TRỰC TUYẾN</h4>
      </div>
      <div class="col">
        <!--SỬA-->
        <h4>THÀNH VIÊN 1</h4>
        <p>Họ & Tên: <a href="#">...</a></p>

        <p>MSSV: <a href="#">...</a></p>

        <p>Email: <a href="#">...</a></p>
        <!--END SỬA-->
      </div>
      <div class="col">
        <!--SỬA-->
        <h4>THÀNH VIÊN 2</h4>
        <p>Họ & Tên: <a href="#">...</a></p>

        <p>MSSV: <a href="#">...</a></p>

        <p>Email: <a href="#">...</a></p>
        <!--END SỬA-->
      </div>
      <div class="col">
        <!--SỬA-->
        <h4>THÀNH VIÊN 3</h4>
        <p>Họ & Tên: <a href="#">...</a></p>

        <p>MSSV: <a href="#">...</a></p>

        <p>Email: <a href="#">...</a></p>
        <!--END SỬA-->
      </div>
    </div>
    <!--SỬA-->
    <div class="footer-bottom">© 2025 ĐỀ TÀI XÂY DỰNG WEB BÁN ĐIỆN THOẠI TRỰC TUYẾN</div>
  </footer>

  <script>
    // simple dropdown toggle for filter bar
    document.querySelectorAll('.filter-item').forEach(fi => {
      const btn = fi.querySelector('.filter-btn');
      const menu = fi.querySelector('.filter-menu');
      if (!menu) return;
      btn.addEventListener('click', () => {
        const open = menu.style.display === 'block';
        document.querySelectorAll('.filter-menu').forEach(m => m.style.display = 'none');
        menu.style.display = open ? 'none' : 'block';
      });
    });
    // close menus on outside click
    document.addEventListener('click', (e) => {
      if (!e.target.closest('.filter-item')) {
        document.querySelectorAll('.filter-menu').forEach(m => m.style.display = 'none');
      }
    });
  </script>
  <script>
    // danh mục dropdown (shared behavior)
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
</body>

</html>