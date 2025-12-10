<?php
//Xử lí việc thêm giỏ hàng từ ChiTietSanPham -> GioHang.php
session_start();
include_once '../../config/config.php';
// Giả sử user đã đăng nhập
$id_nguoi_dung = $_SESSION['id_nguoi_dung'] ?? null;
$id_bien_the = $_POST['id_bien_the'] ?? null;
// 0. Kiểm tra giá biến thể có NULL không
$sql = "SELECT gia FROM bien_the WHERE id_bien_the = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_bien_the]);
$variant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$variant || $variant['gia'] === null) {
    die("Sản phẩm không thể thêm vào giỏ hàng (Liên hệ).");
}

$qty = $_POST['qty'] ?? 1;

if (!$id_nguoi_dung) {
    die("Bạn cần đăng nhập");
}

if (!$id_bien_the) {
    die("Thiếu id_bien_the");
}

// 1. Kiểm tra xem user đã có giỏ hàng chưa
$sql = "SELECT id_gio_hang FROM gio_hang WHERE id_nguoi_dung = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_nguoi_dung]);
$cart = $stmt->fetch(PDO::FETCH_ASSOC);

if ($cart) {
    $id_gio_hang = $cart['id_gio_hang'];
} else {
    // Tạo giỏ hàng
    $sql = "INSERT INTO gio_hang (id_nguoi_dung) VALUES (?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_nguoi_dung]);
    $id_gio_hang = $pdo->lastInsertId();
}

// 2. Kiểm tra sản phẩm đã có chưa
$sql = "SELECT id_chi_tiet, so_luong FROM gio_hang_chi_tiet 
        WHERE id_gio_hang = ? AND id_bien_the = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_gio_hang, $id_bien_the]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if ($item) {
    // Update tăng số lượng
    $sql = "UPDATE gio_hang_chi_tiet 
            SET so_luong = so_luong + ? 
            WHERE id_chi_tiet = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$qty, $item['id_chi_tiet']]);
} else {
    // Thêm mới
    $sql = "INSERT INTO gio_hang_chi_tiet (id_gio_hang, id_bien_the, so_luong) 
            VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_gio_hang, $id_bien_the, $qty]);
}

header("Location: ../../GioHang.php");
exit();
