<?php
declare(strict_types=1);

use MoDun\Nextdns\DichVu\TaiNguyenNextdnsDichVu;

return static function (string $maMoDun, PDO $pdo): void {
    (new TaiNguyenNextdnsDichVu())->xoaTaiNguyenCongKhai();
    $pdo->exec('DROP TABLE IF EXISTS nextdns_danh_sach_cho_phep');
    $pdo->exec('DROP TABLE IF EXISTS nextdns_danh_sach_chan');
    $pdo->exec('DROP TABLE IF EXISTS nextdns_cache_bo_loc');
    $pdo->exec('DROP TABLE IF EXISTS nextdns_cache_thong_ke');
    $pdo->exec('DROP TABLE IF EXISTS nextdns_profile');
    $pdo->exec('DROP TABLE IF EXISTS nextdns_bo_nho_dem');
    $pdo->exec('DROP TABLE IF EXISTS nextdns_nhat_ky');
    $pdo->exec('DROP TABLE IF EXISTS nextdns_cau_hinh');

    $danhSachQuyen = [
        'xem_nextdns',
        'quan_ly_profile_nextdns',
        'cap_nhat_du_lieu_nextdns',
        'quan_ly_bo_loc_nextdns',
        'quan_ly_danh_sach_chan_nextdns',
        'quan_ly_danh_sach_cho_phep_nextdns',
        'kiem_tra_ten_mien_nextdns',
        'them_nextdns',
        'sua_nextdns',
        'xoa_nextdns',
        'dong_bo_nextdns',
    ];

    $thamSo = [];
    $cauLenh = [];
    foreach ($danhSachQuyen as $chiSo => $maQuyen) {
        $khoa = 'q' . $chiSo;
        $thamSo[$khoa] = $maQuyen;
        $cauLenh[] = ':' . $khoa;
    }
    $danhSach = implode(', ', $cauLenh);

    if ($danhSach !== '') {
        if ($pdo->query("SHOW TABLES LIKE 'quyen_vai_tro'")->fetchColumn()) {
            $cot = $pdo->query("SHOW COLUMNS FROM quyen_vai_tro LIKE 'ma_quyen'")->fetchColumn() ? 'ma_quyen' : ($pdo->query("SHOW COLUMNS FROM quyen_vai_tro LIKE 'quyen_ma'")->fetchColumn() ? 'quyen_ma' : null);
            if ($cot !== null) {
                $stm = $pdo->prepare("DELETE FROM quyen_vai_tro WHERE {$cot} IN ({$danhSach})");
                $stm->execute($thamSo);
            }
        }

        if ($pdo->query("SHOW TABLES LIKE 'quyen'")->fetchColumn()) {
            $cot = $pdo->query("SHOW COLUMNS FROM quyen LIKE 'ma'")->fetchColumn() ? 'ma' : null;
            if ($cot !== null) {
                $stm = $pdo->prepare("DELETE FROM quyen WHERE {$cot} IN ({$danhSach})");
                $stm->execute($thamSo);
            }
        }
    }
};
