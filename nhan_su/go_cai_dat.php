<?php

return static function (string $maMoDun, PDO $pdo): void {
    // Framework se tu dong chay co_so_du_lieu/uninstall.sql cua module neu tep ton tai,
    // sau do cleanup menu / permission / registry theo logic uninstall moi.
    // Hook nay giu lai de mo dun tuong thich day du convention lifecycle.
};
