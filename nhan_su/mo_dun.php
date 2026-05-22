<?php

use HeThong\BoLoc;

BoLoc::themBoLoc('menu.truoc_hien_thi', function (array $menu): array {
    $ketQua = [];
    foreach ($menu as $muc) {
        if (($muc['ma'] ?? '') === 'nhan_su') {
            $con = $muc['con'] ?? [];
            if (empty($con)) {
                continue;
            }
        }
        $ketQua[] = $muc;
    }
    return $ketQua;
});
