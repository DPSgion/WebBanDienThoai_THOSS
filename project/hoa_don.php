<?php
session_start();
require_once 'config/config.php';

$ma_don = $_GET["id"] ?? 0;

// Lấy thông tin đơn hàng
$sql = "SELECT * FROM don_hang WHERE id_don_hang = ? AND id_nguoi_dung = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$ma_don, $_SESSION["id_nguoi_dung"]]);
$don = $stmt->fetch();

// Lấy chi tiết đơn
$sqlCT = "SELECT sp.ten_san_pham, bt.rom, bt.mau, ctdh.so_luong, ctdh.gia_luc_mua  FROM chi_tiet_don_hang ctdh
join bien_the bt on bt.id_bien_the = ctdh.id_bien_the
join san_pham sp on sp.id_san_pham = bt.id_san_pham
 WHERE id_don_hang = ?";
$stmt = $pdo->prepare($sqlCT);
$stmt->execute([$ma_don]);
$ct = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Hóa đơn #<?= $ma_don ?></title>
    <style>
        body {
            font-family: Arial;
            margin: 30px;
        }

        .invoice {
            width: 700px;
            margin: auto;
            border: 1px solid #ddd;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .right {
            text-align: right;
        }

        .print-btn {
            margin: 20px 0;
            padding: 10px 20px;
            background: blue;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <div class="invoice">

        <h2>HÓA ĐƠN MUA HÀNG</h2>
        <p><b>Mã đơn:</b> <?= $don["id_don_hang"] ?></p>
        <p><b>Ngày đặt:</b> <?= $don["ngay_dat"] ?></p>
        <p><b>Khách hàng:</b> <?= $_SESSION["ho_ten"] ?></p>
        <p><b>Địa chỉ giao:</b> <?= $don['dia_chi'] ?></p>

        <table>
            <tr>
                <th>Sản phẩm</th>
                <th>ROM</th>
                <th>Màu</th>
                <th>SL</th>
                <th>Giá</th>
            </tr>

            <?php foreach ($ct as $row): ?>
                <tr>
                    <td><?= $row["ten_san_pham"] ?></td>
                    <td><?= $row["rom"] ?></td>
                    <td><?= $row["mau"] ?></td>
                    <td><?= $row["so_luong"] ?></td>
                    <td><?= number_format($row["gia_luc_mua"]) ?>đ</td>
                </tr>
            <?php endforeach; ?>

        </table>

        <h3 class="right">Tổng tiền: <?= number_format($don["tong_tien"]) ?>đ</h3>

        <button class="print-btn" onclick="window.print()">In hóa đơn</button>
    </div>

</body>

</html>