<?php
declare(strict_types=1);

namespace MoDun\NhanSu\DieuKhien;

use HeThong\YeuCau;
use MoDun\NhanSu\MoHinh\NhanSuMoHinh;

class ChucVuDieuKhien
{
    private static function coQuyenQuanLy(): bool
    {
        return co_quyen('nhan_su.quan_ly');
    }
    public static function danhSach(?YeuCau $yeuCau = null, array $thamSo = []): void
    {
        if (!co_quyen('chuc_vu.xem')) { http_response_code(403); echo 'Ban khong co quyen xem chuc vu.'; return; }
        $ds = (new NhanSuMoHinh())->layDanhSachChucVu();
        hien_thi_bo_cuc(GOC_DU_AN . '/ung_dung/mo_dun/nhan_su/GiaoDien/chuc_vu/danh_sach.php', ['ds' => $ds]);
    }

    public static function formThem(?YeuCau $yeuCau = null, array $thamSo = []): void
    {
        if (!co_quyen('chuc_vu.them')) { http_response_code(403); echo 'Ban khong co quyen them chuc vu.'; return; }
        hien_thi_bo_cuc(GOC_DU_AN . '/ung_dung/mo_dun/nhan_su/GiaoDien/chuc_vu/form.php', ['cheDo' => 'them', 'duLieu' => [], 'loiTheoTruong' => []]);
    }

    public static function luuMoi(YeuCau $yeuCau, array $thamSo = []): void
    {
        self::luu($yeuCau, null);
    }

    public static function formSua(YeuCau $yeuCau, array $thamSo): void
    {
        if (!co_quyen('chuc_vu.sua')) { http_response_code(403); echo 'Ban khong co quyen sua chuc vu.'; return; }
        $id = (int)($thamSo['id'] ?? 0);
        $duLieu = (new NhanSuMoHinh())->timChucVuTheoId($id);
        if (!$duLieu) {
            http_response_code(404);
            echo 'Khong tim thay chuc vu.';
            return;
        }
        hien_thi_bo_cuc(GOC_DU_AN . '/ung_dung/mo_dun/nhan_su/GiaoDien/chuc_vu/form.php', ['cheDo' => 'sua', 'duLieu' => $duLieu, 'loiTheoTruong' => []]);
    }

    public static function luuSua(YeuCau $yeuCau, array $thamSo): void
    {
        self::luu($yeuCau, (int)($thamSo['id'] ?? 0));
    }

    public static function xoa(YeuCau $yeuCau, array $thamSo): void
    {
        if (!co_quyen('chuc_vu.xoa')) { http_response_code(403); echo 'Ban khong co quyen xoa chuc vu.'; return; }
        if (!self::coQuyenQuanLy()) { http_response_code(403); echo 'Ban khong co quyen quan ly nhan su.'; return; }
        (new NhanSuMoHinh())->xoaChucVu((int)($thamSo['id'] ?? 0));
        $_SESSION['_thong_bao'] = ['loai' => 'success', 'noi_dung' => 'Da xoa chuc vu.'];
        chuyen_huong('/nhan-su/chuc-vu');
    }

    private static function luu(YeuCau $yeuCau, ?int $id): void
    {
        if (!self::coQuyenQuanLy()) { http_response_code(403); echo 'Ban khong co quyen quan ly nhan su.'; return; }
        if ($id === null && !co_quyen('chuc_vu.them')) { http_response_code(403); echo 'Ban khong co quyen them chuc vu.'; return; }
        if ($id !== null && !co_quyen('chuc_vu.sua')) { http_response_code(403); echo 'Ban khong co quyen sua chuc vu.'; return; }
        $moHinh = new NhanSuMoHinh();
        $duLieu = [
            'code' => strtoupper(trim((string)$yeuCau->dauVao('code'))),
            'name' => trim((string)$yeuCau->dauVao('name')),
            'description' => trim((string)$yeuCau->dauVao('description', '')) ?: null,
            'status' => trim((string)$yeuCau->dauVao('status', 'hoat_dong')),
        ];
        $loi = [];
        if ($duLieu['code'] === '') $loi['code'] = 'Ma chuc vu khong duoc de trong.';
        if ($duLieu['name'] === '') $loi['name'] = 'Ten chuc vu khong duoc de trong.';
        if (!in_array($duLieu['status'], ['hoat_dong', 'khong_hoat_dong'], true)) $loi['status'] = 'Trang thai khong hop le.';
        if ($moHinh->maChucVuDaTonTai($duLieu['code'], $id)) $loi['code'] = 'Ma chuc vu da ton tai.';

        if ($loi !== []) {
            $duLieu['id'] = $id;
            hien_thi_bo_cuc(GOC_DU_AN . '/ung_dung/mo_dun/nhan_su/GiaoDien/chuc_vu/form.php', ['cheDo' => $id === null ? 'them' : 'sua', 'duLieu' => $duLieu, 'loiTheoTruong' => $loi]);
            return;
        }

        if ($id === null) {
            $moHinh->taoChucVu($duLieu);
            $_SESSION['_thong_bao'] = ['loai' => 'success', 'noi_dung' => 'Da them chuc vu.'];
        } else {
            $moHinh->capNhatChucVu($id, $duLieu);
            $_SESSION['_thong_bao'] = ['loai' => 'success', 'noi_dung' => 'Da cap nhat chuc vu.'];
        }
        chuyen_huong('/nhan-su/chuc-vu');
    }
}
