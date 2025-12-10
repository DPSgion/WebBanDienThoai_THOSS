<?php
session_start();
// dùng đường dẫn tuyệt đối tương đối an toàn
require_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["id_nguoi_dung"])) {
    echo json_encode(["error" => "Not logged in"]);
    exit;
}

$id = $_SESSION["id_nguoi_dung"];

try {
    $sql = "SELECT ho_ten, sdt FROM nguoi_dung WHERE id_nguoi_dung = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($user ?: []);
} catch (Exception $e) {
    // trả lỗi cho dev; trong production nên log lại
    echo json_encode(["error" => "DB_ERROR", "msg" => $e->getMessage()]);
}
