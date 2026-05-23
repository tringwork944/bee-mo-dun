<?php
declare(strict_types=1);

use HeThong\BoLoc;
use HeThong\SuKien;

SuKien::themHanhDong('he_thong.khoi_dong', static function (): void {
});

BoLoc::themBoLoc('menu.truoc_hien_thi', static function (array $menu): array {
    return $menu;
});
