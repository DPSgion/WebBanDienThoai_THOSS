<?php
require "config/config.php";  // file kết nối CSDL

// kiểm tra id
if (!isset($_GET['id']) || empty($_GET['id'])) {
  die("Không tìm thấy sản phẩm.");
}

$id = $_GET['id'];

// 1️⃣ Lấy thông tin sản phẩm
$sql = "SELECT * FROM san_pham WHERE id_san_pham = :id LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
  die("Sản phẩm không tồn tại.");
}

// 2️⃣ Lấy danh sách biến thể (ROM, màu, giá)
$sqlVar = "SELECT * FROM bien_the WHERE id_san_pham = :id ORDER BY gia ASC";
$stmtVar = $pdo->prepare($sqlVar);
$stmtVar->execute(['id' => $id]);
$variants = $stmtVar->fetchAll(PDO::FETCH_ASSOC);

// 3️⃣ Lấy danh sách ảnh
$sqlImg = "SELECT * FROM anh_san_pham WHERE id_san_pham = :id";
$stmtImg = $pdo->prepare($sqlImg);
$stmtImg->execute(['id' => $id]);
$images = $stmtImg->fetchAll(PDO::FETCH_ASSOC);

// Chọn ảnh đại diện
$mainImage = $images[0]['duong_dan_anh'] ?? 'uploads/no-image.png';

// Tạo list màu và ROM
$romList = [];
$colorList = [];

foreach ($variants as $v) {
  if (!in_array($v['rom'], $romList)) $romList[] = $v['rom'];
  if (!in_array($v['mau'], $colorList)) $colorList[] = $v['mau'];
}

// Giá thấp nhất
$minPrice = min(array_column($variants, 'gia'));
?>
<!doctype html>
<html lang="vi">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Chi tiết sản phẩm — ĐIỆN THOẠI TRỰC TUYẾN</title>
  <link rel="stylesheet" href="assets/css/stylesTC.css">
  <link rel="stylesheet" href="assets/css/stylesCT.css">
</head>

<body>



  <!-- MAIN HEADER / NAV -->
  <header class="main-header">
    <div class="container header-row">
      <div class="logo-left">
        <div class="logo">ĐIỆN THOẠI TRỰC TUYẾN</div>
      </div>

      <div class="search-center">
        <input class="search-input" placeholder="Tìm kiếm sản phẩm" />
        <button class="search-btn" aria-label="Tìm kiếm">🔍</button>
      </div>

      <div class="icons-right">
        <!--SỬA-->
        <a href="TrangChu.php" class="icon-btn cart" aria-label="Trang chủ">🏠 </a>
        <a href="GioHang.php" class="icon-btn cart" aria-label="Giỏ hàng">🛒 </span></a>
        <a id="accountLink" href="User.php">👤</a>
        <div class="danh-container">
          <button type="button" class="danh-muc" aria-haspopup="true" aria-expanded="false">☰ Danh mục</button>
          <ul class="danh-menu" role="menu">
            <li><a href="TimKiem.html" class="danh-link">iPhone</a></li>
            <li><a href="#">Samsung</a></li>
            <!--SỬA-->
            <li><a href="#">Máy tính bảng</a></li>
          </ul>
        </div>
      </div>
    </div>
  </header>

  <!-- MAIN CONTENT -->
  <main class="container product-page">
    <div class="product-grid">
      <!-- LEFT COLUMN -->
      <section class="left-col">
        <div class="product-gallery">
          <button class="slide-btn prev" aria-label="Previous">◀</button>
          <div class="slide-frame" id="slideFrame">
            <img src="<?= $mainImage ?>" class="main-thumb" id="mainThumb">
          </div>
          <button class="slide-btn next" aria-label="Next">▶</button>
        </div>

        <div class="thumbs">
          <?php foreach ($images as $i): ?>
            <img class="thumb small" data-src="<?= $i['duong_dan_anh'] ?>"
              src="<?= $i['duong_dan_anh'] ?>" alt="">
          <?php endforeach; ?>
        </div>

        <div class="specs">
          <h3>Thông số kỹ thuật</h3>
          <div class="spec-table">

            <div class="spec-row">
              <div class="spec-name">Màn hình</div>
              <div class="spec-value"><?= $product['man_hinh'] ?></div>
            </div>

            <div class="spec-row">
              <div class="spec-name">Camera sau</div>
              <div class="spec-value"><?= $product['camera_sau'] ?></div>
            </div>

            <div class="spec-row">
              <div class="spec-name">Camera trước</div>
              <div class="spec-value"><?= $product['camera_truoc'] ?></div>
            </div>

            <div class="spec-row">
              <div class="spec-name">Pin</div>
              <div class="spec-value"><?= $product['pin'] ?></div>
            </div>

          </div>
        </div>
      </section>

      <!-- RIGHT COLUMN -->
      <aside class="right-col">
        <h1 class="product-title"><?= $product['ten_san_pham'] ?></h1>

        <div class="variants">
          <div class="variant-group">
            <label class="variant-label">Bộ nhớ:</label>
            <div class="variant-options" id="storageOptions">
              <?php foreach ($romList as $i => $rom): ?>
                <button class="variant opt <?= $i == 0 ? 'active' : '' ?>" data-value="<?= $rom ?>">
                  <?= $rom ?>
                </button>
              <?php endforeach; ?>
            </div>

            <div class="variant-group">
              <label class="variant-label">Màu sắc:</label>
              <div class="variant-options" id="colorOptions">
                <?php foreach ($colorList as $i => $color): ?>
                  <button class="variant color <?= $i == 0 ? 'active' : '' ?>" data-color="<?= $color ?>">
                    <?= ucfirst($color) ?>
                  </button>
                <?php endforeach; ?>
              </div>
            </div>
          </div>

          <?php
          $displayPrice = $variantPrice ?? ($minPrice ?? 0);
          ?>
          <div class="price-block">
            <div class="original-price">Giá gốc: <span class="strike">41.999.000đ</span></div>
            <div class="sale-price">
              Giá: <span class="price-red">
                <?= number_format((float)$displayPrice, 0, ',', '.') ?>đ
              </span>
            </div>
          </div>
          <div class="quantity-block">
            <label>Số lượng</label>
            <div class="qty-controls">
              <button id="qtyMinus" class="qty-btn">−</button>
              <input id="qtyInput" type="number" value="1" min="1">
              <button id="qtyPlus" class="qty-btn">+</button>
            </div>
          </div>

          <div class="tradein-banner">
            <h4>Ưu đãi thu cũ đổi mới</h4>
            <p>Tặng quà trị giá 200.000đ — Tặng Dock sạc dành cho iPhone</p>
            <button class="cta small">XEM NGAY</button>
          </div>

          <div class="product-info">
            <h4>Thông tin sản phẩm</h4>
            <ul>
              <li>Hàng chính hãng Apple Việt Nam (AAR)</li>
              <li>Đầy đủ hóa đơn VAT</li>
              <li>Bộ sản phẩm: máy, cáp, sách HDSD, tờ tải dịch vụ</li>
              <li>Bảo hành 12 tháng</li>
              <li>Đổi mới trong 33 ngày nếu lỗi</li>
            </ul>
          </div>

          <div class="purchase-actions">
            <button id="addCart" class="btn outline">Thêm vào giỏ hàng</button>
            <button id="buyNow" class="btn primary">Mua ngay</button>
          </div>
      </aside>
    </div>
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
    // simple gallery: change main thumb via data-src attribute
    (function() {
      const main = document.getElementById('mainThumb');
      const thumbs = document.querySelectorAll('.thumb');
      thumbs.forEach(t => t.addEventListener('click', () => {
        thumbs.forEach(x => x.classList.remove('active'));
        t.classList.add('active');
        const src = t.dataset.src || t.getAttribute('src');
        if (src) main.src = src;
      }));

      // prev/next (cycle thumbs)
      const prev = document.querySelector('.slide-btn.prev');
      const next = document.querySelector('.slide-btn.next');
      prev.addEventListener('click', () => {
        let i = Array.from(thumbs).findIndex(t => t.classList.contains('active'));
        i = (i - 1 + thumbs.length) % thumbs.length;
        thumbs[i].click();
      });
      next.addEventListener('click', () => {
        let i = Array.from(thumbs).findIndex(t => t.classList.contains('active'));
        i = (i + 1) % thumbs.length;
        thumbs[i].click();
      });
    })();

    // qty controls
    (function() {
      const minus = document.getElementById('qtyMinus');
      const plus = document.getElementById('qtyPlus');
      const input = document.getElementById('qtyInput');
      minus.addEventListener('click', () => {
        input.value = Math.max(1, Number(input.value) - 1);
      });
      plus.addEventListener('click', () => {
        input.value = Math.max(1, Number(input.value) + 1);
      });
    })();

    // variant selection
    (function() {
      function groupHandler(selector) {
        const container = document.querySelector(selector);
        if (!container) return;
        container.addEventListener('click', (e) => {
          const btn = e.target.closest('button');
          if (!btn) return;
          container.querySelectorAll('button').forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
        });
      }
      groupHandler('#storageOptions');
      groupHandler('#colorOptions');
    })();
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

    //XỬ lí giá của Backend
    document
      .querySelectorAll('#storageOptions button, #colorOptions button')
      .forEach(btn => btn.addEventListener('click', updatePrice));

    function updatePrice() {

      const activeRom = document.querySelector('#storageOptions .active');
      const activeColor = document.querySelector('#colorOptions .active');

      if (!activeRom || !activeColor) return;

      const rom = activeRom.dataset.value;
      const color = activeColor.dataset.color;
      const id = "<?= $id ?>";

      fetch(`getPrice.php?id=${id}&rom=${rom}&color=${color}`)
        .then(res => res.text())
        .then(price => {

          // Nếu giá là liên hệ
          if (price === "LH") {
            document.querySelector('.price-red').innerText = "Liên hệ";
            return;
          }

          // Nếu giá là số thì format
          price = Number(price);
          document.querySelector('.price-red').innerText =
            price.toLocaleString("vi-VN") + "đ";
        });
    }
  </script>

</body>

</html>