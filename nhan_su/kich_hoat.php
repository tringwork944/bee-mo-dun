<?php

return static function (string $maMoDun, PDO $pdo): void {
    $stm = $pdo->prepare("SELECT trang_thai FROM mo_dun WHERE ma = :ma LIMIT 1");

    $stm->execute(['ma' => 'xac_thuc']);
    if ($stm->fetchColumn() !== 'dang_bat') {
        throw new RuntimeException('Mo dun Nhan su can mo dun Xac thuc dang hoat dong.');
    }

    $stm->execute(['ma' => 'tai_khoan']);
    if ($stm->fetchColumn() !== 'dang_bat') {
        throw new RuntimeException('Mo dun Nhan su can mo dun Tai khoan dang hoat dong.');
    }

    $stm = $pdo->prepare("UPDATE menu_he_thong SET trang_thai = 1, ngay_cap_nhat = NOW() WHERE mo_dun_ma = :ma");
    $stm->execute(['ma' => $maMoDun]);
};
