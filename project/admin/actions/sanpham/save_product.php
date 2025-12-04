<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/models/ProductModel.php';

try {

    if (!isset($_POST['name'])) {
        throw new Exception("Dữ liệu POST lỗi.");
    }

    // gom dữ liệu
    $data = [
        'id_category' => $_POST['id_category'],
        'name' => $_POST['name'],
        'os' => $_POST['os'],
        'cpu' => $_POST['cpu'],
        'screen' => $_POST['screen'],
        'front_cam' => $_POST['front_cam'],
        'rear_cam' => $_POST['rear_cam'],
        'pin' => $_POST['pin'],
        'options' => $_POST['options'],  // array
    ];

    $images = $_FILES['images'] ?? null;

    $productModel = new ProductModel($pdo);
    $productId = $productModel->addProduct($data, $images);

    echo json_encode(['success' => true, 'product_id' => $productId]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

