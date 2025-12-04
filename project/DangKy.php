<?php
session_start();
// Đảm bảo đường dẫn config/config.php là chính xác
include 'config/config.php';

$error_message = "";
$success_message = "";

// Xử lý khi form được gửi đi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'register') {

  // 1. Lấy và làm sạch dữ liệu
  $ho_ten = trim($_POST['fullname']);
  $sdt = trim($_POST['identifier']); // Giả định đây là Số điện thoại
  $mat_khau_nhap = $_POST['password'];
  $xac_nhan_mat_khau = $_POST['password2'];
  $chap_nhan_dieu_khoan = isset($_POST['acceptTerms']);

  // 2. Xác thực phía Server
  if (empty($ho_ten) || empty($sdt) || empty($mat_khau_nhap) || empty($xac_nhan_mat_khau)) {
    $error_message = "Vui lòng điền đầy đủ tất cả các trường.";
  } else if ($mat_khau_nhap !== $xac_nhan_mat_khau) {
    $error_message = "Mật khẩu và Xác nhận mật khẩu không khớp.";
  } else if (strlen($mat_khau_nhap) < 6) {
    $error_message = "Mật khẩu phải có ít nhất 6 ký tự.";
  } else if (!$chap_nhan_dieu_khoan) {
    $error_message = "Bạn phải đồng ý với điều khoản để đăng ký.";
  } else {
    // 3. Kiểm tra số điện thoại đã tồn tại chưa
    try {
      $sql_check = "SELECT COUNT(*) FROM nguoi_dung WHERE sdt = ?";
      $stmt_check = $pdo->prepare($sql_check);
      $stmt_check->execute([$sdt]);

      if ($stmt_check->fetchColumn() > 0) {
        $error_message = "Số điện thoại này đã được đăng ký.";
      } else {

        // 4. Băm mật khẩu (HASHING)
        $mat_khau_hash = password_hash($mat_khau_nhap, PASSWORD_DEFAULT);
        $vai_tro = 'khachhang'; // Đăng ký luôn là khách hàng

        // 5. Chèn dữ liệu vào Database
        $sql_insert = "INSERT INTO nguoi_dung (ho_ten, sdt, mat_khau, vai_tro) 
                               VALUES (?, ?, ?, ?)";
        $stmt_insert = $pdo->prepare($sql_insert);

        if ($stmt_insert->execute([$ho_ten, $sdt, $mat_khau_hash, $vai_tro])) {
          // Đăng ký thành công, chuyển hướng về trang đăng nhập
          // Lưu ý: nên chuyển hướng thay vì chỉ hiển thị thông báo
          header("location: login.php?registered=success");
          exit;
        } else {
          $error_message = "Đăng ký không thành công. Vui lòng thử lại.";
        }
      }
    } catch (PDOException $e) {
      $error_message = "Lỗi hệ thống: " . $e->getMessage();
    }
  }
}
?>
<!doctype html>
<html lang="vi">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Đăng ký — ĐIỆN THOẠI TRỰC TUYẾN</title>
  <link rel="stylesheet" href="assets/css/stylesTC.css">
  <link rel="stylesheet" href="assets/css/stylesRegister.css">
</head>

<body>

  <header class="main-header">
    <div class="header-top">
      <div class="logo">ĐIỆN THOẠI TRỰC TUYẾN</div>
    </div>
  </header>

  <main class="container login-page">
    <div class="login-card">
      <h2>Tạo tài khoản</h2>
      <?php if (!empty($error_message)): ?>
        <p style="color: red; text-align: center; margin-bottom: 15px; border: 1px solid red; padding: 10px; background-color: #ffebeb; border-radius: 4px;">
          <?php echo $error_message; ?>
        </p>
      <?php endif; ?>
      <form method="POST" action="DangKy.php">
        <input type="hidden" name="action" value="register">

        <label class="field">
          <div class="label">Họ và tên</div>
          <input id="fullname" name="fullname" type="text" placeholder="Nguyễn Văn A" required
            value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>">
        </label>

        <label class="field">
          <div class="label">Số điện thoại</div>
          <input id="identifier" name="identifier" type="text" placeholder="ví dụ: 0123456789" required
            value="<?php echo isset($_POST['identifier']) ? htmlspecialchars($_POST['identifier']) : ''; ?>">
        </label>

        <label class="field">
          <div class="label">Mật khẩu</div>
          <input id="password" name="password" type="password" placeholder="Mật khẩu (ít nhất 6 ký tự)" required>
        </label>

        <label class="field">
          <div class="label">Xác nhận mật khẩu</div>
          <input id="password2" name="password2" type="password" placeholder="Nhập lại mật khẩu" required>
        </label>

        <label class="field row between">
          <div class="remember"><input id="acceptTerms" name="acceptTerms" type="checkbox" <?php echo isset($_POST['acceptTerms']) ? 'checked' : ''; ?>> Tôi đồng ý với điều khoản</div>
        </label>

        <div class="actions">
          <button type="submit" class="btn primary">Đăng ký</button>
          <a class="btn outline" href="TrangChu.php">Hủy</a>
        </div>
      </form>

      <div class="register-note">Đã có tài khoản? <a href="login.php">Đăng nhập</a></div>
    </div>
  </main>

  <script>
    // Giữ lại JS cho danh mục dropdown nếu cần
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