<?php

require_once './includes/header.php'; 


$sqlIphone = "
SELECT sp.*, bt.gia, a.duong_dan_anh 
FROM san_pham sp
JOIN bien_the bt ON sp.id_san_pham = bt.id_san_pham
JOIN anh_san_pham a ON sp.id_san_pham = a.id_san_pham
JOIN danh_muc dm ON sp.id_danh_muc = dm.id_danh_muc
WHERE dm.ten_danh_muc = 'iPhone'
GROUP BY sp.id_san_pham
LIMIT 5";

$stmtIphone = $pdo->prepare($sqlIphone);
$stmtIphone->execute();
$iphoneList = $stmtIphone->fetchAll(PDO::FETCH_ASSOC);

$sqlSamsung = "
SELECT sp.*, bt.gia, a.duong_dan_anh 
FROM san_pham sp
JOIN bien_the bt ON sp.id_san_pham = bt.id_san_pham
JOIN anh_san_pham a ON sp.id_san_pham = a.id_san_pham
JOIN danh_muc dm ON sp.id_danh_muc = dm.id_danh_muc
WHERE dm.ten_danh_muc = 'Samsung'
GROUP BY sp.id_san_pham
LIMIT 5";

$stmtSamsung = $pdo->prepare($sqlSamsung);
$stmtSamsung->execute();
$samsungList = $stmtSamsung->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="main-banner">
    <div class="container">
      <div class="carousel" id="mainCarousel" tabindex="0" aria-roledescription="carousel">
        <div class="slides">
          <div class="slide">
            <div class="banner-inner">
              <div class="banner-text">
                <h1>Siêu ưu đãi cho Galaxy S9</h1>
                <p class="price">Giá <strong>15.990.000₫</strong></p>
                <p class="promo">Khuyến mãi giảm giá đến <strong>4.000.000₫</strong></p>
                <p class="cta"><a class="btn" href="#">SỞ HỮU NGAY</a></p>
              </div>
              <div class="banner-image" aria-hidden="true">
                <svg width="240" height="420" viewBox="0 0 240 420" xmlns="http://www.w3.org/2000/svg">
                  <rect rx="28" width="240" height="420" fill="#0f172a" />
                  <rect x="14" y="30" width="212" height="360" rx="18" fill="#eef2ff" />
                  <circle cx="120" cy="80" r="22" fill="#7c3aed" />
                </svg>
              </div>
            </div>
          </div>
          <div class="carousel-dots" aria-hidden="false"></div>
        </div>
      </div>
  </section>

<section class="services container">
    <div class="service">🚚<div>Giao hàng tận nơi</div></div>
    <div class="service">🔁<div>Hỗ trợ đổi trả 30 ngày</div></div>
    <div class="service">🔒<div>100% thanh toán an toàn</div></div>
    <div class="service">✔️<div>Cam kết sản phẩm chính hãng</div></div>
</section>

<section class="categories container">
    <h3 class="categories-title">Danh mục sản phẩm</h3>
    <div class="categories-list">
        <?php foreach ($dsDanhMuc as $cat): ?>
            <div class="cat">
                <a href="TimKiem.php?danhmuc=<?php echo $cat['id_danh_muc']; ?>">
                    📱 <?php echo htmlspecialchars($cat['ten_danh_muc']); ?>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="sub-banner">
    <div class="container">
        <div class="sub-carousel" id="subCarousel">
            <div class="sub-slides">
                <div class="sub-slide">
                    <div class="sub-inner">
                        <div class="sub-text">Nhiều mẫu điện thoại - Giá tốt, lựa chọn đa dạng</div>
                        <div class="sub-graphic">📱📱📱📱</div>
                    </div>
                </div>
            </div>
            <div class="sub-dots"></div>
        </div>
    </div>
</section>

<section class="featured container">
    <div class="section-header">
        <h2>iPhone chính hãng</h2>
        <a class="view-more" href="SanPham.php">Xem Thêm →</a>
    </div>
    <div class="products">
        <?php foreach ($iphoneList as $sp): ?>
            <div class="product">
                <a href="ChiTietSanPham.php?id=<?= $sp['id_san_pham'] ?>">
                    <img src="<?= $sp['duong_dan_anh'] ?>" width="120">
                </a>
                <div class="name"><?= $sp['ten_san_pham'] ?></div>
                <div class="current-price">
                    <?= number_format($sp['gia'], 0, ',', '.') ?>₫
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="featured container">
    <div class="section-header">
        <h2>Samsung chính hãng</h2>
        <a class="view-more" href="SanPham.php">Xem Thêm →</a>
    </div>
    <div class="products">
        <?php foreach ($samsungList as $sp): ?>
            <div class="product">
                <a href="ChiTietSanPham.php?id=<?= $sp['id_san_pham'] ?>">
                    <img src="<?= $sp['duong_dan_anh'] ?>" width="120">
                </a>
                <div class="name"><?= $sp['ten_san_pham'] ?></div>
                <div class="current-price">
                    <?= number_format($sp['gia'], 0, ',', '.') ?>₫
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once './includes/footer.php'; ?>