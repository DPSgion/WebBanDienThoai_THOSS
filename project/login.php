<?php
include 'config/config.php';
session_start();

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'login') {

  if (!empty($_POST['identifier']) && !empty($_POST['password'])) {

    $sdt = trim($_POST['identifier']);
    $mat_khau_nhap = $_POST['password'];

    try {
      $sql = "SELECT id_nguoi_dung, mat_khau, ho_ten, vai_tro 
                    FROM nguoi_dung 
                    WHERE sdt = ?";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$sdt]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($user) {
        if (password_verify($mat_khau_nhap, $user['mat_khau'])) {

          $_SESSION['loggedin'] = true;
          $_SESSION['id_nguoi_dung'] = $user['id_nguoi_dung'];
          $_SESSION['ho_ten'] = $user['ho_ten'];
          $_SESSION['vai_tro'] = $user['vai_tro'];

          if ($user['vai_tro'] === 'admin') {
            header("Location: ./admin/index.php");
            exit;
          }

          header("Location: TrangChu.php");

          exit;
        } else {
          $error_message = "Số điện thoại hoặc mật khẩu không đúng.";
        }
      } else {
        $error_message = "Số điện thoại hoặc mật khẩu không đúng.";
      }
    } catch (PDOException $e) {
      $error_message = "Lỗi hệ thống. Vui lòng thử lại sau.";
    }
  } else {
    $error_message = "Vui lòng điền đầy đủ thông tin.";
  }
}
?>


<!doctype html>
<html lang="vi">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Đăng nhập — ĐIỆN THOẠI TRỰC TUYẾN</title>
  <link rel="stylesheet" href="assets/css/stylesTC.css">
  <link rel="stylesheet" href="assets/css/stylesLogin.css">
</head>

<body>

  <header class="main-header">
    <div class="header-top">
      <div class="logo">ĐIỆN THOẠI TRỰC TUYẾN</div>
    </div>
  </header>
  <main class="container login-page">
    <div class="login-card">
      <h2>Đăng nhập</h2>

      <?php if (!empty($error_message)): ?>
        <p style="color: red; text-align: center; margin-bottom: 15px; border: 1px solid red; padding: 10px; background-color: #ffebeb; border-radius: 4px;">
          <?php echo $error_message; ?>
        </p>
      <?php endif; ?>

      <form method="POST" action="login.php">
        <input type="hidden" name="action" value="login">

        <label class="field">
          <div class="label">Số điện thoại</div>
          <input id="identifier" name="identifier" type="text" placeholder="ví dụ:0123456789" required
            value="<?php echo isset($_POST['identifier']) ? htmlspecialchars($_POST['identifier']) : ''; ?>">
        </label>

        <label class="field">
          <div class="label">Mật khẩu</div>
          <input id="password" name="password" type="password" placeholder="Mật khẩu" required>
        </label>

        <div class="actions">
          <button type="submit" class="btn primary">Đăng nhập</button>
          <a class="btn outline" href="TrangChu.php">Hủy</a>
        </div>
      </form>

      <div class="register-note">Chưa có tài khoản? <a href="DangKy.php">Đăng ký</a></div>
    </div>
  </main>

</body>

</html>