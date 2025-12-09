<?php
session_start();
include_once 'config/config.php';
// Lấy danh mục
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
$categories = get_all_categories($pdo);

$id_user = $_SESSION['id_nguoi_dung'];

$sql = "SELECT ghct.*, bt.gia, sp.ten_san_pham, asp.duong_dan_anh
        FROM gio_hang_chi_tiet ghct
        JOIN bien_the bt ON ghct.id_bien_the = bt.id_bien_the
        JOIN san_pham sp ON bt.id_san_pham = sp.id_san_pham
        LEFT JOIN anh_san_pham asp ON asp.id_san_pham = sp.id_san_pham
        WHERE ghct.id_gio_hang = (SELECT id_gio_hang FROM gio_hang WHERE id_nguoi_dung = ? LIMIT 1)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_user]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
//
if (isset($_GET['delete'])) {
  $id_delete = $_GET['delete'];

  // Xóa trong database
  $sql = "DELETE FROM gio_hang_chi_tiet WHERE id_chi_tiet = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$id_delete]);

  // Load lại trang
  header("Location: GioHang.php");
  exit();
}
?>

<!doctype html>
<html lang="vi">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Giỏ hàng — ĐIỆN THOẠI TRỰC TUYẾN</title>
  <link rel="stylesheet" href="assets/css/stylesTC.css">
  <link rel="stylesheet" href="assets/css/stylesCart.css">
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
        <a href="TrangChu.php" class="icon-btn cart" aria-label="Trang chủ">🏠 </a>
        <a id="accountLink" href="User.php">👤</a>
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

  <!-- CART MAIN -->
  <main class="container cart-page">
    <div class="cart-wrapper">
      <div class="cart-header">
        <h2>GIỎ HÀNG CỦA BẠN <span class="lock">🔒</span></h2>
      </div>
      <div class="select-all">
        <h4><input type="checkbox" id="selectAll"> TẤT CẢ</h4>
      </div>
      <div class="cart-list" id="cartList">
        <!-- product item template -->
        <?php if (!empty($cart_items)):
          foreach ($cart_items as $item): ?>
            <div class="cart-item" data-price="<?= $item['gia'] ?>">
              <div class="item-left">
                <img src="uploads/products/<?= $item['duong_dan_anh'] ?? '' ?>" alt="" class="item-thumb">
              </div>
              <div class="item-mid">
                <div class="item-name"><?= $item['ten_san_pham'] ?></div>
                <div class="item-price price-red">
                  <?= $item['gia'] === null ? 'Liên hệ' : number_format($item['gia'], 0, ',', '.') . 'đ' ?>
                </div>
                <div class="item-controls">
                  <div class="qty-box">
                    <button class="qty-btn qty-minus">−</button>
                    <input class="qty-input" type="number" value="<?= $item['so_luong'] ?>" min="1">
                    <button class="qty-btn qty-plus">+</button>
                  </div>
                  <button class="btn choose">CHỌN</button>
                </div>
              </div>
              <div class="item-right">
                <button class="del" data-key="<?= $item['id_chi_tiet'] ?>">×</button>
                <label class="select-wrap"><input type="checkbox" class="select-item"> Chọn</label>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>Giỏ hàng hiện đang trống</p>
        <?php endif; ?>
        <div class="cart-footer">
          <div class="summary">
            <button id="totalBtn" class="btn total">TỔNG CỘNG: 0 VND</button>
            <a id="checkout" class="btn checkout" href="ThanhToan.php">THANH TOÁN</a>
          </div>
        </div>
      </div>
  </main>
  <footer class="site-footer">
    <div class="container footer-grid">
      <div class="col">
        <h4>ĐIỆN THOẠI TRỰC TUYẾN</h4>
      </div>
      <div class="col">

        <h4>THÀNH VIÊN 1</h4>
        <p>Họ & Tên: <a href="#">...</a></p>

        <p>MSSV: <a href="#">...</a></p>

        <p>Email: <a href="#">...</a></p>

      </div>
      <div class="col">

        <h4>THÀNH VIÊN 2</h4>
        <p>Họ & Tên: <a href="#">...</a></p>

        <p>MSSV: <a href="#">...</a></p>

        <p>Email: <a href="#">...</a></p>

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
    (function() {
      function formatVND(n) {
        return n.toLocaleString('vi-VN') + ' VND';
      }

      const cartList = document.getElementById('cartList');
      const totalBtn = document.getElementById('totalBtn');
      const selectAll = document.getElementById('selectAll');

      function computeTotal() {
        let sum = 0;
        cartList.querySelectorAll('.cart-item').forEach(item => {
          const chk = item.querySelector('.select-item');
          if (chk && chk.checked) {
            if (price === "LH") {
              document.querySelector('.price-red').innerText = "Liên hệ";
              return;
            }
            const price = Number(item.dataset.price || 0);
            const qty = Number(item.querySelector('.qty-input').value || 1);
            sum += price * qty;
          }
        });
        totalBtn.textContent = 'TỔNG CỘNG: ' + formatVND(sum);
      }

      // quantity handlers
      cartList.addEventListener('click', (e) => {
        if (e.target.matches('.qty-plus')) {
          const input = e.target.parentElement.querySelector('.qty-input');
          input.value = Math.max(1, Number(input.value) + 1);
          computeTotal();
        } else if (e.target.matches('.qty-minus')) {
          const input = e.target.parentElement.querySelector('.qty-input');
          input.value = Math.max(1, Number(input.value) - 1);
          computeTotal();
        } else if (e.target.matches('.del')) {
          const key = e.target.dataset.key;

          if (confirm("Xóa sản phẩm này khỏi giỏ hàng?")) {
            window.location.href = "GioHang.php?delete=" + key;
          }
        }
      });

      // checkbox change handlers
      cartList.addEventListener('change', (e) => {
        if (e.target.matches('.select-item')) {
          computeTotal();
        }
        if (e.target.matches('.qty-input')) {
          e.target.value = Math.max(1, Number(e.target.value));
          computeTotal();
        }
      });

      selectAll.addEventListener('change', () => {
        const checked = selectAll.checked;
        cartList.querySelectorAll('.select-item').forEach(c => c.checked = checked);
        computeTotal();
      });

      // initial total
      computeTotal();
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
  </script>

</body>

</html>