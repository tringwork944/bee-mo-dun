INSERT INTO phong_ban (ma_phong_ban, ten_phong_ban, mo_ta, trang_thai, updated_at) VALUES
('HR', 'Nhan su', 'Quan ly nhan su va phat trien to chuc', 'hoat_dong', NOW()),
('ACC', 'Ke toan', 'Quan ly tai chinh va ke toan', 'hoat_dong', NOW())
ON DUPLICATE KEY UPDATE
ten_phong_ban = VALUES(ten_phong_ban),
mo_ta = VALUES(mo_ta),
trang_thai = VALUES(trang_thai),
updated_at = NOW();

INSERT INTO chuc_vu (ma_chuc_vu, ten_chuc_vu, mo_ta, trang_thai, updated_at) VALUES
('HRM', 'Truong phong nhan su', 'Dieu phoi hoat dong nhan su', 'hoat_dong', NOW()),
('STAFF', 'Nhan vien', 'Vi tri nhan vien chuyen mon', 'hoat_dong', NOW())
ON DUPLICATE KEY UPDATE
ten_chuc_vu = VALUES(ten_chuc_vu),
mo_ta = VALUES(mo_ta),
trang_thai = VALUES(trang_thai),
updated_at = NOW();

INSERT INTO cham_cong (nhan_vien_id, ngay, gio_vao, gio_ra, tong_gio_lam, trang_thai, ghi_chu, updated_at)
SELECT nv.id, CURDATE(), '08:05:00', '17:10:00', 9.08, 'di_muon', 'Seed du lieu mau', NOW()
FROM nhan_vien nv
WHERE nv.deleted_at IS NULL
ORDER BY nv.id ASC
LIMIT 1
ON DUPLICATE KEY UPDATE
gio_vao = VALUES(gio_vao),
gio_ra = VALUES(gio_ra),
tong_gio_lam = VALUES(tong_gio_lam),
trang_thai = VALUES(trang_thai),
ghi_chu = VALUES(ghi_chu),
updated_at = NOW();

