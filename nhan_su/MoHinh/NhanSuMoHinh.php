<?php
declare(strict_types=1);

namespace MoDun\NhanSu\MoHinh;

use HeThong\CoSoDuLieu;
use PDO;

class NhanSuMoHinh
{
    public function layDanhSachNhanVien(array $boLoc, int $trang, int $moiTrang): array
    {
        $dieuKien = ['nv.deleted_at IS NULL'];
        $thamSo = [];
        if (($boLoc['tu_khoa'] ?? '') !== '') {
            $dieuKien[] = '(nv.ma_nhan_vien LIKE :tu_khoa OR nv.ho_ten LIKE :tu_khoa OR nv.email LIKE :tu_khoa)';
            $thamSo['tu_khoa'] = '%' . $boLoc['tu_khoa'] . '%';
        }
        if (($boLoc['trang_thai'] ?? '') !== '') {
            $dieuKien[] = 'nv.trang_thai = :trang_thai';
            $thamSo['trang_thai'] = $boLoc['trang_thai'];
        }
        if (!empty($boLoc['department_id'])) {
            $dieuKien[] = 'nv.phong_ban_id = :department_id';
            $thamSo['department_id'] = (int)$boLoc['department_id'];
        }
        if (!empty($boLoc['position_id'])) {
            $dieuKien[] = 'nv.chuc_vu_id = :position_id';
            $thamSo['position_id'] = (int)$boLoc['position_id'];
        }
        $where = implode(' AND ', $dieuKien);
        $pdo = CoSoDuLieu::layKetNoi();
        $dem = $pdo->prepare("SELECT COUNT(*) FROM nhan_vien nv WHERE {$where}");
        $dem->execute($thamSo);
        $tong = (int)$dem->fetchColumn();

        $offset = max(0, ($trang - 1) * $moiTrang);
        $sql = "SELECT nv.*,
                    pb.ten_phong_ban AS department_name,
                    cv.ten_chuc_vu AS position_name,
                    tk.email AS account_email,
                    nv.ma_nhan_vien AS employee_code,
                    nv.ho_ten AS full_name,
                    nv.so_dien_thoai AS phone,
                    nv.ngay_sinh AS birth_date,
                    nv.ngay_vao_lam AS join_date,
                    nv.ghi_chu AS note,
                    nv.trang_thai AS status,
                    nv.phong_ban_id AS department_id,
                    nv.chuc_vu_id AS position_id
                FROM nhan_vien nv
                LEFT JOIN phong_ban pb ON pb.id = nv.phong_ban_id
                LEFT JOIN chuc_vu cv ON cv.id = nv.chuc_vu_id
                LEFT JOIN tai_khoan tk ON tk.id = nv.account_id
                WHERE {$where}
                ORDER BY nv.id DESC
                LIMIT :limit OFFSET :offset";
        $stm = $pdo->prepare($sql);
        foreach ($thamSo as $k => $v) $stm->bindValue(':' . $k, $v);
        $stm->bindValue(':limit', $moiTrang, PDO::PARAM_INT);
        $stm->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stm->execute();
        return ['du_lieu' => $stm->fetchAll(PDO::FETCH_ASSOC), 'tong' => $tong];
    }

    public function thongKeTongQuan(): array
    {
        $pdo = CoSoDuLieu::layKetNoi();
        return [
            'tong_nhan_vien' => (int)$pdo->query("SELECT COUNT(*) FROM nhan_vien WHERE deleted_at IS NULL")->fetchColumn(),
            'dang_lam' => (int)$pdo->query("SELECT COUNT(*) FROM nhan_vien WHERE deleted_at IS NULL AND trang_thai = 'dang_lam'")->fetchColumn(),
            'tam_nghi' => (int)$pdo->query("SELECT COUNT(*) FROM nhan_vien WHERE deleted_at IS NULL AND trang_thai = 'tam_nghi'")->fetchColumn(),
            'nghi_viec' => (int)$pdo->query("SELECT COUNT(*) FROM nhan_vien WHERE deleted_at IS NULL AND trang_thai = 'nghi_viec'")->fetchColumn(),
            'tong_phong_ban' => (int)$pdo->query("SELECT COUNT(*) FROM phong_ban WHERE deleted_at IS NULL")->fetchColumn(),
            'tong_chuc_vu' => (int)$pdo->query("SELECT COUNT(*) FROM chuc_vu WHERE deleted_at IS NULL")->fetchColumn(),
        ];
    }

    public function timNhanVienTheoId(int $id): ?array
    {
        $sql = "SELECT nv.*, pb.ten_phong_ban AS department_name, cv.ten_chuc_vu AS position_name, tk.email AS account_email,
                    nv.ma_nhan_vien AS employee_code, nv.ho_ten AS full_name, nv.so_dien_thoai AS phone, nv.ngay_sinh AS birth_date,
                    nv.ngay_vao_lam AS join_date, nv.ghi_chu AS note, nv.trang_thai AS status, nv.phong_ban_id AS department_id, nv.chuc_vu_id AS position_id
                FROM nhan_vien nv
                LEFT JOIN phong_ban pb ON pb.id = nv.phong_ban_id
                LEFT JOIN chuc_vu cv ON cv.id = nv.chuc_vu_id
                LEFT JOIN tai_khoan tk ON tk.id = nv.account_id
                WHERE nv.id = :id AND nv.deleted_at IS NULL LIMIT 1";
        $stm = CoSoDuLieu::layKetNoi()->prepare($sql);
        $stm->execute(['id' => $id]);
        $r = $stm->fetch(PDO::FETCH_ASSOC);
        return is_array($r) ? $r : null;
    }

    public function maNhanVienDaTonTai(string $ma, ?int $boQuaId = null): bool
    {
        $sql = 'SELECT 1 FROM nhan_vien WHERE ma_nhan_vien = :ma';
        $thamSo = ['ma' => $ma];
        if ($boQuaId !== null) { $sql .= ' AND id <> :id'; $thamSo['id'] = $boQuaId; }
        $sql .= ' LIMIT 1';
        $stm = CoSoDuLieu::layKetNoi()->prepare($sql);
        $stm->execute($thamSo);
        return (bool)$stm->fetchColumn();
    }

    public function emailNhanVienDaTonTai(string $email, ?int $boQuaId = null): bool
    {
        $sql = 'SELECT 1 FROM nhan_vien WHERE email = :email';
        $thamSo = ['email' => $email];
        if ($boQuaId !== null) { $sql .= ' AND id <> :id'; $thamSo['id'] = $boQuaId; }
        $sql .= ' LIMIT 1';
        $stm = CoSoDuLieu::layKetNoi()->prepare($sql);
        $stm->execute($thamSo);
        return (bool)$stm->fetchColumn();
    }

    public function taoNhanVien(array $duLieu): void
    {
        $sql = "INSERT INTO nhan_vien (ma_nhan_vien, ho_ten, email, so_dien_thoai, ngay_sinh, gioi_tinh, dia_chi, account_id, phong_ban_id, chuc_vu_id, ngay_vao_lam, trang_thai, ghi_chu, updated_at)
                VALUES (:employee_code, :full_name, :email, :phone, :birth_date, :gender, :address, :account_id, :department_id, :position_id, :join_date, :status, :note, NOW())";
        CoSoDuLieu::layKetNoi()->prepare($sql)->execute($duLieu);
    }

    public function capNhatNhanVien(int $id, array $duLieu): void
    {
        $duLieu['id'] = $id;
        $sql = "UPDATE nhan_vien SET
                ma_nhan_vien = :employee_code,
                ho_ten = :full_name,
                email = :email,
                so_dien_thoai = :phone,
                ngay_sinh = :birth_date,
                gioi_tinh = :gender,
                dia_chi = :address,
                account_id = :account_id,
                phong_ban_id = :department_id,
                chuc_vu_id = :position_id,
                ngay_vao_lam = :join_date,
                trang_thai = :status,
                ghi_chu = :note,
                updated_at = NOW()
                WHERE id = :id";
        CoSoDuLieu::layKetNoi()->prepare($sql)->execute($duLieu);
    }

    public function xoaMemNhanVien(int $id): void
    {
        CoSoDuLieu::layKetNoi()->prepare('UPDATE nhan_vien SET deleted_at = NOW(), updated_at = NOW() WHERE id = :id')->execute(['id' => $id]);
    }

    public function layDanhSachPhongBan(): array
    {
        return CoSoDuLieu::layKetNoi()->query('SELECT id, ma_phong_ban AS code, ten_phong_ban AS name, mo_ta AS description, trang_thai AS status FROM phong_ban WHERE deleted_at IS NULL ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function timPhongBanTheoId(int $id): ?array
    {
        $stm = CoSoDuLieu::layKetNoi()->prepare('SELECT id, ma_phong_ban AS code, ten_phong_ban AS name, mo_ta AS description, trang_thai AS status FROM phong_ban WHERE id = :id AND deleted_at IS NULL LIMIT 1');
        $stm->execute(['id' => $id]);
        $r = $stm->fetch(PDO::FETCH_ASSOC);
        return is_array($r) ? $r : null;
    }

    public function maPhongBanDaTonTai(string $ma, ?int $boQuaId = null): bool
    {
        $sql = 'SELECT 1 FROM phong_ban WHERE ma_phong_ban = :ma AND deleted_at IS NULL';
        $thamSo = ['ma' => $ma];
        if ($boQuaId !== null) { $sql .= ' AND id <> :id'; $thamSo['id'] = $boQuaId; }
        $sql .= ' LIMIT 1';
        $stm = CoSoDuLieu::layKetNoi()->prepare($sql);
        $stm->execute($thamSo);
        return (bool)$stm->fetchColumn();
    }

    public function taoPhongBan(array $duLieu): void
    {
        CoSoDuLieu::layKetNoi()->prepare('INSERT INTO phong_ban (ma_phong_ban, ten_phong_ban, mo_ta, trang_thai, updated_at) VALUES (:code, :name, :description, :status, NOW())')->execute($duLieu);
    }

    public function capNhatPhongBan(int $id, array $duLieu): void
    {
        $duLieu['id'] = $id;
        CoSoDuLieu::layKetNoi()->prepare('UPDATE phong_ban SET ma_phong_ban = :code, ten_phong_ban = :name, mo_ta = :description, trang_thai = :status, updated_at = NOW() WHERE id = :id')->execute($duLieu);
    }

    public function xoaPhongBan(int $id): void
    {
        CoSoDuLieu::layKetNoi()->prepare('UPDATE phong_ban SET deleted_at = NOW(), updated_at = NOW() WHERE id = :id')->execute(['id' => $id]);
    }

    public function layDanhSachChucVu(): array
    {
        return CoSoDuLieu::layKetNoi()->query('SELECT id, ma_chuc_vu AS code, ten_chuc_vu AS name, mo_ta AS description, trang_thai AS status FROM chuc_vu WHERE deleted_at IS NULL ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function timChucVuTheoId(int $id): ?array
    {
        $stm = CoSoDuLieu::layKetNoi()->prepare('SELECT id, ma_chuc_vu AS code, ten_chuc_vu AS name, mo_ta AS description, trang_thai AS status FROM chuc_vu WHERE id = :id AND deleted_at IS NULL LIMIT 1');
        $stm->execute(['id' => $id]);
        $r = $stm->fetch(PDO::FETCH_ASSOC);
        return is_array($r) ? $r : null;
    }

    public function maChucVuDaTonTai(string $ma, ?int $boQuaId = null): bool
    {
        $sql = 'SELECT 1 FROM chuc_vu WHERE ma_chuc_vu = :ma AND deleted_at IS NULL';
        $thamSo = ['ma' => $ma];
        if ($boQuaId !== null) { $sql .= ' AND id <> :id'; $thamSo['id'] = $boQuaId; }
        $sql .= ' LIMIT 1';
        $stm = CoSoDuLieu::layKetNoi()->prepare($sql);
        $stm->execute($thamSo);
        return (bool)$stm->fetchColumn();
    }

    public function taoChucVu(array $duLieu): void
    {
        CoSoDuLieu::layKetNoi()->prepare('INSERT INTO chuc_vu (ma_chuc_vu, ten_chuc_vu, mo_ta, trang_thai, updated_at) VALUES (:code, :name, :description, :status, NOW())')->execute($duLieu);
    }

    public function capNhatChucVu(int $id, array $duLieu): void
    {
        $duLieu['id'] = $id;
        CoSoDuLieu::layKetNoi()->prepare('UPDATE chuc_vu SET ma_chuc_vu = :code, ten_chuc_vu = :name, mo_ta = :description, trang_thai = :status, updated_at = NOW() WHERE id = :id')->execute($duLieu);
    }

    public function xoaChucVu(int $id): void
    {
        CoSoDuLieu::layKetNoi()->prepare('UPDATE chuc_vu SET deleted_at = NOW(), updated_at = NOW() WHERE id = :id')->execute(['id' => $id]);
    }

    public function layDuLieuDashboard(): array
    {
        $pdo = CoSoDuLieu::layKetNoi();
        $thongKe = $this->thongKeTongQuan();
        $homNay = date('Y-m-d');
        $thongKe['tong_cong_hom_nay'] = (int)$pdo->query("SELECT COUNT(*) FROM cham_cong WHERE deleted_at IS NULL AND ngay = '{$homNay}'")->fetchColumn();
        $thongKe['di_muon_hom_nay'] = (int)$pdo->query("SELECT COUNT(*) FROM cham_cong WHERE deleted_at IS NULL AND ngay = '{$homNay}' AND trang_thai = 'di_muon'")->fetchColumn();
        $thongKe['vang_hom_nay'] = (int)$pdo->query("SELECT COUNT(*) FROM cham_cong WHERE deleted_at IS NULL AND ngay = '{$homNay}' AND trang_thai = 'vang'")->fetchColumn();
        $duLieuPhongBan = $pdo->query("SELECT pb.ten_phong_ban AS name, COUNT(nv.id) AS total FROM phong_ban pb LEFT JOIN nhan_vien nv ON nv.phong_ban_id = pb.id AND nv.deleted_at IS NULL GROUP BY pb.id ORDER BY total DESC")->fetchAll(PDO::FETCH_ASSOC);
        $duLieuTrangThai = $pdo->query("SELECT trang_thai AS status, COUNT(*) AS total FROM nhan_vien WHERE deleted_at IS NULL GROUP BY trang_thai")->fetchAll(PDO::FETCH_ASSOC);
        $duLieu7Ngay = $pdo->query("SELECT ngay AS attendance_date, COUNT(*) AS total FROM cham_cong WHERE deleted_at IS NULL AND ngay >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY ngay ORDER BY ngay ASC")->fetchAll(PDO::FETCH_ASSOC);
        $duLieuTangTruong = $pdo->query("SELECT DATE_FORMAT(ngay, '%Y-%m') AS ym, COUNT(*) AS total FROM cham_cong WHERE deleted_at IS NULL AND ngay >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH) GROUP BY ym ORDER BY ym ASC")->fetchAll(PDO::FETCH_ASSOC);
        $nhanVienMoi = $pdo->query("SELECT id, ma_nhan_vien AS employee_code, ho_ten AS full_name, created_at FROM nhan_vien WHERE deleted_at IS NULL ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        $sinhNhatGanDay = $pdo->query("SELECT id, ma_nhan_vien AS employee_code, ho_ten AS full_name, ngay_sinh AS birth_date FROM nhan_vien WHERE deleted_at IS NULL AND ngay_sinh IS NOT NULL ORDER BY CASE WHEN DATE_FORMAT(ngay_sinh, '%m-%d') >= DATE_FORMAT(CURDATE(), '%m-%d') THEN DATE_FORMAT(ngay_sinh, '%m-%d') ELSE DATE_FORMAT(DATE_ADD(ngay_sinh, INTERVAL 1 YEAR), '%m-%d') END ASC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        $diMuonNhieu = $pdo->query("SELECT nv.id, nv.ma_nhan_vien AS employee_code, nv.ho_ten AS full_name, COUNT(cc.id) AS late_count
            FROM cham_cong cc INNER JOIN nhan_vien nv ON nv.id = cc.nhan_vien_id
            WHERE cc.deleted_at IS NULL AND nv.deleted_at IS NULL AND cc.trang_thai = 'di_muon'
            GROUP BY nv.id, nv.ma_nhan_vien, nv.ho_ten
            ORDER BY late_count DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        $chamCongBatThuong = $pdo->query("SELECT nv.ho_ten AS full_name, cc.trang_thai AS status, cc.ngay AS attendance_date FROM cham_cong cc INNER JOIN nhan_vien nv ON nv.id = cc.nhan_vien_id WHERE cc.deleted_at IS NULL AND cc.ngay = CURDATE() AND cc.trang_thai IN ('di_muon','vang') ORDER BY cc.id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        $tkThang = $pdo->query("SELECT COUNT(*) AS tong_cong_thang_nay, COALESCE(SUM(tong_gio_lam),0) AS tong_gio_lam_thang_nay FROM cham_cong WHERE deleted_at IS NULL AND DATE_FORMAT(ngay, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')")->fetch(PDO::FETCH_ASSOC) ?: ['tong_cong_thang_nay'=>0,'tong_gio_lam_thang_nay'=>0];
        $thongKe['tong_cong_thang_nay'] = (int)$tkThang['tong_cong_thang_nay'];
        $thongKe['tong_gio_lam_thang_nay'] = (float)$tkThang['tong_gio_lam_thang_nay'];
        return compact('thongKe', 'duLieuPhongBan', 'duLieuTrangThai', 'duLieu7Ngay', 'duLieuTangTruong', 'nhanVienMoi', 'sinhNhatGanDay', 'diMuonNhieu', 'chamCongBatThuong');
    }

    public function layDanhSachChamCong(array $boLoc, int $trang, int $moiTrang): array
    {
        $dieuKien = ['cc.deleted_at IS NULL'];
        $thamSo = [];
        if (($boLoc['tu_ngay'] ?? '') !== '') { $dieuKien[] = 'cc.ngay >= :tu_ngay'; $thamSo['tu_ngay'] = $boLoc['tu_ngay']; }
        if (($boLoc['den_ngay'] ?? '') !== '') { $dieuKien[] = 'cc.ngay <= :den_ngay'; $thamSo['den_ngay'] = $boLoc['den_ngay']; }
        if (!empty($boLoc['employee_id'])) { $dieuKien[] = 'cc.nhan_vien_id = :employee_id'; $thamSo['employee_id'] = (int)$boLoc['employee_id']; }
        if (!empty($boLoc['department_id'])) { $dieuKien[] = 'nv.phong_ban_id = :department_id'; $thamSo['department_id'] = (int)$boLoc['department_id']; }
        if (($boLoc['status'] ?? '') !== '') {
            $trangThaiDb = ($boLoc['status'] === 'co_mat') ? 'di_lam' : $boLoc['status'];
            $dieuKien[] = 'cc.trang_thai = :status';
            $thamSo['status'] = $trangThaiDb;
        }
        $where = implode(' AND ', $dieuKien);
        $pdo = CoSoDuLieu::layKetNoi();
        $dem = $pdo->prepare("SELECT COUNT(*) FROM cham_cong cc INNER JOIN nhan_vien nv ON nv.id = cc.nhan_vien_id WHERE {$where}");
        $dem->execute($thamSo);
        $tong = (int)$dem->fetchColumn();
        $offset = max(0, ($trang - 1) * $moiTrang);
        $sql = "SELECT cc.*, nv.ma_nhan_vien AS employee_code, nv.ho_ten AS full_name, pb.ten_phong_ban AS department_name,
                    cc.ngay AS attendance_date, cc.gio_vao AS check_in, cc.gio_ra AS check_out, cc.tong_gio_lam AS total_hours, cc.trang_thai AS status, cc.ghi_chu AS note
                FROM cham_cong cc
                INNER JOIN nhan_vien nv ON nv.id = cc.nhan_vien_id
                LEFT JOIN phong_ban pb ON pb.id = nv.phong_ban_id
                WHERE {$where}
                ORDER BY cc.ngay DESC, cc.id DESC
                LIMIT :limit OFFSET :offset";
        $stm = $pdo->prepare($sql);
        foreach ($thamSo as $k => $v) $stm->bindValue(':' . $k, $v);
        $stm->bindValue(':limit', $moiTrang, PDO::PARAM_INT);
        $stm->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stm->execute();
        $thang = ($boLoc['thang'] ?? '') !== '' ? $boLoc['thang'] : date('Y-m');
        $tk = $pdo->prepare("SELECT COUNT(*) AS tong_cong, COALESCE(SUM(tong_gio_lam),0) AS tong_gio,
            SUM(CASE WHEN trang_thai='di_muon' THEN 1 ELSE 0 END) AS tong_di_muon,
            SUM(CASE WHEN trang_thai='vang' THEN 1 ELSE 0 END) AS tong_vang,
            SUM(CASE WHEN trang_thai='tang_ca' THEN 1 ELSE 0 END) AS tong_tang_ca
            FROM cham_cong WHERE deleted_at IS NULL AND DATE_FORMAT(ngay, '%Y-%m')=:thang");
        $tk->execute(['thang' => $thang]);
        return ['du_lieu' => $stm->fetchAll(PDO::FETCH_ASSOC), 'tong' => $tong, 'thong_ke' => $tk->fetch(PDO::FETCH_ASSOC) ?: []];
    }

    public function layThongKeChamCongTheoThang(array $boLoc): array
    {
        $thang = (int)($boLoc['thang'] ?? (int)date('n'));
        $nam = (int)($boLoc['nam'] ?? (int)date('Y'));
        $dieuKien = ['cc.deleted_at IS NULL', 'MONTH(cc.ngay) = :thang', 'YEAR(cc.ngay) = :nam'];
        $thamSo = ['thang' => $thang, 'nam' => $nam];

        if (!empty($boLoc['employee_id'])) {
            $dieuKien[] = 'cc.nhan_vien_id = :employee_id';
            $thamSo['employee_id'] = (int)$boLoc['employee_id'];
        }
        if (!empty($boLoc['department_id'])) {
            $dieuKien[] = 'nv.phong_ban_id = :department_id';
            $thamSo['department_id'] = (int)$boLoc['department_id'];
        }
        if (($boLoc['status'] ?? '') !== '') {
            $trangThaiDb = ($boLoc['status'] === 'co_mat') ? 'di_lam' : $boLoc['status'];
            $dieuKien[] = 'cc.trang_thai = :status';
            $thamSo['status'] = $trangThaiDb;
        }

        $where = implode(' AND ', $dieuKien);
        $sql = "SELECT
                    cc.ngay AS ngay,
                    COUNT(*) AS tong_cham_cong,
                    SUM(CASE WHEN cc.trang_thai = 'di_muon' THEN 1 ELSE 0 END) AS tong_di_muon,
                    SUM(CASE WHEN cc.trang_thai = 'vang' THEN 1 ELSE 0 END) AS tong_vang,
                    SUM(CASE WHEN cc.trang_thai = 'nghi_phep' THEN 1 ELSE 0 END) AS tong_nghi_phep,
                    SUM(CASE WHEN cc.trang_thai = 'tang_ca' THEN 1 ELSE 0 END) AS tong_tang_ca
                FROM cham_cong cc
                INNER JOIN nhan_vien nv ON nv.id = cc.nhan_vien_id
                WHERE {$where}
                GROUP BY cc.ngay
                ORDER BY cc.ngay ASC";
        $stm = CoSoDuLieu::layKetNoi()->prepare($sql);
        $stm->execute($thamSo);
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    public function layChiTietChamCongTheoNgay(string $ngay, array $boLoc): array
    {
        $dieuKien = ['cc.deleted_at IS NULL', 'cc.ngay = :ngay'];
        $thamSo = ['ngay' => $ngay];
        if (!empty($boLoc['employee_id'])) {
            $dieuKien[] = 'cc.nhan_vien_id = :employee_id';
            $thamSo['employee_id'] = (int)$boLoc['employee_id'];
        }
        if (!empty($boLoc['department_id'])) {
            $dieuKien[] = 'nv.phong_ban_id = :department_id';
            $thamSo['department_id'] = (int)$boLoc['department_id'];
        }
        if (($boLoc['status'] ?? '') !== '') {
            $trangThaiDb = ($boLoc['status'] === 'co_mat') ? 'di_lam' : $boLoc['status'];
            $dieuKien[] = 'cc.trang_thai = :status';
            $thamSo['status'] = $trangThaiDb;
        }
        $where = implode(' AND ', $dieuKien);
        $sql = "SELECT cc.*, nv.ho_ten AS full_name, nv.ma_nhan_vien AS employee_code,
                    cc.ngay AS attendance_date, cc.gio_vao AS check_in, cc.gio_ra AS check_out,
                    cc.tong_gio_lam AS total_hours, cc.trang_thai AS status
                FROM cham_cong cc
                INNER JOIN nhan_vien nv ON nv.id = cc.nhan_vien_id
                WHERE {$where}
                ORDER BY nv.ho_ten ASC, cc.id DESC";
        $stm = CoSoDuLieu::layKetNoi()->prepare($sql);
        $stm->execute($thamSo);
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    public function layMaTranChamCongTheoThang(array $boLoc): array
    {
        $thang = max(1, min(12, (int)($boLoc['month'] ?? $boLoc['thang'] ?? (int)date('n'))));
        $nam = max(1970, (int)($boLoc['year'] ?? $boLoc['nam'] ?? (int)date('Y')));
        $tuNgay = sprintf('%04d-%02d-01', $nam, $thang);
        $denNgay = date('Y-m-t', strtotime($tuNgay));
        $soNgay = (int)date('t', strtotime($tuNgay));
        $today = date('Y-m-d');

        $days = [];
        $tongNgayLamViec = 0;
        for ($i = 1; $i <= $soNgay; $i++) {
            $date = sprintf('%04d-%02d-%02d', $nam, $thang, $i);
            $thu = (int)date('N', strtotime($date));
            $isWeekend = $thu >= 6;
            if (!$isWeekend) $tongNgayLamViec++;
            $days[] = [
                'day' => $i,
                'date' => $date,
                'weekday' => [1=>'T2',2=>'T3',3=>'T4',4=>'T5',5=>'T6',6=>'T7',7=>'CN'][$thu],
                'is_weekend' => $isWeekend,
                'is_holiday' => false,
                'is_today' => $date === $today,
            ];
        }

        $dieuKien = ['nv.deleted_at IS NULL'];
        $thamSo = [];
        if (!empty($boLoc['department_id'])) {
            $dieuKien[] = 'nv.phong_ban_id = :department_id';
            $thamSo['department_id'] = (int)$boLoc['department_id'];
        }
        if (($boLoc['keyword'] ?? '') !== '') {
            $dieuKien[] = '(nv.ho_ten LIKE :keyword OR nv.ma_nhan_vien LIKE :keyword)';
            $thamSo['keyword'] = '%' . $boLoc['keyword'] . '%';
        }
        $where = implode(' AND ', $dieuKien);
        $sqlNv = "SELECT nv.id, nv.ma_nhan_vien, nv.ho_ten, nv.account_id,
                    pb.ten_phong_ban AS phong_ban, cv.ten_chuc_vu AS chuc_vu
                FROM nhan_vien nv
                LEFT JOIN phong_ban pb ON pb.id = nv.phong_ban_id
                LEFT JOIN chuc_vu cv ON cv.id = nv.chuc_vu_id
                WHERE {$where}
                ORDER BY nv.ho_ten ASC";
        $stmNv = CoSoDuLieu::layKetNoi()->prepare($sqlNv);
        $stmNv->execute($thamSo);
        $nhanVien = $stmNv->fetchAll(PDO::FETCH_ASSOC);

        $ids = array_map(static fn(array $r): int => (int)$r['id'], $nhanVien);
        $chamCongTheoNhanVien = [];
        $ngayLeTheoNgay = [];
        if ($ids !== []) {
            $pdo = CoSoDuLieu::layKetNoi();
            $inPlaceholders = implode(',', array_fill(0, count($ids), '?'));
            $sqlCc = "SELECT cc.id, cc.nhan_vien_id, cc.ngay, cc.gio_vao, cc.gio_ra, cc.tong_gio_lam, cc.trang_thai, cc.ghi_chu
                    FROM cham_cong cc
                    WHERE cc.deleted_at IS NULL AND cc.ngay BETWEEN ? AND ? AND cc.nhan_vien_id IN ({$inPlaceholders})
                    ORDER BY cc.ngay ASC";
            $stmCc = $pdo->prepare($sqlCc);
            $params = array_merge([$tuNgay, $denNgay], $ids);
            $stmCc->execute($params);
            while ($r = $stmCc->fetch(PDO::FETCH_ASSOC)) {
                $eid = (int)$r['nhan_vien_id'];
                $ngay = (string)$r['ngay'];
                $chamCongTheoNhanVien[$eid][$ngay] = [
                    'id' => (int)$r['id'],
                    'trang_thai' => self::mapTrangThaiChamCong((string)$r['trang_thai']),
                    'trang_thai_goc' => (string)$r['trang_thai'],
                    'gio_vao' => $r['gio_vao'],
                    'gio_ra' => $r['gio_ra'],
                    'tong_gio' => (float)$r['tong_gio_lam'],
                    'ghi_chu' => $r['ghi_chu'],
                ];
                if ((string)$r['trang_thai'] === 'ngay_le') $ngayLeTheoNgay[$ngay] = true;
            }
        }
        foreach ($days as &$dayMeta) {
            if (!empty($ngayLeTheoNgay[$dayMeta['date']])) $dayMeta['is_holiday'] = true;
        }
        unset($dayMeta);

        $nguoiDung = $_SESSION['nguoi_dung'] ?? [];
        $taiKhoanId = (int)($nguoiDung['id'] ?? 0);
        $employees = [];
        foreach ($nhanVien as $nv) {
            $eid = (int)$nv['id'];
            $daysMap = [];
            $tongCong = 0.0;
            foreach ($days as $d) {
                $date = $d['date'];
                $item = $chamCongTheoNhanVien[$eid][$date] ?? null;
                if ($item === null) {
                    $daysMap[$date] = ['trang_thai' => 'chua_co_du_lieu', 'gio_vao' => null, 'gio_ra' => null, 'tong_gio' => 0, 'ghi_chu' => null, 'id' => null];
                    continue;
                }
                if (($boLoc['status'] ?? '') !== '' && $item['trang_thai'] !== $boLoc['status']) {
                    $daysMap[$date] = ['trang_thai' => 'chua_co_du_lieu', 'gio_vao' => null, 'gio_ra' => null, 'tong_gio' => 0, 'ghi_chu' => null, 'id' => null];
                    continue;
                }
                $daysMap[$date] = $item;
                $tongCong += self::diemCongTheoTrangThai($item['trang_thai']);
            }
            $employees[] = [
                'id' => $eid,
                'ma_nhan_vien' => (string)$nv['ma_nhan_vien'],
                'ho_ten' => (string)$nv['ho_ten'],
                'chuc_vu' => (string)($nv['chuc_vu'] ?? ''),
                'phong_ban' => (string)($nv['phong_ban'] ?? ''),
                'is_current_user' => $taiKhoanId > 0 && $taiKhoanId === (int)($nv['account_id'] ?? 0),
                'tong_cong' => $tongCong,
                'tong_ngay_lam_viec' => $tongNgayLamViec,
                'days' => $daysMap,
            ];
        }

        return ['month' => $thang, 'year' => $nam, 'days' => $days, 'employees' => $employees];
    }

    private static function mapTrangThaiChamCong(string $trangThai): string
    {
        return match ($trangThai) {
            'di_lam' => 'co_mat',
            default => $trangThai,
        };
    }

    private static function diemCongTheoTrangThai(string $trangThai): float
    {
        return match ($trangThai) {
            'co_mat', 'di_muon', 'lam_o_nha', 'tang_ca' => 1.0,
            'nua_ngay' => 0.5,
            default => 0.0,
        };
    }

    public function timChamCongTheoId(int $id): ?array
    {
        $sql = "SELECT cc.*, nv.ma_nhan_vien AS employee_code, nv.ho_ten AS full_name, pb.ten_phong_ban AS department_name,
                    cc.ngay AS attendance_date, cc.gio_vao AS check_in, cc.gio_ra AS check_out, cc.tong_gio_lam AS total_hours, cc.trang_thai AS status, cc.ghi_chu AS note
                FROM cham_cong cc
                INNER JOIN nhan_vien nv ON nv.id = cc.nhan_vien_id
                LEFT JOIN phong_ban pb ON pb.id = nv.phong_ban_id
                WHERE cc.id = :id AND cc.deleted_at IS NULL LIMIT 1";
        $stm = CoSoDuLieu::layKetNoi()->prepare($sql);
        $stm->execute(['id' => $id]);
        $r = $stm->fetch(PDO::FETCH_ASSOC);
        return is_array($r) ? $r : null;
    }

    public function taoChamCong(array $duLieu): void
    {
        CoSoDuLieu::layKetNoi()->prepare("INSERT INTO cham_cong (nhan_vien_id, ngay, gio_vao, gio_ra, tong_gio_lam, trang_thai, ghi_chu, updated_at)
            VALUES (:employee_id, :attendance_date, :check_in, :check_out, :total_hours, :status, :note, NOW())")->execute($duLieu);
    }

    public function capNhatChamCong(int $id, array $duLieu): void
    {
        $duLieu['id'] = $id;
        CoSoDuLieu::layKetNoi()->prepare("UPDATE cham_cong SET nhan_vien_id=:employee_id, ngay=:attendance_date, gio_vao=:check_in, gio_ra=:check_out, tong_gio_lam=:total_hours, trang_thai=:status, ghi_chu=:note, updated_at=NOW() WHERE id=:id")->execute($duLieu);
    }

    public function xoaMemChamCong(int $id): void
    {
        CoSoDuLieu::layKetNoi()->prepare('UPDATE cham_cong SET deleted_at = NOW(), updated_at = NOW() WHERE id = :id')->execute(['id' => $id]);
    }

    public function chamCongDaTonTai(int $employeeId, string $ngay, ?int $boQuaId = null): bool
    {
        $sql = 'SELECT 1 FROM cham_cong WHERE nhan_vien_id = :employee_id AND ngay = :attendance_date AND deleted_at IS NULL';
        $thamSo = ['employee_id' => $employeeId, 'attendance_date' => $ngay];
        if ($boQuaId !== null) { $sql .= ' AND id <> :id'; $thamSo['id'] = $boQuaId; }
        $sql .= ' LIMIT 1';
        $stm = CoSoDuLieu::layKetNoi()->prepare($sql);
        $stm->execute($thamSo);
        return (bool)$stm->fetchColumn();
    }

    public function layNhanVienDangLam(): array
    {
        return CoSoDuLieu::layKetNoi()->query("SELECT id, ma_nhan_vien AS employee_code, ho_ten AS full_name FROM nhan_vien WHERE deleted_at IS NULL AND trang_thai = 'dang_lam' ORDER BY ho_ten ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function accountDaLienKet(int $accountId, ?int $boQuaNhanVienId = null): bool
    {
        $sql = 'SELECT 1 FROM nhan_vien WHERE account_id = :account_id AND deleted_at IS NULL';
        $thamSo = ['account_id' => $accountId];
        if ($boQuaNhanVienId !== null) { $sql .= ' AND id <> :id'; $thamSo['id'] = $boQuaNhanVienId; }
        $sql .= ' LIMIT 1';
        $stm = CoSoDuLieu::layKetNoi()->prepare($sql);
        $stm->execute($thamSo);
        return (bool)$stm->fetchColumn();
    }

    public function layDanhSachTaiKhoanChuaLienKet(): array
    {
        $sql = "SELECT tk.id, tk.email, tk.ho_ten
                FROM tai_khoan tk
                LEFT JOIN nhan_vien nv ON nv.account_id = tk.id AND nv.deleted_at IS NULL
                WHERE nv.id IS NULL
                ORDER BY tk.id DESC";
        return CoSoDuLieu::layKetNoi()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function taoTaiKhoanNhanhTuNhanVien(int $employeeId): ?int
    {
        $nv = $this->timNhanVienTheoId($employeeId);
        if (!$nv || !empty($nv['account_id'])) return null;
        $pdo = CoSoDuLieu::layKetNoi();
        $roleId = (int)$pdo->query("SELECT id FROM vai_tro ORDER BY id ASC LIMIT 1")->fetchColumn();
        if ($roleId <= 0) $roleId = 1;
        $stm = $pdo->prepare("INSERT INTO tai_khoan (vai_tro_id, ho_ten, email, mat_khau, trang_thai, ngay_cap_nhat) VALUES (:vai_tro_id, :ho_ten, :email, :mat_khau, 1, NOW())");
        $stm->execute([
            'vai_tro_id' => $roleId,
            'ho_ten' => $nv['full_name'],
            'email' => $nv['email'],
            'mat_khau' => password_hash('12345678', PASSWORD_DEFAULT),
        ]);
        $accountId = (int)$pdo->lastInsertId();
        if ($accountId > 0) {
            $pdo->prepare('UPDATE nhan_vien SET account_id = :account_id, updated_at = NOW() WHERE id = :id')->execute(['account_id' => $accountId, 'id' => $employeeId]);
            return $accountId;
        }
        return null;
    }
}
