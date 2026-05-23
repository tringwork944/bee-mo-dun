<?php
$daCauHinh = !empty($cauHinh['da_cau_hinh']);
?>
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Cau hinh ket noi NextDNS</h3>
            </div>
            <form method="post" action="/nextdns/cau-hinh">
                <input type="hidden" name="_csrf" value="<?= bao_mat_chuoi(csrf_tao()) ?>">
                <div class="card-body d-flex flex-column gap-3">
                    <?php if (!empty($cauHinh['loi_cau_hinh'])): ?>
                        <div class="alert alert-warning mb-0">
                            Cau hinh cu dang gap loi giai ma. Ban co the nhap API key moi de cap nhat lai.
                        </div>
                    <?php endif; ?>
                    <div>
                        <label class="form-label" for="ma_ho_so">Profile ID</label>
                        <input class="form-control" id="ma_ho_so" name="ma_ho_so" maxlength="64" required value="<?= bao_mat_chuoi((string) ($cauHinh['ma_ho_so'] ?? '')) ?>" placeholder="abc123">
                    </div>
                    <div>
                        <label class="form-label" for="khoa_api">API key</label>
                        <input class="form-control" id="khoa_api" name="khoa_api" autocomplete="off" placeholder="<?= $daCauHinh ? 'De trong neu muon giu API key hien tai' : 'Nhap API key NextDNS' ?>">
                        <?php if ($daCauHinh): ?>
                            <div class="form-hint">API key hien tai: <?= bao_mat_chuoi((string) ($cauHinh['khoa_api_an'] ?? '')) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary"><i class="ti ti-edit me-1"></i>Luu cau hinh</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Kiem tra nhanh</h3>
            </div>
            <div class="card-body d-flex flex-column gap-3">
                <div>
                    <div class="text-secondary small">Trang thai</div>
                    <div class="fw-bold"><?= $daCauHinh ? 'Da cau hinh' : 'Chua cau hinh' ?></div>
                </div>
                <div>
                    <div class="text-secondary small">Profile dang luu</div>
                    <div class="fw-bold"><?= bao_mat_chuoi((string) ($cauHinh['ma_ho_so'] ?? 'Chua co')) ?></div>
                </div>
                <form method="post" action="/nextdns/kiem-tra-ket-noi">
                    <input type="hidden" name="_csrf" value="<?= bao_mat_chuoi(csrf_tao()) ?>">
                    <button type="submit" class="btn btn-outline-primary" <?= $daCauHinh ? '' : 'disabled' ?>><i class="ti ti-search me-1"></i>Kiem tra ket noi API</button>
                </form>
                <small class="text-secondary">API key luon duoc che tren giao dien.</small>
            </div>
        </div>
    </div>
</div>
