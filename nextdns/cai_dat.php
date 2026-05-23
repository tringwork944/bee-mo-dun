<?php
declare(strict_types=1);

use MoDun\Nextdns\DichVu\TaiNguyenNextdnsDichVu;

return static function (string $maMoDun, PDO $pdo): void {
    (new TaiNguyenNextdnsDichVu())->dongBoTaiNguyenCongKhai();
    $pdo->exec("DELETE FROM menu_he_thong WHERE mo_dun_ma = 'nextdns' AND ma NOT IN ('nextdns', 'nextdns_tong_quan', 'nextdns_quan_ly_profile', 'nextdns_quan_ly_bo_loc', 'nextdns_danh_sach_chan_thu_cong', 'nextdns_danh_sach_cho_phep_thu_cong', 'nextdns_kiem_tra_ten_mien')");
    $pdo->exec("DELETE FROM quyen_vai_tro WHERE ma_quyen IN ('them_nextdns', 'sua_nextdns', 'xoa_nextdns', 'dong_bo_nextdns')");
};
