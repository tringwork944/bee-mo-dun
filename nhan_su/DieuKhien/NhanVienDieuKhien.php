<?php
declare(strict_types=1);

namespace MoDun\NhanSu\DieuKhien;

use HeThong\YeuCau;
use MoDun\NhanSu\MoHinh\NhanSuMoHinh;

class NhanVienDieuKhien
{
    private static function coQuyenQuanLy(): bool
    {
        return co_quyen('nhan_su.quan_ly');
    }

    public static function danhSach(YeuCau $yeuCau, array $thamSo = []): void
    {
        if (!co_quyen('nhan_vien.xem')) {
            http_response_code(403);
            echo 'Ban khong co quyen xem nhan vien.';
            return;
        }
        $trang = max(1, (int)$yeuCau->dauVao('trang', 1));
        $moiTrang = 10;
        $boLoc = [
            'tu_khoa' => trim((string)$yeuCau->dauVao('tu_khoa', '')),
            'trang_thai' => trim((string)$yeuCau->dauVao('trang_thai', '')),
            'department_id' => (int)$yeuCau->dauVao('department_id', 0),
            'position_id' => (int)$yeuCau->dauVao('position_id', 0),
        ];
        $moHinh = new NhanSuMoHinh();
        $ketQua = $moHinh->layDanhSachNhanVien($boLoc, $trang, $moiTrang);
        hien_thi_bo_cuc(GOC_DU_AN . '/ung_dung/mo_dun/nhan_su/GiaoDien/nhan_vien/danh_sach.php', [
            'ds' => $ketQua['du_lieu'],
            'tong' => $ketQua['tong'],
            'trang' => $trang,
            'moiTrang' => $moiTrang,
            'boLoc' => $boLoc,
            'phongBan' => $moHinh->layDanhSachPhongBan(),
            'chucVu' => $moHinh->layDanhSachChucVu(),
            'thongKe' => $moHinh->thongKeTongQuan(),
            'taiKhoan' => $moHinh->layDanhSachTaiKhoanChuaLienKet(),
        ]);
    }

    public static function chiTiet(YeuCau $yeuCau, array $thamSo): void
    {
        if (!co_quyen('nhan_vien.xem')) {
            http_response_code(403);
            echo 'Ban khong co quyen xem nhan vien.';
            return;
        }
        $id = (int)($thamSo['id'] ?? 0);
        $nv = (new NhanSuMoHinh())->timNhanVienTheoId($id);
        if (!$nv) {
            http_response_code(404);
            echo 'Khong tim thay nhan vien.';
            return;
        }
        hien_thi_bo_cuc(GOC_DU_AN . '/ung_dung/mo_dun/nhan_su/GiaoDien/nhan_vien/chi_tiet.php', ['nv' => $nv]);
    }

    public static function formThem(?YeuCau $yeuCau = null, array $thamSo = []): void
    {
        if (!co_quyen('nhan_vien.them')) { http_response_code(403); echo 'Ban khong co quyen them nhan vien.'; return; }
        $moHinh = new NhanSuMoHinh();
        hien_thi_bo_cuc(GOC_DU_AN . '/ung_dung/mo_dun/nhan_su/GiaoDien/nhan_vien/form.php', [
            'cheDo' => 'them',
            'duLieu' => [],
            'loiTheoTruong' => [],
            'phongBan' => $moHinh->layDanhSachPhongBan(),
            'chucVu' => $moHinh->layDanhSachChucVu(),
            'taiKhoan' => $moHinh->layDanhSachTaiKhoanChuaLienKet(),
        ]);
    }

    public static function luuMoi(YeuCau $yeuCau, array $thamSo = []): void
    {
        self::luu($yeuCau, null);
    }

    public static function formSua(YeuCau $yeuCau, array $thamSo): void
    {
        if (!co_quyen('nhan_vien.sua')) { http_response_code(403); echo 'Ban khong co quyen sua nhan vien.'; return; }
        $id = (int)($thamSo['id'] ?? 0);
        $moHinh = new NhanSuMoHinh();
        $nv = $moHinh->timNhanVienTheoId($id);
        if (!$nv) {
            http_response_code(404);
            echo 'Khong tim thay nhan vien.';
            return;
        }
        hien_thi_bo_cuc(GOC_DU_AN . '/ung_dung/mo_dun/nhan_su/GiaoDien/nhan_vien/form.php', [
            'cheDo' => 'sua',
            'duLieu' => $nv,
            'loiTheoTruong' => [],
            'phongBan' => $moHinh->layDanhSachPhongBan(),
            'chucVu' => $moHinh->layDanhSachChucVu(),
            'taiKhoan' => $moHinh->layDanhSachTaiKhoanChuaLienKet(),
        ]);
    }

    public static function luuSua(YeuCau $yeuCau, array $thamSo): void
    {
        $id = (int)($thamSo['id'] ?? 0);
        self::luu($yeuCau, $id);
    }

    public static function xoa(YeuCau $yeuCau, array $thamSo): void
    {
        if (!co_quyen('nhan_vien.xoa')) { http_response_code(403); echo 'Ban khong co quyen xoa nhan vien.'; return; }
        if (!self::coQuyenQuanLy()) {
            http_response_code(403);
            echo 'Ban khong co quyen quan ly nhan su.';
            return;
        }
        $id = (int)($thamSo['id'] ?? 0);
        if ($id > 0) {
            (new NhanSuMoHinh())->xoaMemNhanVien($id);
            $_SESSION['_thong_bao'] = ['loai' => 'success', 'noi_dung' => 'Da xoa nhan vien.'];
        }
        chuyen_huong('/nhan-su/nhan-vien');
    }

    private static function luu(YeuCau $yeuCau, ?int $id): void
    {
        if (!self::coQuyenQuanLy()) {
            http_response_code(403);
            echo 'Ban khong co quyen quan ly nhan su.';
            return;
        }
        if ($id === null && !co_quyen('nhan_vien.them')) { http_response_code(403); echo 'Ban khong co quyen them nhan vien.'; return; }
        if ($id !== null && !co_quyen('nhan_vien.sua')) { http_response_code(403); echo 'Ban khong co quyen sua nhan vien.'; return; }
        $moHinh = new NhanSuMoHinh();
        $duLieu = [
            'employee_code' => strtoupper(trim((string)$yeuCau->dauVao('employee_code'))),
            'full_name' => trim((string)$yeuCau->dauVao('full_name')),
            'email' => strtolower(trim((string)$yeuCau->dauVao('email'))),
            'phone' => trim((string)$yeuCau->dauVao('phone', '')) ?: null,
            'birth_date' => trim((string)$yeuCau->dauVao('birth_date', '')) ?: null,
            'gender' => trim((string)$yeuCau->dauVao('gender', '')) ?: null,
            'address' => trim((string)$yeuCau->dauVao('address', '')) ?: null,
            'account_id' => ((int)$yeuCau->dauVao('account_id', 0)) ?: null,
            'department_id' => ((int)$yeuCau->dauVao('department_id', 0)) ?: null,
            'position_id' => ((int)$yeuCau->dauVao('position_id', 0)) ?: null,
            'join_date' => trim((string)$yeuCau->dauVao('join_date', '')) ?: null,
            'status' => trim((string)$yeuCau->dauVao('status', 'dang_lam')),
            'note' => trim((string)$yeuCau->dauVao('note', '')) ?: null,
        ];
        $loi = self::kiemTraHopLe($moHinh, $duLieu, $id);

        if ($loi !== []) {
            $duLieu['id'] = $id;
            hien_thi_bo_cuc(GOC_DU_AN . '/ung_dung/mo_dun/nhan_su/GiaoDien/nhan_vien/form.php', [
                'cheDo' => $id === null ? 'them' : 'sua',
                'duLieu' => $duLieu,
                'loiTheoTruong' => $loi,
                'phongBan' => $moHinh->layDanhSachPhongBan(),
                'chucVu' => $moHinh->layDanhSachChucVu(),
                'taiKhoan' => $moHinh->layDanhSachTaiKhoanChuaLienKet(),
            ]);
            return;
        }

        if ($id === null) {
            $moHinh->taoNhanVien($duLieu);
            $_SESSION['_thong_bao'] = ['loai' => 'success', 'noi_dung' => 'Da them nhan vien.'];
        } else {
            $moHinh->capNhatNhanVien($id, $duLieu);
            $_SESSION['_thong_bao'] = ['loai' => 'success', 'noi_dung' => 'Da cap nhat nhan vien.'];
        }
        chuyen_huong('/nhan-su/nhan-vien');
    }

    private static function kiemTraHopLe(NhanSuMoHinh $moHinh, array $duLieu, ?int $id): array
    {
        $loi = [];
        if ($duLieu['employee_code'] === '') $loi['employee_code'] = 'Ma nhan vien khong duoc de trong.';
        if ($duLieu['full_name'] === '') $loi['full_name'] = 'Ho ten khong duoc de trong.';
        if ($duLieu['email'] === '' || !filter_var($duLieu['email'], FILTER_VALIDATE_EMAIL)) $loi['email'] = 'Email khong hop le.';
        if (!in_array($duLieu['status'], ['dang_lam', 'nghi_viec', 'tam_nghi'], true)) $loi['status'] = 'Trang thai khong hop le.';
        if ($duLieu['gender'] !== null && !in_array($duLieu['gender'], ['nam', 'nu', 'khac'], true)) $loi['gender'] = 'Gioi tinh khong hop le.';
        if ($duLieu['phone'] !== null && !preg_match('/^[0-9+\\-\\s().]{8,30}$/', $duLieu['phone'])) $loi['phone'] = 'So dien thoai khong hop le.';
        if ($duLieu['birth_date'] !== null && !preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $duLieu['birth_date'])) $loi['birth_date'] = 'Ngay sinh khong hop le.';
        if ($duLieu['join_date'] !== null && !preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $duLieu['join_date'])) $loi['join_date'] = 'Ngay vao lam khong hop le.';
        if ($moHinh->maNhanVienDaTonTai($duLieu['employee_code'], $id)) $loi['employee_code'] = 'Ma nhan vien da ton tai.';
        if ($moHinh->emailNhanVienDaTonTai($duLieu['email'], $id)) $loi['email'] = 'Email da ton tai.';
        if ($duLieu['account_id'] !== null && $moHinh->accountDaLienKet((int)$duLieu['account_id'], $id)) $loi['account_id'] = 'Tai khoan da lien ket voi nhan vien khac.';
        return $loi;
    }

    public static function taoTaiKhoanNhanh(YeuCau $yeuCau, array $thamSo): void
    {
        if (!self::coQuyenQuanLy() || !co_quyen('tai_khoan.them')) {
            http_response_code(403);
            echo 'Ban khong co quyen tao tai khoan.';
            return;
        }
        $id = (int)($thamSo['id'] ?? 0);
        if (!co_quyen('nhan_vien.sua')) { http_response_code(403); echo 'Ban khong co quyen sua nhan vien.'; return; }
        $accountId = (new NhanSuMoHinh())->taoTaiKhoanNhanhTuNhanVien($id);
        if ($accountId) {
            $_SESSION['_thong_bao'] = ['loai' => 'success', 'noi_dung' => 'Da tao tai khoan nhanh cho nhan vien.'];
        } else {
            $_SESSION['_thong_bao'] = ['loai' => 'danger', 'noi_dung' => 'Khong the tao tai khoan nhanh.'];
        }
        chuyen_huong('/nhan-su/nhan-vien/sua/' . $id);
    }
}
