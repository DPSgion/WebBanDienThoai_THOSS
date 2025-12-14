<?php
session_start();
include_once 'config/config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['id_nguoi_dung'])) {
  header("Location: Login.php");
  exit();
}

$id_nguoi_dung = $_SESSION['id_nguoi_dung'];

/* Lấy danh mục */
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

$is_buy_now = false;

if (!empty($_POST['id_bien_the'])) {
  $is_buy_now = true;

  $id_bien_the = (int)$_POST['id_bien_the'];
  $qty = max(1, (int)($_POST['qty'] ?? 1));

  $sql = "SELECT bt.id_san_pham, bt.gia, bt.rom, bt.mau, bt.so_luong_ton,
               sp.ten_san_pham, asp.duong_dan_anh
        FROM bien_the bt
        JOIN san_pham sp ON sp.id_san_pham = bt.id_san_pham
        JOIN anh_san_pham asp ON asp.id_san_pham = sp.id_san_pham
        WHERE bt.id_bien_the = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$id_bien_the]);
  $buy_now_item = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$buy_now_item) {
    $_SESSION['cart_error'] = "Sản phẩm không tồn tại";
    header("Location: ChiTietSanPham.php?id=$id_san_pham");
    exit();
  }

  if ($qty > $buy_now_item['so_luong_ton']) {
    $_SESSION['cart_error'] =
      "Số lượng vượt tồn kho. Hiện còn {$buy_now_item['so_luong_ton']} sản phẩm.";
    header("Location: ChiTietSanPham.php?id=" . $buy_now_item['id_san_pham']);
    exit();
  }

  $buy_now_item['so_luong'] = $qty;
  $subtotal = $buy_now_item['gia'] * $qty;
} else {
  // TRƯỜNG HỢP TỪ GIỎ HÀNG
  if (!empty($_POST['selected_items'])) {
    $ids = json_decode($_POST['selected_items'], true);
    $selected_items = $ids;
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $sql = "SELECT ghct.id_chi_tiet,ghct.id_bien_the, ghct.so_luong,
                   bt.gia, bt.rom, bt.mau,
                   sp.ten_san_pham, asp.duong_dan_anh
            FROM gio_hang_chi_tiet ghct
            JOIN gio_hang gh ON gh.id_gio_hang = ghct.id_gio_hang
            JOIN bien_the bt ON bt.id_bien_the = ghct.id_bien_the
            JOIN san_pham sp ON sp.id_san_pham = bt.id_san_pham
            JOIN anh_san_pham asp ON asp.id_san_pham = sp.id_san_pham
            WHERE ghct.id_chi_tiet IN ($placeholders)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($ids);

    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  if (!$cart_items) {
    die("Giỏ hàng rỗng!");
  }

  $subtotal = 0;
  foreach ($cart_items as $item) {
    $subtotal += $item['gia'] * $item['so_luong'];
  }
}
?>
<!doctype html>
<html lang="vi">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Thanh toán — ĐIỆN THOẠI TRỰC TUYẾN</title>
  <link rel="stylesheet" href="assets/css/stylesTC.css">
  <link rel="stylesheet" href="assets/css/stylesCheckout.css">
</head>

<body>
  <header class="main-header">
    <div class="container header-row">
      <div class="logo-left">
        <div class="logo">ĐIỆN THOẠI TRỰC TUYẾN</div>
      </div>

      <div class="search-center">
        <form action="TimKiem.php" method="get" style="width: 500px;">
          <input class="search" placeholder="Tìm kiếm" name="q" aria-label="Tìm kiếm" />
          <button class="search-btn" aria-label="Tìm kiếm" type="submit">🔍</button>
        </form>
      </div>

      <div class="icons-right">
        <a href="TrangChu.php" class="icon-btn cart" aria-label="Trang chủ">🏠 </a>
        <a href="GioHang.php" class="icon-btn cart" aria-label="Giỏ hàng">🛒 </a>
        <a id="accountLink" href="User.php">👤</a>
        <a href="logout.php" class="icon-btn cart">🚪</a>
        <div class="danh-container">
          <button class="danh-muc" aria-haspopup="true" aria-expanded="false">☰ Danh mục</button>
          <ul class="danh-menu" role="menu">
            <?php foreach ($categories as $cat): ?>
              <li><a href="TimKiem.php?cat_id=<?php echo htmlspecialchars($cat['id_danh_muc']); ?>" class="danh-link"><?php echo htmlspecialchars($cat['ten_danh_muc']); ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
  </header>

  <main class="container checkout-page">
    <h1>Thanh toán</h1>
    <div class="checkout-grid">
      <section class="checkout-left">
        <form action="./includes/functionsKhachHang/xu_ly_dat_hang.php" method="POST">

          <h2>Thông tin giao hàng</h2>

          <div class="form-row">
            <label>Địa chỉ nhận hàng
              <input name="address" type="text" required>
            </label>
          </div>

          <div class="form-row">
            <label>Tỉnh / Thành
              <input name="city" type="text" required>
            </label>

            <label>Quận / Huyện
              <input name="district" type="text" required>
            </label>
          </div>
          <input type="hidden" name="id_bien_the" value="<?= $_POST['id_bien_the'] ?? '' ?>">
          <input type="hidden" name="qty" value="<?= $buy_now_item['so_luong'] ?? '' ?>">
          <?php if (!empty($selected_items) && is_array($selected_items)): ?>
            <?php foreach ($selected_items as $chi_tiet_id): ?>
              <input type="hidden" name="selected_items[]" value="<?= htmlspecialchars($chi_tiet_id) ?>">
            <?php endforeach; ?>
          <?php endif; ?>
          <div class="actions">
            <button type="submit" class="btn primary">Đặt hàng và thanh toán</button>
            <a class="btn outline" href="GioHang.php">Quay lại giỏ hàng</a>
          </div>
        </form>
      </section>

      <aside class="checkout-right">
        <h2>Đơn hàng của bạn</h2>
        <div class="order-list">
          <?php if ($is_buy_now): ?>
            <div class="order-item">
              <div class="order-card">
                <img src="uploads/products/<?= $buy_now_item['duong_dan_anh'] ?>" class="prod-thumb">
                <div class="order-mid">
                  <div class="prod-name"><?= $buy_now_item['ten_san_pham'] ?></div>
                  <div class="prod-meta"><?= $buy_now_item['rom'] ?>GB • <?= $buy_now_item['mau'] ?></div>
                  <div>Số lượng: <?= $buy_now_item['so_luong'] ?></div>
                </div>
                <div class="order-price">
                  <span class="price-red"><?= number_format($buy_now_item['gia']) ?>đ</span>
                </div>
              </div>
            </div>
          <?php else: ?>
            <?php foreach ($cart_items as $item): ?>
              <div class="order-item">
                <div class="order-card" data-price="38999000">
                  <img src="uploads/products/<?= $item['duong_dan_anh'] ?>" class="prod-thumb">
                  <div class="order-mid">
                    <div class="prod-name"><?= htmlspecialchars($item['ten_san_pham']) ?></div>
                    <div class="prod-meta"><?= $item['rom'] ?>GB • <?= $item['mau'] ?></div>
                    <div>Số lượng: <?= $item['so_luong'] ?></div>
                  </div>
                  <div class="order-price">
                    <span class="price-red"><?= number_format($item['gia']) ?>đ</span>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>


          <div class="summary">
            <div class="row"><span>Tạm tính</span><span class="value"><?= number_format($subtotal) ?>đ</span></div>
            <div class="row"><span>Phí vận chuyển</span><span class="value">0đ</span></div>
            <div class="row total"><span>Tổng cộng</span><span class="value"><?= number_format($subtotal) ?>đ</span></div>
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
    // payment method toggle
    document.querySelectorAll('input[name="pay"]').forEach(r => r.addEventListener('change', (e) => {
      const card = document.getElementById('cardDetails');
      if (e.target.value === 'card') card.style.display = 'block';
      else card.style.display = 'none';
    }));

    // simple submit handler
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const fd = new FormData(this);
      if (!fd.get('name') || !fd.get('phone') || !fd.get('address')) {
        alert('Vui lòng điền đầy đủ thông tin giao hàng.');
        return;
      }
      alert('Cảm ơn! Đơn hàng của bạn đã được nhận (demo).');
      window.location.href = 'TrangChu.html';
    });

    // qty controls for checkout order list + totals
    function parseNumber(str) {
      return Number(String(str).replace(/[^0-9]/g, '')) || 0;
    }

    function formatVND(n) {
      return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') + 'đ';
    }

    function recalcTotals() {
      let subtotal = 0;
      document.querySelectorAll('.order-card').forEach(card => {
        const price = Number(card.dataset.price) || 0;
        const qty = Number(card.querySelector('.qty-input').value) || 1;
        subtotal += price * qty;
      });
      const subtEl = document.querySelector('.summary .row .value');
      // set first .value to subtotal and last to total
      const values = document.querySelectorAll('.summary .value');
      if (values.length >= 1) values[0].textContent = formatVND(subtotal);
      if (values.length >= 3) values[2].textContent = formatVND(subtotal);
    }

    document.querySelectorAll('.qty-minus').forEach(btn => btn.addEventListener('click', (e) => {
      const input = btn.parentElement.querySelector('.qty-input');
      input.value = Math.max(1, Number(input.value) - 1);
      recalcTotals();
    }));
    document.querySelectorAll('.qty-plus').forEach(btn => btn.addEventListener('click', (e) => {
      const input = btn.parentElement.querySelector('.qty-input');
      input.value = Math.max(1, Number(input.value) + 1);
      recalcTotals();
    }));
    document.querySelectorAll('.qty-input').forEach(inp => inp.addEventListener('change', () => {
      if (Number(inp.value) < 1) inp.value = 1;
      recalcTotals();
    }));

    // initial totals
    recalcTotals();

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