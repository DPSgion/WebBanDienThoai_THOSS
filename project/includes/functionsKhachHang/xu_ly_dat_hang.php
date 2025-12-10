<?php
session_start();
include_once '../../config/config.php';

$id_nguoi_dung = $_SESSION['id_nguoi_dung'] ?? null;

if (!$id_nguoi_dung) {
    die("Bạn cần đăng nhập");
}
$is_buy_now = isset($_POST['id_bien_the']) && $_POST['id_bien_the'] != '';
if ($is_buy_now) {

    $id_bien_the = (int)$_POST['id_bien_the'];
    $so_luong    = (int)($_POST['qty'] ?? 1);

    // Lấy thông tin biến thể
    $sql = "SELECT gia, so_luong_ton FROM bien_the WHERE id_bien_the = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_bien_the]);
    $variant = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$variant) {
        die("Biến thể không tồn tại");
    }

    // Kiểm tra tồn kho
    if ($variant['so_luong_ton'] < $so_luong) {
        die("Không đủ hàng trong kho");
    }

    // Tính tổng tiền
    $tong_tien = $variant['gia'] * $so_luong;

    // Tạo đơn hàng
    $sql = "INSERT INTO don_hang (id_nguoi_dung, tong_tien, dia_chi)
            VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_nguoi_dung, $tong_tien, $dia_chi]);
    $id_don_hang = $pdo->lastInsertId();

    // Chi tiết đơn hàng
    $sql = "INSERT INTO chi_tiet_don_hang (id_don_hang, id_bien_the, so_luong, gia_luc_mua)
            VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_don_hang, $id_bien_the, $so_luong, $variant['gia']]);

    // Trừ tồn kho
    $sql = "UPDATE bien_the SET so_luong_ton = so_luong_ton - ? WHERE id_bien_the = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$so_luong, $id_bien_the]);

    // Không động vào giỏ hàng!

    header("Location: ../../User.php?tab=orders&success=1");
    exit;
}
//Xử lí bên giỏ hàng
$selected_items = $_POST['selected_items'] ?? [];

if (empty($selected_items)) {
    die("Không có sản phẩm được chọn để đặt hàng.");
}
$address  = $_POST['address'] ?? '';
$city     = $_POST['city'] ?? '';
$district = $_POST['district'] ?? '';

$dia_chi = $address . ', ' . $city . ', ' . $district;

if (!$dia_chi) {
    die("Thiếu địa chỉ nhận hàng");
}

// 1. Lấy giỏ hàng của user
$sql = "SELECT id_gio_hang FROM gio_hang WHERE id_nguoi_dung = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_nguoi_dung]);
$gio = $stmt->fetch();

if (!$gio) {
    die("Không có giỏ hàng");
}

$id_gio_hang = $gio['id_gio_hang'];

// 2. Lấy sản phẩm được chọn trong giỏ
// nếu client gửi selected_items (id_chi_tiet), chỉ xử lý chúng
if (!empty($_POST['selected_items']) && is_array($_POST['selected_items'])) {
    $ids = $_POST['selected_items']; // array of id_chi_tiet (strings)
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $sql = "SELECT gct.id_chi_tiet, gct.id_bien_the, gct.so_luong, bt.gia
            FROM gio_hang_chi_tiet gct
            JOIN bien_the bt ON gct.id_bien_the = bt.id_bien_the
            WHERE gct.id_chi_tiet IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($ids);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($cart_items)) die("Không có mục nào để thanh toán");
} else {
    // cũ: lấy toàn bộ giỏ
    $sql = "SELECT gct.id_chi_tiet, gct.id_bien_the, gct.so_luong, bt.gia 
            FROM gio_hang_chi_tiet gct
            INNER JOIN bien_the bt ON gct.id_bien_the = bt.id_bien_the
            WHERE gct.id_gio_hang = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_gio_hang]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($cart_items)) die("Giỏ hàng trống");
}

if (empty($cart_items)) {
    die("Giỏ hàng trống");
}

// 3. Tính tổng tiền
$tong_tien = 0;
foreach ($cart_items as $item) {
    $tong_tien += $item['gia'] * $item['so_luong'];
}

// 4. Tạo đơn hàng
$sql = "INSERT INTO don_hang (id_nguoi_dung, tong_tien, dia_chi) VALUES (?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_nguoi_dung, $tong_tien, $dia_chi]);
$id_don_hang = $pdo->lastInsertId();

// 5. Tạo chi tiết đơn hàng + trừ tồn kho
foreach ($cart_items as $item) {

    // Insert chi tiết đơn hàng
    $sql = "INSERT INTO chi_tiet_don_hang (id_don_hang, id_bien_the, so_luong, gia_luc_mua)
            VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_don_hang, $item['id_bien_the'], $item['so_luong'], $item['gia']]);

    // Update tồn kho
    $sql = "UPDATE bien_the SET so_luong_ton = so_luong_ton - ? WHERE id_bien_the = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$item['so_luong'], $item['id_bien_the']]);
}

// Xóa chi tiết giỏ hàng đã bán
if (!empty($ids) && is_array($ids)) {
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = "DELETE FROM gio_hang_chi_tiet WHERE id_chi_tiet IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($ids);
}
// kiểm tra còn dòng nào trong gio_hang_chi_tiet cho id_gio_hang không
$sql = "SELECT COUNT(*) FROM gio_hang_chi_tiet WHERE id_gio_hang = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_gio_hang]);
$remaining = $stmt->fetchColumn();

if ($remaining == 0) {
    $sql = "DELETE FROM gio_hang WHERE id_gio_hang = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_gio_hang]);
}
// 8. Chuyển tới User.php -> lịch sử đơn hàng
header("Location: ../../User.php?tab=orders&success=1");
exit;
