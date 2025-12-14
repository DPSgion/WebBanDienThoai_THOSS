<?php
session_start();
include_once '../../config/config.php';

$id_nguoi_dung = $_SESSION['id_nguoi_dung'] ?? null;
$id_bien_the   = $_POST['id_bien_the'] ?? null;
$id_san_pham   = $_POST['id_san_pham'] ?? null;
$qty           = (int)($_POST['qty'] ?? 1);

// ===== HÀM TRẢ LỖI & REDIRECT =====
function backWithError($msg, $id_san_pham)
{
    $_SESSION['cart_error'] = $msg;
    header("Location: ../../ChiTietSanPham.php?id=" . $id_san_pham);
    exit();
}

// ===== KIỂM TRA CƠ BẢN =====
if (!$id_nguoi_dung) {
    backWithError("Bạn cần đăng nhập để thêm vào giỏ hàng", $id_san_pham);
}

if (!$id_bien_the || !$id_san_pham) {
    backWithError("Thiếu thông tin sản phẩm", $id_san_pham);
}

if ($qty <= 0) {
    backWithError("Số lượng không hợp lệ", $id_san_pham);
}

// ===== LẤY GIÁ + TỒN KHO =====
$sql = "SELECT gia, so_luong_ton FROM bien_the WHERE id_bien_the = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_bien_the]);
$variant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$variant || $variant['gia'] === null) {
    backWithError("Sản phẩm chưa có giá. Vui lòng liên hệ cửa hàng.", $id_san_pham);
}

$ton_kho = (int)$variant['so_luong_ton'];

// ===== LẤY GIỎ HÀNG =====
$sql = "SELECT id_gio_hang FROM gio_hang WHERE id_nguoi_dung = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_nguoi_dung]);
$cart = $stmt->fetch(PDO::FETCH_ASSOC);

if ($cart) {
    $id_gio_hang = $cart['id_gio_hang'];
} else {
    $sql = "INSERT INTO gio_hang (id_nguoi_dung) VALUES (?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_nguoi_dung]);
    $id_gio_hang = $pdo->lastInsertId();
}

// ===== KIỂM TRA BIẾN THỂ ĐÃ CÓ TRONG GIỎ CHƯA =====
$sql = "SELECT id_chi_tiet, so_luong 
        FROM gio_hang_chi_tiet 
        WHERE id_gio_hang = ? AND id_bien_the = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_gio_hang, $id_bien_the]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if ($item) {
    $tong_moi = $item['so_luong'] + $qty;

    if ($tong_moi > $ton_kho) {
        backWithError("Số lượng vượt quá tồn kho. Hiện còn $ton_kho sản phẩm.", $id_san_pham);
    }

    $sql = "UPDATE gio_hang_chi_tiet 
            SET so_luong = ? 
            WHERE id_chi_tiet = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$tong_moi, $item['id_chi_tiet']]);
} else {
    if ($qty > $ton_kho) {
        backWithError("Số lượng vượt quá tồn kho. Hiện còn $ton_kho sản phẩm.", $id_san_pham);
    }

    $sql = "INSERT INTO gio_hang_chi_tiet (id_gio_hang, id_bien_the, so_luong) 
            VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_gio_hang, $id_bien_the, $qty]);
}

// ===== THÀNH CÔNG =====
header("Location: ../../GioHang.php");
exit();
