<?php
require_once '../../config/config.php';
// require_once '../../includes/models/ProductModel.php';

class ProductModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function addProduct($data) {
        try {
            // BẮT ĐẦU TRANSACTION để tránh lỗi thêm sản phẩm nhưng biến thể không thêm
            $this->pdo->beginTransaction();

            // === 1) Thêm sản phẩm vào bảng san_pham ===
            $stmt = $this->pdo->prepare(
                "INSERT INTO san_pham 
                    (ten_san_pham, cpu, pin, man_hinh, os, camera_truoc, camera_sau) 
                 VALUES 
                    (:name, :cpu, :pin, :screen, :os, :front_cam, :rear_cam)"
            );

            $stmt->execute([
                ':name' => $data['name'],
                ':os' => $data['os'],
                ':cpu' => $data['cpu'],
                ':screen' => $data['screen'],
                ':front_cam' => $data['front_cam'],
                ':rear_cam' => $data['rear_cam'],
                ':pin' => $data['pin']
            ]);

            $product_id = $this->pdo->lastInsertId();


            // === 2) Thêm các biến thể sản phẩm ===
            $stmtOpt = $this->pdo->prepare(
                "INSERT INTO bien_the 
                    (id_san_pham, ram, rom, mau, gia, so_luong_ton)
                 VALUES
                    (:id_san_pham, :ram, :rom, :mau, :gia, :so_luong)"
            );

            foreach ($data['options'] as $opt) {
                $stmtOpt->execute([
                    ':id_san_pham' => $product_id,
                    ':ram' => $opt['ram'],
                    ':rom' => $opt['rom'],
                    ':mau' => $opt['color'],
                    ':gia' => $opt['price'],
                    ':so_luong' => $opt['quantity']
                ]);
            }

            // Xác nhận giao dịch
            $this->pdo->commit();

            return $product_id;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Lỗi khi thêm sản phẩm: " . $e->getMessage());
        }
    }

}
