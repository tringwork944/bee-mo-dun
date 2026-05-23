<?php
declare(strict_types=1);

namespace MoDun\Nextdns\DieuKhien;

use HeThong\YeuCau;
use MoDun\Nextdns\DichVu\NextdnsDichVu;

class NextdnsDieuKhien
{
    public static function tongQuan(?YeuCau $yeuCau = null, array $thamSo = []): void
    {
        $dichVu = new NextdnsDichVu();
        $cauHinh = $dichVu->layCauHinhHienThi();
        $tongQuan = $dichVu->layTongQuan();

        $GLOBALS['tieu_de_trang'] = 'NextDNS - Tong quan';
        hien_thi_bo_cuc(GOC_DU_AN . '/ung_dung/mo_dun/nextdns/giao_dien/tong_quan.php', [
            'cauHinh' => $cauHinh,
            'tongQuan' => $tongQuan,
        ]);
    }

    public static function cauHinh(?YeuCau $yeuCau = null, array $thamSo = []): void
    {
        $GLOBALS['tieu_de_trang'] = 'NextDNS - Cau hinh';
        hien_thi_bo_cuc(GOC_DU_AN . '/ung_dung/mo_dun/nextdns/giao_dien/cau_hinh.php', [
            'cauHinh' => (new NextdnsDichVu())->layCauHinhHienThi(),
            'duLieu' => [],
            'loiTheoTruong' => [],
        ]);
    }

    public static function luuCauHinh(YeuCau $yeuCau, array $thamSo): void
    {
        $dichVu = new NextdnsDichVu();
        $maHoSo = trim((string) $yeuCau->dauVao('ma_ho_so'));
        $khoaApi = trim((string) $yeuCau->dauVao('khoa_api', ''));
        $ketQua = $dichVu->luuCauHinh($maHoSo, $khoaApi !== '' ? $khoaApi : null);

        $_SESSION['_thong_bao'] = [
            'loai' => !empty($ketQua['ok']) ? 'success' : 'danger',
            'noi_dung' => (string) ($ketQua['thong_bao'] ?? 'Khong the luu cau hinh.'),
        ];

        chuyen_huong('/nextdns/cau-hinh');
    }

    public static function kiemTraKetNoi(YeuCau $yeuCau, array $thamSo): void
    {
        $ketQua = (new NextdnsDichVu())->kiemTraKetNoi();
        $_SESSION['_thong_bao'] = [
            'loai' => !empty($ketQua['ok']) ? 'success' : 'danger',
            'noi_dung' => (string) ($ketQua['thong_bao'] ?? 'Khong the kiem tra ket noi.'),
        ];
        chuyen_huong('/nextdns/cau-hinh');
    }

    public static function logs(YeuCau $yeuCau, array $thamSo): void
    {
        $dichVu = new NextdnsDichVu();
        $ketQua = $dichVu->layLogs([
            'tu_khoa' => (string) $yeuCau->dauVao('tu_khoa', ''),
            'trang_thai' => (string) $yeuCau->dauVao('trang_thai', 'tat_ca'),
            'cursor' => (string) $yeuCau->dauVao('cursor', ''),
            'gioi_han' => (int) $yeuCau->dauVao('gioi_han', 50),
        ]);

        $GLOBALS['tieu_de_trang'] = 'NextDNS - Logs';
        hien_thi_bo_cuc(GOC_DU_AN . '/ung_dung/mo_dun/nextdns/giao_dien/logs.php', [
            'ketQua' => $ketQua,
            'cauHinh' => $dichVu->layCauHinhHienThi(),
        ]);
    }

    public static function denylist(YeuCau $yeuCau, array $thamSo): void
    {
        $dichVu = new NextdnsDichVu();
        $ketQua = $dichVu->layDenylist((string) $yeuCau->dauVao('cursor', ''));

        $GLOBALS['tieu_de_trang'] = 'NextDNS - Denylist';
        hien_thi_bo_cuc(GOC_DU_AN . '/ung_dung/mo_dun/nextdns/giao_dien/denylist.php', [
            'ketQua' => $ketQua,
            'cauHinh' => $dichVu->layCauHinhHienThi(),
        ]);
    }

    public static function themDenylist(YeuCau $yeuCau, array $thamSo): void
    {
        $ketQua = (new NextdnsDichVu())->themDenylist((string) $yeuCau->dauVao('mien', ''));
        $_SESSION['_thong_bao'] = [
            'loai' => !empty($ketQua['ok']) ? 'success' : 'danger',
            'noi_dung' => (string) ($ketQua['thong_bao'] ?? 'Khong the them denylist.'),
        ];
        chuyen_huong('/nextdns/denylist');
    }

    public static function xoaDenylist(YeuCau $yeuCau, array $thamSo): void
    {
        $ketQua = (new NextdnsDichVu())->xoaDenylist((string) $yeuCau->dauVao('mien', ''));
        $_SESSION['_thong_bao'] = [
            'loai' => !empty($ketQua['ok']) ? 'success' : 'danger',
            'noi_dung' => (string) ($ketQua['thong_bao'] ?? 'Khong the xoa denylist.'),
        ];
        chuyen_huong('/nextdns/denylist');
    }
}
