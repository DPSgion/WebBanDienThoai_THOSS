<?php
header('Content-Type: application/json');
// Lưu ý đường dẫn require phải đúng với cấu trúc thư mục của bạn
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/models/DonHangModel.php';

try {
    if (!isset($_GET['id'])) {
        throw new Exception("Thiếu ID");
    }

    $model = new DonHangModel();
    $info = $model->getOrderInfo($_GET['id']);
    $items = $model->getOrderItems($_GET['id']);

    if (!$info) {
        throw new Exception("Không tìm thấy đơn hàng trong CSDL");
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'info' => $info,
            'items' => $items
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>