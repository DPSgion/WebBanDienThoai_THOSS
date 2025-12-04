<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/models/ProductModel.php';

try {
    if (!isset($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'Thiếu id sản phẩm']);
        exit;
    }

    $id = intval($_GET['id']);

    // --- Lấy thông tin sản phẩm ---
    $stmt = $pdo->prepare("SELECT * FROM san_pham WHERE id_san_pham = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
        exit;
    }

    // --- Lấy biến thể ---
    $stmt = $pdo->prepare("SELECT * FROM bien_the WHERE id_san_pham = ?");
    $stmt->execute([$id]);
    $options = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- Lấy ảnh ---
    $stmt = $pdo->prepare("SELECT * FROM anh_san_pham WHERE id_san_pham = ?");
    $stmt->execute([$id]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'product' => $product,
        'options' => $options,
        'images' => $images
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
