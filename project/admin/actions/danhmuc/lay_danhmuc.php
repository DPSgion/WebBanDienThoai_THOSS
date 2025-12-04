<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/models/DanhmucModel.php';

try {
    $category = new CategoryModel();
    $list = $category->getAll();
    
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