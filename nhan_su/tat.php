<?php

return static function (string $maMoDun, PDO $pdo): void {
    // Chi an menu va tat truy cap runtime cua module.
    // Du lieu nghiep vu nhan_vien / phong_ban / chuc_vu / cham_cong duoc giu nguyen.
    $stm = $pdo->prepare("UPDATE menu_he_thong SET trang_thai = 0, ngay_cap_nhat = NOW() WHERE mo_dun_ma = :ma");
    $stm->execute(['ma' => $maMoDun]);
};
