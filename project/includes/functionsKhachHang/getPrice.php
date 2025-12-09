<?php
//File này hỗ trợ Chi tiết sản phẩm

require_once "/project/config/config.php";

$id = $_GET['id'] ?? '';
$rom = $_GET['rom'] ?? '';
$color = $_GET['color'] ?? '';

$sql = "SELECT gia FROM bien_the 
        WHERE id_san_pham = :id 
        AND rom = :rom 
        AND mau = :mau
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':id' => $id,
    ':rom' => $rom,
    ':mau' => $color
]);

$row = $stmt->fetch(PDO::FETCH_ASSOC);

echo $row['gia'] ?? "LH";
