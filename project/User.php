<?php
// Kiểm tra session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Dùng __DIR__ để tránh lỗi path config
require_once __DIR__ . '/config/config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['id_nguoi_dung'])) {
  header("Location: Login.php");
  exit();
}

$id_nguoi_dung = $_SESSION['id_nguoi_dung'];

// --- ĐÃ XÓA HÀM get_all_categories (Vì header.php đã có rồi) ---

// Lấy lịch sử mua hàng (Giữ nguyên logic của bạn)
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

  <?php require_once './includes/header.php'; ?>

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

  <?php require_once './includes/footer.php'; ?>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      // Fetch thông tin user để điền vào form (Logic cũ của bạn)
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
            // Trim khoảng trắng thừa để switch case chính xác
            code = code.trim(); 
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
                alert("Lỗi hoặc phản hồi lạ: " + code);
            }

            document.getElementById("currentPassword").value = "";
            document.getElementById("newPassword").value = "";
            document.getElementById("confirmPassword").value = "";
          })
          .catch(err => console.error("Lỗi fetch update:", err));
      });

    });
  </script>

</body>
</html>