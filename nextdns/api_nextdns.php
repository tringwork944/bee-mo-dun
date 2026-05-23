<?php
declare(strict_types=1);

namespace MoDun\Nextdns;

use RuntimeException;

class ApiNextdns
{
    public const BASE_URL = 'https://api.nextdns.io';
    private const TEN_MA_HOA = 'AES-256-CBC';

    public function maHoaApiKey(string $apiKey): string
    {
        if (!function_exists('openssl_encrypt')) {
            throw new RuntimeException('May chu chua bat OpenSSL de ma hoa API key.');
        }

        $iv = random_bytes(16);
        $khoa = hash('sha256', (string) env('UNG_DUNG_MUOI', 'bee-frame-nextdns'), true);
        $banMa = openssl_encrypt($apiKey, self::TEN_MA_HOA, $khoa, OPENSSL_RAW_DATA, $iv);
        if ($banMa === false) {
            throw new RuntimeException('Khong the ma hoa API key NextDNS.');
        }

        $xacThuc = hash_hmac('sha256', $banMa, $khoa, true);
        return base64_encode($iv . $xacThuc . $banMa);
    }

    public function giaiMaApiKey(string $duLieuMaHoa): string
    {
        if (!function_exists('openssl_decrypt')) {
            throw new RuntimeException('May chu chua bat OpenSSL de giai ma API key.');
        }

        $duLieu = base64_decode($duLieuMaHoa, true);
        if (!is_string($duLieu) || strlen($duLieu) <= 48) {
            throw new RuntimeException('Du lieu API key NextDNS khong hop le.');
        }

        $iv = substr($duLieu, 0, 16);
        $xacThuc = substr($duLieu, 16, 32);
        $banMa = substr($duLieu, 48);
        $khoa = hash('sha256', (string) env('UNG_DUNG_MUOI', 'bee-frame-nextdns'), true);
        $xacThucTinh = hash_hmac('sha256', $banMa, $khoa, true);
        if (!hash_equals($xacThuc, $xacThucTinh)) {
            throw new RuntimeException('Khong the xac minh API key da luu.');
        }

        $giaiMa = openssl_decrypt($banMa, self::TEN_MA_HOA, $khoa, OPENSSL_RAW_DATA, $iv);
        if ($giaiMa === false) {
            throw new RuntimeException('Khong the giai ma API key NextDNS.');
        }

        return $giaiMa;
    }

    public function anApiKey(string $apiKey): string
    {
        $doDai = strlen($apiKey);
        if ($doDai <= 8) {
            return str_repeat('*', $doDai);
        }

        return substr($apiKey, 0, 4) . str_repeat('*', max(4, $doDai - 8)) . substr($apiKey, -4);
    }

    public function goi(string $phuongThuc, string $maProfile, string $apiKey, string $duongDan, array $truyVan = [], ?array $noiDung = null, int $timeout = 15): array
    {
        $url = self::BASE_URL . '/profiles/' . rawurlencode($maProfile) . $duongDan;
        if ($truyVan !== []) {
            $url .= '?' . http_build_query($truyVan);
        }

        $headers = [
            'Accept: application/json',
            'X-Api-Key: ' . $apiKey,
        ];
        $phanHoi = $this->guiYeuCau(strtoupper($phuongThuc), $url, $headers, $noiDung, $timeout);
        $maTrangThai = (int) ($phanHoi['ma_trang_thai'] ?? 0);
        $duLieu = json_decode((string) ($phanHoi['noi_dung'] ?? ''), true);

        if (!is_array($duLieu)) {
            throw new RuntimeException('NextDNS tra ve du lieu khong hop le.');
        }

        $thongBaoLoi = $this->rutGonLoi((array) ($duLieu['errors'] ?? []));
        if ($thongBaoLoi !== '') {
            throw new RuntimeException($thongBaoLoi);
        }

        if ($maTrangThai >= 400) {
            throw new RuntimeException($this->thongBaoHttp($maTrangThai));
        }

        return $duLieu;
    }

    public function layBoLoc(string $maProfile, string $apiKey): array
    {
        return $this->goi('GET', $maProfile, $apiKey, '/privacy/blocklists');
    }

    public function layDanhSachChan(string $maProfile, string $apiKey): array
    {
        return $this->goi('GET', $maProfile, $apiKey, '/denylist');
    }

    public function themDanhSachChan(string $maProfile, string $apiKey, string $domain): array
    {
        return $this->goi('POST', $maProfile, $apiKey, '/denylist', [], ['id' => $domain]);
    }

    public function xoaDanhSachChan(string $maProfile, string $apiKey, string $domain): array
    {
        return $this->goi('DELETE', $maProfile, $apiKey, '/denylist/' . rawurlencode($domain));
    }

    public function layDanhSachChoPhep(string $maProfile, string $apiKey): array
    {
        return $this->goi('GET', $maProfile, $apiKey, '/allowlist');
    }

    public function themDanhSachChoPhep(string $maProfile, string $apiKey, string $domain): array
    {
        return $this->goi('POST', $maProfile, $apiKey, '/allowlist', [], ['id' => $domain]);
    }

    public function xoaDanhSachChoPhep(string $maProfile, string $apiKey, string $domain): array
    {
        return $this->goi('DELETE', $maProfile, $apiKey, '/allowlist/' . rawurlencode($domain));
    }

    public function kiem_tra_ten_mien_nextdns(string $profile, string $api_key, string $ten_mien): array
    {
        // NextDNS API cong khai hien khong co endpoint "test domain" on dinh cho profile.
        // Module kiem tra truc tiep bang logs gan nhat cua domain va suy ra trang thai tu ban ghi moi nhat.
        return $this->goi('GET', $profile, $api_key, '/logs', [
            'from' => '-24h',
            'search' => $ten_mien,
            'limit' => 20,
        ]);
    }

    private function guiYeuCau(string $phuongThuc, string $url, array $headers, ?array $noiDung, int $timeout): array
    {
        $payload = $noiDung !== null ? (string) json_encode($noiDung, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null;
        if ($payload !== null) {
            $headers[] = 'Content-Type: application/json';
        }

        if (function_exists('curl_init')) {
            $curl = curl_init($url);
            if ($curl === false) {
                throw new RuntimeException('Khong the khoi tao ket noi toi NextDNS.');
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
                $maLoi = curl_errno($curl);
                curl_close($curl);
                if (in_array($maLoi, [CURLE_OPERATION_TIMEDOUT, CURLE_COULDNT_CONNECT, CURLE_COULDNT_RESOLVE_HOST], true)) {
                    throw new RuntimeException('Ket noi NextDNS bi timeout hoac khong the truy cap API.');
                }
                throw new RuntimeException('Khong the goi API NextDNS.');
            }

            $doDaiHeader = (int) curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $maTrangThai = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            return [
                'ma_trang_thai' => $maTrangThai,
                'noi_dung' => substr($phanHoi, $doDaiHeader),
            ];
        }

        $context = stream_context_create([
            'http' => [
                'method' => $phuongThuc,
                'header' => implode("\r\n", $headers),
                'content' => $payload ?? '',
                'timeout' => $timeout,
                'ignore_errors' => true,
            ],
        ]);
        $noiDung = @file_get_contents($url, false, $context);
        if ($noiDung === false) {
            throw new RuntimeException('Khong the ket noi toi NextDNS.');
        }

        $maTrangThai = 0;
        foreach (($http_response_header ?? []) as $dongHeader) {
            if (preg_match('#HTTP/\S+\s+(\d{3})#', $dongHeader, $khop)) {
                $maTrangThai = (int) $khop[1];
                break;
            }
        }

        return [
            'ma_trang_thai' => $maTrangThai,
            'noi_dung' => $noiDung,
        ];
    }

    private function rutGonLoi(array $danhSachLoi): string
    {
        if ($danhSachLoi === []) {
            return '';
        }

        $thongBao = [];
        foreach ($danhSachLoi as $loi) {
            if (is_array($loi)) {
                $chiTiet = (string) ($loi['detail'] ?? $loi['message'] ?? $loi['code'] ?? '');
                if ($chiTiet !== '') {
                    $thongBao[] = $chiTiet;
                }
            } elseif (is_string($loi) && $loi !== '') {
                $thongBao[] = $loi;
            }
        }

        $thongBao = array_values(array_unique(array_filter($thongBao)));
        return $thongBao === [] ? 'Yeu cau NextDNS khong hop le.' : implode(' ', $thongBao);
    }

    private function thongBaoHttp(int $maTrangThai): string
    {
        return match ($maTrangThai) {
            400 => 'Yeu cau NextDNS khong hop le.',
            401 => 'API key NextDNS khong hop le.',
            403 => 'API key NextDNS khong du quyen truy cap du lieu nay.',
            404 => 'Profile NextDNS khong ton tai hoac endpoint khong dung.',
            408 => 'Yeu cau toi NextDNS bi timeout.',
            429 => 'NextDNS dang gioi han tan suat goi API. Vui long thu lai sau.',
            500, 502, 503, 504 => 'API NextDNS dang gap loi tam thoi.',
            default => 'Khong the lay du lieu tu NextDNS.',
        };
    }
}
