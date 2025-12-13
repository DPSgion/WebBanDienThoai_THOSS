<?php
require_once __DIR__ . '/../../config/config.php';

class ProductModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function addProduct($data, $images) {
        try {
            $this->pdo->beginTransaction();

            // 1) thêm sản phẩm
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


            // 2) thêm biến thể
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


            // 3) upload hình
            if ($images && isset($images['name'])) {

                $uploadPath = __DIR__ . '/../../uploads/';
                if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

                $stmtImg = $this->pdo->prepare(
                    "INSERT INTO anh_san_pham (id_san_pham, duong_dan_anh)
                    VALUES (?, ?)"
                );

                for ($i = 0; $i < count($images['name']); $i++) {

                    $tmp = $images['tmp_name'][$i];
                    $name = time() . "_" . basename($images['name'][$i]);
                    $dest = $uploadPath . $name;


                    if (!move_uploaded_file($tmp, $dest)) {
                        throw new Exception("Upload ảnh thất bại");
                    }

                    // lưu đường dẫn vào DB
                    $stmtImg->execute([$product_id, $name]);
                }
            }


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

            // 3) Nếu hết biến thể → xóa luôn sản phẩm và hình ảnh trong folder uploads
            if ($count == 0) {
                // Xóa ảnh trong bảng + file ảnh
                $stmtImg = $this->pdo->prepare("SELECT duong_dan_anh FROM anh_san_pham WHERE id_san_pham = ?");
                $stmtImg->execute([$product_id]);
                $images = $stmtImg->fetchAll(PDO::FETCH_ASSOC);

                // Xóa file
                $uploadPath = __DIR__ . '/../../uploads/';

                foreach ($images as $img) {
                    $file = $uploadPath . $img['duong_dan_anh'];
                    if (file_exists($file)) {
                        unlink($file);
                    }
                }

                $stmtDelSP = $this->pdo->prepare("DELETE FROM san_pham WHERE id_san_pham = ?");
                $stmtDelSP->execute([$product_id]);
            }

            return true;

        } catch (Exception $e) {
            throw new Exception("Không thể xóa biến thể: " . $e->getMessage());
        }
    }

        // TRONG CLASS ProductModel (thêm 2 phương thức dưới đây)

    public function getProductById($id_san_pham) {
        // lấy thông tin sản phẩm
        $stmt = $this->pdo->prepare("
            SELECT id_san_pham, id_danh_muc, ten_san_pham, cpu, pin, man_hinh, os, camera_truoc, camera_sau
            FROM san_pham
            WHERE id_san_pham = ?
        ");
        $stmt->execute([$id_san_pham]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$product) throw new Exception("Sản phẩm không tồn tại");

        // lấy biến thể
        $stmt = $this->pdo->prepare("SELECT id_bien_the, ram, rom, mau, gia, so_luong_ton FROM bien_the WHERE id_san_pham = ? ORDER BY id_bien_the ASC");
        $stmt->execute([$id_san_pham]);
        $variants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // lấy ảnh
        $stmt = $this->pdo->prepare("SELECT id_anh, duong_dan_anh FROM anh_san_pham WHERE id_san_pham = ?");
        $stmt->execute([$id_san_pham]);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'product' => $product,
            'variants' => $variants,
            'images' => $images
        ];
    }


    /**
     * updateProduct: cập nhật sản phẩm
     * - $keepImages: array of filenames (duong_dan_anh) that should be kept
     * - $images: $_FILES['images'] (new uploads)
     *
     * Strategy:
     * 1) update san_pham
     * 2) delete all bien_the for product_id (you chose cách 2)
     * 3) insert new variants from $data['options']
     * 4) find existing images in DB; remove those NOT in $keepImages (unlink + delete DB record)
     * 5) store uploaded files and insert into anh_san_pham
     */
    public function updateProduct($product_id, $data, $images, $keepImages = []) {
        try {
            $this->pdo->beginTransaction();

            // 1) update san_pham
            $stmt = $this->pdo->prepare("
                UPDATE san_pham SET
                    id_danh_muc = :id_category,
                    ten_san_pham = :name,
                    cpu = :cpu,
                    pin = :pin,
                    man_hinh = :screen,
                    os = :os,
                    camera_truoc = :front_cam,
                    camera_sau = :rear_cam
                WHERE id_san_pham = :id_san_pham
            ");
            $stmt->execute([
                ':id_category' => $data['id_category'],
                ':name' => $data['name'],
                ':cpu' => $data['cpu'],
                ':pin' => $data['pin'],
                ':screen' => $data['screen'],
                ':os' => $data['os'],
                ':front_cam' => $data['front_cam'],
                ':rear_cam' => $data['rear_cam'],
                ':id_san_pham' => $product_id
            ]);

            // 2) reset variants (xóa tất cả rồi insert mới)
            $stmtDelVar = $this->pdo->prepare("DELETE FROM bien_the WHERE id_san_pham = ?");
            $stmtDelVar->execute([$product_id]);

            $stmtInsVar = $this->pdo->prepare("
                INSERT INTO bien_the (id_san_pham, ram, rom, mau, gia, so_luong_ton)
                VALUES (:id_san_pham, :ram, :rom, :mau, :gia, :so_luong)
            ");

            foreach ($data['options'] as $opt) {
                // validate minimal
                if (trim($opt['ram']) === '' || trim($opt['rom']) === '' || trim($opt['color']) === '') {
                    $this->pdo->rollBack();
                    throw new Exception("Dữ liệu biến thể không hợp lệ");
                }
                $stmtInsVar->execute([
                    ':id_san_pham' => $product_id,
                    ':ram' => $opt['ram'],
                    ':rom' => $opt['rom'],
                    ':mau' => $opt['color'],
                    ':gia' => $opt['price'],
                    ':so_luong' => $opt['quantity']
                ]);
            }

            // 3) xử lý ảnh: lấy danh sách ảnh hiện có
            $stmt = $this->pdo->prepare("SELECT id_anh, duong_dan_anh FROM anh_san_pham WHERE id_san_pham = ?");
            $stmt->execute([$product_id]);
            $existing = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $uploadPath = __DIR__ . '/../../uploads/';

            // normalize keepImages (filenames)
            $keep = array_values($keepImages);

            // find to-delete
            $toDelete = [];
            foreach ($existing as $row) {
                if (!in_array($row['duong_dan_anh'], $keep, true)) {
                    $toDelete[] = $row;
                }
            }

            // delete files + db rows for those
            if (!empty($toDelete)) {
                $stmtDelImg = $this->pdo->prepare("DELETE FROM anh_san_pham WHERE id_anh = ?");
                foreach ($toDelete as $d) {
                    $file = $uploadPath . $d['duong_dan_anh'];
                    if (file_exists($file)) @unlink($file);
                    $stmtDelImg->execute([$d['id_anh']]);
                }
            }

            // 4) save uploaded new images (if any)
            if ($images && isset($images['name'])) {
                $stmtInsImg = $this->pdo->prepare("INSERT INTO anh_san_pham (id_san_pham, duong_dan_anh) VALUES (?, ?)");
                $uploadPath = __DIR__ . '/../../uploads/';
                if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

                for ($i = 0; $i < count($images['name']); $i++) {
                    if ($images['error'][$i] !== UPLOAD_ERR_OK) continue;

                    $tmp = $images['tmp_name'][$i];
                    $original = basename($images['name'][$i]); // tên gốc
                    $name = time() . "_" . $original; // time() + tên gốc
                    $dest = $uploadPath . $name;

                    if (!move_uploaded_file($tmp, $dest)) {
                        $this->pdo->rollBack();
                        throw new Exception("Upload ảnh thất bại");
                    }

                    $stmtInsImg->execute([$product_id, $name]);
                }
            }


            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }


}
