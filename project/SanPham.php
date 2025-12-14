<?php
// 1. Start Session & Config
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/config.php';

// 2. XỬ LÝ LOGIC LỌC SẢN PHẨM
// Lấy tham số từ URL
$romFilter   = $_GET['rom']   ?? '';
$osFilter    = $_GET['os']    ?? '';
$priceFilter = $_GET['price'] ?? '';
$colorFilter = $_GET['color'] ?? '';

// Câu SQL gốc
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

// --- Nối chuỗi điều kiện ---

// Lọc ROM
if (!empty($romFilter)) {
  $sqlAll .= " AND bt.rom = :rom ";
}

// Lọc OS (Dùng so sánh cứng để an toàn, không cần bindParam)
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

// Group by để tránh trùng sản phẩm do join
$sqlAll .= " GROUP BY sp.id_san_pham ";

// Lọc giá (Sắp xếp)
if ($priceFilter == "low_high") {
  $sqlAll .= " ORDER BY gia ASC ";
} elseif ($priceFilter == "high_low") {
  $sqlAll .= " ORDER BY gia DESC ";
} else {
    // Mặc định sắp xếp mới nhất hoặc theo tên
    $sqlAll .= " ORDER BY sp.id_san_pham DESC ";
}

// --- Chuẩn bị và Thực thi ---
$stmt = $pdo->prepare($sqlAll);

// Bind tham số
if (!empty($romFilter)) {
  $stmt->bindParam(':rom', $romFilter);
}
if (!empty($colorFilter)) {
  $stmt->bindParam(':color', $colorFilter);
}

$stmt->execute();
$allProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hàm hỗ trợ tạo URL giữ lại các tham số cũ (Helper function)
function makeUrl($key, $value) {
    // Lấy tất cả tham số hiện tại
    $params = $_GET; 
    // Gán/Ghi đè tham số mới
    if ($value === '') {
        unset($params[$key]); // Nếu value rỗng thì bỏ tham số đó đi (chọn All)
    } else {
        $params[$key] = $value;
    }
    // Tạo query string
    return '?' . http_build_query($params);
}
?>

<!doctype html>
<html lang="vi">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Sản phẩm & Tìm kiếm — ĐIỆN THOẠI TRỰC TUYẾN</title>
  <link rel="stylesheet" href="assets/css/stylesTC.css">
  <link rel="stylesheet" href="assets/css/stylesSanPham.css">
  <style>
    /* CSS inline nhỏ cho menu lọc */
    .filter-menu li a {
      text-decoration: none;
      color: inherit;
      display: block;
      padding: 5px 10px;
    }
    .filter-menu li a:hover {
      background-color: #f5f5f5;
    }
    .filter-menu a.active {
      background: #f0f0f0;
      font-weight: bold;
      color: #d00;
      border-left: 3px solid #d00;
    }
  </style>
</head>

<body>
  <?php require_once './includes/header.php'; ?>

  <main class="container search-page">
    
    <div class="filter-bar">
      
      <div class="filter-item">
        <button class="filter-btn">Bộ nhớ (ROM) <span class="arrow">▾</span></button>
        <ul class="filter-menu">
          <li><a href="<?= makeUrl('rom', '') ?>">Tất cả</a></li>
          <li><a class="<?= ($romFilter == '64GB') ? 'active' : '' ?>" href="<?= makeUrl('rom', '64GB') ?>">64GB</a></li>
          <li><a class="<?= ($romFilter == '128GB') ? 'active' : '' ?>" href="<?= makeUrl('rom', '128GB') ?>">128GB</a></li>
          <li><a class="<?= ($romFilter == '256GB') ? 'active' : '' ?>" href="<?= makeUrl('rom', '256GB') ?>">256GB</a></li>
          <li><a class="<?= ($romFilter == '512GB') ? 'active' : '' ?>" href="<?= makeUrl('rom', '512GB') ?>">512GB</a></li>
          <li><a class="<?= ($romFilter == '1TB') ? 'active' : '' ?>" href="<?= makeUrl('rom', '1TB') ?>">1TB</a></li>
        </ul>
      </div>

      <div class="filter-item">
        <button class="filter-btn">Hệ điều hành <span class="arrow">▾</span></button>
        <ul class="filter-menu">
          <li><a href="<?= makeUrl('os', '') ?>">Tất cả</a></li>
          <li><a class="<?= ($osFilter == 'iOS') ? 'active' : '' ?>" href="<?= makeUrl('os', 'iOS') ?>">iOS</a></li>
          <li><a class="<?= ($osFilter == 'Android') ? 'active' : '' ?>" href="<?= makeUrl('os', 'Android') ?>">Android</a></li>
        </ul>
      </div>

      <div class="filter-item">
        <button class="filter-btn">Giá <span class="arrow">▾</span></button>
        <ul class="filter-menu">
          <li><a href="<?= makeUrl('price', '') ?>">Mặc định</a></li>
          <li><a class="<?= ($priceFilter == 'low_high') ? 'active' : '' ?>" href="<?= makeUrl('price', 'low_high') ?>">Giá thấp → cao</a></li>
          <li><a class="<?= ($priceFilter == 'high_low') ? 'active' : '' ?>" href="<?= makeUrl('price', 'high_low') ?>">Giá cao → thấp</a></li>
        </ul>
      </div>

      <div class="filter-item">
        <button class="filter-btn">Màu sắc <span class="arrow">▾</span></button>
        <ul class="filter-menu">
          <li><a href="<?= makeUrl('color', '') ?>">Tất cả</a></li>
          <li><a class="<?= ($colorFilter == 'Titan') ? 'active' : '' ?>" href="<?= makeUrl('color', 'Titan') ?>">Titan</a></li>
          <li><a class="<?= ($colorFilter == 'Vàng') ? 'active' : '' ?>" href="<?= makeUrl('color', 'Vàng') ?>">Vàng</a></li>
          <li><a class="<?= ($colorFilter == 'Trắng') ? 'active' : '' ?>" href="<?= makeUrl('color', 'Trắng') ?>">Trắng</a></li>
          <li><a class="<?= ($colorFilter == 'Đen') ? 'active' : '' ?>" href="<?= makeUrl('color', 'Đen') ?>">Đen</a></li>
          <li><a class="<?= ($colorFilter == 'Xanh') ? 'active' : '' ?>" href="<?= makeUrl('color', 'Xanh') ?>">Xanh</a></li>
        </ul>
      </div>
    </div>
    <section class="section-grid">
      <div class="section-header">
        <h2>
            <?php 
                if(empty($allProducts)) echo "Không tìm thấy sản phẩm nào";
                else echo "Danh sách sản phẩm";
            ?>
        </h2>
      </div>
      
      <div class="products-grid">
        <?php foreach ($allProducts as $p): ?>
          <div class="product-card">
            <div class="label">Trả góp 0%</div>
            
            <a href="ChiTietSanPham.php?id=<?= $p['id_san_pham'] ?>" aria-label="Xem chi tiết <?= htmlspecialchars($p['ten_san_pham']) ?>">
              <img src="<?= !empty($p['hinh_anh']) ? $p['hinh_anh'] : 'assets/images/no-image.png' ?>" 
                   alt="<?= htmlspecialchars($p['ten_san_pham']) ?>" class="prod-img">
              
              <div class="prod-name"><?= htmlspecialchars($p['ten_san_pham']) ?></div>
            </a>

            <div class="prod-prices">
                <?php if ($p['gia'] === null): ?>
                    <div class="sale">Liên hệ</div>
                <?php else: ?>
                    <div class="sale"><?= number_format($p['gia'], 0, ',', '.') ?>đ</div>
                    <div class="orig"><?= number_format($p['gia'] * 1.1, 0, ',', '.') ?>đ</div>
                <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <?php if(count($allProducts) >= 10): ?>
      <div class="see-more">
        <button class="btn see">XEM THÊM →</button>
      </div>
      <?php endif; ?>

    </section>
  </main>

  <?php require_once './includes/footer.php'; ?>

  <script>
    // Xử lý bật tắt menu bộ lọc
    document.querySelectorAll('.filter-item').forEach(fi => {
      const btn = fi.querySelector('.filter-btn');
      const menu = fi.querySelector('.filter-menu');
      if (!menu) return;
      
      btn.addEventListener('click', (e) => {
        e.stopPropagation(); // Chặn sự kiện nổi bọt
        const isOpen = menu.style.display === 'block';
        
        // Đóng tất cả menu khác trước
        document.querySelectorAll('.filter-menu').forEach(m => m.style.display = 'none');
        
        // Toggle menu hiện tại
        menu.style.display = isOpen ? 'none' : 'block';
      });
    });

    // Click ra ngoài thì đóng menu
    document.addEventListener('click', (e) => {
      if (!e.target.closest('.filter-item')) {
        document.querySelectorAll('.filter-menu').forEach(m => m.style.display = 'none');
      }
    });
  </script>

</body>
</html>