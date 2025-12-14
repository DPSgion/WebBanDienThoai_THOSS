<?php
session_start();
require_once __DIR__ . '/config/config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
  die("Không tìm thấy sản phẩm.");
}

// 2. LOGIC RIÊNG CỦA TRANG CHI TIẾT (Lấy sản phẩm, biến thể...)
// Kiểm tra ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Không tìm thấy sản phẩm.");
}
$id = $_GET['id'];

// Lấy thông tin sản phẩm
$sql = "SELECT * FROM san_pham WHERE id_san_pham = :id LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Sản phẩm không tồn tại.");
}

// Lấy danh sách biến thể
$sqlVar = "SELECT * FROM bien_the WHERE id_san_pham = :id ORDER BY gia ASC";
$stmtVar = $pdo->prepare($sqlVar);
$stmtVar->execute(['id' => $id]);
$variants = $stmtVar->fetchAll(PDO::FETCH_ASSOC);
$hasVariant = !empty($variants);

// Lấy ảnh
$sqlImg = "SELECT * FROM anh_san_pham WHERE id_san_pham = :id";
$stmtImg = $pdo->prepare($sqlImg);
$stmtImg->execute(['id' => $id]);
$images = $stmtImg->fetchAll(PDO::FETCH_ASSOC);
$mainImage = $images[0]['duong_dan_anh'] ?? 'uploads/no-image.png';

// Xử lý dữ liệu biến thể cho JS
$romList = [];
$colorList = [];
$variantMap = []; // Map để JS tra cứu nhanh

foreach ($variants as $v) {
    $variantMap[] = [
        "id" => $v['id_bien_the'],
        "rom" => $v["rom"],
        "mau" => $v["mau"],
        "gia" => $v["gia"],
        "kho" => $v["so_luong_ton"]
    ];
    if (!in_array($v['rom'], $romList)) $romList[] = $v['rom'];
    if (!in_array($v['mau'], $colorList)) $colorList[] = $v['mau'];
}

// Set biến thể mặc định (để hiển thị lúc đầu)
$default_id_bien_the = null;
if ($hasVariant) {
    // Logic đơn giản: lấy biến thể đầu tiên trong danh sách variants
    $default_id_bien_the = $variants[0]['id_bien_the'];
}
?>

<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= htmlspecialchars($product['ten_san_pham']) ?> — ĐIỆN THOẠI TRỰC TUYẾN</title>
  
  <link rel="stylesheet" href="assets/css/stylesTC.css">
  <link rel="stylesheet" href="assets/css/stylesCT.css">
  <style>
    .variant.invalid { opacity: 0.4; cursor: not-allowed; }
    .variant.valid { opacity: 1; cursor: pointer; }
    /* Fix lỗi dropdown bị banner che khuất (Lý do phổ biến khiến menu không hiện) */
    .danh-container { position: relative; z-index: 1000; }
    .danh-menu { z-index: 1001; }
  </style>
</head>

<body>

  <?php require_once './includes/header.php'; ?>

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
            <img class="thumb small" data-src="<?= $i['duong_dan_anh'] ?>" src="<?= $i['duong_dan_anh'] ?>" alt="">
          <?php endforeach; ?>
        </div>

        <div class="specs">
          <h3>Thông số kỹ thuật</h3>
          <div class="spec-table">
            <div class="spec-row"><div class="spec-name">Màn hình</div><div class="spec-value"><?= $product['man_hinh'] ?></div></div>
            <div class="spec-row"><div class="spec-name">Camera sau</div><div class="spec-value"><?= $product['camera_sau'] ?></div></div>
            <div class="spec-row"><div class="spec-name">Camera trước</div><div class="spec-value"><?= $product['camera_truoc'] ?></div></div>
            <div class="spec-row"><div class="spec-name">Pin</div><div class="spec-value"><?= $product['pin'] ?></div></div>
          </div>
        </div>
      </section>

      <aside class="right-col">
        <h1 class="product-title"><?= $product['ten_san_pham'] ?></h1>
        
        <div class="variants">
          <?php if ($hasVariant): ?>
            <div class="variant-group">
              <label class="variant-label">Bộ nhớ:</label>
              <div class="variant-options" id="storageOptions">
                <?php foreach ($romList as $i => $rom): ?>
                  <button class="variant opt <?= $i == 0 ? 'active' : '' ?>" data-value="<?= $rom ?>"><?= $rom ?></button>
                <?php endforeach; ?>
              </div>

              <div class="variant-group" style="margin-top:10px;">
                <label class="variant-label">Màu sắc:</label>
                <div class="variant-options" id="colorOptions">
                  <?php foreach ($colorList as $i => $color): ?>
                    <button class="variant color <?= $i == 0 ? 'active' : '' ?>" data-color="<?= $color ?>"><?= ucfirst($color) ?></button>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          <?php else: ?>
            <h3 class="coming-soon">Sản phẩm Sắp ra mắt</h3>
          <?php endif; ?>

          <div class="price-block">
            <div class="sale-price">Giá: <span class="price-red">Liên hệ</span></div>
          </div>

          <div class="quantity-block">
            <label>Số lượng</label>
            <div class="qty-controls">
                <button id="qtyMinus" class="qty-btn">−</button>
                <input id="qtyInput" type="number" value="1" min="1">
                <button id="qtyPlus" class="qty-btn">+</button>
            </div>
            <div id="stockMessage" style="font-size: 0.9rem; color: #666; margin-top: 5px;"></div>
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
                
                <input type="hidden" name="qty" id="qtyHidden" value="1">
                
                <input type="hidden" name="rom" id="romInput" value="<?= $romList[0] ?>">
                <input type="hidden" name="color" id="colorInput" value="<?= $colorList[0] ?>">
                <input type="hidden" name="id_san_pham" value="<?= $product['id_san_pham'] ?>">
                
                <button id="addCart" class="btn outline">Thêm vào giỏ hàng</button>
              </form>

              <form id="buyNowForm" action="ThanhToan.php" method="POST">
                <input type="hidden" name="id_bien_the" id="buyNow_idBienThe" value="<?= $default_id_bien_the ?>">
                <input type="hidden" name="qty" id="buyNow_qty" value="1">
                <input type="hidden" name="rom" id="buyNow_rom" value="<?= $romList[0] ?>">
                <input type="hidden" name="color" id="buyNow_color" value="<?= $colorList[0] ?>">

                <button type="submit" id="buyNowBtn" class="btn primary">Mua ngay</button>
              </form>
            <?php else: ?>
              <p style="color:red; font-weight:bold;">Tạm hết hàng</p>
            <?php endif; ?>
          </div>
        </div>
      </aside>
    </div>
  </main>

  <?php require_once './includes/footer.php'; ?>
  <script>
    // --- 1. Gallery (Giữ nguyên) ---
    const mainImg = document.getElementById('mainThumb');
    document.querySelectorAll('.thumb').forEach(t => {
        t.addEventListener('click', () => {
            document.querySelectorAll('.thumb').forEach(x => x.classList.remove('active'));
            t.classList.add('active');
            mainImg.src = t.dataset.src;
        });
    });

    // --- 2. Logic Giá & Biến thể & Tồn kho ---
    const variantMap = <?= json_encode($variantMap) ?>;
    const qtyInput = document.getElementById('qtyInput');
    const stockMsg = document.getElementById('stockMessage');
    const btnAddCart = document.getElementById('addCart');
    const btnBuyNow = document.getElementById('buyNowBtn');

    function formatCurrency(amount) {
        return Number(amount).toLocaleString('vi-VN') + 'đ';
    }

    // Hàm cập nhật giới hạn số lượng
    function updateQtyLimit(maxStock) {
        // Cập nhật thuộc tính max cho input
        qtyInput.max = maxStock;
        
        // Nếu số lượng đang chọn lớn hơn tồn kho mới -> giảm xuống bằng tồn kho
        if (parseInt(qtyInput.value) > maxStock) {
            qtyInput.value = maxStock;
        }
        
        // Nếu kho hết -> về 0 hoặc 1 tùy logic (ở đây xử lý disable nút mua bên dưới)
        if (maxStock <= 0) qtyInput.value = 1; 

        // Cập nhật lại các input hidden
        updateHiddenInputs();
    }

    function updateUI() {
        const activeRom = document.querySelector('#storageOptions .active');
        const activeColor = document.querySelector('#colorOptions .active');
        
        if (!activeRom || !activeColor) return;

        const rom = activeRom.dataset.value;
        const color = activeColor.dataset.color;

        // Cập nhật value cho input hidden text (rom/color)
        document.getElementById('romInput').value = rom;
        document.getElementById('colorInput').value = color;
        document.getElementById('buyNow_rom').value = rom;
        document.getElementById('buyNow_color').value = color;

        // Tìm biến thể tương ứng
        const found = variantMap.find(v => v.rom == rom && v.mau == color);
        const priceText = document.querySelector('.price-red');

        if (found) {
            // 1. Cập nhật giá
            priceText.textContent = formatCurrency(found.gia);
            
            // 2. Cập nhật ID biến thể
            document.getElementById('idBienTheInput').value = found.id;
            document.getElementById('buyNow_idBienThe').value = found.id;

            // 3. Xử lý Tồn kho (LOGIC MỚI)
            const stock = parseInt(found.kho);
            updateQtyLimit(stock); // Gọi hàm giới hạn

            if (stock > 0) {
                stockMsg.textContent = `Còn ${stock} sản phẩm`;
                stockMsg.style.color = 'green';
                
                // Mở khóa nút mua
                if(btnAddCart) btnAddCart.disabled = false;
                if(btnBuyNow) btnBuyNow.disabled = false;
                qtyInput.disabled = false;
            } else {
                stockMsg.textContent = `Tạm hết hàng`;
                stockMsg.style.color = 'red';
                
                // Khóa nút mua
                if(btnAddCart) btnAddCart.disabled = true;
                if(btnBuyNow) btnBuyNow.disabled = true;
                qtyInput.disabled = true;
            }

        } else {
            // Trường hợp không tìm thấy biến thể (Lỗi data hoặc chưa set)
            priceText.textContent = "Liên hệ";
            stockMsg.textContent = "";
            if(btnAddCart) btnAddCart.disabled = true;
            if(btnBuyNow) btnBuyNow.disabled = true;
        }
    }

    // --- 3. Logic Input Số lượng ---
    
    // Hàm cập nhật giá trị vào form hidden
    function updateHiddenInputs() {
        let val = parseInt(qtyInput.value);
        if (isNaN(val) || val < 1) val = 1;
        
        document.getElementById('qtyHidden').value = val;
        document.getElementById('buyNow_qty').value = val;
    }

    // Hàm kiểm tra khi người dùng thay đổi số lượng
    function checkQtyValidity() {
        let currentVal = parseInt(qtyInput.value);
        let maxVal = parseInt(qtyInput.max); // Lấy max từ thuộc tính đã set ở updateUI

        if (isNaN(currentVal) || currentVal < 1) {
            currentVal = 1;
        }

        // Nếu nhập quá số tồn -> trả về max
        if (currentVal > maxVal) {
            alert(`Xin lỗi, chỉ còn ${maxVal} sản phẩm trong kho!`);
            currentVal = maxVal;
        }

        qtyInput.value = currentVal;
        updateHiddenInputs();
    }

    // Sự kiện nút cộng trừ
    document.getElementById('qtyPlus').onclick = () => { 
        let currentVal = parseInt(qtyInput.value);
        let maxVal = parseInt(qtyInput.max);
        
        if (currentVal < maxVal) {
            qtyInput.value = currentVal + 1;
            updateHiddenInputs();
        } else {
            alert('Đã đạt số lượng tối đa trong kho!');
        }
    };

    document.getElementById('qtyMinus').onclick = () => { 
        if(qtyInput.value > 1) {
            qtyInput.value--; 
            updateHiddenInputs(); 
        }
    };

    // Sự kiện khi gõ trực tiếp
    qtyInput.addEventListener('change', checkQtyValidity);
    // qtyInput.addEventListener('input', checkQtyValidity); // Nếu muốn chặn ngay khi gõ

    // Sự kiện Click chọn Option
    document.querySelectorAll('#storageOptions .variant').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('#storageOptions .variant').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            updateUI();
        });
    });

    document.querySelectorAll('#colorOptions .variant').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('#colorOptions .variant').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            updateUI();
        });
    });

    // Chạy lần đầu
    updateUI();
</script>
</body>
</html>