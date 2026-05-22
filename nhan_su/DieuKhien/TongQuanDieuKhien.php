<?php
declare(strict_types=1);

namespace MoDun\NhanSu\DieuKhien;

use HeThong\YeuCau;
use MoDun\NhanSu\MoHinh\NhanSuMoHinh;

class TongQuanDieuKhien
{
    public static function index(?YeuCau $yeuCau = null, array $thamSo = []): void
    {
        if (!co_quyen('nhan_su.dashboard')) {
            http_response_code(403);
            echo 'Ban khong co quyen truy cap dashboard nhan su.';
            return;
        }
        $duLieu = (new NhanSuMoHinh())->layDuLieuDashboard();
        hien_thi_bo_cuc(GOC_DU_AN . '/ung_dung/mo_dun/nhan_su/GiaoDien/tong_quan/index.php', $duLieu);
    }

    public static function apiThongKe(?YeuCau $yeuCau = null, array $thamSo = []): void
    {
        if (!co_quyen('nhan_su.dashboard')) {
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => false, 'thong_bao' => 'Khong co quyen']);
            return;
        }
        $duLieu = (new NhanSuMoHinh())->layDuLieuDashboard();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => true, 'du_lieu' => $duLieu], JSON_UNESCAPED_UNICODE);
    }
}
