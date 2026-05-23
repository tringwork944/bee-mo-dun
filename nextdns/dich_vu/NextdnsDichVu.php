<?php
declare(strict_types=1);

namespace MoDun\Nextdns\DichVu;

use MoDun\Nextdns\MoHinh\NextdnsMoHinh;

class NextdnsDichVu
{
    private const TEN_MA_HOA = 'AES-256-CBC';
    private const THOI_GIAN_SONG_TONG_QUAN = 120;
    private const THOI_GIAN_SONG_NHAT_KY = 45;
    private const THOI_GIAN_SONG_DENYLIST = 90;

    private NextdnsMoHinh $moHinh;

    public function __construct(?NextdnsMoHinh $moHinh = null)
    {
        $this->moHinh = $moHinh ?? new NextdnsMoHinh();
    }

    public function layCauHinhHienThi(): array
    {
        $cauHinh = $this->moHinh->layCauHinh();
        if ($cauHinh === null) {
            return [
                'da_cau_hinh' => false,
                'ma_ho_so' => '',
                'khoa_api_an' => '',
                'thoi_gian_cho_giay' => 12,
            ];
        }

        $khoaApi = '';
        $loiCauHinh = '';
        try {
            $khoaApi = $this->giaiMaKhoaApi((string) $cauHinh['khoa_api_ma_hoa']);
        } catch (\Throwable $ngoaiLe) {
            $loiCauHinh = $ngoaiLe->getMessage();
        }

        return [
            'da_cau_hinh' => true,
            'ma_ho_so' => (string) $cauHinh['ma_ho_so'],
            'khoa_api' => $khoaApi,
            'khoa_api_an' => $this->anKhoaApi($khoaApi),
            'thoi_gian_cho_giay' => (int) ($cauHinh['thoi_gian_cho_giay'] ?? 12),
            'loi_cau_hinh' => $loiCauHinh,
        ];
    }

    public function luuCauHinh(string $maHoSo, ?string $khoaApiMoi = null): array
    {
        $maHoSo = trim($maHoSo);
        $khoaApiMoi = $khoaApiMoi !== null ? trim($khoaApiMoi) : null;
        $cauHinhCu = $this->layCauHinhHienThi();

        if ($maHoSo === '' || !preg_match('/^[A-Za-z0-9]+$/', $maHoSo)) {
            return ['ok' => false, 'thong_bao' => 'Profile ID khong hop le.'];
        }

        $khoaApi = $khoaApiMoi;
        if ($khoaApi === null || $khoaApi === '') {
            $khoaApi = $cauHinhCu['khoa_api'] ?? '';
        }

        if ($khoaApi === '' || strlen($khoaApi) < 20) {
            return ['ok' => false, 'thong_bao' => 'API key khong hop le.'];
        }

        $this->moHinh->luuCauHinh($maHoSo, $this->maHoaKhoaApi($khoaApi), 12);
        $this->moHinh->xoaTatCaBoNhoDem();

        return ['ok' => true, 'thong_bao' => 'Da luu cau hinh NextDNS an toan.'];
    }

    public function kiemTraKetNoi(): array
    {
        try {
            $ketQua = $this->goiApi('GET', '/profiles/:profile/analytics/status', [
                'from' => '-1d',
                'limit' => 10,
            ]);

            return [
                'ok' => true,
                'thong_bao' => 'Ket noi NextDNS thanh cong.',
                'du_lieu' => $ketQua['data'] ?? [],
            ];
        } catch (\Throwable $e) {
            return ['ok' => false, 'thong_bao' => $e->getMessage()];
        }
    }

    public function layTongQuan(): array
    {
        try {
            $duLieu = $this->layHoacTaiBoNhoDem('tong_quan_chinh', self::THOI_GIAN_SONG_TONG_QUAN, function (): array {
                $trangThai = $this->goiApi('GET', '/profiles/:profile/analytics/status', [
                    'from' => '-7d',
                    'limit' => 10,
                ]);
                $bieuDo = $this->goiApi('GET', '/profiles/:profile/analytics/status;series', [
                    'from' => '-7d',
                    'interval' => 86400,
                    'alignment' => 'end',
                    'partials' => 'all',
                    'limit' => 10,
                ]);
                $topChan = $this->goiApi('GET', '/profiles/:profile/analytics/domains', [
                    'from' => '-7d',
                    'status' => 'blocked',
                    'limit' => 10,
                ]);
                $topTruyVan = $this->goiApi('GET', '/profiles/:profile/analytics/domains', [
                    'from' => '-7d',
                    'limit' => 10,
                ]);

                return [
                    'trang_thai' => $trangThai,
                    'bieu_do' => $bieuDo,
                    'top_chan' => $topChan,
                    'top_truy_van' => $topTruyVan,
                ];
            });

            $trangThai = $this->boChiSoTrangThai((array) ($duLieu['trang_thai']['data'] ?? []));
            $tong = $trangThai['tong'];
            $phanTram = [
                'blocked' => $tong > 0 ? round(($trangThai['blocked'] / $tong) * 100, 2) : 0.0,
                'allowed' => $tong > 0 ? round(($trangThai['allowed'] / $tong) * 100, 2) : 0.0,
                'default' => $tong > 0 ? round(($trangThai['default'] / $tong) * 100, 2) : 0.0,
            ];

            return [
                'ok' => true,
                'chi_so' => $trangThai,
                'phan_tram' => $phanTram,
                'bieu_do' => $this->boDuLieuBieuDo((array) ($duLieu['bieu_do']['data'] ?? []), (array) ($duLieu['bieu_do']['meta']['series']['times'] ?? [])),
                'top_chan' => (array) ($duLieu['top_chan']['data'] ?? []),
                'top_truy_van' => (array) ($duLieu['top_truy_van']['data'] ?? []),
                'cap_nhat_luc' => date('Y-m-d H:i:s'),
            ];
        } catch (\Throwable $e) {
            return ['ok' => false, 'thong_bao' => $e->getMessage()];
        }
    }

    public function layLogs(array $boLoc): array
    {
        try {
            $tuKhoa = trim((string) ($boLoc['tu_khoa'] ?? ''));
            $trangThai = trim((string) ($boLoc['trang_thai'] ?? 'tat_ca'));
            $cursor = trim((string) ($boLoc['cursor'] ?? ''));
            $gioiHan = (int) ($boLoc['gioi_han'] ?? 50);
            $gioiHan = max(10, min(100, $gioiHan));

            $truyVan = [
                'from' => '-7d',
                'limit' => $gioiHan,
                'sort' => 'desc',
            ];
            if ($tuKhoa !== '') {
                $truyVan['search'] = $tuKhoa;
            }
            if (in_array($trangThai, ['blocked', 'allowed'], true)) {
                $truyVan['status'] = $trangThai;
            }
            if ($cursor !== '') {
                $truyVan['cursor'] = $cursor;
            }

            $khoaDem = 'logs:' . sha1((string) json_encode($truyVan));
            $duLieu = $this->layHoacTaiBoNhoDem($khoaDem, self::THOI_GIAN_SONG_NHAT_KY, function () use ($truyVan): array {
                return $this->goiApi('GET', '/profiles/:profile/logs', $truyVan);
            });

            $danhSach = [];
            foreach ((array) ($duLieu['data'] ?? []) as $dong) {
                if (!is_array($dong)) {
                    continue;
                }
                $danhSach[] = [
                    'id' => (string) ($dong['id'] ?? ''),
                    'domain' => (string) ($dong['domain'] ?? ''),
                    'root' => (string) ($dong['root'] ?? ''),
                    'trang_thai' => (string) ($dong['status'] ?? 'default'),
                    'thoi_gian' => (string) ($dong['timestamp'] ?? ''),
                    'thiet_bi' => (string) (($dong['device']['name'] ?? '') ?: ($dong['device']['id'] ?? '')),
                    'ly_do' => $this->ghepLyDo((array) ($dong['reasons'] ?? [])),
                ];
            }

            return [
                'ok' => true,
                'danh_sach' => $danhSach,
                'cursor_tiep' => (string) ($duLieu['meta']['pagination']['cursor'] ?? ''),
                'stream_id' => (string) ($duLieu['meta']['stream']['id'] ?? ''),
                'bo_loc' => [
                    'tu_khoa' => $tuKhoa,
                    'trang_thai' => $trangThai,
                    'gioi_han' => $gioiHan,
                ],
            ];
        } catch (\Throwable $e) {
            return ['ok' => false, 'thong_bao' => $e->getMessage(), 'danh_sach' => [], 'cursor_tiep' => ''];
        }
    }

    public function layDenylist(string $cursor = ''): array
    {
        try {
            $truyVan = ['limit' => 100];
            if ($cursor !== '') {
                $truyVan['cursor'] = $cursor;
            }
            $khoaDem = 'denylist:' . sha1((string) json_encode($truyVan));
            $duLieu = $this->layHoacTaiBoNhoDem($khoaDem, self::THOI_GIAN_SONG_DENYLIST, function () use ($truyVan): array {
                return $this->goiApi('GET', '/profiles/:profile/denylist', $truyVan);
            });

            return [
                'ok' => true,
                'danh_sach' => (array) ($duLieu['data'] ?? []),
                'cursor_tiep' => (string) ($duLieu['meta']['pagination']['cursor'] ?? ''),
            ];
        } catch (\Throwable $e) {
            return ['ok' => false, 'thong_bao' => $e->getMessage(), 'danh_sach' => [], 'cursor_tiep' => ''];
        }
    }

    public function themDenylist(string $mien): array
    {
        $mien = strtolower(trim($mien));
        if (!$this->mienHopLe($mien)) {
            return ['ok' => false, 'thong_bao' => 'Domain khong hop le.'];
        }

        try {
            $this->goiApi('POST', '/profiles/:profile/denylist', [], [
                'id' => $mien,
                'active' => true,
            ]);
            $this->moHinh->xoaBoNhoDemTheoTienTo('denylist:');
            $this->moHinh->xoaBoNhoDemTheoTienTo('tong_quan_');
            return ['ok' => true, 'thong_bao' => 'Da them domain vao denylist.'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'thong_bao' => $e->getMessage()];
        }
    }

    public function xoaDenylist(string $mien): array
    {
        $mien = strtolower(trim($mien));
        if (!$this->mienHopLe($mien)) {
            return ['ok' => false, 'thong_bao' => 'Domain khong hop le.'];
        }

        try {
            $this->goiApi('DELETE', '/profiles/:profile/denylist/' . rawurlencode($mien));
            $this->moHinh->xoaBoNhoDemTheoTienTo('denylist:');
            $this->moHinh->xoaBoNhoDemTheoTienTo('tong_quan_');
            return ['ok' => true, 'thong_bao' => 'Da xoa domain khoi denylist.'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'thong_bao' => $e->getMessage()];
        }
    }

    private function layHoacTaiBoNhoDem(string $maDem, int $thoiGianSong, callable $taiMoi): array
    {
        $duLieu = $this->moHinh->layBoNhoDem($maDem);
        if ($duLieu !== null) {
            return $duLieu;
        }

        $duLieu = $taiMoi();
        $this->moHinh->luuBoNhoDem($maDem, $duLieu, $thoiGianSong);
        return $duLieu;
    }

    private function boChiSoTrangThai(array $duLieu): array
    {
        $ketQua = [
            'tong' => 0,
            'blocked' => 0,
            'allowed' => 0,
            'default' => 0,
            'error' => 0,
        ];

        foreach ($duLieu as $dong) {
            if (!is_array($dong)) {
                continue;
            }
            $trangThai = (string) ($dong['status'] ?? '');
            $soLuong = (int) ($dong['queries'] ?? 0);
            if (array_key_exists($trangThai, $ketQua)) {
                $ketQua[$trangThai] += $soLuong;
            }
            $ketQua['tong'] += $soLuong;
        }

        return $ketQua;
    }

    private function boDuLieuBieuDo(array $duLieu, array $mocThoiGian): array
    {
        $nhan = [];
        foreach ($mocThoiGian as $moc) {
            $dau = strtotime((string) $moc);
            $nhan[] = $dau !== false ? date('d/m', $dau) : (string) $moc;
        }

        $banDo = [
            'blocked' => [],
            'allowed' => [],
            'default' => [],
        ];

        foreach ($duLieu as $dong) {
            if (!is_array($dong)) {
                continue;
            }
            $trangThai = (string) ($dong['status'] ?? '');
            if (!isset($banDo[$trangThai]) || !is_array($dong['queries'] ?? null)) {
                continue;
            }
            $banDo[$trangThai] = array_map(static fn($giaTri): int => (int) $giaTri, (array) $dong['queries']);
        }

        return [
            'nhan' => $nhan,
            'du_lieu' => $banDo,
        ];
    }

    private function goiApi(string $phuongThuc, string $duongDan, array $truyVan = [], ?array $noiDung = null): array
    {
        $cauHinh = $this->layCauHinhHienThi();
        if (empty($cauHinh['da_cau_hinh'])) {
            throw new \RuntimeException('Chua cau hinh API key va profile ID cho NextDNS.');
        }

        $phuongThuc = strtoupper($phuongThuc);
        $duongDan = str_replace(':profile', rawurlencode((string) $cauHinh['ma_ho_so']), $duongDan);
        $url = 'https://api.nextdns.io' . $duongDan;
        if ($truyVan !== []) {
            $url .= '?' . http_build_query($truyVan);
        }

        $headers = [
            'Accept: application/json',
            'X-Api-Key: ' . (string) $cauHinh['khoa_api'],
        ];
        $timeout = (int) ($cauHinh['thoi_gian_cho_giay'] ?? 12);
        $phanHoi = $this->guiYeuCauHttp($phuongThuc, $url, $headers, $noiDung, $timeout);
        $maTrangThai = (int) ($phanHoi['ma_trang_thai'] ?? 0);
        $duLieu = json_decode((string) ($phanHoi['noi_dung'] ?? ''), true);

        if (!is_array($duLieu)) {
            throw new \RuntimeException('NextDNS tra ve du lieu khong hop le.');
        }

        $loiApi = $this->rutGonLoiApi((array) ($duLieu['errors'] ?? []));
        if ($loiApi !== '') {
            throw new \RuntimeException($loiApi);
        }

        if ($maTrangThai >= 400) {
            throw new \RuntimeException($this->thongBaoHttp($maTrangThai));
        }

        return $duLieu;
    }

    private function guiYeuCauHttp(string $phuongThuc, string $url, array $headers, ?array $noiDung, int $timeout): array
    {
        $payload = $noiDung !== null ? (string) json_encode($noiDung, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null;
        if ($payload !== null) {
            $headers[] = 'Content-Type: application/json';
        }

        if (function_exists('curl_init')) {
            $curl = curl_init($url);
            if ($curl === false) {
                throw new \RuntimeException('Khong the khoi tao ket noi NextDNS.');
            }

            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => $phuongThuc,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_TIMEOUT => $timeout,
                CURLOPT_CONNECTTIMEOUT => min($timeout, 5),
                CURLOPT_HEADER => true,
            ]);
            if ($payload !== null) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
            }

            $phanHoi = curl_exec($curl);
            if ($phanHoi === false) {
                $loi = curl_error($curl);
                $maLoi = curl_errno($curl);
                curl_close($curl);
                if (in_array($maLoi, [CURLE_OPERATION_TIMEDOUT, CURLE_COULDNT_CONNECT, CURLE_COULDNT_RESOLVE_HOST], true)) {
                    throw new \RuntimeException('Ket noi NextDNS bi timeout hoac khong the truy cap API.');
                }
                throw new \RuntimeException('Khong the goi API NextDNS.');
            }

            $doDaiHeader = (int) curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $maTrangThai = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            return [
                'ma_trang_thai' => $maTrangThai,
                'noi_dung' => substr($phanHoi, $doDaiHeader),
            ];
        }

        $noiDungHeader = implode("\r\n", $headers);
        $nguCanh = stream_context_create([
            'http' => [
                'method' => $phuongThuc,
                'header' => $noiDungHeader . ($payload !== null ? "\r\nContent-Length: " . strlen($payload) : ''),
                'content' => $payload,
                'timeout' => $timeout,
                'ignore_errors' => true,
            ],
        ]);

        $noiDung = @file_get_contents($url, false, $nguCanh);
        if ($noiDung === false) {
            throw new \RuntimeException('Khong the ket noi toi NextDNS.');
        }

        $maTrangThai = 0;
        foreach (($http_response_header ?? []) as $dong) {
            if (preg_match('#HTTP/\S+\s+(\d{3})#', $dong, $khop)) {
                $maTrangThai = (int) $khop[1];
                break;
            }
        }

        return [
            'ma_trang_thai' => $maTrangThai,
            'noi_dung' => $noiDung,
        ];
    }

    private function thongBaoHttp(int $maTrangThai): string
    {
        return match ($maTrangThai) {
            401, 403 => 'API key NextDNS khong hop le hoac khong du quyen.',
            404 => 'Profile ID NextDNS khong ton tai.',
            408 => 'Yeu cau toi NextDNS bi timeout.',
            429 => 'NextDNS dang gioi han tan suat goi API. Vui long thu lai sau.',
            500, 502, 503, 504 => 'API NextDNS dang gap loi tam thoi.',
            default => 'Khong the lay du lieu tu NextDNS.',
        };
    }

    private function rutGonLoiApi(array $danhSachLoi): string
    {
        if ($danhSachLoi === []) {
            return '';
        }

        $thongBao = [];
        foreach ($danhSachLoi as $loi) {
            if (is_array($loi)) {
                $thongBao[] = (string) ($loi['message'] ?? $loi['code'] ?? 'Loi API NextDNS');
                continue;
            }
            if (is_string($loi) && $loi !== '') {
                $thongBao[] = $loi;
            }
        }

        $thongBao = array_values(array_filter(array_unique($thongBao)));
        return $thongBao === [] ? 'Yeu cau NextDNS khong hop le.' : implode(' ', $thongBao);
    }

    private function ghepLyDo(array $lyDo): string
    {
        $ketQua = [];
        foreach ($lyDo as $dong) {
            if (is_array($dong)) {
                $giaTri = (string) ($dong['name'] ?? $dong['id'] ?? '');
                if ($giaTri !== '') {
                    $ketQua[] = $giaTri;
                }
            } elseif (is_string($dong) && $dong !== '') {
                $ketQua[] = $dong;
            }
        }

        return implode(', ', array_values(array_unique($ketQua)));
    }

    private function mienHopLe(string $mien): bool
    {
        if ($mien === '' || strlen($mien) > 253) {
            return false;
        }

        return (bool) preg_match('/^(?=.{1,253}$)(?!-)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/i', $mien);
    }

    private function maHoaKhoaApi(string $khoaApi): string
    {
        if (!function_exists('openssl_encrypt')) {
            throw new \RuntimeException('May chu chua bat OpenSSL de ma hoa API key.');
        }

        $iv = random_bytes(16);
        $khoa = hash('sha256', (string) env('UNG_DUNG_MUOI', 'bee-frame-nextdns'), true);
        $maHoa = openssl_encrypt($khoaApi, self::TEN_MA_HOA, $khoa, OPENSSL_RAW_DATA, $iv);
        if ($maHoa === false) {
            throw new \RuntimeException('Khong the ma hoa API key NextDNS.');
        }

        $xacThuc = hash_hmac('sha256', $maHoa, $khoa, true);
        return base64_encode($iv . $xacThuc . $maHoa);
    }

    private function giaiMaKhoaApi(string $duLieuMaHoa): string
    {
        if (!function_exists('openssl_decrypt')) {
            throw new \RuntimeException('May chu chua bat OpenSSL de giai ma API key.');
        }

        $duLieu = base64_decode($duLieuMaHoa, true);
        if (!is_string($duLieu) || strlen($duLieu) <= 48) {
            throw new \RuntimeException('Du lieu API key NextDNS khong hop le.');
        }

        $iv = substr($duLieu, 0, 16);
        $xacThuc = substr($duLieu, 16, 32);
        $banMa = substr($duLieu, 48);
        $khoa = hash('sha256', (string) env('UNG_DUNG_MUOI', 'bee-frame-nextdns'), true);
        $xacThucTinh = hash_hmac('sha256', $banMa, $khoa, true);
        if (!hash_equals($xacThuc, $xacThucTinh)) {
            throw new \RuntimeException('Khong the xac minh API key NextDNS da luu.');
        }

        $giaiMa = openssl_decrypt($banMa, self::TEN_MA_HOA, $khoa, OPENSSL_RAW_DATA, $iv);
        if ($giaiMa === false) {
            throw new \RuntimeException('Khong the giai ma API key NextDNS.');
        }

        return $giaiMa;
    }

    private function anKhoaApi(string $khoaApi): string
    {
        $doDai = strlen($khoaApi);
        if ($doDai <= 8) {
            return str_repeat('*', $doDai);
        }

        return substr($khoaApi, 0, 4) . str_repeat('*', max(4, $doDai - 8)) . substr($khoaApi, -4);
    }
}
