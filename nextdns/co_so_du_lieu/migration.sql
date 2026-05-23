CREATE TABLE IF NOT EXISTS nextdns_profile (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ten_profile VARCHAR(150) NOT NULL,
  ma_profile_nextdns VARCHAR(64) NOT NULL,
  api_key_ma_hoa TEXT NOT NULL,
  trang_thai TINYINT NOT NULL DEFAULT 1,
  ghi_chu TEXT NULL,
  ngay_cap_nhat DATETIME NULL,
  ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uk_nextdns_profile_ma (ma_profile_nextdns),
  KEY idx_nextdns_profile_trang_thai (trang_thai)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS nextdns_cache_thong_ke (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  profile_id INT NOT NULL,
  du_lieu_json MEDIUMTEXT NOT NULL,
  ngay_cap_nhat DATETIME NOT NULL,
  ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uk_nextdns_cache_profile (profile_id),
  CONSTRAINT fk_nextdns_cache_profile FOREIGN KEY (profile_id) REFERENCES nextdns_profile(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS nextdns_cache_bo_loc (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  profile_id INT NOT NULL,
  du_lieu_json MEDIUMTEXT NOT NULL,
  ghi_chu TEXT NULL,
  trang_thai TINYINT NOT NULL DEFAULT 1,
  cap_nhat_cuoi DATETIME NULL,
  ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
  ngay_sua DATETIME NULL,
  UNIQUE KEY uk_nextdns_cache_bo_loc_profile (profile_id),
  CONSTRAINT fk_nextdns_cache_bo_loc_profile FOREIGN KEY (profile_id) REFERENCES nextdns_profile(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS nextdns_danh_sach_chan (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  profile_id INT NOT NULL,
  domain VARCHAR(253) NOT NULL,
  ghi_chu TEXT NULL,
  trang_thai TINYINT NOT NULL DEFAULT 1,
  cap_nhat_cuoi DATETIME NULL,
  ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
  ngay_sua DATETIME NULL,
  UNIQUE KEY uk_nextdns_chan_profile_domain (profile_id, domain),
  KEY idx_nextdns_chan_profile (profile_id),
  CONSTRAINT fk_nextdns_chan_profile FOREIGN KEY (profile_id) REFERENCES nextdns_profile(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS nextdns_danh_sach_cho_phep (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  profile_id INT NOT NULL,
  domain VARCHAR(253) NOT NULL,
  ghi_chu TEXT NULL,
  trang_thai TINYINT NOT NULL DEFAULT 1,
  cap_nhat_cuoi DATETIME NULL,
  ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
  ngay_sua DATETIME NULL,
  UNIQUE KEY uk_nextdns_cho_phep_profile_domain (profile_id, domain),
  KEY idx_nextdns_cho_phep_profile (profile_id),
  CONSTRAINT fk_nextdns_cho_phep_profile FOREIGN KEY (profile_id) REFERENCES nextdns_profile(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS nextdns_bo_nho_dem;
DROP TABLE IF EXISTS nextdns_nhat_ky;
DROP TABLE IF EXISTS nextdns_cau_hinh;
