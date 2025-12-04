<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/models/DanhmucModel.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data || empty($data['id']) || empty($data['name'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Dữ liệu không hợp lệ'
        ]);
        exit;
    }

    $category = new CategoryModel();
    $ok = $category->updateCategory($data['id'], trim($data['name']));

    echo json_encode([
        'success' => $ok,
        'message' => $ok ? 'Cập nhật danh mục thành công' : 'Lỗi khi cập nhật'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}