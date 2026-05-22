CREATE TABLE IF NOT EXISTS phong_ban (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ma_phong_ban VARCHAR(50) NOT NULL,
  ten_phong_ban VARCHAR(150) NOT NULL,
  mo_ta TEXT NULL,
  trang_thai VARCHAR(20) NOT NULL DEFAULT 'hoat_dong',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL,
  deleted_at DATETIME NULL,
  UNIQUE KEY uq_phong_ban_ma (ma_phong_ban)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS chuc_vu (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ma_chuc_vu VARCHAR(50) NOT NULL,
  ten_chuc_vu VARCHAR(150) NOT NULL,
  mo_ta TEXT NULL,
  trang_thai VARCHAR(20) NOT NULL DEFAULT 'hoat_dong',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL,
  deleted_at DATETIME NULL,
  UNIQUE KEY uq_chuc_vu_ma (ma_chuc_vu)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS nhan_vien (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ma_nhan_vien VARCHAR(50) NOT NULL,
  ho_ten VARCHAR(150) NOT NULL,
  email VARCHAR(190) NOT NULL,
  so_dien_thoai VARCHAR(30) NULL,
  ngay_sinh DATE NULL,
  gioi_tinh VARCHAR(20) NULL,
  dia_chi VARCHAR(255) NULL,
  account_id INT NULL,
  phong_ban_id INT NULL,
  chuc_vu_id INT NULL,
  ngay_vao_lam DATE NULL,
  trang_thai VARCHAR(20) NOT NULL DEFAULT 'dang_lam',
  ghi_chu TEXT NULL,
  deleted_at DATETIME NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL,
  UNIQUE KEY uq_nhan_vien_ma (ma_nhan_vien),
  UNIQUE KEY uq_nhan_vien_email (email),
  UNIQUE KEY uq_nhan_vien_account (account_id),
  KEY idx_nhan_vien_trang_thai (trang_thai),
  KEY idx_nhan_vien_phong_ban (phong_ban_id),
  KEY idx_nhan_vien_chuc_vu (chuc_vu_id),
  CONSTRAINT fk_nhan_vien_account FOREIGN KEY (account_id) REFERENCES tai_khoan(id) ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT fk_nhan_vien_phong_ban FOREIGN KEY (phong_ban_id) REFERENCES phong_ban(id) ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT fk_nhan_vien_chuc_vu FOREIGN KEY (chuc_vu_id) REFERENCES chuc_vu(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS cham_cong (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nhan_vien_id INT NOT NULL,
  ngay DATE NOT NULL,
  gio_vao TIME NULL,
  gio_ra TIME NULL,
  tong_gio_lam DECIMAL(5,2) NOT NULL DEFAULT 0,
  trang_thai VARCHAR(20) NOT NULL DEFAULT 'di_lam',
  ghi_chu TEXT NULL,
  deleted_at DATETIME NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL,
  KEY idx_cham_cong_nhan_vien (nhan_vien_id),
  KEY idx_cham_cong_ngay (ngay),
  KEY idx_cham_cong_trang_thai (trang_thai),
  UNIQUE KEY uq_cham_cong_nhan_vien_ngay (nhan_vien_id, ngay),
  CONSTRAINT fk_cham_cong_nhan_vien FOREIGN KEY (nhan_vien_id) REFERENCES nhan_vien(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

