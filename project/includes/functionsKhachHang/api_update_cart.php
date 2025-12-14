<?php
// File: includes/functionsKhachHang/api_update_cart.php
session_start();

// 1. Kết nối Database (Sửa đường dẫn require cho đúng với cấu trúc thư mục của bạn)
// Giả sử file này nằm ở: du_an/includes/functionsKhachHang/
// Config nằm ở: du_an/config/
require_once __DIR__ . '/../../config/config.php'; 

header('Content-Type: application/json');

// 2. Kiểm tra đăng nhập
if (!isset($_SESSION['id_nguoi_dung'])) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn chưa đăng nhập']);
    exit;
}

// 3. Nhận dữ liệu
$id_chi_tiet = $_POST['id_chi_tiet'] ?? null;
$qty = $_POST['qty'] ?? null;

if (!$id_chi_tiet || !$qty) {
    echo json_encode(['status' => 'error', 'message' => 'Thiếu dữ liệu']);
    exit;
}

try {
    // 4. Kiểm tra tồn kho trước khi update
    $stmtCheck = $pdo->prepare("
        SELECT bt.so_luong_ton 
        FROM gio_hang_chi_tiet ghct
        JOIN bien_the bt ON ghct.id_bien_the = bt.id_bien_the
        WHERE ghct.id_chi_tiet = ?
    ");
    $stmtCheck->execute([$id_chi_tiet]);
    $stock = $stmtCheck->fetchColumn();

    if ($stock === false) {
        throw new Exception("Sản phẩm không tồn tại trong giỏ");
    }

    // 5. Logic chốt số lượng
    $new_qty = (int)$qty;
    if ($new_qty < 1) $new_qty = 1;
    if ($new_qty > $stock) $new_qty = $stock; // Không cho vượt quá tồn kho

    // 6. Cập nhật vào Database (QUAN TRỌNG NHẤT)
    $stmtUpdate = $pdo->prepare("UPDATE gio_hang_chi_tiet SET so_luong = ? WHERE id_chi_tiet = ?");
    $result = $stmtUpdate->execute([$new_qty, $id_chi_tiet]);

    if ($result) {
        echo json_encode([
            'status' => 'success', 
            'message' => 'Đã cập nhật giỏ hàng',
            'fixed_qty' => $new_qty // Trả về số lượng đã chốt để JS sửa lại nếu cần
        ]);
    } else {
        throw new Exception("Lỗi SQL update");
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>