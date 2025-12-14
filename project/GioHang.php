<?php
// 1. Start Session & Config
// Kiểm tra session status trước khi start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/config.php';

// 2. LOGIC GIỎ HÀNG (Backend)
$id_user = $_SESSION['id_nguoi_dung'] ?? '';

$sql = "SELECT ghct.*,bt.gia,sp.ten_san_pham, asp.duong_dan_anh
FROM gio_hang_chi_tiet ghct
JOIN bien_the bt ON ghct.id_bien_the = bt.id_bien_the
JOIN san_pham sp ON bt.id_san_pham = sp.id_san_pham
LEFT JOIN anh_san_pham asp 
    ON asp.id_san_pham = sp.id_san_pham
    AND asp.id_anh = (
        SELECT MIN(id_anh)
        FROM anh_san_pham
        WHERE id_san_pham = sp.id_san_pham
    )
WHERE ghct.id_gio_hang = (
    SELECT id_gio_hang 
    FROM gio_hang 
    WHERE id_nguoi_dung = ? 
    LIMIT 1
);";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_user]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
//
// Nếu chưa đăng nhập thì chặn hoặc xử lý tùy ý (ở đây mình để trống thì query sẽ không ra gì)
if (empty($id_user)) {
    // Có thể chuyển hướng về login: header("Location: login.php"); exit;
}

// Xử lý XÓA sản phẩm
if (isset($_GET['delete'])) {
  $id_delete = $_GET['delete'];
  // Xóa trong database
  $sql = "DELETE FROM gio_hang_chi_tiet WHERE id_chi_tiet = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$id_delete]);
  // Load lại trang để cập nhật danh sách
  header("Location: GioHang.php");
  exit();
}

// Lấy danh sách sản phẩm trong giỏ
$sql = "SELECT ghct.*, bt.gia, sp.ten_san_pham, asp.duong_dan_anh, bt.rom, bt.mau, bt.so_luong_ton
        FROM gio_hang_chi_tiet ghct
        JOIN bien_the bt ON ghct.id_bien_the = bt.id_bien_the
        JOIN san_pham sp ON bt.id_san_pham = sp.id_san_pham
        LEFT JOIN anh_san_pham asp ON asp.id_san_pham = sp.id_san_pham
        WHERE ghct.id_gio_hang = (SELECT id_gio_hang FROM gio_hang WHERE id_nguoi_dung = ? LIMIT 1)
        GROUP BY ghct.id_chi_tiet";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_user]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="vi">

<body>

    <?php require_once './includes/header.php'; ?>

    <link rel="stylesheet" href="assets/css/stylesCart.css">

    <main class="container cart-page">
        <div class="cart-wrapper">
            <div class="cart-header">
                <h2>GIỎ HÀNG CỦA BẠN <span class="lock">🔒</span></h2>
            </div>

            <div class="select-all">
                <h4><input type="checkbox" id="selectAll"> TẤT CẢ</h4>
            </div>

            <div class="cart-list" id="cartList">
                <?php if (!empty($cart_items)):
          foreach ($cart_items as $item): ?>
                <div class="cart-item" data-price="<?= $item['gia'] ?>">
                    <div class="item-left">
                        <img src="<?= !empty($item['duong_dan_anh']) ? $item['duong_dan_anh'] : 'assets/images/no-image.png' ?>"
                            alt="" class="item-thumb">
                    </div>
                    <div class="item-mid">
                        <div class="item-name">
                            <a href="ChiTietSanPham.php?id=<?= /* Bạn cần lấy id_san_pham nếu muốn link */ '#' ?>"
                                style="text-decoration:none; color:inherit;">
                                <?= htmlspecialchars($item['ten_san_pham']) ?>
                            </a>
                        </div>
                        <div style="font-size: 0.9em; color: #666; margin-bottom: 5px;">
                            Phân loại:
                            <?= $item['rom'] ?> -
                            <?= ucfirst($item['mau']) ?>
                        </div>

                        <div class="item-price price-red">
                            <?= $item['gia'] === null ? 'Liên hệ' : number_format($item['gia'], 0, ',', '.') . 'đ' ?>
                        </div>

                        <div class="item-controls">
                            <div class="qty-box">
                                <button class="qty-btn qty-minus" data-id="<?= $item['id_chi_tiet'] ?>"
                                    data-stock="<?= $item['so_luong_ton'] ?>">−</button>

                                <input class="qty-input" type="number" value="<?= $item['so_luong'] ?>" min="1"
                                    max="<?= $item['so_luong_ton'] ?>" data-id="<?= $item['id_chi_tiet'] ?>"
                                    data-stock="<?= $item['so_luong_ton'] ?>">

                                <button class="qty-btn qty-plus" data-id="<?= $item['id_chi_tiet'] ?>"
                                    data-stock="<?= $item['so_luong_ton'] ?>">+</button>
                            </div>
                            <div style="font-size: 11px; color: #666; margin-top: 5px;">
                                Trong kho còn:
                                <?= $item['so_luong_ton'] ?>
                            </div>
                        </div>
                    </div>

                    <div class="item-right">
                        <button class="del" data-key="<?= $item['id_chi_tiet'] ?>">×</button>
                        <label class="select-wrap">
                            <input type="checkbox" class="select-item" value="<?= $item['id_chi_tiet'] ?>"> Chọn
                        </label>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <div style="text-align: center; padding: 50px;">
                    <p>Giỏ hàng hiện đang trống</p>
                    <a href="TrangChu.php" class="btn">Tiếp tục mua sắm</a>
                </div>
                <?php endif; ?>

                <div class="cart-footer">
                    <div class="summary">
                        <button id="totalBtn" class="btn total">TỔNG CỘNG: 0 VND</button>
                        <form id="checkoutForm" action="ThanhToan.php" method="POST">
                            <input type="hidden" name="selected_items" id="selectedItems">
                            <button type="submit" class="btn checkout">THANH TOÁN</button>
                        </form>
                    </div>
                </div>
            </div>
    </main>

    <?php require_once './includes/footer.php'; ?>

    <script>
    // Hàm định dạng tiền tệ
    function formatVND(n) {
        return Number(n).toLocaleString('vi-VN') + 'đ';
    }

    // --- HÀM GỬI AJAX CẬP NHẬT DB ---
    function updateCartDatabase(idChiTiet, newQty, inputElement) {
        // Tạo dữ liệu gửi đi
        let formData = new FormData();
        formData.append('id_chi_tiet', idChiTiet);
        formData.append('qty', newQty);

        // Gửi đến file PHP bạn vừa tạo ở Bước 1
        fetch('includes/functionsKhachHang/api_update_cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                console.log('Đã lưu vào DB:', data.message);
                
                // Nếu server trả về số lượng khác (do bị giới hạn kho), cập nhật lại ô input
                if (parseInt(inputElement.value) !== data.fixed_qty) {
                    inputElement.value = data.fixed_qty;
                    alert("Số lượng đã được điều chỉnh về mức tối đa trong kho!");
                }
                
                // Tính lại tổng tiền sau khi cập nhật thành công
                computeTotal();
            } else {
                console.error('Lỗi:', data.message);
                alert('Có lỗi khi cập nhật giỏ hàng: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    // --- HÀM TÍNH TỔNG TIỀN TRÊN GIAO DIỆN ---
    function computeTotal() {
        let sum = 0;
        document.querySelectorAll('.cart-item').forEach(item => {
            const chk = item.querySelector('.select-item');
            if (chk && chk.checked) {
                const price = Number(item.dataset.price || 0);
                const input = item.querySelector('.qty-input');
                const qty = Number(input.value || 1);
                sum += price * qty;
            }
        });
        document.getElementById('totalBtn').textContent = 'TỔNG CỘNG: ' + formatVND(sum);
    }

    // --- SỰ KIỆN CLICK (Uỷ quyền sự kiện) ---
    const cartList = document.getElementById('cartList');
    
    cartList.addEventListener('click', function(e) {
        // 1. Xử lý nút CỘNG
        if (e.target.matches('.qty-plus')) {
            const btn = e.target;
            const input = btn.parentElement.querySelector('.qty-input');
            const maxStock = parseInt(btn.dataset.stock);
            const idDetail = btn.dataset.id;
            
            let currentQty = parseInt(input.value);
            
            if (currentQty < maxStock) {
                input.value = currentQty + 1; // Tăng trên giao diện trước cho mượt
                updateCartDatabase(idDetail, input.value, input); // Lưu vào DB
            } else {
                alert(`Chỉ còn ${maxStock} sản phẩm!`);
            }
        }

        // 2. Xử lý nút TRỪ
        if (e.target.matches('.qty-minus')) {
            const btn = e.target;
            const input = btn.parentElement.querySelector('.qty-input');
            const idDetail = btn.dataset.id;
            
            let currentQty = parseInt(input.value);
            
            if (currentQty > 1) {
                input.value = currentQty - 1; // Giảm trên giao diện
                updateCartDatabase(idDetail, input.value, input); // Lưu vào DB
            }
        }
        
        // 3. Xử lý nút XÓA
        if (e.target.matches('.del')) {
            if(!confirm("Bạn chắc chắn muốn xóa?")) {
                e.preventDefault();
            } else {
                 const key = e.target.dataset.key;
                 window.location.href = "GioHang.php?delete=" + key;
            }
        }
    });

    // --- SỰ KIỆN NHẬP TAY VÀO Ô INPUT ---
    cartList.addEventListener('change', function(e) {
        if (e.target.matches('.qty-input')) {
            const input = e.target;
            const maxStock = parseInt(input.dataset.stock);
            const idDetail = input.dataset.id;
            let val = parseInt(input.value);

            // Validate số nhập vào
            if (isNaN(val) || val < 1) val = 1;
            if (val > maxStock) {
                val = maxStock;
                alert(`Kho chỉ còn ${maxStock} sản phẩm!`);
            }

            input.value = val;
            // Lưu vào DB ngay khi nhập xong
            updateCartDatabase(idDetail, val, input);
        }
        
        // Checkbox chọn sản phẩm
        if (e.target.matches('.select-item')) {
            computeTotal();
        }
    });

    // Select All logic
    document.getElementById('selectAll').addEventListener('change', function() {
        const isChecked = this.checked;
        document.querySelectorAll('.select-item').forEach(c => c.checked = isChecked);
        computeTotal();
    });

    // Tính tổng lần đầu
    computeTotal();

    // Form Submit (Thanh toán)
    document.getElementById("checkoutForm").addEventListener("submit", function(e) {
        let ids = [];
        document.querySelectorAll(".select-item:checked").forEach(chk => {
            ids.push(chk.value);
        });
        if (ids.length === 0) {
            alert("Vui lòng chọn sản phẩm để thanh toán.");
            e.preventDefault();
            return;
        }
        document.getElementById("selectedItems").value = JSON.stringify(ids);
    });
</script>

</body>

</html>