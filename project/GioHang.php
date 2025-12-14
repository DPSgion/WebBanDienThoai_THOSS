<?php
// 1. Start Session & Config
// Kiểm tra session status trước khi start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/config.php';

// 2. LOGIC GIỎ HÀNG (Backend)
$id_user = $_SESSION['id_nguoi_dung'] ?? '';

$sql = "SELECT ghct.*,bt.gia,sp.ten_san_pham, asp.duong_dan_anh
FROM gio_hang_chi_tiet ghct
JOIN bien_the bt ON ghct.id_bien_the = bt.id_bien_the
JOIN san_pham sp ON bt.id_san_pham = sp.id_san_pham
LEFT JOIN anh_san_pham asp 
    ON asp.id_san_pham = sp.id_san_pham
    AND asp.id_anh = (
        SELECT MIN(id_anh)
        FROM anh_san_pham
        WHERE id_san_pham = sp.id_san_pham
    )
WHERE ghct.id_gio_hang = (
    SELECT id_gio_hang 
    FROM gio_hang 
    WHERE id_nguoi_dung = ? 
    LIMIT 1
);";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_user]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
//
// Nếu chưa đăng nhập thì chặn hoặc xử lý tùy ý (ở đây mình để trống thì query sẽ không ra gì)
if (empty($id_user)) {
    // Có thể chuyển hướng về login: header("Location: login.php"); exit;
}

// Xử lý XÓA sản phẩm
if (isset($_GET['delete'])) {
  $id_delete = $_GET['delete'];
  // Xóa trong database
  $sql = "DELETE FROM gio_hang_chi_tiet WHERE id_chi_tiet = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$id_delete]);
  // Load lại trang để cập nhật danh sách
  header("Location: GioHang.php");
  exit();
}

// Lấy danh sách sản phẩm trong giỏ
$sql = "SELECT ghct.*, bt.gia, sp.ten_san_pham, asp.duong_dan_anh, bt.rom, bt.mau
        FROM gio_hang_chi_tiet ghct
        JOIN bien_the bt ON ghct.id_bien_the = bt.id_bien_the
        JOIN san_pham sp ON bt.id_san_pham = sp.id_san_pham
        LEFT JOIN anh_san_pham asp ON asp.id_san_pham = sp.id_san_pham
        WHERE ghct.id_gio_hang = (SELECT id_gio_hang FROM gio_hang WHERE id_nguoi_dung = ? LIMIT 1)
        GROUP BY ghct.id_chi_tiet"; // Group by để tránh lặp ảnh nếu sản phẩm nhiều ảnh

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_user]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="vi">
<body>
  
  <?php require_once './includes/header.php'; ?>
  
  <link rel="stylesheet" href="assets/css/stylesCart.css">

  <main class="container cart-page">
    <div class="cart-wrapper">
      <div class="cart-header">
        <h2>GIỎ HÀNG CỦA BẠN <span class="lock">🔒</span></h2>
      </div>
      
      <div class="select-all">
        <h4><input type="checkbox" id="selectAll"> TẤT CẢ</h4>
      </div>
      
      <div class="cart-list" id="cartList">
        <?php if (!empty($cart_items)):
          foreach ($cart_items as $item): ?>
            <div class="cart-item" data-price="<?= $item['gia'] ?>">
              <div class="item-left">
                <img src="<?= !empty($item['duong_dan_anh']) ? $item['duong_dan_anh'] : 'assets/images/no-image.png' ?>" alt="" class="item-thumb">
              </div>
              <div class="item-mid">
                <div class="item-name">
                    <a href="ChiTietSanPham.php?id=<?= /* Bạn cần lấy id_san_pham nếu muốn link */ '#' ?>" style="text-decoration:none; color:inherit;">
                        <?= htmlspecialchars($item['ten_san_pham']) ?>
                    </a>
                </div>
                <div style="font-size: 0.9em; color: #666; margin-bottom: 5px;">
                    Phân loại: <?= $item['rom'] ?> - <?= ucfirst($item['mau']) ?>
                </div>

                <div class="item-price price-red">
                  <?= $item['gia'] === null ? 'Liên hệ' : number_format($item['gia'], 0, ',', '.') . 'đ' ?>
                </div>
                
                <div class="item-controls">
                  <div class="qty-box">
                    <button class="qty-btn qty-minus">−</button>
                    <input class="qty-input" type="number" value="<?= $item['so_luong'] ?>" min="1">
                    <button class="qty-btn qty-plus">+</button>
                  </div>
                </div>
              </div>
              
              <div class="item-right">
                <button class="del" data-key="<?= $item['id_chi_tiet'] ?>">×</button>
                <label class="select-wrap">
                    <input type="checkbox" class="select-item" value="<?= $item['id_chi_tiet'] ?>"> Chọn
                </label>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div style="text-align: center; padding: 50px;">
              <p>Giỏ hàng hiện đang trống</p>
              <a href="TrangChu.php" class="btn">Tiếp tục mua sắm</a>
          </div>
        <?php endif; ?>

        <div class="cart-footer">
          <div class="summary">
            <button id="totalBtn" class="btn total">TỔNG CỘNG: 0 VND</button>
            <form id="checkoutForm" action="ThanhToan.php" method="POST">
              <input type="hidden" name="selected_items" id="selectedItems">
              <button type="submit" class="btn checkout">THANH TOÁN</button>
            </form>
          </div>
        </div>
      </div>
  </main>

  <?php require_once './includes/footer.php'; ?>

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
            const price = Number(item.dataset.price || 0);
            const qty = Number(item.querySelector('.qty-input').value || 1);
            sum += price * qty;
          }
        });
        totalBtn.textContent = 'TỔNG CỘNG: ' + formatVND(sum);
      }

      // Quantity & Delete handlers
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

      // Checkbox change handlers
      cartList.addEventListener('change', (e) => {
        if (e.target.matches('.select-item')) {
          computeTotal();
          
          // Logic bỏ chọn "Select All" nếu bỏ chọn 1 item con
          if (!e.target.checked) {
             selectAll.checked = false;
          }
        }
        if (e.target.matches('.qty-input')) {
          e.target.value = Math.max(1, Number(e.target.value));
          computeTotal();
        }
      });

      // Select All handler
      selectAll.addEventListener('change', () => {
        const checked = selectAll.checked;
        cartList.querySelectorAll('.select-item').forEach(c => c.checked = checked);
        computeTotal();
      });

      // Tính tổng lần đầu khi load trang
      computeTotal();
    })();

    // XỬ LÝ SUBMIT FORM THANH TOÁN
    document.getElementById("checkoutForm").addEventListener("submit", function(e) {
      let ids = [];
      document.querySelectorAll(".cart-item").forEach(item => {
        const chk = item.querySelector(".select-item");
        if (chk.checked) {
            // Lấy value của checkbox (chính là id_chi_tiet đã gán ở trên)
            ids.push(chk.value); 
        }
      });

      if (ids.length === 0) {
        alert("Vui lòng chọn ít nhất 1 sản phẩm để thanh toán.");
        e.preventDefault();
        return;
      }

      // Gán mảng ID vào input hidden dưới dạng JSON string
      document.getElementById("selectedItems").value = JSON.stringify(ids);
    });
  </script>

</body>
</html>