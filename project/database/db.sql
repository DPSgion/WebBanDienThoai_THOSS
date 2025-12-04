-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3308
-- Thời gian đã tạo: Th12 04, 2025 lúc 03:48 PM
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `anh_san_pham`
--

INSERT INTO `anh_san_pham` (`id_anh`, `id_san_pham`, `duong_dan_anh`) VALUES
(1, 13, 'uploads/products/iphone 13 pro max.webp'),
(2, 14, 'uploads/products/iphone 15 pro max.webp'),
(3, 15, 'uploads/products/iphone 16 pro max.webp'),
(4, 16, 'uploads/products/iphone13 pro max.webp'),
(5, 17, 'uploads/products/iphone17.webp');

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
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `bien_the`
--

INSERT INTO `bien_the` (`id_bien_the`, `id_san_pham`, `ram`, `rom`, `mau`, `gia`, `so_luong_ton`) VALUES
(21, 13, '8GB', '256GB', 'Titan Natural', 29990000.00, 10),
(22, 13, '8GB', '512GB', 'Titan Blue', 33990000.00, 8),
(23, 14, '6GB', '128GB', 'Black', 21990000.00, 12),
(24, 14, '6GB', '256GB', 'Pink', 23990000.00, 5),
(25, 15, '6GB', '256GB', 'Purple', 27990000.00, 7),
(26, 16, '4GB', '128GB', 'Red', 14990000.00, 15),
(27, 22, '4GB', '64GB', 'White', 9990000.00, 20);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_muc`
--

DROP TABLE IF EXISTS `danh_muc`;
CREATE TABLE IF NOT EXISTS `danh_muc` (
  `id_danh_muc` int NOT NULL AUTO_INCREMENT,
  `ten_danh_muc` varchar(100) NOT NULL,
  PRIMARY KEY (`id_danh_muc`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `danh_muc`
--

INSERT INTO `danh_muc` (`id_danh_muc`, `ten_danh_muc`) VALUES
(5, 'Xiaomi'),
(6, 'Iphone'),
(8, 'SSS'),
(9, 'iPhone'),
(10, 'Samsung'),
(11, 'Xiaomi');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `don_hang`
--

DROP TABLE IF EXISTS `don_hang`;
CREATE TABLE IF NOT EXISTS `don_hang` (
  `id_don_hang` int NOT NULL AUTO_INCREMENT,
  `id_nguoi_dung` int DEFAULT NULL,
  `trang_thai` enum('choxuly','danggiao','hoanthanh','dahuy') DEFAULT 'choxuly',
  `tong_tien` decimal(10,2) DEFAULT NULL,
  `dia_chi` varchar(255) DEFAULT NULL,
  `ngay_dat` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_don_hang`),
  KEY `id_nguoi_dung` (`id_nguoi_dung`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`id_nguoi_dung`, `ho_ten`, `sdt`, `mat_khau`, `vai_tro`) VALUES
(1, 'Admin', '123', '$2y$12$uUX9sJFZu1fjW5l6N1opCOlSO5T5CpZtyTWidRrClzKgmBiTBoE9q', 'admin'),
(3, 'Nguyễn Trọng Tín', '0123456789', '$2y$10$UCiIdU7YmE4l92.IBdg3meQMszpyaZk0MhDaRjqaYkzFQfLvrABt2', 'khachhang');

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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `san_pham`
--

INSERT INTO `san_pham` (`id_san_pham`, `id_danh_muc`, `ten_san_pham`, `cpu`, `pin`, `man_hinh`, `os`, `camera_truoc`, `camera_sau`) VALUES
(13, 6, 'iPhone 15 Pro Max', 'A17 Pro', '4422 mAh', '6.7\" OLED 120Hz', 'iOS 17', '12MP', '48MP'),
(14, 6, 'iPhone 15', 'A16 Bionic', '3877 mAh', '6.1\" OLED', 'iOS 17', '12MP', '48MP'),
(15, 6, 'iPhone 14 Pro Max', 'A16 Bionic', '4323 mAh', '6.7\" OLED 120Hz', 'iOS 16', '12MP', '48MP'),
(16, 6, 'iPhone 13', 'A15 Bionic', '3227 mAh', '6.1\" OLED', 'iOS 15', '12MP', '12MP'),
(17, 6, 'iPhone SE 2022', 'A15 Bionic', '2018 mAh', '4.7\" LCD', 'iOS 15', '7MP', '12MP'),
(18, 6, 'iPhone 15 Pro Max', 'A17 Pro', '4422 mAh', '6.7\" OLED 120Hz', 'iOS 17', '12MP', '48MP'),
(19, 6, 'iPhone 15', 'A16 Bionic', '3877 mAh', '6.1\" OLED', 'iOS 17', '12MP', '48MP'),
(20, 6, 'iPhone 14 Pro Max', 'A16 Bionic', '4323 mAh', '6.7\" OLED 120Hz', 'iOS 16', '12MP', '48MP'),
(21, 6, 'iPhone 13', 'A15 Bionic', '3227 mAh', '6.1\" OLED', 'iOS 15', '12MP', '12MP'),
(22, 6, 'iPhone SE 2022', 'A15 Bionic', '2018 mAh', '4.7\" LCD', 'iOS 15', '7MP', '12MP');

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
