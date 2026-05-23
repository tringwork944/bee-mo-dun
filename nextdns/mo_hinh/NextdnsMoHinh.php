<?php
declare(strict_types=1);

namespace MoDun\Nextdns\MoHinh;

use HeThong\CoSoDuLieu;
use PDO;

class NextdnsMoHinh
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = CoSoDuLieu::layKetNoi();
    }

    public function layCauHinh(): ?array
    {
        $dong = $this->pdo->query('SELECT id, ma_ho_so, khoa_api_ma_hoa, thoi_gian_cho_giay, ngay_cap_nhat, ngay_tao FROM nextdns_cau_hinh ORDER BY id DESC LIMIT 1')
            ->fetch(PDO::FETCH_ASSOC);
        return is_array($dong) ? $dong : null;
    }

    public function luuCauHinh(string $maHoSo, string $khoaApiMaHoa, int $thoiGianChoGiay = 12): void
    {
        $banGhi = $this->layCauHinh();
        if ($banGhi === null) {
            $stm = $this->pdo->prepare('INSERT INTO nextdns_cau_hinh (ma_ho_so, khoa_api_ma_hoa, thoi_gian_cho_giay, ngay_cap_nhat) VALUES (:ma_ho_so, :khoa_api_ma_hoa, :thoi_gian_cho_giay, NOW())');
            $stm->execute([
                'ma_ho_so' => $maHoSo,
                'khoa_api_ma_hoa' => $khoaApiMaHoa,
                'thoi_gian_cho_giay' => $thoiGianChoGiay,
            ]);
            return;
        }

        $stm = $this->pdo->prepare('UPDATE nextdns_cau_hinh SET ma_ho_so = :ma_ho_so, khoa_api_ma_hoa = :khoa_api_ma_hoa, thoi_gian_cho_giay = :thoi_gian_cho_giay, ngay_cap_nhat = NOW() WHERE id = :id');
        $stm->execute([
            'id' => (int) $banGhi['id'],
            'ma_ho_so' => $maHoSo,
            'khoa_api_ma_hoa' => $khoaApiMaHoa,
            'thoi_gian_cho_giay' => $thoiGianChoGiay,
        ]);
    }

    public function layBoNhoDem(string $maDuLieu): ?array
    {
        $stm = $this->pdo->prepare('SELECT du_lieu_json, het_han_luc FROM nextdns_bo_nho_dem WHERE ma_du_lieu = :ma_du_lieu LIMIT 1');
        $stm->execute(['ma_du_lieu' => $maDuLieu]);
        $dong = $stm->fetch(PDO::FETCH_ASSOC);
        if (!is_array($dong)) {
            return null;
        }

        if (strtotime((string) $dong['het_han_luc']) <= time()) {
            $this->xoaBoNhoDemTheoKhoa($maDuLieu);
            return null;
        }

        $duLieu = json_decode((string) $dong['du_lieu_json'], true);
        return is_array($duLieu) ? $duLieu : null;
    }

    public function luuBoNhoDem(string $maDuLieu, array $duLieu, int $thoiGianSong): void
    {
        $stm = $this->pdo->prepare('INSERT INTO nextdns_bo_nho_dem (ma_du_lieu, du_lieu_json, het_han_luc, ngay_cap_nhat) VALUES (:ma_du_lieu, :du_lieu_json, DATE_ADD(NOW(), INTERVAL :thoi_gian_song SECOND), NOW()) ON DUPLICATE KEY UPDATE du_lieu_json = VALUES(du_lieu_json), het_han_luc = VALUES(het_han_luc), ngay_cap_nhat = NOW()');
        $stm->bindValue(':ma_du_lieu', $maDuLieu);
        $stm->bindValue(':du_lieu_json', (string) json_encode($duLieu, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $stm->bindValue(':thoi_gian_song', $thoiGianSong, PDO::PARAM_INT);
        $stm->execute();
    }

    public function xoaBoNhoDemTheoTienTo(string $tienTo): void
    {
        $stm = $this->pdo->prepare('DELETE FROM nextdns_bo_nho_dem WHERE ma_du_lieu LIKE :tien_to');
        $stm->execute(['tien_to' => $tienTo . '%']);
    }

    public function xoaTatCaBoNhoDem(): void
    {
        $this->pdo->exec('DELETE FROM nextdns_bo_nho_dem');
    }

    private function xoaBoNhoDemTheoKhoa(string $maDuLieu): void
    {
        $stm = $this->pdo->prepare('DELETE FROM nextdns_bo_nho_dem WHERE ma_du_lieu = :ma_du_lieu');
        $stm->execute(['ma_du_lieu' => $maDuLieu]);
    }
}
