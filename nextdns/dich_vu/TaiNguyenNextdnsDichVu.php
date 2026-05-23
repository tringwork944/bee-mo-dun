<?php
declare(strict_types=1);

namespace MoDun\Nextdns\DichVu;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class TaiNguyenNextdnsDichVu
{
    public function dongBoTaiNguyenCongKhai(): void
    {
        $nguon = $this->duongDanNguon();
        $dich = $this->duongDanDich();

        if (!is_dir($nguon)) {
            return;
        }

        if (!is_dir($dich) && !mkdir($dich, 0777, true) && !is_dir($dich)) {
            throw new \RuntimeException('Khong the tao thu muc tai nguyen NextDNS.');
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($nguon, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $muc) {
            $tuongDoi = substr($muc->getPathname(), strlen($nguon) + 1);
            $mucDich = $dich . DIRECTORY_SEPARATOR . $tuongDoi;
            if ($muc->isDir()) {
                if (!is_dir($mucDich) && !mkdir($mucDich, 0777, true) && !is_dir($mucDich)) {
                    throw new \RuntimeException('Khong the tao thu muc tai nguyen: ' . $tuongDoi);
                }
                continue;
            }

            $thuMucCha = dirname($mucDich);
            if (!is_dir($thuMucCha) && !mkdir($thuMucCha, 0777, true) && !is_dir($thuMucCha)) {
                throw new \RuntimeException('Khong the tao thu muc tai nguyen: ' . $thuMucCha);
            }
            if (!copy($muc->getPathname(), $mucDich)) {
                throw new \RuntimeException('Khong the sao chep tai nguyen: ' . $tuongDoi);
            }
        }
    }

    public function xoaTaiNguyenCongKhai(): void
    {
        $dich = $this->duongDanDich();
        if (!is_dir($dich)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dich, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $muc) {
            if ($muc->isDir()) {
                @rmdir($muc->getPathname());
                continue;
            }
            @unlink($muc->getPathname());
        }

        @rmdir($dich);
    }

    private function duongDanNguon(): string
    {
        return GOC_DU_AN . '/ung_dung/mo_dun/nextdns/tai_nguyen';
    }

    private function duongDanDich(): string
    {
        return GOC_DU_AN . '/cong_khai/mo_dun/nextdns';
    }
}
