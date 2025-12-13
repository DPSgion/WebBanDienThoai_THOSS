<?php
session_start();
include_once 'config/config.php';
// Kiểm tra đăng nhập
if (!isset($_SESSION['id_nguoi_dung'])) {
  header("Location: Login.php");
  exit();
}

$id_nguoi_dung = $_SESSION['id_nguoi_dung'];


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
//Lấy lịch sử mua hàng
$sql = "SELECT * FROM don_hang WHERE id_nguoi_dung = ? ORDER BY ngay_dat DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_nguoi_dung]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="vi">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Trang người dùng — ĐIỆN THOẠI TRỰC TUYẾN</title>
  <link rel="stylesheet" href="assets/css/stylesTC.css">
  <link rel="stylesheet" href="assets/css/stylesUser.css">
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
        <!--SỬA-->
        <a href="TrangChu.php" class="icon-btn cart" aria-label="Trang chủ">🏠 </a>
        <a href="GioHang.php" class="icon-btn cart" aria-label="Giỏ hàng">🛒 </span></a>
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

  <main class="container user-page">
    <div class="user-grid">
      <section class="profile-card">
        <h2>Thông tin cá nhân</h2>
        <form id="profileForm">
          <label class="field">
            <div class="label">Họ và tên</div>
            <input id="fullname" name="fullname" type="text" required>
          </label>

          <label class="field">
            <div class="label">Số điện thoại</div>
            <input id="phone" name="phone" type="text" readonly>
          </label>

          <label class="field">
            <div class="label">Mật khẩu hiện tại</div>
            <input id="currentPassword" name="currentPassword" type="password" placeholder="Mật khẩu hiện tại">
          </label>

          <label class="field">
            <div class="label">Mật khẩu mới</div>
            <input id="newPassword" name="newPassword" type="password" placeholder="Mật khẩu mới (ít nhất 6 ký tự)">
          </label>

          <label class="field">
            <div class="label">Xác nhận mật khẩu mới</div>
            <input id="confirmPassword" name="confirmPassword" type="password" placeholder="Nhập lại mật khẩu mới">
          </label>

          <div class="actions">
            <button type="submit" class="btn primary">Lưu thông tin</button>
            <a class="btn outline" href="TrangChu.php ">Quay lại</a>
          </div>
        </form>

        <hr>


      </section>

      <section class="orders-card">
        <h2>Lịch sử mua hàng</h2>
        <?php if (empty($orders)): ?>
          <div class="muted">Chưa có đơn hàng nào.</div>
        <?php else: ?>
          <?php foreach ($orders as $o): ?>
            <div class="order-item">
              <div class="order-head">
                <div><strong>Đơn #<?= $o['id_don_hang'] ?></strong> — <?= $o['ngay_dat'] ?></div>
                <div class="order-right">
                  <?= $o['trang_thai'] ?> •
                  <strong><?= number_format($o['tong_tien']) ?>₫</strong>
                </div>
              </div>

              <div class="order-actions">
                <a href="hoa_don.php?id=<?= $o['id_don_hang'] ?>" class="btn small">
                  Xem chi tiết
                </a>
              </div>
            </div>
          <?php endforeach; ?>

        <?php endif; ?>
      </section>
    </div>
  </main>


  <script>
    // shared dropdown behavior
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


    document.addEventListener("DOMContentLoaded", () => {
      fetch("includes/functionsKhachHang/getUser.php")
        .then(res => res.json())
        .then(data => {
          if (data.error) return;
          document.getElementById("fullname").value = data.ho_ten;
          document.getElementById("phone").value = data.sdt;
        });

      // Submit form
      document.getElementById("profileForm").addEventListener("submit", function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch("includes/functionsKhachHang/updateUser.php", {
            method: "POST",
            body: formData
          })
          .then(res => res.text())
          .then(code => {
            switch (code) {
              case "OK":
                alert("Cập nhật thành công!");
                break;
              case "NAME_EMPTY":
                alert("Vui lòng nhập họ tên.");
                break;
              case "WRONG_PASSWORD":
                alert("Mật khẩu hiện tại không đúng!");
                break;
              case "PW_TOO_SHORT":
                alert("Mật khẩu mới phải có ít nhất 6 ký tự.");
                break;
              default:
                alert("Lỗi không xác định: " + code);
            }

            document.getElementById("currentPassword").value = "";
            document.getElementById("newPassword").value = "";
            document.getElementById("confirmPassword").value = "";
          });
      });

    });
  </script>

</body>

</html>