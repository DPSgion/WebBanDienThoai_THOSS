<?php
// đường dẫn tùy vị trí file, nếu file đang ở includes/functionsKhachHang/
require_once __DIR__ . '/../../config/config.php';

// Lấy và sanitize param
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$rom = isset($_GET['rom']) ? trim($_GET['rom']) : '';
$color = isset($_GET['color']) ? trim($_GET['color']) : '';

// Trả thẳng về 0 nếu thiếu param
if ($id <= 0 || $rom === '' || $color === '') {
    echo "0";
    exit;
}

$sql = "SELECT id_bien_the FROM bien_the 
        WHERE id_san_pham = :id AND rom = :rom AND mau = :color LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'id' => $id,
    'rom' => $rom,
    'color' => $color
]);
$variant = $stmt->fetch(PDO::FETCH_ASSOC);

// đảm bảo echo đúng dạng số, không whitespace
echo $variant ? (int)$variant['id_bien_the'] : "0";
