<?php
// admin/actions/donhang/lay_ds_donhang.php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/models/DonHangModel.php';

try {
    $donhangModel = new DonHangModel();
    
    $keyword = isset($_GET['search']) ? $_GET['search'] : '';
    $status = isset($_GET['status']) ? $_GET['status'] : 'all';

    $list = $donhangModel->getAll($keyword, $status);
    
    echo json_encode([
        'success' => true,
        'data' => $list
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}
?>