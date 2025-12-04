<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/models/ProductModel.php';

$data = json_decode(file_get_contents('php://input'), true);
$id_bien_the = $data['id_bien_the'] ?? null;

if (!$id_bien_the) {
    echo json_encode(['success' => false, 'message' => 'Thiếu id_bien_the']);
    exit;
}

try {
    $model = new ProductModel($pdo);
    $model->deleteVariant($id_bien_the);

    echo json_encode(['success' => true, 'message' => 'Đã xoá']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
