<?php
// 1. Start Session & Config
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/config.php';

// 2. CHECK LOGIN
if (!isset($_SESSION['id_nguoi_dung'])) {
    header("Location: Login.php");
    exit();
}
$id_nguoi_dung = $_SESSION['id_nguoi_dung'];

// 3. XỬ LÝ DỮ LIỆU THANH TOÁN (Logic giữ nguyên)
$is_buy_now = false;
$subtotal = 0;
$cart_items = [];
$buy_now_item = null;
$selected_items = []; // Để lưu danh sách ID gửi qua form

// TRƯỜNG HỢP 1: MUA NGAY (Từ trang chi tiết)
if (!empty($_POST['id_bien_the'])) {
    $is_buy_now = true;
    $id_bien_the = (int)$_POST['id_bien_the'];
    $qty = max(1, (int)($_POST['qty'] ?? 1));

    $sql = "SELECT bt.id_san_pham, bt.gia, bt.rom, bt.mau, bt.so_luong_ton, bt.id_bien_the,
                   sp.ten_san_pham, asp.duong_dan_anh
            FROM bien_the bt
            JOIN san_pham sp ON sp.id_san_pham = bt.id_san_pham
            LEFT JOIN anh_san_pham asp ON asp.id_san_pham = sp.id_san_pham
            WHERE bt.id_bien_the = ?
            LIMIT 1"; // Limit 1 lấy ảnh đầu tiên
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_bien_the]);
    $buy_now_item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$buy_now_item) {
        // Xử lý lỗi nếu hack ID
        header("Location: TrangChu.php");
        exit();
    }

    // Gán số lượng mua
    $buy_now_item['so_luong'] = $qty;
    $subtotal = $buy_now_item['gia'] * $qty;

} else {
    // TRƯỜNG HỢP 2: TỪ GIỎ HÀNG
    if (!empty($_POST['selected_items'])) {
        // Decode JSON từ input hidden bên giỏ hàng
        $ids = json_decode($_POST['selected_items'], true);
        
        if (is_array($ids) && count($ids) > 0) {
            $selected_items = $ids;
            $placeholders = implode(',', array_fill(0, count($ids), '?'));

            $sql = "SELECT ghct.id_chi_tiet, ghct.id_bien_the, ghct.so_luong, 
                           bt.gia, bt.rom, bt.mau,
                           sp.ten_san_pham, asp.duong_dan_anh
                    FROM gio_hang_chi_tiet ghct
                    JOIN gio_hang gh ON gh.id_gio_hang = ghct.id_gio_hang
                    JOIN bien_the bt ON bt.id_bien_the = ghct.id_bien_the
                    JOIN san_pham sp ON sp.id_san_pham = bt.id_san_pham
                    LEFT JOIN (
                        SELECT id_san_pham, MIN(duong_dan_anh) AS duong_dan_anh
                        FROM anh_san_pham
                        GROUP BY id_san_pham
                    ) asp ON asp.id_san_pham = sp.id_san_pham
                    WHERE ghct.id_chi_tiet IN ($placeholders) AND gh.id_nguoi_dung = ?";
            
            // Thêm id_nguoi_dung vào params để bảo mật (tránh thanh toán giỏ người khác)
            $params = $ids;
            $params[] = $id_nguoi_dung;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    if (empty($cart_items)) {
        echo "<script>alert('Giỏ hàng rỗng hoặc chưa chọn sản phẩm!'); window.location.href='GioHang.php';</script>";
        exit();
    }

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
  
  <?php require_once './includes/header.php'; ?>

  <main class="container checkout-page">
    <h1>Thanh toán</h1>
    
    <div class="checkout-grid">
      <section class="checkout-left">
        <form action="./includes/functionsKhachHang/xu_ly_dat_hang.php" method="POST" id="checkoutForm">

          <h2>Thông tin giao hàng</h2>

          <div class="form-row">
            <label>Địa chỉ nhận hàng
              <input name="address" type="text" required placeholder="Số nhà, tên đường...">
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

          <?php if ($is_buy_now): ?>
             <input type="hidden" name="id_bien_the" value="<?= $buy_now_item['id_bien_the'] ?>">
             <input type="hidden" name="qty" value="<?= $buy_now_item['so_luong'] ?>">
             <input type="hidden" name="type" value="buy_now">
          <?php else: ?>
             <input type="hidden" name="type" value="cart">
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
                <img src="<?= !empty($buy_now_item['duong_dan_anh']) ? $buy_now_item['duong_dan_anh'] : 'assets/images/no-image.png' ?>" class="prod-thumb">
                <div class="order-mid">
                  <div class="prod-name"><?= htmlspecialchars($buy_now_item['ten_san_pham']) ?></div>
                  <div class="prod-meta"><?= $buy_now_item['rom'] ?> • <?= $buy_now_item['mau'] ?></div>
                  <div>Số lượng: <strong><?= $buy_now_item['so_luong'] ?></strong></div>
                </div>
                <div class="order-price">
                  <span class="price-red"><?= number_format($buy_now_item['gia']) ?>đ</span>
                </div>
              </div>
            </div>
          
          <?php else: ?>
            <?php foreach ($cart_items as $item): ?>
              <div class="order-item">
                <div class="order-card">
                  <img src="<?= !empty($item['duong_dan_anh']) ? $item['duong_dan_anh'] : 'assets/images/no-image.png' ?>" class="prod-thumb">
                  <div class="order-mid">
                    <div class="prod-name"><?= htmlspecialchars($item['ten_san_pham']) ?></div>
                    <div class="prod-meta"><?= $item['rom'] ?> • <?= $item['mau'] ?></div>
                    <div>Số lượng: <strong><?= $item['so_luong'] ?></strong></div>
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
            <div class="row"><span>Phí vận chuyển</span><span class="value">Miễn phí</span></div>
            <div class="row total"><span>Tổng cộng</span><span class="value"><?= number_format($subtotal) ?>đ</span></div>
          </div>
        </div>
      </aside>
    </div>
  </main>

  <?php require_once './includes/footer.php'; ?>

</body>
</html>