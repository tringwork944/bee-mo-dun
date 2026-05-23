<?php

if (!function_exists('render_header_nextdns')) {
    function render_header_nextdns(array $cauHinh = []): void
    {
        $tieuDe = (string) ($cauHinh['tieu_de'] ?? 'NextDNS');
        $moTa = (string) ($cauHinh['mo_ta'] ?? '');
        $icon = trim((string) ($cauHinh['icon'] ?? 'ti ti-dashboard'));
        $hanhDong = (string) ($cauHinh['hanh_dong'] ?? '');
        ?>
        <div class="card mb-3 nextdns-page-header">
            <div class="card-body p-3 p-lg-4">
                <div class="row g-3 align-items-center">
                    <div class="col">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="avatar avatar-sm bg-primary-lt text-primary">
                                <i class="<?= bao_mat_chuoi($icon) ?>"></i>
                            </span>
                            <div class="text-primary text-uppercase fw-bold small">NextDNS</div>
                        </div>
                        <h2 class="page-title nextdns-page-title mb-1"><?= bao_mat_chuoi($tieuDe) ?></h2>
                        <?php if ($moTa !== ''): ?>
                            <div class="text-secondary"><?= bao_mat_chuoi($moTa) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex align-items-end justify-content-end flex-wrap gap-2 nextdns-page-header-actions">
                            <?= $hanhDong ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
