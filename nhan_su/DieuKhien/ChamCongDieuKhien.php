<?php
declare(strict_types=1);

namespace MoDun\NhanSu\DieuKhien;

use HeThong\YeuCau;
use MoDun\NhanSu\MoHinh\NhanSuMoHinh;

class ChamCongDieuKhien
{
    private static function coQuyenQuanLy(): bool
    {
        return co_quyen('nhan_su.quan_ly');
    }
    public static function danhSach(YeuCau $yeuCau, array $thamSo = []): void
    {
        if (!co_quyen('cham_cong.xem')) { http_response_code(403); echo 'Ban khong co quyen xem cham cong.'; return; }
        $thangMacDinh = (int)$yeuCau->dauVao('month', (int)date('n'));
        $namMacDinh = (int)$yeuCau->dauVao('year', (int)date('Y'));
        $boLoc = [
            'tu_ngay' => trim((string)$yeuCau->dauVao('tu_ngay', '')),
            'den_ngay' => trim((string)$yeuCau->dauVao('den_ngay', '')),
            'thang' => trim((string)$yeuCau->dauVao('thang', '')),
            'nam' => (int)$yeuCau->dauVao('nam', $namMacDinh),
            'month' => max(1, min(12, $thangMacDinh)),
            'year' => max(1970, $namMacDinh),
            'employee_id' => (int)$yeuCau->dauVao('employee_id', 0),
            'department_id' => (int)$yeuCau->dauVao('department_id', 0),
            'status' => trim((string)$yeuCau->dauVao('status', '')),
            'keyword' => trim((string)$yeuCau->dauVao('keyword', '')),
        ];
        if (preg_match('/^\d{4}-\d{2}$/', $boLoc['thang'])) {
            $boLoc['nam'] = (int)substr($boLoc['thang'], 0, 4);
            $boLoc['thang'] = substr($boLoc['thang'], 5, 2);
            $boLoc['month'] = (int)$boLoc['thang'];
            $boLoc['year'] = $boLoc['nam'];
        } else {
            $boLoc['thang'] = str_pad((string)$boLoc['month'], 2, '0', STR_PAD_LEFT);
        }
        $cheDoXem = trim((string)$yeuCau->dauVao('view', 'employee_calendar'));
        if (!in_array($cheDoXem, ['employee_calendar', 'list'], true)) $cheDoXem = 'employee_calendar';
        $trang = max(1, (int)$yeuCau->dauVao('trang', 1));
        $moiTrang = 15;
        $moHinh = new NhanSuMoHinh();
        $ketQua = $moHinh->layDanhSachChamCong($boLoc, $trang, $moiTrang);
        $duLieuMaTran = $moHinh->layMaTranChamCongTheoThang($boLoc);
        hien_thi_bo_cuc(GOC_DU_AN . '/ung_dung/mo_dun/nhan_su/GiaoDien/cham_cong/danh_sach.php', [
            'ds' => $ketQua['du_lieu'],
            'tong' => $ketQua['tong'],
            'thongKe' => $ketQua['thong_ke'],
            'trang' => $trang,
            'moiTrang' => $moiTrang,
            'boLoc' => $boLoc,
            'cheDoXem' => $cheDoXem,
            'duLieuMaTran' => $duLieuMaTran,
            'nhanVien' => $moHinh->layNhanVienDangLam(),
            'phongBan' => $moHinh->layDanhSachPhongBan(),
        ]);
    }

    public static function apiCalendar(YeuCau $yeuCau, array $thamSo = []): void
    {
        if (!co_quyen('cham_cong.xem')) {
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => false, 'thong_bao' => 'Khong co quyen']);
            return;
        }
        $thang = max(1, min(12, (int)$yeuCau->dauVao('month', (int)date('n'))));
        $nam = max(1970, (int)$yeuCau->dauVao('year', (int)date('Y')));
        $boLoc = [
            'thang' => str_pad((string)$thang, 2, '0', STR_PAD_LEFT),
            'nam' => $nam,
            'employee_id' => (int)$yeuCau->dauVao('nhan_vien_id', 0),
            'department_id' => (int)$yeuCau->dauVao('phong_ban_id', 0),
            'status' => trim((string)$yeuCau->dauVao('trang_thai', '')),
        ];
        $duLieu = (new NhanSuMoHinh())->layThongKeChamCongTheoThang($boLoc);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => true, 'du_lieu' => $duLieu], JSON_UNESCAPED_UNICODE);
    }

    public static function apiMonthlyMatrix(YeuCau $yeuCau, array $thamSo = []): void
    {
        if (!co_quyen('cham_cong.xem')) {
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => false, 'thong_bao' => 'Khong co quyen']);
            return;
        }
        $boLoc = [
            'month' => max(1, min(12, (int)$yeuCau->dauVao('month', (int)date('n')))),
            'year' => max(1970, (int)$yeuCau->dauVao('year', (int)date('Y'))),
            'department_id' => (int)$yeuCau->dauVao('phong_ban_id', 0),
            'status' => trim((string)$yeuCau->dauVao('trang_thai', '')),
            'keyword' => trim((string)$yeuCau->dauVao('keyword', '')),
        ];
        $duLieu = (new NhanSuMoHinh())->layMaTranChamCongTheoThang($boLoc);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => true] + $duLieu, JSON_UNESCAPED_UNICODE);
    }

    public static function formThem(?YeuCau $yeuCau = null, array $thamSo = []): void
    {
        if (!co_quyen('cham_cong.them')) { http_response_code(403); echo 'Ban khong co quyen them cham cong.'; return; }
        $moHinh = new NhanSuMoHinh();
        $ngayMacDinh = trim((string)($yeuCau?->dauVao('attendance_date', date('Y-m-d')) ?? date('Y-m-d')));
        $nhanVienMacDinh = (int)($yeuCau?->dauVao('employee_id', 0) ?? 0);
        $quayLai = trim((string)($yeuCau?->dauVao('quay_lai', '/nhan-su/cham-cong') ?? '/nhan-su/cham-cong'));
        hien_thi_bo_cuc(GOC_DU_AN . '/ung_dung/mo_dun/nhan_su/GiaoDien/cham_cong/form.php', [
            'cheDo' => 'them',
            'duLieu' => ['attendance_date' => $ngayMacDinh, 'status' => 'di_lam', 'employee_id' => $nhanVienMacDinh],
            'quayLai' => $quayLai,
            'nhanVien' => $moHinh->layNhanVienDangLam(),
            'loiTheoTruong' => [],
        ]);
    }

    public static function luuMoi(YeuCau $yeuCau, array $thamSo = []): void
    {
        self::luu($yeuCau, null);
    }

    public static function formSua(YeuCau $yeuCau, array $thamSo): void
    {
        if (!co_quyen('cham_cong.sua')) { http_response_code(403); echo 'Ban khong co quyen sua cham cong.'; return; }
        $id = (int)($thamSo['id'] ?? 0);
        $moHinh = new NhanSuMoHinh();
        $duLieu = $moHinh->timChamCongTheoId($id);
        if (!$duLieu) {
            http_response_code(404);
            echo 'Khong tim thay ban ghi cham cong.';
            return;
        }
        hien_thi_bo_cuc(GOC_DU_AN . '/ung_dung/mo_dun/nhan_su/GiaoDien/cham_cong/form.php', [
            'cheDo' => 'sua',
            'duLieu' => $duLieu,
            'quayLai' => trim((string)$yeuCau->dauVao('quay_lai', '/nhan-su/cham-cong')),
            'nhanVien' => $moHinh->layNhanVienDangLam(),
            'loiTheoTruong' => [],
        ]);
    }

    public static function luuSua(YeuCau $yeuCau, array $thamSo): void
    {
        self::luu($yeuCau, (int)($thamSo['id'] ?? 0));
    }

    public static function chiTiet(YeuCau $yeuCau, array $thamSo): void
    {
        if (!co_quyen('cham_cong.xem')) { http_response_code(403); echo 'Ban khong co quyen xem cham cong.'; return; }
        $id = (int)($thamSo['id'] ?? 0);
        $duLieu = (new NhanSuMoHinh())->timChamCongTheoId($id);
        if (!$duLieu) {
            http_response_code(404);
            echo 'Khong tim thay ban ghi cham cong.';
            return;
        }
        hien_thi_bo_cuc(GOC_DU_AN . '/ung_dung/mo_dun/nhan_su/GiaoDien/cham_cong/chi_tiet.php', ['duLieu' => $duLieu]);
    }

    public static function xoa(YeuCau $yeuCau, array $thamSo): void
    {
        if (!co_quyen('cham_cong.xoa')) { http_response_code(403); echo 'Ban khong co quyen xoa cham cong.'; return; }
        if (!self::coQuyenQuanLy()) { http_response_code(403); echo 'Ban khong co quyen quan ly nhan su.'; return; }
        (new NhanSuMoHinh())->xoaMemChamCong((int)($thamSo['id'] ?? 0));
        $_SESSION['_thong_bao'] = ['loai' => 'success', 'noi_dung' => 'Da xoa ban ghi cham cong.'];
        chuyen_huong('/nhan-su/cham-cong');
    }

    private static function luu(YeuCau $yeuCau, ?int $id): void
    {
        if (!self::coQuyenQuanLy()) { http_response_code(403); echo 'Ban khong co quyen quan ly nhan su.'; return; }
        if ($id === null && !co_quyen('cham_cong.them')) { http_response_code(403); echo 'Ban khong co quyen them cham cong.'; return; }
        if ($id !== null && !co_quyen('cham_cong.sua')) { http_response_code(403); echo 'Ban khong co quyen sua cham cong.'; return; }
        $moHinh = new NhanSuMoHinh();
        $checkIn = trim((string)$yeuCau->dauVao('check_in', ''));
        $checkOut = trim((string)$yeuCau->dauVao('check_out', ''));
        $tongGio = self::tinhTongGio($checkIn, $checkOut);
        $duLieu = [
            'employee_id' => (int)$yeuCau->dauVao('employee_id', 0),
            'attendance_date' => trim((string)$yeuCau->dauVao('attendance_date')),
            'check_in' => $checkIn !== '' ? $checkIn : null,
            'check_out' => $checkOut !== '' ? $checkOut : null,
            'total_hours' => $tongGio,
            'status' => trim((string)$yeuCau->dauVao('status', 'di_lam')),
            'note' => trim((string)$yeuCau->dauVao('note', '')) ?: null,
        ];

        $loi = [];
        if ($duLieu['employee_id'] <= 0) $loi['employee_id'] = 'Vui long chon nhan vien.';
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $duLieu['attendance_date'])) $loi['attendance_date'] = 'Ngay khong hop le.';
        if (!in_array($duLieu['status'], ['di_lam', 'di_muon', 'nghi_phep', 'vang', 'tang_ca', 'lam_o_nha', 'nua_ngay', 'ngay_le', 'ngay_nghi'], true)) $loi['status'] = 'Trang thai khong hop le.';
        if ($duLieu['attendance_date'] !== '' && $duLieu['employee_id'] > 0 && $moHinh->chamCongDaTonTai($duLieu['employee_id'], $duLieu['attendance_date'], $id)) {
            $loi['attendance_date'] = 'Nhan vien da co ban ghi cham cong trong ngay nay.';
        }

        if ($loi !== []) {
            $duLieu['id'] = $id;
            hien_thi_bo_cuc(GOC_DU_AN . '/ung_dung/mo_dun/nhan_su/GiaoDien/cham_cong/form.php', [
                'cheDo' => $id === null ? 'them' : 'sua',
                'duLieu' => $duLieu,
                'nhanVien' => $moHinh->layNhanVienDangLam(),
                'quayLai' => trim((string)$yeuCau->dauVao('quay_lai', '/nhan-su/cham-cong')),
                'loiTheoTruong' => $loi,
            ]);
            return;
        }

        if ($id === null) {
            $moHinh->taoChamCong($duLieu);
            $_SESSION['_thong_bao'] = ['loai' => 'success', 'noi_dung' => 'Da tao cham cong.'];
        } else {
            $moHinh->capNhatChamCong($id, $duLieu);
            $_SESSION['_thong_bao'] = ['loai' => 'success', 'noi_dung' => 'Da cap nhat cham cong.'];
        }
        chuyen_huong(trim((string)$yeuCau->dauVao('quay_lai', '/nhan-su/cham-cong')) ?: '/nhan-su/cham-cong');
    }

    private static function tinhTongGio(string $checkIn, string $checkOut): float
    {
        if ($checkIn === '' || $checkOut === '') return 0.0;
        $vao = strtotime('1970-01-01 ' . $checkIn);
        $ra = strtotime('1970-01-01 ' . $checkOut);
        if ($vao === false || $ra === false || $ra <= $vao) return 0.0;
        return round(($ra - $vao) / 3600, 2);
    }
}
