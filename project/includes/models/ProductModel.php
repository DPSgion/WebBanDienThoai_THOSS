<?php
require_once __DIR__ . '/../../config/config.php';

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
                    (id_danh_muc, ten_san_pham, cpu, pin, man_hinh, os, camera_truoc, camera_sau) 
                 VALUES 
                    (:id_category, :name, :cpu, :pin, :screen, :os, :front_cam, :rear_cam)"
            );

            $stmt->execute([
                ':id_category' => $data['id_category'],
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

    public function getProducts() {
        $sql = "
            SELECT 
                sp.id_san_pham,
                sp.ten_san_pham,
                sp.cpu,
                sp.pin,
                sp.man_hinh,
                sp.os,
                bt.id_bien_the,
                bt.ram,
                bt.rom,
                bt.mau,
                bt.gia,
                bt.so_luong_ton
            FROM san_pham sp
            LEFT JOIN bien_the bt ON sp.id_san_pham = bt.id_san_pham
            ORDER BY sp.id_san_pham, bt.gia ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteVariant($id_bien_the) {
        try {
            // Lấy id_san_pham của biến thể này
            $stmt = $this->pdo->prepare("SELECT id_san_pham FROM bien_the WHERE id_bien_the = ?");
            $stmt->execute([$id_bien_the]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                throw new Exception("Biến thể không tồn tại");
            }

            $product_id = $row['id_san_pham'];

            // 1) Xóa biến thể
            $stmtDel = $this->pdo->prepare("DELETE FROM bien_the WHERE id_bien_the = ?");
            $stmtDel->execute([$id_bien_the]);

            // 2) Kiểm tra số lượng biến thể còn lại
            $stmtCount = $this->pdo->prepare("SELECT COUNT(*) as total FROM bien_the WHERE id_san_pham = ?");
            $stmtCount->execute([$product_id]);
            $count = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];

            // 3) Nếu hết biến thể → xóa luôn sản phẩm
            if ($count == 0) {
                $stmtDelSP = $this->pdo->prepare("DELETE FROM san_pham WHERE id_san_pham = ?");
                $stmtDelSP->execute([$product_id]);
            }

            return true;

        } catch (Exception $e) {
            throw new Exception("Không thể xóa biến thể: " . $e->getMessage());
        }
    }



}
