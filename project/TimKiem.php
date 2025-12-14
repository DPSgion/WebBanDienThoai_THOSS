<?php
// 1. Start Session & Config
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/config.php';

// 2. LOGIC TÌM KIẾM & LỌC (Giữ nguyên logic của bạn)
$keyword = trim($_GET["q"] ?? "");

if ($keyword !== "") {
    // --- TRƯỜNG HỢP 1: TÌM THEO TỪ KHÓA ---
    $sqlSearch = "
        SELECT 
            sp.id_san_pham,
            dm.ten_danh_muc,
            sp.ten_san_pham,
            MIN(bt.gia) AS gia,
            (SELECT duong_dan_anh FROM anh_san_pham WHERE id_san_pham = sp.id_san_pham LIMIT 1) AS hinh_anh
        FROM san_pham sp
        JOIN danh_muc dm ON dm.id_danh_muc = sp.id_danh_muc
        LEFT JOIN bien_the bt ON bt.id_san_pham = sp.id_san_pham
        WHERE sp.ten_san_pham LIKE :kw
           OR dm.ten_danh_muc LIKE :kw
        GROUP BY sp.id_san_pham
        ORDER BY sp.ten_san_pham ASC
    ";

    $stmt = $pdo->prepare($sqlSearch);
    $stmt->execute([":kw" => "%$keyword%"]);
    $allProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($allProducts)) {
        $message = "❗ Cửa hàng không có sản phẩm nào khớp với từ khóa '$keyword'.";
    }

} else {
    // --- TRƯỜNG HỢP 2: LỌC THEO DANH MỤC & TIÊU CHÍ KHÁC ---
    $romFilter   = $_GET['rom']   ?? '';
    $osFilter    = $_GET['os']    ?? '';
    $priceFilter = $_GET['price'] ?? '';
    $colorFilter = $_GET['color'] ?? '';
    $cat_id      = $_GET['cat_id'] ?? '';

    $sqlAll = "SELECT 
        sp.id_san_pham, dm.ten_danh_muc,
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
    JOIN danh_muc dm on dm.id_danh_muc = sp.id_danh_muc
    WHERE dm.id_danh_muc = :id_cat ";

    // Nối chuỗi điều kiện
    if (!empty($romFilter)) {
        $sqlAll .= " AND bt.rom = :rom ";
    }
    if (!empty($osFilter)) {
        if ($osFilter === 'iOS') {
            $sqlAll .= " AND sp.os LIKE 'iOS%'";
        } elseif ($osFilter === 'Android') {
            $sqlAll .= " AND sp.os LIKE 'Android%'";
        }
    }
    if (!empty($colorFilter)) {
        $sqlAll .= " AND bt.mau = :color ";
    }

    $sqlAll .= " GROUP BY sp.id_san_pham ";

    // Sắp xếp
    if ($priceFilter == "low_high") {
        $sqlAll .= " ORDER BY gia ASC ";
    }
    if ($priceFilter == "high_low") {
        $sqlAll .= " ORDER BY gia DESC ";
    }

    $stmt = $pdo->prepare($sqlAll);
    $params = [':id_cat' => $cat_id];

    if (!empty($romFilter)) {
        $params[':rom'] = $romFilter;
    }
    if (!empty($colorFilter)) {
        $params[':color'] = $colorFilter;
    }

    $stmt->execute($params);
    $allProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
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
      display: block;
    }
    .filter-menu a.active {
      background: #f0f0f0;
      font-weight: bold;
      color: #d00;
      border-radius: 6px;
    }
  </style>
</head>

<body>
  <?php require_once './includes/header.php'; ?>

  <?php if (!empty($message)): ?>
    <h2 style="color:red; text-align:center; margin:20px 0;">
      <?= $message ?>
    </h2>
  <?php endif; ?>

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

    <section class="section-grid">
      <div class="section-header">
        <h2>
            <?php 
                if (!empty($allProducts)) {
                    echo htmlspecialchars($allProducts[0]['ten_danh_muc'] ?? 'Kết quả tìm kiếm');
                } else {
                    echo 'Không tìm thấy sản phẩm';
                }
            ?>
        </h2>
      </div>
      
      <div class="products-grid">
        <?php foreach ($allProducts as $p): ?>
          <div class="product-card">
            <div class="label">Trả góp 0%</div>
            <button class="fav">♡</button>

            <a href="ChiTietSanPham.php?id=<?= $p['id_san_pham'] ?>" aria-label="Xem chi tiết <?= $p['ten_san_pham'] ?>">
               <img src="<?= !empty($p['hinh_anh']) ? $p['hinh_anh'] : 'assets/images/no-image.png' ?>" 
                   alt="<?= $p['ten_san_pham'] ?>" class="prod-img">
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

      <?php if(!empty($allProducts)): ?>
      <div class="see-more">
        <button class="btn see">XEM THÊM →</button>
      </div>
      <?php endif; ?>
    </section>
  </main>

  <?php require_once './includes/footer.php'; ?>

  <script>
    document.querySelectorAll('.filter-item').forEach(fi => {
      const btn = fi.querySelector('.filter-btn');
      const menu = fi.querySelector('.filter-menu');
      if (!menu) return;
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
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

</body>
</html>