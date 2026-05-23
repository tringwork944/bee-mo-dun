<?php
declare(strict_types=1);

namespace MoDun\Nextdns;

use DateTimeImmutable;
use Exception;
use HeThong\CoSoDuLieu;
use HeThong\YeuCau;
use PDO;

class KhoDuLieuNextdns
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = CoSoDuLieu::layKetNoi();
    }

    public function layDanhSachProfile(): array
    {
        $rows = $this->pdo->query('SELECT * FROM nextdns_profile ORDER BY trang_thai DESC, ten_profile ASC, id ASC')->fetchAll(PDO::FETCH_ASSOC);
        return is_array($rows) ? $rows : [];
    }

    public function timProfile(int $id): ?array
    {
        $stm = $this->pdo->prepare('SELECT * FROM nextdns_profile WHERE id = :id LIMIT 1');
        $stm->execute(['id' => $id]);
        $row = $stm->fetch(PDO::FETCH_ASSOC);
        return is_array($row) ? $row : null;
    }

    public function themProfile(array $duLieu): int
    {
        $stm = $this->pdo->prepare('INSERT INTO nextdns_profile (ten_profile, ma_profile_nextdns, api_key_ma_hoa, trang_thai, ghi_chu, ngay_cap_nhat) VALUES (:ten_profile, :ma_profile_nextdns, :api_key_ma_hoa, :trang_thai, :ghi_chu, NOW())');
        $stm->execute([
            'ten_profile' => $duLieu['ten_profile'],
            'ma_profile_nextdns' => $duLieu['ma_profile_nextdns'],
            'api_key_ma_hoa' => $duLieu['api_key_ma_hoa'],
            'trang_thai' => $duLieu['trang_thai'],
            'ghi_chu' => $duLieu['ghi_chu'],
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function capNhatProfile(int $id, array $duLieu): void
    {
        $cotApiKey = array_key_exists('api_key_ma_hoa', $duLieu) ? ', api_key_ma_hoa = :api_key_ma_hoa' : '';
        $stm = $this->pdo->prepare("UPDATE nextdns_profile SET ten_profile = :ten_profile, ma_profile_nextdns = :ma_profile_nextdns, trang_thai = :trang_thai, ghi_chu = :ghi_chu{$cotApiKey}, ngay_cap_nhat = NOW() WHERE id = :id");
        $thamSo = [
            'id' => $id,
            'ten_profile' => $duLieu['ten_profile'],
            'ma_profile_nextdns' => $duLieu['ma_profile_nextdns'],
            'trang_thai' => $duLieu['trang_thai'],
            'ghi_chu' => $duLieu['ghi_chu'],
        ];
        if (array_key_exists('api_key_ma_hoa', $duLieu)) {
            $thamSo['api_key_ma_hoa'] = $duLieu['api_key_ma_hoa'];
        }
        $stm->execute($thamSo);
    }

    public function xoaProfile(int $id): void
    {
        $stm = $this->pdo->prepare('DELETE FROM nextdns_profile WHERE id = :id');
        $stm->execute(['id' => $id]);
    }

    public function layCacheThongKe(int $profileId): ?array
    {
        $stm = $this->pdo->prepare('SELECT du_lieu_json, ngay_cap_nhat FROM nextdns_cache_thong_ke WHERE profile_id = :profile_id LIMIT 1');
        $stm->execute(['profile_id' => $profileId]);
        $row = $stm->fetch(PDO::FETCH_ASSOC);
        if (!is_array($row)) {
            return null;
        }
        $duLieu = json_decode((string) $row['du_lieu_json'], true);
        if (!is_array($duLieu)) {
            return null;
        }
        return [
            'du_lieu' => $duLieu,
            'ngay_cap_nhat' => (string) $row['ngay_cap_nhat'],
        ];
    }

    public function luuCacheThongKe(int $profileId, array $duLieu): void
    {
        $stm = $this->pdo->prepare('INSERT INTO nextdns_cache_thong_ke (profile_id, du_lieu_json, ngay_cap_nhat) VALUES (:profile_id, :du_lieu_json, NOW()) ON DUPLICATE KEY UPDATE du_lieu_json = VALUES(du_lieu_json), ngay_cap_nhat = NOW()');
        $stm->execute([
            'profile_id' => $profileId,
            'du_lieu_json' => (string) json_encode($duLieu, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);
    }

    public function xoaCacheThongKe(int $profileId): void
    {
        $stm = $this->pdo->prepare('DELETE FROM nextdns_cache_thong_ke WHERE profile_id = :profile_id');
        $stm->execute(['profile_id' => $profileId]);
    }

    public function layCacheBoLoc(int $profileId): ?array
    {
        $stm = $this->pdo->prepare('SELECT du_lieu_json, cap_nhat_cuoi FROM nextdns_cache_bo_loc WHERE profile_id = :profile_id LIMIT 1');
        $stm->execute(['profile_id' => $profileId]);
        $row = $stm->fetch(PDO::FETCH_ASSOC);
        if (!is_array($row)) {
            return null;
        }
        $duLieu = json_decode((string) $row['du_lieu_json'], true);
        return is_array($duLieu) ? ['du_lieu' => $duLieu, 'cap_nhat_cuoi' => (string) $row['cap_nhat_cuoi']] : null;
    }

    public function luuCacheBoLoc(int $profileId, array $duLieu): void
    {
        $stm = $this->pdo->prepare('INSERT INTO nextdns_cache_bo_loc (profile_id, du_lieu_json, trang_thai, cap_nhat_cuoi, ngay_sua) VALUES (:profile_id, :du_lieu_json, 1, NOW(), NOW()) ON DUPLICATE KEY UPDATE du_lieu_json = VALUES(du_lieu_json), trang_thai = 1, cap_nhat_cuoi = NOW(), ngay_sua = NOW()');
        $stm->execute([
            'profile_id' => $profileId,
            'du_lieu_json' => (string) json_encode($duLieu, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);
    }

    public function layDanhSachThuCong(string $bang, int $profileId, string $tuKhoa = ''): array
    {
        $sql = "SELECT * FROM {$bang} WHERE profile_id = :profile_id";
        $thamSo = ['profile_id' => $profileId];
        if ($tuKhoa !== '') {
            $sql .= ' AND domain LIKE :tu_khoa';
            $thamSo['tu_khoa'] = '%' . $tuKhoa . '%';
        }
        $sql .= ' ORDER BY domain ASC, id ASC';
        $stm = $this->pdo->prepare($sql);
        $stm->execute($thamSo);
        $rows = $stm->fetchAll(PDO::FETCH_ASSOC);
        return is_array($rows) ? $rows : [];
    }

    public function timMucThuCong(string $bang, int $id): ?array
    {
        $stm = $this->pdo->prepare("SELECT * FROM {$bang} WHERE id = :id LIMIT 1");
        $stm->execute(['id' => $id]);
        $row = $stm->fetch(PDO::FETCH_ASSOC);
        return is_array($row) ? $row : null;
    }

    public function themMucThuCong(string $bang, array $duLieu): int
    {
        $stm = $this->pdo->prepare("INSERT INTO {$bang} (profile_id, domain, ghi_chu, trang_thai, cap_nhat_cuoi, ngay_sua) VALUES (:profile_id, :domain, :ghi_chu, :trang_thai, NOW(), NOW())");
        $stm->execute([
            'profile_id' => $duLieu['profile_id'],
            'domain' => $duLieu['domain'],
            'ghi_chu' => $duLieu['ghi_chu'],
            'trang_thai' => $duLieu['trang_thai'],
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function capNhatMucThuCong(string $bang, int $id, array $duLieu): void
    {
        $stm = $this->pdo->prepare("UPDATE {$bang} SET domain = :domain, ghi_chu = :ghi_chu, trang_thai = :trang_thai, cap_nhat_cuoi = NOW(), ngay_sua = NOW() WHERE id = :id");
        $stm->execute([
            'id' => $id,
            'domain' => $duLieu['domain'],
            'ghi_chu' => $duLieu['ghi_chu'],
            'trang_thai' => $duLieu['trang_thai'],
        ]);
    }

    public function capNhatTrangThaiMucThuCong(string $bang, int $id, int $trangThai): void
    {
        $stm = $this->pdo->prepare("UPDATE {$bang} SET trang_thai = :trang_thai, cap_nhat_cuoi = NOW(), ngay_sua = NOW() WHERE id = :id");
        $stm->execute([
            'id' => $id,
            'trang_thai' => $trangThai,
        ]);
    }

    public function xoaMucThuCong(string $bang, int $id): void
    {
        $stm = $this->pdo->prepare("DELETE FROM {$bang} WHERE id = :id");
        $stm->execute(['id' => $id]);
    }

    public function thayTheDanhSachThuCong(string $bang, int $profileId, array $danhSach): void
    {
        $this->pdo->beginTransaction();
        try {
            $xoa = $this->pdo->prepare("DELETE FROM {$bang} WHERE profile_id = :profile_id");
            $xoa->execute(['profile_id' => $profileId]);
            $them = $this->pdo->prepare("INSERT INTO {$bang} (profile_id, domain, ghi_chu, trang_thai, cap_nhat_cuoi, ngay_sua) VALUES (:profile_id, :domain, :ghi_chu, 1, NOW(), NOW())");
            foreach ($danhSach as $dong) {
                if (!is_array($dong)) {
                    continue;
                }
                $domain = trim((string) ($dong['id'] ?? $dong['domain'] ?? ''));
                if ($domain === '') {
                    continue;
                }
                $them->execute([
                    'profile_id' => $profileId,
                    'domain' => $domain,
                    'ghi_chu' => (string) ($dong['note'] ?? $dong['ghi_chu'] ?? ''),
                ]);
            }
            $this->pdo->commit();
        } catch (\Throwable $ngoaiLe) {
            $this->pdo->rollBack();
            throw $ngoaiLe;
        }
    }

}

class DichVuNextdns
{
    private KhoDuLieuNextdns $khoDuLieu;
    private ApiNextdns $apiNextdns;

    public function __construct(?KhoDuLieuNextdns $khoDuLieu = null, ?ApiNextdns $apiNextdns = null)
    {
        $this->khoDuLieu = $khoDuLieu ?? new KhoDuLieuNextdns();
        $this->apiNextdns = $apiNextdns ?? new ApiNextdns();
    }

    public function duLieuTongQuan(array $truyVan, bool $choPhepTaiLanDau = true): array
    {
        $danhSachProfile = $this->danhSachProfileHienThi();
        $profileDangChon = $this->profileDangChon($danhSachProfile, (int) ($truyVan['profile_id'] ?? 0));
        $tongQuan = ['ok' => false, 'co_du_lieu' => false, 'thong_bao' => 'Chua chon profile NextDNS.'];

        if ($profileDangChon !== null) {
            $cache = $this->khoDuLieu->layCacheThongKe((int) $profileDangChon['id']);
            if ($cache !== null) {
                $tongQuan = $this->taoDuLieuTongQuanTuCache($cache);
            } elseif ($choPhepTaiLanDau) {
                $tongQuan = $this->capNhatThongKe((int) $profileDangChon['id']);
            } else {
                $tongQuan = ['ok' => false, 'co_du_lieu' => false, 'thong_bao' => 'Chua co du lieu, vui long cap nhat.', 'ngay_cap_nhat' => ''];
            }
        }

        return [
            'danh_sach_profile' => $danhSachProfile,
            'profile_dang_chon' => $profileDangChon,
            'tong_quan' => $tongQuan,
            'co_cap_nhat_du_lieu' => co_quyen('cap_nhat_du_lieu_nextdns'),
        ];
    }

    public function duLieuQuanLyProfile(array $truyVan): array
    {
        $danhSachProfile = $this->danhSachProfileHienThi();
        $profileSua = null;
        $idSua = (int) ($truyVan['sua_profile'] ?? 0);
        if ($idSua > 0) {
            foreach ($danhSachProfile as $profile) {
                if ((int) $profile['id'] === $idSua) {
                    $profileSua = $profile;
                    break;
                }
            }
        }

        return [
            'danh_sach_profile' => $danhSachProfile,
            'profile_sua' => $profileSua,
            'co_quan_ly_profile' => co_quyen('quan_ly_profile_nextdns'),
        ];
    }

    public function duLieuQuanLyBoLoc(array $truyVan): array
    {
        $danhSachProfile = $this->danhSachProfileHienThi();
        $profileDangChon = $this->profileDangChon($danhSachProfile, (int) ($truyVan['profile_id'] ?? 0));
        $cache = $profileDangChon !== null ? $this->khoDuLieu->layCacheBoLoc((int) $profileDangChon['id']) : null;
        $danhSach = $this->chuanHoaDanhSachBoLoc((array) ($cache['du_lieu']['data'] ?? $cache['du_lieu'] ?? []));

        return [
            'danh_sach_profile' => $danhSachProfile,
            'profile_dang_chon' => $profileDangChon,
            'danh_sach_bo_loc' => $danhSach,
            'cap_nhat_cuoi' => (string) ($cache['cap_nhat_cuoi'] ?? ''),
            'co_quan_ly_bo_loc' => co_quyen('quan_ly_bo_loc_nextdns'),
            'co_cap_nhat_du_lieu' => co_quyen('cap_nhat_du_lieu_nextdns'),
        ];
    }

    public function capNhatBoLoc(int $profileId): array
    {
        $profile = $this->khoDuLieu->timProfile($profileId);
        if ($profile === null) {
            return ['ok' => false, 'thong_bao' => 'Khong tim thay profile NextDNS.'];
        }

        try {
            $duLieu = $this->apiNextdns->layBoLoc(
                (string) $profile['ma_profile_nextdns'],
                $this->apiNextdns->giaiMaApiKey((string) $profile['api_key_ma_hoa'])
            );
            $this->khoDuLieu->luuCacheBoLoc($profileId, $duLieu);
            return ['ok' => true, 'thong_bao' => 'Da dong bo bo loc NextDNS.'];
        } catch (\Throwable $ngoaiLe) {
            return ['ok' => false, 'thong_bao' => $ngoaiLe->getMessage()];
        }
    }

    public function duLieuDanhSachThuCong(array $truyVan, string $loai): array
    {
        $danhSachProfile = $this->danhSachProfileHienThi();
        $profileDangChon = $this->profileDangChon($danhSachProfile, (int) ($truyVan['profile_id'] ?? 0));
        $tuKhoa = trim((string) ($truyVan['tu_khoa'] ?? ''));
        $bang = $this->bangDanhSachThuCong($loai);
        $mucSua = null;
        $idSua = (int) ($truyVan['sua'] ?? 0);
        if ($idSua > 0) {
            $mucSua = $this->khoDuLieu->timMucThuCong($bang, $idSua);
        }

        return [
            'loai_danh_sach' => $loai,
            'danh_sach_profile' => $danhSachProfile,
            'profile_dang_chon' => $profileDangChon,
            'tu_khoa' => $tuKhoa,
            'danh_sach' => $profileDangChon !== null ? $this->khoDuLieu->layDanhSachThuCong($bang, (int) $profileDangChon['id'], $tuKhoa) : [],
            'muc_sua' => $mucSua,
            'co_quan_ly' => co_quyen($loai === 'chan' ? 'quan_ly_danh_sach_chan_nextdns' : 'quan_ly_danh_sach_cho_phep_nextdns'),
        ];
    }

    public function themMucDanhSachThuCong(string $loai, array $duLieuThuan): array
    {
        $duLieu = $this->xacThucMucDanhSachThuCong($duLieuThuan);
        if ($duLieu['loi'] !== []) {
            return ['ok' => false, 'thong_bao' => implode(' ', $duLieu['loi'])];
        }

        try {
            $this->themMucDanhSachQuaApi($loai, $duLieu['profile_id'], $duLieu['domain']);
            $this->khoDuLieu->themMucThuCong($this->bangDanhSachThuCong($loai), $duLieu);
            return ['ok' => true, 'thong_bao' => 'Da them domain vao danh sach.'];
        } catch (\Throwable $ngoaiLe) {
            return ['ok' => false, 'thong_bao' => $ngoaiLe->getMessage() !== '' ? $ngoaiLe->getMessage() : 'Khong the them domain. Domain co the da ton tai trong profile nay.'];
        }
    }

    public function capNhatMucDanhSachThuCong(string $loai, int $id, array $duLieuThuan): array
    {
        $duLieu = $this->xacThucMucDanhSachThuCong($duLieuThuan);
        if ($duLieu['loi'] !== []) {
            return ['ok' => false, 'thong_bao' => implode(' ', $duLieu['loi'])];
        }

        try {
            $bang = $this->bangDanhSachThuCong($loai);
            $mucCu = $this->khoDuLieu->timMucThuCong($bang, $id);
            if ($mucCu !== null && (string) $mucCu['domain'] !== $duLieu['domain']) {
                $this->xoaMucDanhSachQuaApi($loai, (int) $mucCu['profile_id'], (string) $mucCu['domain']);
                $this->themMucDanhSachQuaApi($loai, $duLieu['profile_id'], $duLieu['domain']);
            } elseif ($mucCu === null) {
                $this->themMucDanhSachQuaApi($loai, $duLieu['profile_id'], $duLieu['domain']);
            }
            $this->khoDuLieu->capNhatMucThuCong($bang, $id, $duLieu);
            return ['ok' => true, 'thong_bao' => 'Da cap nhat domain.'];
        } catch (\Throwable $ngoaiLe) {
            return ['ok' => false, 'thong_bao' => $ngoaiLe->getMessage() !== '' ? $ngoaiLe->getMessage() : 'Khong the cap nhat domain.'];
        }
    }

    public function xoaMucDanhSachThuCong(string $loai, int $id): array
    {
        try {
            $bang = $this->bangDanhSachThuCong($loai);
            $muc = $this->khoDuLieu->timMucThuCong($bang, $id);
            if ($muc !== null) {
                $this->xoaMucDanhSachQuaApi($loai, (int) $muc['profile_id'], (string) $muc['domain']);
            }
            $this->khoDuLieu->xoaMucThuCong($bang, $id);
            return ['ok' => true, 'thong_bao' => 'Da xoa domain khoi danh sach.'];
        } catch (\Throwable $ngoaiLe) {
            return ['ok' => false, 'thong_bao' => $ngoaiLe->getMessage() !== '' ? $ngoaiLe->getMessage() : 'Khong the xoa domain.'];
        }
    }

    public function chuyenTrangThaiMucDanhSachThuCong(string $loai, int $id): array
    {
        $bang = $this->bangDanhSachThuCong($loai);
        $muc = $this->khoDuLieu->timMucThuCong($bang, $id);
        if ($muc === null) {
            return ['ok' => false, 'thong_bao' => 'Khong tim thay domain can cap nhat.'];
        }

        $trangThaiMoi = (int) ($muc['trang_thai'] ?? 0) === 1 ? 0 : 1;

        try {
            if ($trangThaiMoi === 1) {
                $this->themMucDanhSachQuaApi($loai, (int) $muc['profile_id'], (string) $muc['domain']);
            } else {
                $this->xoaMucDanhSachQuaApi($loai, (int) $muc['profile_id'], (string) $muc['domain']);
            }

            $this->khoDuLieu->capNhatTrangThaiMucThuCong($bang, $id, $trangThaiMoi);

            return [
                'ok' => true,
                'thong_bao' => $trangThaiMoi === 1 ? 'Da bat lai domain trong danh sach.' : 'Da tat domain trong danh sach.',
            ];
        } catch (\Throwable $ngoaiLe) {
            return ['ok' => false, 'thong_bao' => $ngoaiLe->getMessage() !== '' ? $ngoaiLe->getMessage() : 'Khong the cap nhat trang thai domain.'];
        }
    }

    public function dongBoDanhSachThuCong(string $loai, int $profileId): array
    {
        $profile = $this->khoDuLieu->timProfile($profileId);
        if ($profile === null) {
            return ['ok' => false, 'thong_bao' => 'Khong tim thay profile NextDNS.'];
        }

        try {
            $maProfile = (string) $profile['ma_profile_nextdns'];
            $apiKey = $this->apiNextdns->giaiMaApiKey((string) $profile['api_key_ma_hoa']);
            $duLieu = $loai === 'chan'
                ? $this->apiNextdns->layDanhSachChan($maProfile, $apiKey)
                : $this->apiNextdns->layDanhSachChoPhep($maProfile, $apiKey);
            $this->khoDuLieu->thayTheDanhSachThuCong($this->bangDanhSachThuCong($loai), $profileId, (array) ($duLieu['data'] ?? []));
            return ['ok' => true, 'thong_bao' => 'Da dong bo danh sach tu NextDNS.'];
        } catch (\Throwable $ngoaiLe) {
            return ['ok' => false, 'thong_bao' => $ngoaiLe->getMessage()];
        }
    }

    public function duLieuKiemTraTenMien(array $duLieuNhap = [], ?array $ketQua = null): array
    {
        $danhSachProfile = $this->danhSachProfileHienThi();
        $profileDangChon = $this->profileDangChon($danhSachProfile, (int) ($duLieuNhap['profile_id'] ?? 0));

        return [
            'danh_sach_profile' => $danhSachProfile,
            'profile_dang_chon' => $profileDangChon,
            'ten_mien' => trim((string) ($duLieuNhap['ten_mien'] ?? '')),
            'ket_qua_kiem_tra' => $ketQua,
            'co_kiem_tra_ten_mien' => co_quyen('kiem_tra_ten_mien_nextdns'),
        ];
    }

    public function kiemTraTenMien(int $profileId, string $tenMien): array
    {
        $tenMien = strtolower(trim($tenMien));
        if ($profileId <= 0) {
            return ['ok' => false, 'thong_bao' => 'Chua chon profile NextDNS.'];
        }
        if ($tenMien === '') {
            return ['ok' => false, 'thong_bao' => 'Chua nhap ten mien can kiem tra.'];
        }
        if (!$this->tenMienKiemTraHopLe($tenMien)) {
            return ['ok' => false, 'thong_bao' => 'Ten mien khong hop le. Chi chap nhan dang example.com hoac sub.example.com.'];
        }
        if (!$this->choPhepKiemTraTenMien()) {
            return ['ok' => false, 'thong_bao' => 'Ban thao tac qua nhanh. Vui long thu lai sau vai giay.'];
        }

        $profile = $this->khoDuLieu->timProfile($profileId);
        if ($profile === null) {
            return ['ok' => false, 'thong_bao' => 'Profile NextDNS khong ton tai hoac chua duoc chon.'];
        }

        try {
            $duLieu = $this->apiNextdns->kiem_tra_ten_mien_nextdns(
                (string) $profile['ma_profile_nextdns'],
                $this->apiNextdns->giaiMaApiKey((string) $profile['api_key_ma_hoa']),
                $tenMien
            );
            return $this->ketQuaKiemTraTenMienTuLogs($tenMien, $profile, (array) ($duLieu['data'] ?? []));
        } catch (\Throwable $ngoaiLe) {
            return [
                'ok' => false,
                'thong_bao' => $ngoaiLe->getMessage(),
                'ten_mien' => $tenMien,
                'profile' => (string) ($profile['ten_profile'] ?? ''),
                'trang_thai' => 'khong_xac_dinh',
                'thoi_gian_kiem_tra' => date('Y-m-d H:i:s'),
            ];
        }
    }

    public function themProfile(array $duLieuThuan): array
    {
        $duLieu = $this->xacThucDuLieuProfile($duLieuThuan, true);
        if ($duLieu['loi'] !== []) {
            return ['ok' => false, 'thong_bao' => implode(' ', $duLieu['loi'])];
        }

        try {
            $this->khoDuLieu->themProfile([
                'ten_profile' => $duLieu['ten_profile'],
                'ma_profile_nextdns' => $duLieu['ma_profile_nextdns'],
                'api_key_ma_hoa' => $this->apiNextdns->maHoaApiKey($duLieu['api_key']),
                'trang_thai' => $duLieu['trang_thai'],
                'ghi_chu' => $duLieu['ghi_chu'],
            ]);
            return ['ok' => true, 'thong_bao' => 'Da them profile NextDNS.'];
        } catch (\Throwable $ngoaiLe) {
            return ['ok' => false, 'thong_bao' => $this->thongBaoLuuProfile($ngoaiLe->getMessage())];
        }
    }

    public function capNhatProfile(int $id, array $duLieuThuan): array
    {
        if ($this->khoDuLieu->timProfile($id) === null) {
            return ['ok' => false, 'thong_bao' => 'Khong tim thay profile NextDNS can cap nhat.'];
        }

        $duLieu = $this->xacThucDuLieuProfile($duLieuThuan, false);
        if ($duLieu['loi'] !== []) {
            return ['ok' => false, 'thong_bao' => implode(' ', $duLieu['loi'])];
        }

        try {
            $duLieuCapNhat = [
                'ten_profile' => $duLieu['ten_profile'],
                'ma_profile_nextdns' => $duLieu['ma_profile_nextdns'],
                'trang_thai' => $duLieu['trang_thai'],
                'ghi_chu' => $duLieu['ghi_chu'],
            ];
            if ($duLieu['api_key'] !== '') {
                $duLieuCapNhat['api_key_ma_hoa'] = $this->apiNextdns->maHoaApiKey($duLieu['api_key']);
            }
            $this->khoDuLieu->capNhatProfile($id, $duLieuCapNhat);
            $this->khoDuLieu->xoaCacheThongKe($id);
            return ['ok' => true, 'thong_bao' => 'Da cap nhat profile NextDNS.'];
        } catch (\Throwable $ngoaiLe) {
            return ['ok' => false, 'thong_bao' => $this->thongBaoLuuProfile($ngoaiLe->getMessage())];
        }
    }

    public function xoaProfile(int $id): array
    {
        if ($this->khoDuLieu->timProfile($id) === null) {
            return ['ok' => false, 'thong_bao' => 'Khong tim thay profile NextDNS can xoa.'];
        }
        try {
            $this->khoDuLieu->xoaProfile($id);
            return ['ok' => true, 'thong_bao' => 'Da xoa profile NextDNS va cache lien quan.'];
        } catch (\Throwable) {
            return ['ok' => false, 'thong_bao' => 'Khong the xoa profile NextDNS.'];
        }
    }

    public function capNhatThongKe(int $profileId): array
    {
        $profile = $this->khoDuLieu->timProfile($profileId);
        if ($profile === null) {
            return ['ok' => false, 'co_du_lieu' => false, 'thong_bao' => 'Khong tim thay profile NextDNS.'];
        }

        try {
            $apiKey = $this->apiNextdns->giaiMaApiKey((string) $profile['api_key_ma_hoa']);
            $maProfile = (string) $profile['ma_profile_nextdns'];
            $thamSoStatus = ['from' => '-24h'];
            $thamSoSeries = [
                'from' => '-7d',
                'interval' => 86400,
                'alignment' => 'end',
                'partials' => 'all',
            ];

            $duLieu = [
                'trang_thai' => $this->apiNextdns->goi('GET', $maProfile, $apiKey, '/analytics/status', $thamSoStatus),
                'chuoi_trang_thai' => $this->apiNextdns->goi('GET', $maProfile, $apiKey, '/analytics/status;series', $thamSoSeries),
                'top_truy_van' => $this->apiNextdns->goi('GET', $maProfile, $apiKey, '/analytics/domains', ['from' => '-7d', 'limit' => 10]),
                'top_bi_chan' => $this->apiNextdns->goi('GET', $maProfile, $apiKey, '/analytics/domains', ['from' => '-7d', 'status' => 'blocked', 'limit' => 10]),
            ];
            $logsBiChan = $this->apiNextdns->goi('GET', $maProfile, $apiKey, '/logs', ['from' => '-7d', 'status' => 'blocked', 'limit' => 100]);
            $duLieu['bo_loc_chan'] = $this->tongHopBoLocChanTuLogs((array) ($logsBiChan['data'] ?? []));

            $this->khoDuLieu->luuCacheThongKe($profileId, $duLieu);
            $cache = $this->khoDuLieu->layCacheThongKe($profileId);
            return $cache !== null
                ? $this->taoDuLieuTongQuanTuCache($cache, 'Da cap nhat du lieu NextDNS thanh cong.')
                : ['ok' => false, 'co_du_lieu' => false, 'thong_bao' => 'Khong the luu du lieu thong ke NextDNS.'];
        } catch (\Throwable $ngoaiLe) {
            return ['ok' => false, 'co_du_lieu' => false, 'thong_bao' => $ngoaiLe->getMessage()];
        }
    }

    private function taoDuLieuTongQuanTuCache(array $cache, ?string $thongBao = null): array
    {
        $duLieu = (array) ($cache['du_lieu'] ?? []);
        $chiSo = $this->tongHopChiSoTrangThai((array) ($duLieu['trang_thai']['data'] ?? []));
        $boLocChan = $this->chuanHoaDuLieuBoLocChan((array) ($duLieu['bo_loc_chan'] ?? []));
        return [
            'ok' => true,
            'co_du_lieu' => true,
            'thong_bao' => $thongBao,
            'ngay_cap_nhat' => (string) ($cache['ngay_cap_nhat'] ?? ''),
            'chi_so' => $chiSo,
            'bieu_do_trang_thai' => $this->duLieuBieuDoTrangThai((array) ($duLieu['chuoi_trang_thai']['data'] ?? []), (array) ($duLieu['chuoi_trang_thai']['meta']['series']['times'] ?? [])),
            'top_truy_van' => (array) ($duLieu['top_truy_van']['data'] ?? []),
            'top_bi_chan' => (array) ($duLieu['top_bi_chan']['data'] ?? []),
            'bo_loc_chan' => $boLocChan,
            'bieu_do_top_truy_van' => $this->duLieuBieuDoCot((array) ($duLieu['top_truy_van']['data'] ?? []), 'domain'),
            'bieu_do_top_bi_chan' => $this->duLieuBieuDoCot((array) ($duLieu['top_bi_chan']['data'] ?? []), 'domain'),
            'bieu_do_bo_loc_chan' => $boLocChan,
        ];
    }

    private function danhSachProfileHienThi(): array
    {
        $ketQua = [];
        foreach ($this->khoDuLieu->layDanhSachProfile() as $profile) {
            try {
                $apiKey = $this->apiNextdns->giaiMaApiKey((string) $profile['api_key_ma_hoa']);
                $apiKeyAn = $this->apiNextdns->anApiKey($apiKey);
            } catch (\Throwable) {
                $apiKeyAn = 'Khong doc duoc API key';
            }

            $cache = $this->khoDuLieu->layCacheThongKe((int) $profile['id']);
            $ketQua[] = [
                'id' => (int) $profile['id'],
                'ten_profile' => (string) $profile['ten_profile'],
                'ma_profile_nextdns' => (string) $profile['ma_profile_nextdns'],
                'api_key_an' => $apiKeyAn,
                'trang_thai' => (int) ($profile['trang_thai'] ?? 0),
                'ghi_chu' => (string) ($profile['ghi_chu'] ?? ''),
                'ngay_cap_nhat' => (string) ($profile['ngay_cap_nhat'] ?? ''),
                'ngay_cap_nhat_cache' => (string) ($cache['ngay_cap_nhat'] ?? ''),
            ];
        }
        return $ketQua;
    }

    private function profileDangChon(array $danhSachProfile, int $profileId): ?array
    {
        foreach ($danhSachProfile as $profile) {
            if ($profileId > 0 && (int) $profile['id'] === $profileId) {
                return $profile;
            }
        }
        foreach ($danhSachProfile as $profile) {
            if ((int) $profile['trang_thai'] === 1) {
                return $profile;
            }
        }
        return $danhSachProfile[0] ?? null;
    }

    private function xacThucDuLieuProfile(array $duLieuThuan, bool $laThemMoi): array
    {
        $tenProfile = trim((string) ($duLieuThuan['ten_profile'] ?? ''));
        $maProfile = trim((string) ($duLieuThuan['ma_profile_nextdns'] ?? ''));
        $apiKey = trim((string) ($duLieuThuan['api_key'] ?? ''));
        $ghiChu = trim((string) ($duLieuThuan['ghi_chu'] ?? ''));
        $trangThai = (int) ($duLieuThuan['trang_thai'] ?? 0) === 1 ? 1 : 0;
        $loi = [];

        if ($tenProfile === '' || mb_strlen($tenProfile) > 150) {
            $loi[] = 'Ten profile bat buoc va toi da 150 ky tu.';
        }
        if ($maProfile === '' || !preg_match('/^[A-Za-z0-9]+$/', $maProfile)) {
            $loi[] = 'Ma profile NextDNS khong hop le.';
        }
        if ($laThemMoi && ($apiKey === '' || strlen($apiKey) < 20)) {
            $loi[] = 'API key NextDNS bat buoc khi them profile.';
        }
        if (!$laThemMoi && $apiKey !== '' && strlen($apiKey) < 20) {
            $loi[] = 'API key NextDNS khong hop le.';
        }
        if (mb_strlen($ghiChu) > 2000) {
            $loi[] = 'Ghi chu toi da 2000 ky tu.';
        }

        return [
            'ten_profile' => $tenProfile,
            'ma_profile_nextdns' => $maProfile,
            'api_key' => $apiKey,
            'trang_thai' => $trangThai,
            'ghi_chu' => $ghiChu,
            'loi' => $loi,
        ];
    }

    private function xacThucMucDanhSachThuCong(array $duLieuThuan): array
    {
        $profileId = (int) ($duLieuThuan['profile_id'] ?? 0);
        $domain = strtolower(trim((string) ($duLieuThuan['domain'] ?? '')));
        $ghiChu = trim((string) ($duLieuThuan['ghi_chu'] ?? ''));
        $trangThai = (int) ($duLieuThuan['trang_thai'] ?? 1) === 1 ? 1 : 0;
        $loi = [];

        if ($profileId <= 0 || $this->khoDuLieu->timProfile($profileId) === null) {
            $loi[] = 'Profile NextDNS khong hop le.';
        }
        if (!$this->domainHopLe($domain)) {
            $loi[] = 'Domain khong hop le.';
        }
        if (mb_strlen($ghiChu) > 1000) {
            $loi[] = 'Ghi chu toi da 1000 ky tu.';
        }

        return [
            'profile_id' => $profileId,
            'domain' => $domain,
            'ghi_chu' => $ghiChu,
            'trang_thai' => $trangThai,
            'loi' => $loi,
        ];
    }

    private function bangDanhSachThuCong(string $loai): string
    {
        return $loai === 'chan' ? 'nextdns_danh_sach_chan' : 'nextdns_danh_sach_cho_phep';
    }

    private function domainHopLe(string $domain): bool
    {
        if ($domain === '' || strlen($domain) > 253 || str_contains($domain, '://')) {
            return false;
        }
        return (bool) preg_match('/^(?:\*\.)?(?=.{1,253}$)(?!-)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/i', $domain);
    }

    private function tenMienKiemTraHopLe(string $tenMien): bool
    {
        if (str_contains($tenMien, '*') || str_contains($tenMien, '/') || str_contains($tenMien, '<') || str_contains($tenMien, '>')) {
            return false;
        }
        return (bool) preg_match('/^(?=.{1,253}$)(?!-)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/i', $tenMien);
    }

    private function choPhepKiemTraTenMien(): bool
    {
        $lanCuoi = (int) ($_SESSION['_nextdns_kiem_tra_ten_mien_lan_cuoi'] ?? 0);
        if ($lanCuoi > 0 && time() - $lanCuoi < 3) {
            return false;
        }
        $_SESSION['_nextdns_kiem_tra_ten_mien_lan_cuoi'] = time();
        return true;
    }

    private function ketQuaKiemTraTenMienTuLogs(string $tenMien, array $profile, array $logs): array
    {
        $banGhi = null;
        foreach ($logs as $dong) {
            if (!is_array($dong)) {
                continue;
            }
            $domainLog = strtolower((string) ($dong['domain'] ?? ''));
            $rootLog = strtolower((string) ($dong['root'] ?? ''));
            if ($domainLog === $tenMien || $rootLog === $tenMien) {
                $banGhi = $dong;
                break;
            }
        }

        if ($banGhi === null) {
            return [
                'ok' => true,
                'ten_mien' => $tenMien,
                'profile' => (string) ($profile['ten_profile'] ?? ''),
                'trang_thai' => 'khong_xac_dinh',
                'ly_do_chan' => 'Khong co du lieu logs gan day cho ten mien nay.',
                'bo_loc' => '',
                'thoi_gian_kiem_tra' => date('Y-m-d H:i:s'),
            ];
        }

        $trangThaiApi = (string) ($banGhi['status'] ?? '');
        $trangThai = $trangThaiApi === 'blocked' ? 'bi_chan' : (in_array($trangThaiApi, ['allowed', 'default'], true) ? 'khong_bi_chan' : 'khong_xac_dinh');
        $nhanBoLoc = $this->nhanBoLocChan($banGhi);

        return [
            'ok' => true,
            'ten_mien' => $tenMien,
            'profile' => (string) ($profile['ten_profile'] ?? ''),
            'trang_thai' => $trangThai,
            'ly_do_chan' => $trangThai === 'bi_chan' ? $nhanBoLoc : '',
            'bo_loc' => $trangThai === 'bi_chan' ? $nhanBoLoc : '',
            'thoi_gian_kiem_tra' => date('Y-m-d H:i:s'),
            'thoi_gian_log' => (string) ($banGhi['timestamp'] ?? ''),
        ];
    }

    private function chuanHoaDanhSachBoLoc(array $duLieu): array
    {
        $ketQua = [];
        foreach ($duLieu as $dong) {
            if (!is_array($dong)) {
                continue;
            }
            $ketQua[] = [
                'ten' => (string) ($dong['name'] ?? $dong['id'] ?? $dong['ten'] ?? 'Khong ro'),
                'trang_thai' => (int) (($dong['enabled'] ?? $dong['active'] ?? $dong['trang_thai'] ?? 1) ? 1 : 0),
                'loai' => (string) ($dong['type'] ?? $dong['category'] ?? $dong['loai'] ?? 'Blocklist'),
                'cap_nhat_cuoi' => (string) ($dong['updatedAt'] ?? $dong['cap_nhat_cuoi'] ?? ''),
            ];
        }
        return $ketQua;
    }

    private function themMucDanhSachQuaApi(string $loai, int $profileId, string $domain): void
    {
        $profile = $this->khoDuLieu->timProfile($profileId);
        if ($profile === null) {
            throw new \RuntimeException('Khong tim thay profile NextDNS.');
        }
        $maProfile = (string) $profile['ma_profile_nextdns'];
        $apiKey = $this->apiNextdns->giaiMaApiKey((string) $profile['api_key_ma_hoa']);
        if ($loai === 'chan') {
            $this->apiNextdns->themDanhSachChan($maProfile, $apiKey, $domain);
            return;
        }
        $this->apiNextdns->themDanhSachChoPhep($maProfile, $apiKey, $domain);
    }

    private function xoaMucDanhSachQuaApi(string $loai, int $profileId, string $domain): void
    {
        $profile = $this->khoDuLieu->timProfile($profileId);
        if ($profile === null) {
            throw new \RuntimeException('Khong tim thay profile NextDNS.');
        }
        $maProfile = (string) $profile['ma_profile_nextdns'];
        $apiKey = $this->apiNextdns->giaiMaApiKey((string) $profile['api_key_ma_hoa']);
        if ($loai === 'chan') {
            $this->apiNextdns->xoaDanhSachChan($maProfile, $apiKey, $domain);
            return;
        }
        $this->apiNextdns->xoaDanhSachChoPhep($maProfile, $apiKey, $domain);
    }

    private function thongBaoLuuProfile(string $thongBao): string
    {
        if (str_contains(strtolower($thongBao), 'duplicate') || str_contains(strtolower($thongBao), 'uk_nextdns_profile_ma')) {
            return 'Ma profile NextDNS da ton tai.';
        }
        return 'Khong the luu profile NextDNS.';
    }

    private function tongHopChiSoTrangThai(array $duLieu): array
    {
        $ketQua = [
            'tong_truy_van' => 0,
            'bi_chan' => 0,
            'duoc_phep' => 0,
            'mac_dinh' => 0,
            'loi' => 0,
        ];
        foreach ($duLieu as $dong) {
            if (!is_array($dong)) {
                continue;
            }
            $soLuong = (int) ($dong['queries'] ?? 0);
            $trangThai = (string) ($dong['status'] ?? '');
            $ketQua['tong_truy_van'] += $soLuong;
            if ($trangThai === 'blocked') {
                $ketQua['bi_chan'] += $soLuong;
            } elseif ($trangThai === 'allowed') {
                $ketQua['duoc_phep'] += $soLuong;
            } elseif ($trangThai === 'default') {
                $ketQua['mac_dinh'] += $soLuong;
            } elseif ($trangThai === 'error') {
                $ketQua['loi'] += $soLuong;
            }
        }
        return $ketQua;
    }

    private function duLieuBieuDoTrangThai(array $duLieu, array $mocThoiGian): array
    {
        $nhan = [];
        foreach ($mocThoiGian as $moc) {
            try {
                $nhan[] = (new DateTimeImmutable((string) $moc))->format('d/m');
            } catch (Exception) {
                $nhan[] = (string) $moc;
            }
        }

        $series = ['Bi chan' => [], 'Duoc phep' => [], 'Mac dinh' => []];
        foreach ($duLieu as $dong) {
            if (!is_array($dong) || !is_array($dong['queries'] ?? null)) {
                continue;
            }
            $nhom = match ((string) ($dong['status'] ?? '')) {
                'blocked' => 'Bi chan',
                'allowed' => 'Duoc phep',
                'default' => 'Mac dinh',
                default => null,
            };
            if ($nhom !== null) {
                $series[$nhom] = array_map(static fn($giaTri): int => (int) $giaTri, (array) $dong['queries']);
            }
        }

        $soNhan = count($nhan);
        foreach ($series as $ten => $giaTri) {
            $series[$ten] = array_values(array_pad(array_slice($giaTri, 0, $soNhan), $soNhan, 0));
        }

        return ['nhan' => $nhan, 'series' => $series];
    }

    private function duLieuBieuDoCot(array $duLieu, string $truongNhan): array
    {
        $nhan = [];
        $giaTri = [];
        foreach ($duLieu as $dong) {
            if (!is_array($dong)) {
                continue;
            }
            $nhan[] = (string) ($dong[$truongNhan] ?? 'Khac');
            $giaTri[] = (int) ($dong['queries'] ?? 0);
        }
        return ['nhan' => $nhan, 'gia_tri' => $giaTri];
    }

    private function tongHopBoLocChanTuLogs(array $logs): array
    {
        $dem = [];
        foreach ($logs as $dong) {
            if (!is_array($dong)) {
                continue;
            }
            $trangThai = (string) ($dong['status'] ?? 'blocked');
            if ($trangThai !== '' && $trangThai !== 'blocked') {
                continue;
            }
            $nhan = $this->nhanBoLocChan($dong);
            $dem[$nhan] = ($dem[$nhan] ?? 0) + 1;
        }

        arsort($dem);
        $dem = array_slice($dem, 0, 10, true);
        return [
            'labels' => array_keys($dem),
            'values' => array_values($dem),
        ];
    }

    private function chuanHoaDuLieuBoLocChan(array $duLieu): array
    {
        $labels = array_values(array_map('strval', (array) ($duLieu['labels'] ?? [])));
        $values = array_values(array_map(static fn($giaTri): int => (int) $giaTri, (array) ($duLieu['values'] ?? [])));
        $soLuong = min(count($labels), count($values));
        if ($soLuong <= 0) {
            return ['labels' => [], 'values' => []];
        }

        return [
            'labels' => array_slice($labels, 0, $soLuong),
            'values' => array_slice($values, 0, $soLuong),
        ];
    }

    private function nhanBoLocChan(array $dong): string
    {
        $truongTrucTiep = ['reason', 'list', 'matchedName', 'blocklist', 'category'];
        $truongTrongNhom = ['reason', 'list', 'matchedName', 'root', 'blocklist', 'category', 'name', 'id'];
        $nhan = $this->nhanBoLocTuMang($dong, $truongTrucTiep);
        if ($nhan !== null) {
            return $nhan;
        }

        foreach (['reasons', 'lists', 'matches'] as $truongNhom) {
            foreach ((array) ($dong[$truongNhom] ?? []) as $muc) {
                if (is_array($muc)) {
                    $nhan = $this->nhanBoLocTuMang($muc, $truongTrongNhom);
                    if ($nhan !== null) {
                        return $nhan;
                    }
                    continue;
                }
                $nhan = $this->chuanHoaNhanBoLoc($muc);
                if ($nhan !== null) {
                    return $nhan;
                }
            }
        }

        return 'Khong xac dinh';
    }

    private function nhanBoLocTuMang(array $duLieu, array $truongUuTien): ?string
    {
        foreach ($truongUuTien as $truong) {
            $nhan = $this->chuanHoaNhanBoLoc($duLieu[$truong] ?? null);
            if ($nhan !== null) {
                return $nhan;
            }
        }
        return null;
    }

    private function chuanHoaNhanBoLoc(mixed $giaTri): ?string
    {
        if (is_array($giaTri)) {
            foreach (['name', 'id', 'reason', 'list', 'matchedName', 'root', 'blocklist', 'category'] as $truong) {
                $nhan = $this->chuanHoaNhanBoLoc($giaTri[$truong] ?? null);
                if ($nhan !== null) {
                    return $nhan;
                }
            }
            return null;
        }

        $nhan = trim((string) ($giaTri ?? ''));
        if ($nhan === '' || $nhan === '__UNIDENTIFIED__') {
            return null;
        }
        return $nhan;
    }
}

class XuLy
{
    public static function tongQuan(?YeuCau $yeuCau = null, array $thamSo = []): void
    {
        $duLieu = (new DichVuNextdns())->duLieuTongQuan(is_array($_GET) ? $_GET : [], true);
        $GLOBALS['tieu_de_trang'] = 'NextDNS - Tong quan';
        $GLOBALS['breadcrumb'] = [
            ['tieu_de' => 'Quan tri', 'duong_dan' => '/'],
            ['tieu_de' => 'NextDNS', 'duong_dan' => '/nextdns'],
            ['tieu_de' => 'Tong quan'],
        ];
        hien_thi_bo_cuc(GOC_DU_AN . '/ung_dung/mo_dun/nextdns/tong_quan.php', $duLieu);
    }

    public static function capNhatDuLieu(YeuCau $yeuCau, array $thamSo): void
    {
        $profileId = (int) $yeuCau->dauVao('profile_id', 0);
        $ketQua = (new DichVuNextdns())->capNhatThongKe($profileId);
        self::ganThongBao($ketQua);
        chuyen_huong('/nextdns?profile_id=' . $profileId);
    }

    public static function quanLyProfile(?YeuCau $yeuCau = null, array $thamSo = []): void
    {
        $duLieu = (new DichVuNextdns())->duLieuQuanLyProfile(is_array($_GET) ? $_GET : []);
        $GLOBALS['tieu_de_trang'] = 'NextDNS - Quan ly profile';
        $GLOBALS['breadcrumb'] = [
            ['tieu_de' => 'Quan tri', 'duong_dan' => '/'],
            ['tieu_de' => 'NextDNS', 'duong_dan' => '/nextdns'],
            ['tieu_de' => 'Quan ly profile'],
        ];
        hien_thi_bo_cuc(GOC_DU_AN . '/ung_dung/mo_dun/nextdns/quan_ly_profile.php', $duLieu);
    }

    public static function themProfile(YeuCau $yeuCau, array $thamSo): void
    {
        $ketQua = (new DichVuNextdns())->themProfile([
            'ten_profile' => $yeuCau->dauVao('ten_profile'),
            'ma_profile_nextdns' => $yeuCau->dauVao('ma_profile_nextdns'),
            'api_key' => $yeuCau->dauVao('api_key'),
            'trang_thai' => $yeuCau->dauVao('trang_thai', 0),
            'ghi_chu' => $yeuCau->dauVao('ghi_chu', ''),
        ]);
        self::ganThongBao($ketQua);
        chuyen_huong('/nextdns/quan-ly-profile');
    }

    public static function capNhatProfile(YeuCau $yeuCau, array $thamSo): void
    {
        $ketQua = (new DichVuNextdns())->capNhatProfile((int) ($thamSo['id'] ?? 0), [
            'ten_profile' => $yeuCau->dauVao('ten_profile'),
            'ma_profile_nextdns' => $yeuCau->dauVao('ma_profile_nextdns'),
            'api_key' => $yeuCau->dauVao('api_key', ''),
            'trang_thai' => $yeuCau->dauVao('trang_thai', 0),
            'ghi_chu' => $yeuCau->dauVao('ghi_chu', ''),
        ]);
        self::ganThongBao($ketQua);
        chuyen_huong('/nextdns/quan-ly-profile');
    }

    public static function xoaProfile(YeuCau $yeuCau, array $thamSo): void
    {
        $ketQua = (new DichVuNextdns())->xoaProfile((int) ($thamSo['id'] ?? 0));
        self::ganThongBao($ketQua);
        chuyen_huong('/nextdns/quan-ly-profile');
    }

    public static function quanLyBoLoc(?YeuCau $yeuCau = null, array $thamSo = []): void
    {
        $duLieu = (new DichVuNextdns())->duLieuQuanLyBoLoc(is_array($_GET) ? $_GET : []);
        self::datTieuDe('Quan ly bo loc');
        hien_thi_bo_cuc(GOC_DU_AN . '/ung_dung/mo_dun/nextdns/quan_ly_bo_loc.php', $duLieu);
    }

    public static function capNhatBoLoc(YeuCau $yeuCau, array $thamSo): void
    {
        $profileId = (int) $yeuCau->dauVao('profile_id', 0);
        self::ganThongBao((new DichVuNextdns())->capNhatBoLoc($profileId));
        chuyen_huong('/nextdns/quan-ly-bo-loc?profile_id=' . $profileId);
    }

    public static function danhSachChanThuCong(?YeuCau $yeuCau = null, array $thamSo = []): void
    {
        $duLieu = (new DichVuNextdns())->duLieuDanhSachThuCong(is_array($_GET) ? $_GET : [], 'chan');
        self::datTieuDe('Danh sach chan');
        hien_thi_bo_cuc(GOC_DU_AN . '/ung_dung/mo_dun/nextdns/danh_sach_chan_thu_cong.php', $duLieu);
    }

    public static function danhSachChoPhepThuCong(?YeuCau $yeuCau = null, array $thamSo = []): void
    {
        $duLieu = (new DichVuNextdns())->duLieuDanhSachThuCong(is_array($_GET) ? $_GET : [], 'cho_phep');
        self::datTieuDe('Danh sach cho phep');
        hien_thi_bo_cuc(GOC_DU_AN . '/ung_dung/mo_dun/nextdns/danh_sach_cho_phep_thu_cong.php', $duLieu);
    }

    public static function themDanhSachChan(YeuCau $yeuCau, array $thamSo): void
    {
        self::xuLyThemDanhSach($yeuCau, 'chan', '/nextdns/danh-sach-chan-thu-cong');
    }

    public static function capNhatDanhSachChan(YeuCau $yeuCau, array $thamSo): void
    {
        self::xuLyCapNhatDanhSach($yeuCau, $thamSo, 'chan', '/nextdns/danh-sach-chan-thu-cong');
    }

    public static function xoaDanhSachChan(YeuCau $yeuCau, array $thamSo): void
    {
        self::xuLyXoaDanhSach($yeuCau, $thamSo, 'chan', '/nextdns/danh-sach-chan-thu-cong');
    }

    public static function chuyenTrangThaiDanhSachChan(YeuCau $yeuCau, array $thamSo): void
    {
        self::xuLyChuyenTrangThaiDanhSach($yeuCau, $thamSo, 'chan', '/nextdns/danh-sach-chan-thu-cong');
    }

    public static function dongBoDanhSachChan(YeuCau $yeuCau, array $thamSo): void
    {
        $profileId = (int) $yeuCau->dauVao('profile_id', 0);
        self::ganThongBao((new DichVuNextdns())->dongBoDanhSachThuCong('chan', $profileId));
        chuyen_huong('/nextdns/danh-sach-chan-thu-cong?profile_id=' . $profileId);
    }

    public static function themDanhSachChoPhep(YeuCau $yeuCau, array $thamSo): void
    {
        self::xuLyThemDanhSach($yeuCau, 'cho_phep', '/nextdns/danh-sach-cho-phep-thu-cong');
    }

    public static function capNhatDanhSachChoPhep(YeuCau $yeuCau, array $thamSo): void
    {
        self::xuLyCapNhatDanhSach($yeuCau, $thamSo, 'cho_phep', '/nextdns/danh-sach-cho-phep-thu-cong');
    }

    public static function xoaDanhSachChoPhep(YeuCau $yeuCau, array $thamSo): void
    {
        self::xuLyXoaDanhSach($yeuCau, $thamSo, 'cho_phep', '/nextdns/danh-sach-cho-phep-thu-cong');
    }

    public static function chuyenTrangThaiDanhSachChoPhep(YeuCau $yeuCau, array $thamSo): void
    {
        self::xuLyChuyenTrangThaiDanhSach($yeuCau, $thamSo, 'cho_phep', '/nextdns/danh-sach-cho-phep-thu-cong');
    }

    public static function dongBoDanhSachChoPhep(YeuCau $yeuCau, array $thamSo): void
    {
        $profileId = (int) $yeuCau->dauVao('profile_id', 0);
        self::ganThongBao((new DichVuNextdns())->dongBoDanhSachThuCong('cho_phep', $profileId));
        chuyen_huong('/nextdns/danh-sach-cho-phep-thu-cong?profile_id=' . $profileId);
    }

    public static function kiemTraTenMien(?YeuCau $yeuCau = null, array $thamSo = []): void
    {
        $duLieu = (new DichVuNextdns())->duLieuKiemTraTenMien(is_array($_GET) ? $_GET : []);
        self::datTieuDe('Kiem tra ten mien');
        hien_thi_bo_cuc(GOC_DU_AN . '/ung_dung/mo_dun/nextdns/kiem_tra_ten_mien.php', $duLieu);
    }

    public static function xuLyKiemTraTenMien(YeuCau $yeuCau, array $thamSo): void
    {
        $profileId = (int) $yeuCau->dauVao('profile_id', 0);
        $tenMien = (string) $yeuCau->dauVao('ten_mien', '');
        $dichVu = new DichVuNextdns();
        $ketQua = $dichVu->kiemTraTenMien($profileId, $tenMien);
        $duLieu = $dichVu->duLieuKiemTraTenMien(['profile_id' => $profileId, 'ten_mien' => $tenMien], $ketQua);
        self::datTieuDe('Kiem tra ten mien');
        hien_thi_bo_cuc(GOC_DU_AN . '/ung_dung/mo_dun/nextdns/kiem_tra_ten_mien.php', $duLieu);
    }

    private static function xuLyThemDanhSach(YeuCau $yeuCau, string $loai, string $duongDan): void
    {
        $profileId = (int) $yeuCau->dauVao('profile_id', 0);
        self::ganThongBao((new DichVuNextdns())->themMucDanhSachThuCong($loai, [
            'profile_id' => $profileId,
            'domain' => $yeuCau->dauVao('domain', ''),
            'ghi_chu' => $yeuCau->dauVao('ghi_chu', ''),
            'trang_thai' => $yeuCau->dauVao('trang_thai', 1),
        ]));
        chuyen_huong($duongDan . '?profile_id=' . $profileId);
    }

    private static function xuLyCapNhatDanhSach(YeuCau $yeuCau, array $thamSo, string $loai, string $duongDan): void
    {
        $profileId = (int) $yeuCau->dauVao('profile_id', 0);
        self::ganThongBao((new DichVuNextdns())->capNhatMucDanhSachThuCong($loai, (int) ($thamSo['id'] ?? 0), [
            'profile_id' => $profileId,
            'domain' => $yeuCau->dauVao('domain', ''),
            'ghi_chu' => $yeuCau->dauVao('ghi_chu', ''),
            'trang_thai' => $yeuCau->dauVao('trang_thai', 1),
        ]));
        chuyen_huong($duongDan . '?profile_id=' . $profileId);
    }

    private static function xuLyXoaDanhSach(YeuCau $yeuCau, array $thamSo, string $loai, string $duongDan): void
    {
        $profileId = (int) $yeuCau->dauVao('profile_id', 0);
        self::ganThongBao((new DichVuNextdns())->xoaMucDanhSachThuCong($loai, (int) ($thamSo['id'] ?? 0)));
        chuyen_huong($duongDan . ($profileId > 0 ? '?profile_id=' . $profileId : ''));
    }

    private static function xuLyChuyenTrangThaiDanhSach(YeuCau $yeuCau, array $thamSo, string $loai, string $duongDan): void
    {
        $profileId = (int) $yeuCau->dauVao('profile_id', 0);
        self::ganThongBao((new DichVuNextdns())->chuyenTrangThaiMucDanhSachThuCong($loai, (int) ($thamSo['id'] ?? 0)));
        chuyen_huong($duongDan . ($profileId > 0 ? '?profile_id=' . $profileId : ''));
    }

    private static function datTieuDe(string $tieuDe): void
    {
        $GLOBALS['tieu_de_trang'] = 'NextDNS - ' . $tieuDe;
        $GLOBALS['breadcrumb'] = [
            ['tieu_de' => 'Quan tri', 'duong_dan' => '/'],
            ['tieu_de' => 'NextDNS', 'duong_dan' => '/nextdns'],
            ['tieu_de' => $tieuDe],
        ];
    }

    private static function ganThongBao(array $ketQua): void
    {
        $_SESSION['_thong_bao'] = [
            'loai' => !empty($ketQua['ok']) ? 'success' : 'danger',
            'noi_dung' => (string) ($ketQua['thong_bao'] ?? 'Khong the thuc hien thao tac.'),
        ];
    }
}
