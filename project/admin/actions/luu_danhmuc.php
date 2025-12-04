<?php
header('Content-Type: application/json');
require_once '../../includes/models/DanhmucModel.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['name'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Tên danh mục không hợp lệ'
    ]);
    exit;
}

$category = new CategoryModel();
$ok = $category->addCategory(trim($data['name']));

if ($ok) {
    echo json_encode([
        'success' => true,
        'message' => 'Thêm danh mục thành công'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi thêm dữ liệu'
    ]);
}
