<?php
session_start();
require_once "config/config.php";  // file kết nối CSDL
// kiểm tra id
if (!isset($_GET['id']) || empty($_GET['id'])) {
  die("Không tìm thấy sản phẩm.");
}

$id = $_GET['id'];

//Lấy thông tin sản phẩm
$sql = "SELECT * FROM san_pham WHERE id_san_pham = :id LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
  die("Sản phẩm không tồn tại.");
}

//Lấy danh sách biến thể (ROM, màu, giá)
$sqlVar = "SELECT * FROM bien_the WHERE id_san_pham = :id ORDER BY gia ASC";
$stmtVar = $pdo->prepare($sqlVar);
$stmtVar->execute(['id' => $id]);
$variants = $stmtVar->fetchAll(PDO::FETCH_ASSOC);
if (empty($variants)) {
  $hasVariant = false;
} else {
  $hasVariant = true;
}

//Lấy danh sách ảnh
$sqlImg = "SELECT * FROM anh_san_pham WHERE id_san_pham = :id";
$stmtImg = $pdo->prepare($sqlImg);
$stmtImg->execute(['id' => $id]);
$images = $stmtImg->fetchAll(PDO::FETCH_ASSOC);

// Chọn ảnh đại diện
$mainImage = $images[0]['duong_dan_anh'] ?? 'uploads/no-image.png';

// Tạo list màu và ROM
$romList = [];
$colorList = [];
// Chuẩn bị dữ liệu biến thể đầy đủ để lọc hợp lệ bên JS
$variantMap = [];
foreach ($variants as $v) {
  $variantMap[] = [
    "rom" => $v["rom"],
    "mau" => $v["mau"]
  ];
}

if ($hasVariant) {
  foreach ($variants as $v) {
    if (!in_array($v['rom'], $romList)) $romList[] = $v['rom'];
    if (!in_array($v['mau'], $colorList)) $colorList[] = $v['mau'];
  }
}


//Ham Lay Danh Muc
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
$categories = get_all_categories($pdo);

//Lấy id_biến thể được chọn để sang GioHang
$sqlVariant = "SELECT id_bien_the, gia 
               FROM bien_the 
               WHERE id_san_pham = :id 
                 AND rom = :rom 
                 AND mau = :color
               LIMIT 1";

$stmtV = $pdo->prepare($sqlVariant);
$defaultVariant = null;
$default_id_bien_the = null;

if ($hasVariant) {
  $stmtV->execute([
    'id' => $product['id_san_pham'],
    'rom' => $romList[0],
    'color' => $colorList[0]
  ]);
  $defaultVariant = $stmtV->fetch(PDO::FETCH_ASSOC);
  $default_id_bien_the = $defaultVariant['id_bien_the'] ?? null;
}
?>
<!doctype html>
<html lang="vi">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Chi tiết sản phẩm — ĐIỆN THOẠI TRỰC TUYẾN</title>
  <link rel="stylesheet" href="assets/css/stylesTC.css">
  <link rel="stylesheet" href="assets/css/stylesCT.css">
  <style>
    .variant.invalid {
      opacity: 0.4;
    }

    .variant.valid {
      opacity: 1;
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
        <input class="search-input" placeholder="Tìm kiếm sản phẩm" />
        <button class="search-btn" aria-label="Tìm kiếm">🔍</button>
      </div>

      <div class="icons-right">
        <!--SỬA-->
        <a href="TrangChu.php" class="icon-btn cart" aria-label="Trang chủ">🏠 </a>
        <a href="GioHang.php" class="icon-btn cart" aria-label="Giỏ hàng">🛒</a>
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

  <!-- MAIN CONTENT -->
  <main class="container product-page">
    <div class="product-grid">

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
          <?php if ($hasVariant): ?>
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
          <?php else: ?>
            <h3 class="coming-soon">Sản phẩm Sắp ra mắt</h3>
          <?php endif; ?>
          <?php
          if (!$hasVariant) {
            $displayPrice = 'Sắp ra mắt';
          } else {
            $displayPrice = 'Liên hệ';
          }
          ?>
          <div class="price-block">
            <div class="original-price">Giá gốc: <span class="strike">41.999.000đ</span></div>
            <div class="sale-price">
              Giá: <span class="price-red">
                <?php if (is_string($displayPrice)) echo $displayPrice;
                else echo number_format((float)$displayPrice, 0, ',', '.') ?>
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
          <!-- Lỗi hiện ở đây -->
          <?php if (!empty($_SESSION['cart_error'])): ?>
            <div style="color:red; font-weight:bold; margin:10px 0;">
              <?= $_SESSION['cart_error'] ?>
            </div>
            <?php unset($_SESSION['cart_error']); ?>
          <?php endif; ?>
          <div class="purchase-actions">
            <?php if ($hasVariant): ?>
              <form action="./includes/functionsKhachHang/add_to_cart.php" method="POST">
                <input type="hidden" name="id_bien_the" id="idBienTheInput" value="<?= $default_id_bien_the ?>">
                <!-- sẽ cập nhật bằng JS -->
                <input type="hidden" name="rom" id="romInput" value="<?= $romList[0] ?>">
                <input type="hidden" name="color" id="colorInput" value="<?= $colorList[0] ?>">
                <input type="hidden" name="qty" id="qtyHidden" value="1">
                <input type="hidden" name="id_san_pham" value="<?= $product['id_san_pham'] ?>">
                <button id="addCart" class="btn outline">Thêm vào giỏ hàng</button>

              </form>
              <form id="buyNowForm" action="ThanhToan.php" method="POST">
                <input type="hidden" name="id_bien_the" id="buyNow_idBienThe">
                <input type="hidden" name="rom" id="buyNow_rom">
                <input type="hidden" name="color" id="buyNow_color">
                <input type="hidden" name="qty" id="buyNow_qty">

                <button type="submit" id="buyNowBtn" class="btn primary">Mua ngay</button>
              </form>
            <?php else: ?>
              <p style="color:red; font-weight:bold;">Sản phẩm đang về hàng – Sắp ra mắt</p>
            <?php endif; ?>
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

    //XỬ lí giá của Backend gửi lên để người dùng chọn
    const productId = <?= $id ?>;
    const variantMap = <?= json_encode($variantMap) ?>;
    //hàm tìm rom hợp lệ theo màu
    function getValidRoms(color) {
      return variantMap
        .filter(v => v.mau === color)
        .map(v => v.rom);
    }
    //hàm tìm màu hợp lệ theo rom
    function getValidColors(rom) {
      return variantMap
        .filter(v => v.rom === rom)
        .map(v => v.mau);
    }
    //khi chọn rom
    document.querySelectorAll('#storageOptions .variant').forEach(btn => {
      btn.addEventListener('click', () => {
        const rom = btn.dataset.value;
        const validColors = getValidColors(rom);

        document.querySelectorAll('#colorOptions .color').forEach(colorBtn => {
          const c = colorBtn.dataset.color;

          if (validColors.includes(c)) {
            colorBtn.classList.add('valid');
            colorBtn.classList.remove('invalid');
          } else {
            colorBtn.classList.add('invalid');
            colorBtn.classList.remove('valid');
          }
        });

        updatePrice();
      });
    });
    //khi chọn màu
    document.querySelectorAll('#colorOptions .color').forEach(btn => {
      btn.addEventListener('click', () => {
        const color = btn.dataset.color;
        const validRoms = getValidRoms(color);

        // Nếu ROM hiện tại KHÔNG hợp lệ => tự động chọn ROM hợp lệ đầu tiên
        let activeRom = document.querySelector('#storageOptions .active');
        if (!validRoms.includes(activeRom.dataset.value)) {
          const romToSelect = validRoms[0];
          document.querySelector(`#storageOptions .variant[data-value="${romToSelect}"]`).click();
        }

        // Tô màu hợp lệ / không hợp lệ cho ROM
        document.querySelectorAll('#storageOptions .variant').forEach(romBtn => {
          const r = romBtn.dataset.value;
          if (validRoms.includes(r)) {
            romBtn.classList.add('valid');
            romBtn.classList.remove('invalid');
          } else {
            romBtn.classList.add('invalid');
            romBtn.classList.remove('valid');
          }
        });

        updatePrice();
      });
    });


    function updatePrice() {
      const activeRom = document.querySelector('#storageOptions .active');
      const activeColor = document.querySelector('#colorOptions .active');
      if (!activeRom || !activeColor) return;
      const rom = activeRom.dataset.value;
      const color = activeColor.dataset.color;

      fetch(`/project/includes/functionsKhachHang/getPrice.php?id=${productId}&rom=${rom}&color=${color}`)
        .then(res => res.text())
        .then(price => {
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

    //Khi ấn THêm giỏ hàng
    // ROM
    document.querySelectorAll('#storageOptions .variant').forEach(btn => {
      btn.addEventListener('click', () => {
        document.getElementById('romInput').value = btn.dataset.value;
      });
    });

    // COLOR
    document.querySelectorAll('#colorOptions .color').forEach(btn => {
      btn.addEventListener('click', () => {
        document.getElementById('colorInput').value = btn.dataset.color;
      });
    });

    // QTY
    document.getElementById('qtyPlus').onclick = e => {
      e.preventDefault();
      let q = Number(qtyInput.value) + 1;
      qtyInput.value = q;
      qtyHidden.value = q;
    };
    document.getElementById('qtyMinus').onclick = e => {
      e.preventDefault();
      let q = Math.max(1, Number(qtyInput.value) - 1);
      qtyInput.value = q;
      qtyHidden.value = q;
    };
    document.getElementById("addCart").addEventListener("click", function(e) {
      const price = document.querySelector('.price-red').innerText.trim();

      if (price === "Liên hệ") {
        e.preventDefault(); // Chặn gửi form
        alert("Sản phẩm này không có giá. Vui lòng liên hệ cửa hàng.");
        return false;
      }

      // cập nhật qty hidden
      document.getElementById('qtyHidden').value = document.getElementById('qtyInput').value;
    });


    // ===== XỬ LÍ MUA NGAY =====
    document.getElementById("buyNowBtn").addEventListener("click", function(e) {
      e.preventDefault(); // chặn submit mặc định

      const price = document.querySelector('.price-red').innerText.trim();
      if (price === "Liên hệ") {
        alert("Sản phẩm này không có giá. Vui lòng liên hệ cửa hàng.");
        return;
      }

      const activeRom = document.querySelector('#storageOptions .active');
      const activeColor = document.querySelector('#colorOptions .active');
      const qty = document.getElementById('qtyInput').value;

      if (!activeRom || !activeColor) {
        alert("Vui lòng chọn ROM và màu sắc!");
        return;
      }

      const rom = activeRom.dataset.value;
      const color = activeColor.dataset.color;
      fetch(`/project/includes/functionsKhachHang/getIdBienThe.php?id=${productId}&rom=${rom}&color=${color}`)
        .then(res => res.text())
        .then(raw => {
          const id_bt = raw.trim();
          console.log("getIdBienThe trả về (raw):", raw);
          console.log("getIdBienThe trả về (trim):", id_bt);

          if (!id_bt || id_bt === "0") {
            alert("Biến thể không tồn tại! (getIdBienThe trả về 0)");
            return;
          }

          // Gán vào input hidden
          document.getElementById("buyNow_idBienThe").value = id_bt;
          document.getElementById("buyNow_rom").value = rom;
          document.getElementById("buyNow_color").value = color;
          document.getElementById("buyNow_qty").value = qty;

          // Submit form
          document.getElementById("buyNowForm").submit();
        })
        .catch(err => {
          console.error("Lỗi fetch getIdBienThe:", err);
          alert("Không lấy được biến thể. Vui lòng thử lại.");
        });
    });
  </script>

</body>

</html>