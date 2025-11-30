-- =======================================
-- CSDL: He thong ban hang (co phan quyen)
-- =======================================

DROP DATABASE IF EXISTS banhang;
CREATE DATABASE banhang;
USE banhang;

-- =======================================
-- 1. BANG VAI TRO (ROLE)
-- =======================================
CREATE TABLE vai_tro (
    id_vai_tro INT AUTO_INCREMENT PRIMARY KEY,
    ten_vai_tro VARCHAR(50) NOT NULL   -- vi du: admin, nhanvien, khachhang
) ENGINE=InnoDB;

-- ============================
-- THEM DU LIEU MAC DINH
-- ============================
INSERT INTO vai_tro (ten_vai_tro) VALUES
('admin'),
('nhanvien'),
('khachhang');

-- =======================================
-- 2. BANG NGUOI DUNG
-- =======================================
CREATE TABLE nguoi_dung (
    id_nguoi_dung INT AUTO_INCREMENT PRIMARY KEY,
    id_vai_tro INT,
    ho_ten VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    mat_khau VARCHAR(255),
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_vai_tro) REFERENCES vai_tro(id_vai_tro)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =======================================
-- 3. BANG DANH MUC
-- =======================================
CREATE TABLE danh_muc (
    id_danh_muc INT AUTO_INCREMENT PRIMARY KEY,
    ten_danh_muc VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- =======================================
-- 4. BANG SAN PHAM
-- =======================================
CREATE TABLE san_pham (
    id_san_pham INT AUTO_INCREMENT PRIMARY KEY,
    id_danh_muc INT,
    ten_san_pham VARCHAR(255) NOT NULL,
    gia DECIMAL(10,2) NOT NULL,
    mo_ta_ngan TEXT,
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_danh_muc) REFERENCES danh_muc(id_danh_muc)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =======================================
-- 5. BANG ANH SAN PHAM
-- =======================================
CREATE TABLE anh_san_pham (
    id_anh INT AUTO_INCREMENT PRIMARY KEY,
    id_san_pham INT,
    duong_dan_anh VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_san_pham) REFERENCES san_pham(id_san_pham)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =======================================
-- 6. BANG GIO HANG
-- =======================================
CREATE TABLE gio_hang (
    id_gio_hang INT AUTO_INCREMENT PRIMARY KEY,
    id_nguoi_dung INT,
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_nguoi_dung) REFERENCES nguoi_dung(id_nguoi_dung)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =======================================
-- 7. BANG CHI TIET GIO HANG
-- =======================================
CREATE TABLE gio_hang_chi_tiet (
    id_chi_tiet INT AUTO_INCREMENT PRIMARY KEY,
    id_gio_hang INT,
    id_san_pham INT,
    so_luong INT DEFAULT 1,
    FOREIGN KEY (id_gio_hang) REFERENCES gio_hang(id_gio_hang)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (id_san_pham) REFERENCES san_pham(id_san_pham)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;
