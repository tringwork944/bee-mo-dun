<?php
require_once __DIR__ . '/giao_dien/header_trang.php';

$danhSachProfile = (array) ($danh_sach_profile ?? []);
$profileDangChon = is_array($profile_dang_chon ?? null) ? $profile_dang_chon : null;
$tongQuan = (array) ($tong_quan ?? []);
$chiSo = (array) ($tongQuan['chi_so'] ?? []);
$duLieuBieuDo = [
    'debug' => in_array(strtolower((string) env('UNG_DUNG_DEBUG', 'false')), ['1', 'true', 'yes', 'on'], true),
    'tong_quan' => [
        'tong_truy_van' => (int) ($chiSo['tong_truy_van'] ?? 0),
        'bi_chan' => (int) ($chiSo['bi_chan'] ?? 0),
        'duoc_phep' => (int) ($chiSo['duoc_phep'] ?? 0),
        'mac_dinh' => (int) ($chiSo['mac_dinh'] ?? 0),
    ],
    'xu_huong_7_ngay' => [
        'labels' => array_values((array) ($tongQuan['bieu_do_trang_thai']['nhan'] ?? [])),
        'tong' => array_map(
            static fn($a, $b, $c): int => (int) $a + (int) $b + (int) $c,
            (array) ($tongQuan['bieu_do_trang_thai']['series']['Bi chan'] ?? []),
            (array) ($tongQuan['bieu_do_trang_thai']['series']['Duoc phep'] ?? []),
            (array) ($tongQuan['bieu_do_trang_thai']['series']['Mac dinh'] ?? [])
        ),
        'bi_chan' => array_values((array) ($tongQuan['bieu_do_trang_thai']['series']['Bi chan'] ?? [])),
    ],
    'bo_loc_chan' => [
        'labels' => array_values((array) ($tongQuan['bieu_do_bo_loc_chan']['labels'] ?? [])),
        'values' => array_values((array) ($tongQuan['bieu_do_bo_loc_chan']['values'] ?? [])),
    ],
    'top_domain' => [
        'labels' => array_values((array) ($tongQuan['bieu_do_top_truy_van']['nhan'] ?? [])),
        'values' => array_values((array) ($tongQuan['bieu_do_top_truy_van']['gia_tri'] ?? [])),
    ],
    'top_domain_bi_chan' => [
        'labels' => array_values((array) ($tongQuan['bieu_do_top_bi_chan']['nhan'] ?? [])),
        'values' => array_values((array) ($tongQuan['bieu_do_top_bi_chan']['gia_tri'] ?? [])),
    ],
];
$duLieuBieuDoJson = json_encode($duLieuBieuDo, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
if (!is_string($duLieuBieuDoJson)) {
    $duLieuBieuDoJson = '{}';
}

ob_start();
?>
<form method="get" class="d-flex align-items-end justify-content-end flex-wrap gap-2 nextdns-control-row">
    <div>
        <label class="form-label mb-1">Profile</label>
        <select class="form-select" name="profile_id">
            <?php foreach ($danhSachProfile as $profile): ?>
                <option value="<?= (int) $profile['id'] ?>" <?= $profileDangChon !== null && (int) $profileDangChon['id'] === (int) $profile['id'] ? 'selected' : '' ?>>
                    <?= bao_mat_chuoi((string) $profile['ten_profile']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-outline-primary d-inline-flex align-items-center gap-1">
        <i class="ti ti-database"></i>
        <span>Xem du lieu</span>
    </button>
    <?php if (!empty($co_cap_nhat_du_lieu) && $profileDangChon !== null): ?>
        <button type="submit" form="nextdns-form-cap-nhat" class="btn btn-primary d-inline-flex align-items-center gap-1">
            <i class="ti ti-refresh"></i>
            <span>Cap nhat du lieu</span>
        </button>
    <?php endif; ?>
</form>
<?php
$headerHanhDong = ob_get_clean();
?>
<div class="d-flex flex-column gap-3 nextdns-trang">
    <?php render_header_nextdns([
        'tieu_de' => 'Tong quan NextDNS',
        'mo_ta' => 'Theo doi tinh trang DNS va cac truy van bi chan.',
        'icon' => 'ti ti-dashboard',
        'hanh_dong' => $headerHanhDong,
    ]); ?>

    <div class="text-secondary small">
        <i class="ti ti-clock me-1"></i>Cap nhat lan cuoi:
        <span class="fw-semibold"><?= bao_mat_chuoi((string) (($tongQuan['ngay_cap_nhat'] ?? '') !== '' ? $tongQuan['ngay_cap_nhat'] : 'Chua co du lieu')) ?></span>
    </div>
    <?php if (!empty($co_cap_nhat_du_lieu) && $profileDangChon !== null): ?>
        <form method="post" action="/nextdns/cap-nhat-du-lieu" id="nextdns-form-cap-nhat" class="d-none">
            <input type="hidden" name="_csrf" value="<?= bao_mat_chuoi(csrf_tao()) ?>">
            <input type="hidden" name="profile_id" value="<?= (int) $profileDangChon['id'] ?>">
        </form>
    <?php endif; ?>

    <?php if ($danhSachProfile === []): ?>
        <div class="alert alert-warning mb-0">Chua co profile NextDNS nao. Vui long vao menu <strong>Quan ly profile</strong> de them profile.</div>
    <?php elseif (empty($tongQuan['co_du_lieu'])): ?>
        <div class="nextdns-empty">
            <i class="ti ti-chart-dots"></i>
            <div class="fw-semibold">Chua co du lieu de hien thi.</div>
            <div class="text-secondary small"><?= bao_mat_chuoi((string) ($tongQuan['thong_bao'] ?? 'Vui long cap nhat du lieu.')) ?></div>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <div class="col-12 col-md-6 col-xl-3"><div class="card card-sm nextdns-stat-card nextdns-stat-primary h-100"><div class="card-body p-3"><div class="d-flex align-items-center gap-3"><span class="nextdns-stat-icon"><i class="ti ti-activity"></i></span><div><div class="nextdns-so-lon"><?= number_format((int) ($chiSo['tong_truy_van'] ?? 0)) ?></div><div class="text-secondary small">Tong truy van</div></div></div></div></div></div>
            <div class="col-12 col-md-6 col-xl-3"><div class="card card-sm nextdns-stat-card nextdns-stat-danger h-100"><div class="card-body p-3"><div class="d-flex align-items-center gap-3"><span class="nextdns-stat-icon"><i class="ti ti-shield-x"></i></span><div><div class="nextdns-so-lon text-danger"><?= number_format((int) ($chiSo['bi_chan'] ?? 0)) ?></div><div class="text-secondary small">Bi chan</div></div></div></div></div></div>
            <div class="col-12 col-md-6 col-xl-3"><div class="card card-sm nextdns-stat-card nextdns-stat-success h-100"><div class="card-body p-3"><div class="d-flex align-items-center gap-3"><span class="nextdns-stat-icon"><i class="ti ti-shield-check"></i></span><div><div class="nextdns-so-lon text-success"><?= number_format((int) ($chiSo['duoc_phep'] ?? 0)) ?></div><div class="text-secondary small">Duoc cho phep</div></div></div></div></div></div>
            <div class="col-12 col-md-6 col-xl-3"><div class="card card-sm nextdns-stat-card nextdns-stat-info h-100"><div class="card-body p-3"><div class="d-flex align-items-center gap-3"><span class="nextdns-stat-icon"><i class="ti ti-route"></i></span><div><div class="nextdns-so-lon text-primary"><?= number_format((int) ($chiSo['mac_dinh'] ?? 0)) ?></div><div class="text-secondary small">Mac dinh</div></div></div></div></div></div>
        </div>

        <div class="row g-3">
            <div class="col-12 col-xxl-8">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Xu huong 7 ngay</h3></div>
                    <div class="card-body p-3 p-lg-4"><div class="nextdns-bieu-do" id="nextdns_bieu_do_xu_huong"></div></div>
                </div>
            </div>
            <div class="col-12 col-xxl-4">
                <div class="card h-100">
                    <div class="card-header"><h3 class="card-title">Bi chan boi bo loc nao</h3></div>
                    <div class="card-body p-3 p-lg-4"><div class="nextdns-bieu-do" id="nextdns_bieu_do_bo_loc_chan"></div></div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Top domain truy van nhieu</h3></div>
                    <div class="card-body p-3 p-lg-4"><div class="nextdns-bieu-do nextdns-bieu-do-nho" id="nextdns_bieu_do_top_domain"></div></div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Top domain bi chan</h3></div>
                    <div class="card-body p-3 p-lg-4"><div class="nextdns-bieu-do nextdns-bieu-do-nho" id="nextdns_bieu_do_domain_bi_chan"></div></div>
                </div>
            </div>
        </div>
        <script src="<?= bao_mat_chuoi(url_tai_nguyen('mo_dun/nextdns/js/apexcharts.min.js')) ?>"></script>
        <script>
            window.nextdnsDuLieuBieuDo = <?= $duLieuBieuDoJson ?>;
        </script>
        <script src="<?= bao_mat_chuoi(url_tai_nguyen('mo_dun/nextdns/js/nextdns.js')) ?>"></script>
    <?php endif; ?>
</div>
