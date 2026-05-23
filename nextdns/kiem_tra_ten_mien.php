<?php
require_once __DIR__ . '/giao_dien/header_trang.php';

$danhSachProfile = (array) ($danh_sach_profile ?? []);
$profileDangChon = is_array($profile_dang_chon ?? null) ? $profile_dang_chon : null;
$ketQua = is_array($ket_qua_kiem_tra ?? null) ? $ket_qua_kiem_tra : null;
$trangThai = (string) ($ketQua['trang_thai'] ?? 'khong_xac_dinh');
$badge = match ($trangThai) {
    'bi_chan' => 'bg-danger-lt text-danger',
    'khong_bi_chan' => 'bg-success-lt text-success',
    default => 'bg-warning-lt text-warning',
};
$nhanTrangThai = match ($trangThai) {
    'bi_chan' => 'Bi chan',
    'khong_bi_chan' => 'Khong bi chan',
    default => 'Khong xac dinh',
};
$iconTrangThai = match ($trangThai) {
    'bi_chan' => 'ti ti-shield-x',
    'khong_bi_chan' => 'ti ti-shield-check',
    default => 'ti ti-help-triangle',
};
$mauIcon = match ($trangThai) {
    'bi_chan' => 'text-danger bg-danger-lt',
    'khong_bi_chan' => 'text-success bg-success-lt',
    default => 'text-warning bg-warning-lt',
};
?>
<div class="d-flex flex-column gap-3 nextdns-trang">
    <?php render_header_nextdns([
        'tieu_de' => 'Kiem tra ten mien',
        'mo_ta' => 'Kiem tra nhanh mot ten mien co bi chan hay khong.',
        'icon' => 'ti ti-world-search',
        'hanh_dong' => '',
    ]); ?>

    <div class="card nextdns-card-mem">
        <div class="card-body p-3 p-lg-4">
            <?php if (empty($co_kiem_tra_ten_mien)): ?>
                <div class="alert alert-warning mb-0">Ban khong co quyen kiem tra ten mien NextDNS.</div>
            <?php else: ?>
                <form method="post" action="/nextdns/kiem-tra-ten-mien" class="row g-3 align-items-end nextdns-control-row">
                    <input type="hidden" name="_csrf" value="<?= bao_mat_chuoi(csrf_tao()) ?>">
                    <div class="col-12 col-md-4">
                        <label class="form-label">Profile NextDNS</label>
                        <select class="form-select" name="profile_id" required>
                            <?php foreach ($danhSachProfile as $profile): ?>
                                <option value="<?= (int) $profile['id'] ?>" <?= $profileDangChon !== null && (int) $profileDangChon['id'] === (int) $profile['id'] ? 'selected' : '' ?>>
                                    <?= bao_mat_chuoi((string) $profile['ten_profile']) ?> - <?= bao_mat_chuoi((string) $profile['ma_profile_nextdns']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Ten mien</label>
                        <input type="text" class="form-control" name="ten_mien" required maxlength="253" pattern="^(?!-)(?:[A-Za-z0-9](?:[A-Za-z0-9-]{0,61}[A-Za-z0-9])?\.)+[A-Za-z]{2,63}$" placeholder="example.com" value="<?= bao_mat_chuoi((string) ($ten_mien ?? '')) ?>">
                    </div>
                    <div class="col-12 col-md-2 d-grid">
                        <button type="submit" class="btn btn-primary w-100">
                            <span class="nextdns-btn-icon"><i class="ti ti-world-search"></i><span>Kiem tra</span></span>
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($ketQua !== null): ?>
    <div class="modal modal-blur fade" id="nextdns-modal-ket-qua-kiem-tra" tabindex="-1" aria-hidden="true" data-nextdns-tu-dong-hien="1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ket qua kiem tra</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Dong"></button>
                </div>
                <div class="modal-body p-3 p-lg-4">
                    <div class="nextdns-modal-icon <?= bao_mat_chuoi($mauIcon) ?> mx-auto mb-3"><i class="<?= bao_mat_chuoi($iconTrangThai) ?>"></i></div>
                    <div class="mb-3 text-center"><span class="badge <?= bao_mat_chuoi($badge) ?> fs-4"><?= bao_mat_chuoi($nhanTrangThai) ?></span></div>
                    <?php if (empty($ketQua['ok'])): ?>
                        <div class="alert alert-danger"><?= bao_mat_chuoi((string) ($ketQua['thong_bao'] ?? 'Khong the kiem tra ten mien.')) ?></div>
                    <?php endif; ?>
                    <dl class="row mb-0 text-start g-3">
                        <div class="col-12 col-md-6">
                            <dt class="text-secondary small mb-1">Ten mien</dt>
                            <dd class="mb-0"><span class="nextdns-table-domain"><?= bao_mat_chuoi((string) ($ketQua['ten_mien'] ?? $ten_mien ?? '')) ?></span></dd>
                        </div>
                        <div class="col-12 col-md-6">
                            <dt class="text-secondary small mb-1">Profile</dt>
                            <dd class="mb-0"><?= bao_mat_chuoi((string) ($ketQua['profile'] ?? ($profileDangChon['ten_profile'] ?? ''))) ?></dd>
                        </div>
                        <div class="col-12 col-md-6">
                            <dt class="text-secondary small mb-1">Ly do chan</dt>
                            <dd class="mb-0"><?= bao_mat_chuoi((string) (($ketQua['ly_do_chan'] ?? '') !== '' ? $ketQua['ly_do_chan'] : 'Khong co')) ?></dd>
                        </div>
                        <div class="col-12 col-md-6">
                            <dt class="text-secondary small mb-1">Thoi gian kiem tra</dt>
                            <dd class="mb-0"><?= bao_mat_chuoi((string) ($ketQua['thoi_gian_kiem_tra'] ?? date('Y-m-d H:i:s'))) ?></dd>
                        </div>
                        <?php if ((string) ($ketQua['bo_loc'] ?? '') !== ''): ?>
                            <div class="col-12">
                                <dt class="text-secondary small mb-1">Bo loc khop</dt>
                                <dd class="mb-0"><?= bao_mat_chuoi((string) $ketQua['bo_loc']) ?></dd>
                            </div>
                        <?php endif; ?>
                    </dl>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Dong</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                        <span class="nextdns-btn-icon"><i class="ti ti-world-search"></i><span>Kiem tra ten mien khac</span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        (function () {
            function hienThiModalKetQua() {
            var el = document.getElementById('nextdns-modal-ket-qua-kiem-tra');
                if (!el || el.dataset.nextdnsDaHien === '1') {
                    return;
                }
                el.dataset.nextdnsDaHien = '1';

            if (el && window.bootstrap && window.bootstrap.Modal) {
                new window.bootstrap.Modal(el).show();
                    return;
                }

                el.classList.add('show');
                el.style.display = 'block';
                el.removeAttribute('aria-hidden');
                el.setAttribute('aria-modal', 'true');
                document.body.classList.add('modal-open');

                var backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                backdrop.setAttribute('data-nextdns-backdrop', '1');
                document.body.appendChild(backdrop);

                var dongModal = function () {
                    el.classList.remove('show');
                    el.style.display = 'none';
                    el.setAttribute('aria-hidden', 'true');
                    el.removeAttribute('aria-modal');
                    document.body.classList.remove('modal-open');
                    document.querySelectorAll('[data-nextdns-backdrop="1"]').forEach(function (item) {
                        item.remove();
                    });
                };

                el.querySelectorAll('[data-bs-dismiss="modal"]').forEach(function (nut) {
                    nut.addEventListener('click', dongModal);
                });
            }

            if (document.readyState === 'complete') {
                hienThiModalKetQua();
            } else {
                window.addEventListener('load', hienThiModalKetQua);
            }
            window.setTimeout(hienThiModalKetQua, 500);
        })();
    </script>
<?php endif; ?>
