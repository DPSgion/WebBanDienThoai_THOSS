-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3308
-- Thời gian đã tạo: Th12 14, 2025 lúc 01:14 PM
-- Phiên bản máy phục vụ: 9.1.0
-- Phiên bản PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `bandienthoai`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `anh_san_pham`
--

DROP TABLE IF EXISTS `anh_san_pham`;
CREATE TABLE IF NOT EXISTS `anh_san_pham` (
  `id_anh` int NOT NULL AUTO_INCREMENT,
  `id_san_pham` int DEFAULT NULL,
  `duong_dan_anh` varchar(255) NOT NULL,
  PRIMARY KEY (`id_anh`),
  KEY `id_san_pham` (`id_san_pham`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `anh_san_pham`
--

INSERT INTO `anh_san_pham` (`id_anh`, `id_san_pham`, `duong_dan_anh`) VALUES
(7, 23, 'uploads/1765638267_samsung-galaxy-a17-5g.jpg'),
(8, 23, 'uploads/1765638267_samsung-galaxy-a17-5g-2.jpg'),
(9, 23, 'uploads/1765638267_samsung-galaxy-a17-5g-3.jpg'),
(10, 24, 'uploads/1765638530_z-fold7.jpg'),
(11, 24, 'uploads/1765638530_z-fold7-2.jpg'),
(12, 24, 'uploads/1765638530_z-fold7-3.jpg'),
(13, 25, 'uploads/1765638787_a56.jpg'),
(14, 25, 'uploads/1765638787_a56-2.jpg'),
(15, 25, 'uploads/1765638787_a56-3.jpg'),
(16, 26, 'uploads/1765639097_iphone17pro.jpg'),
(17, 26, 'uploads/1765639097_iphone17pro-1.jpg'),
(18, 26, 'uploads/1765639097_iphone17pro-2.jpg'),
(19, 27, 'uploads/1765639333_iphone16.jpg'),
(20, 27, 'uploads/1765639333_iphone16-1.jpg'),
(21, 27, 'uploads/1765639333_iphone16-2.jpg'),
(22, 28, 'uploads/1765639488_xiaomi15t.jpg'),
(23, 28, 'uploads/1765639488_xiaomi15t-1.jpg'),
(24, 28, 'uploads/1765639488_xiaomi15t-2.jpg'),
(25, 29, 'uploads/1765639637_xiaomi15.jpg'),
(26, 29, 'uploads/1765639637_xiaomi15-1.jpg'),
(27, 29, 'uploads/1765639637_xiaomi15-2.jpg'),
(28, 29, 'uploads/1765639637_xiaomi15-3.jpg'),
(29, 46, 'uploads/1765640912_17promax.jpg'),
(30, 46, 'uploads/1765640912_17promax-1.jpg'),
(31, 46, 'uploads/1765640912_17promax-2.jpg');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bien_the`
--

DROP TABLE IF EXISTS `bien_the`;
CREATE TABLE IF NOT EXISTS `bien_the` (
  `id_bien_the` int NOT NULL AUTO_INCREMENT,
  `id_san_pham` int DEFAULT NULL,
  `ram` varchar(20) DEFAULT NULL,
  `rom` varchar(20) DEFAULT NULL,
  `mau` varchar(50) DEFAULT NULL,
  `gia` decimal(10,2) NOT NULL,
  `so_luong_ton` int DEFAULT '0',
  PRIMARY KEY (`id_bien_the`),
  KEY `id_san_pham` (`id_san_pham`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `bien_the`
--

INSERT INTO `bien_the` (`id_bien_the`, `id_san_pham`, `ram`, `rom`, `mau`, `gia`, `so_luong_ton`) VALUES
(38, 26, '12GB', '256GB', 'Cam Vũ Trụ', 34990000.00, 7),
(39, 26, '12GB', '512GB', 'Cam Vũ Trụ', 41490000.00, 32),
(40, 26, '12GB', '1TB', 'Cam Vũ Trụ', 47990000.00, 5),
(41, 26, '12GB', '256GB', 'Xanh Đậm', 34990000.00, 12),
(42, 26, '12GB', '512GB', 'Xanh Đậm', 41490000.00, 24),
(43, 26, '12GB', '1TB', 'Xanh Đậm', 47990000.00, 1),
(46, 24, '12GB', '256GB', 'Xanh Navy', 46990000.00, 10),
(47, 24, '12GB', '512GB', 'Xanh Navy', 50990000.00, 12),
(48, 24, '12GB', '256GB', 'Đen', 46990000.00, 14),
(49, 24, '12GB', '512GB', 'Đen', 50990000.00, 16),
(50, 25, '12GB', '256GB', 'Xám', 10280000.00, 22),
(51, 25, '8GB', '128GB', 'Hồng', 9410000.00, 17),
(52, 25, '8GB', '256GB', 'Hồng', 9590000.00, 22),
(53, 25, '12GB', '256GB', 'Đen', 10280000.00, 22),
(54, 27, '8GB', '128GB', 'Xanh Lưu Ly', 21290000.00, 4),
(55, 27, '8GB', '128GB', 'Đen', 21290000.00, 0),
(56, 27, '8GB', '128GB', 'Hồng', 21290000.00, 21),
(57, 28, '12GB', '256GB', 'Vàng', 18490000.00, 11),
(58, 28, '12GB', '256GB', 'Xám', 18490000.00, 5),
(59, 28, '12GB', '256GB', 'Đen', 18490000.00, 14),
(60, 29, '6GB', '128GB', 'Xám', 4990000.00, 17),
(61, 29, '8GB', '128GB', 'Xám', 4990000.00, 24),
(94, 46, '12GB', '256GB', 'Cam Vũ Trụ', 37990000.00, 12),
(95, 46, '12GB', '512GB', 'Cam Vũ Trụ', 44490000.00, 7),
(98, 23, '8GB', '128GB', 'Xám', 6190000.00, 10),
(99, 23, '8GB', '256GB', 'Xám', 7090000.00, 10);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_don_hang`
--

DROP TABLE IF EXISTS `chi_tiet_don_hang`;
CREATE TABLE IF NOT EXISTS `chi_tiet_don_hang` (
  `id_ctdh` int NOT NULL AUTO_INCREMENT,
  `id_don_hang` int DEFAULT NULL,
  `id_bien_the` int DEFAULT NULL,
  `so_luong` int NOT NULL,
  `gia_luc_mua` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_ctdh`),
  KEY `id_don_hang` (`id_don_hang`),
  KEY `id_bien_the` (`id_bien_the`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `chi_tiet_don_hang`
--

INSERT INTO `chi_tiet_don_hang` (`id_ctdh`, `id_don_hang`, `id_bien_the`, `so_luong`, `gia_luc_mua`) VALUES
(5, 3, 95, 4, 44490000.00),
(6, 4, 55, 4, 21290000.00),
(7, 4, 58, 6, 18490000.00),
(8, 4, 38, 5, 34990000.00),
(9, 5, 55, 8, 21290000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_muc`
--

DROP TABLE IF EXISTS `danh_muc`;
CREATE TABLE IF NOT EXISTS `danh_muc` (
  `id_danh_muc` int NOT NULL AUTO_INCREMENT,
  `ten_danh_muc` varchar(100) NOT NULL,
  PRIMARY KEY (`id_danh_muc`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `danh_muc`
--

INSERT INTO `danh_muc` (`id_danh_muc`, `ten_danh_muc`) VALUES
(12, 'Samsung'),
(13, 'Iphone'),
(14, 'Xiaomi');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `don_hang`
--

DROP TABLE IF EXISTS `don_hang`;
CREATE TABLE IF NOT EXISTS `don_hang` (
  `id_don_hang` int NOT NULL AUTO_INCREMENT,
  `id_nguoi_dung` int DEFAULT NULL,
  `trang_thai` enum('choxuly','danggiao','hoanthanh','dahuy') DEFAULT 'choxuly',
  `tong_tien` bigint DEFAULT NULL,
  `dia_chi` varchar(255) DEFAULT NULL,
  `ngay_dat` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_don_hang`),
  KEY `id_nguoi_dung` (`id_nguoi_dung`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `don_hang`
--

INSERT INTO `don_hang` (`id_don_hang`, `id_nguoi_dung`, `trang_thai`, `tong_tien`, `dia_chi`, `ngay_dat`) VALUES
(3, 4, 'choxuly', 177960000, '341 Lạc Long Quân, Hồ Chí Minh, Quận 11', '2025-12-14 12:58:00'),
(4, 4, 'choxuly', 371050000, 'Địa chỉ nhận thứ 2, Hồ Chí Minh, Quận 12', '2025-12-14 13:05:06'),
(5, 4, 'choxuly', 170320000, 'Địa chỉ nhận thứ 2, Hồ Chí Minh, Quận 11', '2025-12-14 13:08:08');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `gio_hang`
--

DROP TABLE IF EXISTS `gio_hang`;
CREATE TABLE IF NOT EXISTS `gio_hang` (
  `id_gio_hang` int NOT NULL AUTO_INCREMENT,
  `id_nguoi_dung` int DEFAULT NULL,
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_gio_hang`),
  KEY `id_nguoi_dung` (`id_nguoi_dung`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `gio_hang`
--

INSERT INTO `gio_hang` (`id_gio_hang`, `id_nguoi_dung`, `ngay_tao`) VALUES
(1, 5, '2025-12-13 15:51:02');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `gio_hang_chi_tiet`
--

DROP TABLE IF EXISTS `gio_hang_chi_tiet`;
CREATE TABLE IF NOT EXISTS `gio_hang_chi_tiet` (
  `id_chi_tiet` int NOT NULL AUTO_INCREMENT,
  `id_gio_hang` int DEFAULT NULL,
  `id_bien_the` int DEFAULT NULL,
  `so_luong` int DEFAULT '1',
  PRIMARY KEY (`id_chi_tiet`),
  KEY `id_gio_hang` (`id_gio_hang`),
  KEY `id_bien_the` (`id_bien_the`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `gio_hang_chi_tiet`
--

INSERT INTO `gio_hang_chi_tiet` (`id_chi_tiet`, `id_gio_hang`, `id_bien_the`, `so_luong`) VALUES
(1, 1, 38, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoi_dung`
--

DROP TABLE IF EXISTS `nguoi_dung`;
CREATE TABLE IF NOT EXISTS `nguoi_dung` (
  `id_nguoi_dung` int NOT NULL AUTO_INCREMENT,
  `ho_ten` varchar(100) NOT NULL,
  `sdt` varchar(20) DEFAULT NULL,
  `mat_khau` varchar(255) NOT NULL,
  `vai_tro` enum('admin','khachhang') DEFAULT 'khachhang',
  PRIMARY KEY (`id_nguoi_dung`),
  UNIQUE KEY `sdt` (`sdt`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`id_nguoi_dung`, `ho_ten`, `sdt`, `mat_khau`, `vai_tro`) VALUES
(1, 'Admin', '123', '$2y$12$uUX9sJFZu1fjW5l6N1opCOlSO5T5CpZtyTWidRrClzKgmBiTBoE9q', 'admin'),
(3, 'Nguyễn Trọng Tín', '0123456789', '$2y$10$UCiIdU7YmE4l92.IBdg3meQMszpyaZk0MhDaRjqaYkzFQfLvrABt2', 'khachhang'),
(4, 'Nguyễn Trọng Tín', '0792132904', '$2y$10$QbURf0xZfrdtWVE3F6n81ODpPz1.GrqBxLsqVm4hrKkuZp/KV7.Qe', 'khachhang'),
(5, 'Nguyễn Đình Phương', '0123456781', '$2y$12$2LjaP7dTJVJ52d2aI25gJeLo5tKQRz5BjappuWpr1Jwa8EkkZKlJO', 'khachhang');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `san_pham`
--

DROP TABLE IF EXISTS `san_pham`;
CREATE TABLE IF NOT EXISTS `san_pham` (
  `id_san_pham` int NOT NULL AUTO_INCREMENT,
  `id_danh_muc` int NOT NULL,
  `ten_san_pham` varchar(255) NOT NULL,
  `cpu` varchar(100) DEFAULT NULL,
  `pin` varchar(50) DEFAULT NULL,
  `man_hinh` varchar(100) DEFAULT NULL,
  `os` varchar(50) DEFAULT NULL,
  `camera_truoc` varchar(50) DEFAULT NULL,
  `camera_sau` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_san_pham`),
  KEY `id_danh_muc` (`id_danh_muc`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `san_pham`
--

INSERT INTO `san_pham` (`id_san_pham`, `id_danh_muc`, `ten_san_pham`, `cpu`, `pin`, `man_hinh`, `os`, `camera_truoc`, `camera_sau`) VALUES
(23, 12, 'Samsung Galaxy A17 5G', 'Exynos 1330', '5000 mAh', '6.7\" - Tần số quét 90 Hz', 'android', '13 MP', 'Chính 50 MP & Phụ 5 MP, 2 MP'),
(24, 12, 'Samsung Galaxy Z Fold7 5G', 'Qualcomm Snapdragon 8 Elite For Galaxy 8 nhân', '4400 mAh', 'Chính 8.0\" & Phụ 6.5\" - Tần số quét 120 Hz', 'android', 'Trong 10 MP & Ngoài 10 MP', 'Chính 200 MP & Phụ 12 MP, 10 MP'),
(25, 12, 'Samsung Galaxy A56 5G', 'Exynos 1580 8 nhân', '5000 mAh', '6.7\" - Tần số quét 120 Hz', 'android', '12 MP', 'Chính 50 MP & Phụ 12 MP, 5 MP'),
(26, 13, 'iPhone 17 Pro', 'Apple A19 Pro 6 nhân', '31 giờ', '6.3\" - Tần số quét 120 Hz', 'ios', '18 MP', 'Chính 48 MP & Phụ 48 MP, 48 MP'),
(27, 13, 'iPhone 16', 'Apple A18 6 nhân', '3561mAh', '6.1\" - Tần số quét 60 Hz', 'ios', '12 MP', 'Chính 48 MP & Phụ 12 MP'),
(28, 14, 'Xiaomi 15T Pro 5G', 'MediaTek Dimensity 9400+ 8 nhân', ' 5500 mAh', '6.83\" - Tần số quét 144 Hz', 'android', '32 MP', 'Chính 50 MP & Phụ 50 MP, 12 MP'),
(29, 14, 'Xiaomi Redmi 15', 'Snapdragon 685 8 nhân', '7000 mAh', '6.9\" - Tần số quét 144 Hz', 'android', '8 MP', 'Chính 50 MP & Phụ QVGA'),
(46, 13, 'iPhone 17 Pro Max', 'Apple A19 Pro 6 nhân', '4832mAh', '6.9\" - Tần số quét 120 Hz', 'ios', '18 MP', 'Chính 48 MP & Phụ 48 MP, 48 MP');

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `anh_san_pham`
--
ALTER TABLE `anh_san_pham`
  ADD CONSTRAINT `anh_san_pham_ibfk_1` FOREIGN KEY (`id_san_pham`) REFERENCES `san_pham` (`id_san_pham`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `bien_the`
--
ALTER TABLE `bien_the`
  ADD CONSTRAINT `bien_the_ibfk_1` FOREIGN KEY (`id_san_pham`) REFERENCES `san_pham` (`id_san_pham`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `chi_tiet_don_hang`
--
ALTER TABLE `chi_tiet_don_hang`
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_1` FOREIGN KEY (`id_don_hang`) REFERENCES `don_hang` (`id_don_hang`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `chi_tiet_don_hang_ibfk_2` FOREIGN KEY (`id_bien_the`) REFERENCES `bien_the` (`id_bien_the`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `don_hang`
--
ALTER TABLE `don_hang`
  ADD CONSTRAINT `don_hang_ibfk_1` FOREIGN KEY (`id_nguoi_dung`) REFERENCES `nguoi_dung` (`id_nguoi_dung`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `gio_hang`
--
ALTER TABLE `gio_hang`
  ADD CONSTRAINT `gio_hang_ibfk_1` FOREIGN KEY (`id_nguoi_dung`) REFERENCES `nguoi_dung` (`id_nguoi_dung`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `gio_hang_chi_tiet`
--
ALTER TABLE `gio_hang_chi_tiet`
  ADD CONSTRAINT `gio_hang_chi_tiet_ibfk_1` FOREIGN KEY (`id_gio_hang`) REFERENCES `gio_hang` (`id_gio_hang`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gio_hang_chi_tiet_ibfk_2` FOREIGN KEY (`id_bien_the`) REFERENCES `bien_the` (`id_bien_the`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `san_pham`
--
ALTER TABLE `san_pham`
  ADD CONSTRAINT `san_pham_ibfk_1` FOREIGN KEY (`id_danh_muc`) REFERENCES `danh_muc` (`id_danh_muc`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
