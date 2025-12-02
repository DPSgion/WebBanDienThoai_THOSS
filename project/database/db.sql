-- =======================================
-- CSDL: He thong ban dien thoai
-- =======================================

DROP DATABASE IF EXISTS bandienthoai;
CREATE DATABASE bandienthoai;
USE bandienthoai;

-- =======================================
-- 1. BANG DANH MUC
-- =======================================
CREATE TABLE danh_muc (
    id_danh_muc INT AUTO_INCREMENT PRIMARY KEY,
    ten_danh_muc VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- =======================================
-- 2. BANG SAN PHAM
-- =======================================
CREATE TABLE san_pham (
    id_san_pham INT AUTO_INCREMENT PRIMARY KEY,
    id_danh_muc INT,
    ten_san_pham VARCHAR(255) NOT NULL,

    cpu VARCHAR(100),
    pin VARCHAR(50),
    man_hinh VARCHAR(100),
    os VARCHAR(50),
    camera_truoc VARCHAR(50),
    camera_sau VARCHAR(50),

    FOREIGN KEY (id_danh_muc) REFERENCES danh_muc(id_danh_muc)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =======================================
-- 3. BANG ANH SAN PHAM
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
-- 4. BANG BIEN THE SAN PHAM
-- =======================================
CREATE TABLE bien_the (
    id_bien_the INT AUTO_INCREMENT PRIMARY KEY,
    id_san_pham INT,

    ram VARCHAR(20),
    rom VARCHAR(20),
    mau VARCHAR(50),
    gia DECIMAL(10,2) NOT NULL,
    so_luong_ton INT DEFAULT 0,

    FOREIGN KEY (id_san_pham) REFERENCES san_pham(id_san_pham)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =======================================
-- 5. BANG NGUOI DUNG  (KHONG CO BANG VAI TRO)
-- =======================================
CREATE TABLE nguoi_dung (
    id_nguoi_dung INT AUTO_INCREMENT PRIMARY KEY,
    ho_ten VARCHAR(100) NOT NULL,
    sdt VARCHAR(20) UNIQUE,
    mat_khau VARCHAR(255) NOT NULL,
    vai_tro ENUM('admin', 'khachhang') DEFAULT 'khachhang'
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
    id_bien_the INT,
    so_luong INT DEFAULT 1,

    FOREIGN KEY (id_gio_hang) REFERENCES gio_hang(id_gio_hang)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    FOREIGN KEY (id_bien_the) REFERENCES bien_the(id_bien_the)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =======================================
-- 8. BANG DON HANG
-- =======================================
CREATE TABLE don_hang (
    id_don_hang INT AUTO_INCREMENT PRIMARY KEY,
    id_nguoi_dung INT,
    trang_thai ENUM('choxuly','danggiao','hoanthanh','dahuy') DEFAULT 'choxuly',
    tong_tien DECIMAL(10,2),
    dia_chi VARCHAR(255),
    ngay_dat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_nguoi_dung) REFERENCES nguoi_dung(id_nguoi_dung)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =======================================
-- 9. BANG CHI TIET DON HANG
-- =======================================
CREATE TABLE chi_tiet_don_hang (
    id_ctdh INT AUTO_INCREMENT PRIMARY KEY,
    id_don_hang INT,
    id_bien_the INT,
    so_luong INT NOT NULL,
    gia_luc_mua DECIMAL(10,2) NOT NULL,

    FOREIGN KEY (id_don_hang) REFERENCES don_hang(id_don_hang)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    FOREIGN KEY (id_bien_the) REFERENCES bien_the(id_bien_the)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;
