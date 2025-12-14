<?php
// includes/models/DonHangModel.php
require_once __DIR__ . '/../../config/config.php';

class DonHangModel {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    // 1. Lấy danh sách (Cho bảng bên ngoài)
    public function getAll($keyword = '', $status = 'all') {
        $sql = "SELECT dh.*, nd.ho_ten, nd.sdt 
                FROM don_hang dh
                JOIN nguoi_dung nd ON dh.id_nguoi_dung = nd.id_nguoi_dung
                WHERE 1=1";
        
        $params = [];

        if ($status !== 'all' && !empty($status)) {
            $sql .= " AND dh.trang_thai = ?";
            $params[] = $status;
        }

        if (!empty($keyword)) {
            $sql .= " AND (dh.id_don_hang LIKE ? OR nd.ho_ten LIKE ? OR nd.sdt LIKE ?)";
            $keyword = "%$keyword%";
            $params[] = $keyword;
            $params[] = $keyword;
            $params[] = $keyword;
        }

        $sql .= " ORDER BY dh.ngay_dat DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Lấy thông tin người mua (Cho Modal)
    public function getOrderInfo($id) {
        // Chú ý: Cột dia_chi nằm ở bảng don_hang theo CSDL bạn đưa
        $sql = "SELECT dh.*, nd.ho_ten, nd.sdt
                FROM don_hang dh
                JOIN nguoi_dung nd ON dh.id_nguoi_dung = nd.id_nguoi_dung
                WHERE dh.id_don_hang = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 3. Lấy danh sách sản phẩm (Cho Modal)
    public function getOrderItems($id) {
        // Join 3 bảng: chi_tiet -> bien_the -> san_pham
        $sql = "SELECT ct.*, bt.mau, bt.ram, bt.rom, sp.ten_san_pham
                FROM chi_tiet_don_hang ct
                JOIN bien_the bt ON ct.id_bien_the = bt.id_bien_the
                JOIN san_pham sp ON bt.id_san_pham = sp.id_san_pham
                WHERE ct.id_don_hang = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. Cập nhật trạng thái
    public function updateStatus($id_don_hang, $status_new) {
        try {
            $this->pdo->beginTransaction();

            // 1. Lấy trạng thái CŨ của đơn hàng để kiểm tra
            $stmt = $this->pdo->prepare("SELECT trang_thai FROM don_hang WHERE id_don_hang = ?");
            $stmt->execute([$id_don_hang]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                throw new Exception("Đơn hàng không tồn tại");
            }

            $status_old = $order['trang_thai'];

            // 2. Logic hoàn kho: 
            // Nếu trạng thái MỚI là 'dahuy' VÀ trạng thái CŨ KHÔNG PHẢI 'dahuy'
            // (Tránh trường hợp hủy đi hủy lại bị cộng dồn kho nhiều lần)
            if ($status_new == 'dahuy' && $status_old != 'dahuy') {
                
                // Lấy danh sách sản phẩm trong chi tiết đơn hàng
                $stmtDetail = $this->pdo->prepare("SELECT id_bien_the, so_luong FROM chi_tiet_don_hang WHERE id_don_hang = ?");
                $stmtDetail->execute([$id_don_hang]);
                $items = $stmtDetail->fetchAll(PDO::FETCH_ASSOC);

                // Duyệt qua từng sản phẩm để cộng lại kho
                foreach ($items as $item) {
                    $stmtUpdateStock = $this->pdo->prepare("
                        UPDATE bien_the 
                        SET so_luong_ton = so_luong_ton + :so_luong 
                        WHERE id_bien_the = :id_bien_the
                    ");
                    
                    $stmtUpdateStock->execute([
                        ':so_luong' => $item['so_luong'],
                        ':id_bien_the' => $item['id_bien_the']
                    ]);
                }
            }

            // 3. Cập nhật trạng thái đơn hàng
            $stmtUpdate = $this->pdo->prepare("UPDATE don_hang SET trang_thai = ? WHERE id_don_hang = ?");
            $result = $stmtUpdate->execute([$status_new, $id_don_hang]);

            if (!$result) {
                throw new Exception("Lỗi khi cập nhật trạng thái");
            }

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            // Ghi log lỗi nếu cần
            return false;
        }
    }
}
?>