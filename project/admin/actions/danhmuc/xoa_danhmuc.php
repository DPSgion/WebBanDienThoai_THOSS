<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/models/DanhmucModel.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data || empty($data['id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'ID danh mục không hợp lệ'
        ]);
        exit;
    }

    $category = new CategoryModel();
    $ok = $category->deleteCategory($data['id']);

    echo json_encode([
        'success' => $ok,
        'message' => $ok ? 'Xóa danh mục thành công' : 'Lỗi khi xóa danh mục'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}