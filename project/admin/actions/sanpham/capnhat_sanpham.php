<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/models/ProductModel.php';

try {
    // nhận FormData
    $product_id = intval($_POST['product_id'] ?? 0);
    if ($product_id <= 0) throw new Exception("product_id không hợp lệ");

    // required fields
    $name = trim($_POST['name'] ?? '');
    $id_category = intval($_POST['id_category'] ?? 0);
    $os = $_POST['os'] ?? '';
    if ($name === '' || $id_category <= 0 || $os === '') {
        throw new Exception("Thiếu thông tin bắt buộc (tên, danh mục, OS).");
    }

    // options: họ được gửi theo options[0][ram] ... nên PHP tạo mảng $_POST['options']
    $options = $_POST['options'] ?? [];
    // normalize options array => convert to numeric-index array of associative
    $normalizedOptions = [];
    foreach ($options as $k => $v) {
        // nếu v chứa subkeys ram, rom... thì it's already ok; but sometimes browser yields nested arrays
        $normalizedOptions[] = [
            'ram' => $v['ram'] ?? '',
            'rom' => $v['rom'] ?? '',
            'color' => $v['color'] ?? '',
            'quantity' => intval($v['quantity'] ?? 0),
            'price' => floatval($v['price'] ?? 0)
        ];
    }

    if (count($normalizedOptions) === 0) {
        throw new Exception("Phải có ít nhất một biến thể (option).");
    }

    // existing images to KEEP (client sends list of filenames to keep)
    // note: if client doesn't send any, treat as empty array => all old images will be deleted
    $keepImages = $_POST['existing_images'] ?? []; // array of filenames (duong_dan_anh)
    if (!is_array($keepImages)) $keepImages = [];

    // uploaded files
    $images = $_FILES['images'] ?? null;

    // final image count check: keep + new uploads must be >=1
    $newFilesCount = 0;
    if ($images && isset($images['name'])) {
        for ($i = 0; $i < count($images['name']); $i++) {
            if ($images['error'][$i] === UPLOAD_ERR_OK) $newFilesCount++;
        }
    }

    $totalAfter = count($keepImages) + $newFilesCount;
    if ($totalAfter < 1) throw new Exception("Phải có ít nhất 1 hình sau khi cập nhật.");

    // build $data to pass to model
    $data = [
        'id_category' => $id_category,
        'name' => $name,
        'os' => $os,
        'cpu' => $_POST['cpu'] ?? '',
        'screen' => $_POST['screen'] ?? '',
        'front_cam' => $_POST['front_cam'] ?? '',
        'rear_cam' => $_POST['rear_cam'] ?? '',
        'pin' => $_POST['pin'] ?? '',
        'options' => $normalizedOptions
    ];

    $model = new ProductModel($pdo);
    $model->updateProduct($product_id, $data, $images, $keepImages);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
