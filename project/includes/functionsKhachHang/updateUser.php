<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION["id_nguoi_dung"])) {
    echo "NOT_LOGIN";
    exit;
}

$id = $_SESSION["id_nguoi_dung"];

$name  = trim($_POST["fullname"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$currentPw = $_POST["currentPassword"] ?? "";
$newPw     = $_POST["newPassword"] ?? "";

if ($name === "") {
    echo "NAME_EMPTY";
    exit;
}

try {
    // Lấy mật khẩu hiện tại từ DB
    $sql = "SELECT mat_khau FROM nguoi_dung WHERE id_nguoi_dung = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "USER_NOT_FOUND";
        exit;
    }

    $hashed = $user["mat_khau"];

    if ($newPw !== "") {
        if (!password_verify($currentPw, $hashed)) {
            echo "WRONG_PASSWORD";
            exit;
        }
        if (strlen($newPw) < 6) {
            echo "PW_TOO_SHORT";
            exit;
        }

        $newHash = password_hash($newPw, PASSWORD_DEFAULT);
        $sql = "UPDATE nguoi_dung SET ho_ten = ?, mat_khau = ? WHERE id_nguoi_dung = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $phone, $newHash, $id]);
    } else {
        $sql = "UPDATE nguoi_dung SET ho_ten = ? WHERE id_nguoi_dung = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $phone, $id]);
    }

    echo "OK";
} catch (Exception $e) {

    echo "ERROR";
}
