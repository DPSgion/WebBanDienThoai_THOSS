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