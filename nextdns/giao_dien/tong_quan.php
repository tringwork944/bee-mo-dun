<?php
$daCauHinh = !empty($cauHinh['da_cau_hinh']);
$chiSo = (array) ($tongQuan['chi_so'] ?? []);
$phanTram = (array) ($tongQuan['phan_tram'] ?? []);
$duLieuBieuDoJson = (string) json_encode($tongQuan['bieu_do'] ?? ['nhan' => [], 'du_lieu' => []], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
<div class="d-flex flex-column gap-3">
    <?php if (!$daCauHinh): ?>
        <div class="alert alert-warning">
            Chua co cau hinh NextDNS. Vui long <a href="/nextdns/cau-hinh" class="alert-link">cap nhat API key va profile ID</a> truoc khi xem thong ke.
        </div>
    <?php elseif (empty($tongQuan['ok'])): ?>
        <div class="alert alert-danger">
            <?= bao_mat_chuoi((string) ($tongQuan['thong_bao'] ?? 'Khong the tai du lieu tong quan.')) ?>
        </div>
    <?php endif; ?>

    <div class="row g-3">
        <div class="col-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-secondary small">Tong truy van DNS</div>
                    <div class="nextdns-so-lon"><?= number_format((int) ($chiSo['tong'] ?? 0)) ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-secondary small">Bi chan</div>
                    <div class="nextdns-so-lon text-danger"><?= number_format((int) ($chiSo['blocked'] ?? 0)) ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-secondary small">Duoc phep</div>
                    <div class="nextdns-so-lon text-success"><?= number_format((int) ($chiSo['allowed'] ?? 0)) ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-secondary small">Mac dinh</div>
                    <div class="nextdns-so-lon text-primary"><?= number_format((int) ($chiSo['default'] ?? 0)) ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Xu huong truy van 7 ngay</h3>
                </div>
                <div class="card-body">
                    <div id="nextdns-bieu-do-trang-thai" class="nextdns-bieu-do"></div>
                    <script type="application/json" id="nextdns-du-lieu-bieu-do"><?= bao_mat_chuoi($duLieuBieuDoJson) ?></script>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h3 class="card-title">Ty le trang thai</h3>
                </div>
                <div class="card-body d-flex flex-column gap-3">
                    <div class="nextdns-thanh-phan">
                        <span>Blocked</span>
                        <strong><?= number_format((float) ($phanTram['blocked'] ?? 0), 2) ?>%</strong>
                    </div>
                    <div class="progress progress-separated">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: <?= (float) ($phanTram['blocked'] ?? 0) ?>%"></div>
                    </div>
                    <div class="nextdns-thanh-phan">
                        <span>Allowed</span>
                        <strong><?= number_format((float) ($phanTram['allowed'] ?? 0), 2) ?>%</strong>
                    </div>
                    <div class="progress progress-separated">
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= (float) ($phanTram['allowed'] ?? 0) ?>%"></div>
                    </div>
                    <div class="nextdns-thanh-phan">
                        <span>Default</span>
                        <strong><?= number_format((float) ($phanTram['default'] ?? 0), 2) ?>%</strong>
                    </div>
                    <div class="progress progress-separated">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: <?= (float) ($phanTram['default'] ?? 0) ?>%"></div>
                    </div>
                    <small class="text-secondary">Nhan cap nhat khi ban muon lam moi so lieu.</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Top domain bi chan</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                        <tr><th>Domain</th><th class="text-end">So truy van</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ((array) ($tongQuan['top_chan'] ?? []) as $dong): ?>
                            <tr>
                                <td><?= bao_mat_chuoi((string) ($dong['domain'] ?? '')) ?></td>
                                <td class="text-end"><?= number_format((int) ($dong['queries'] ?? 0)) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($tongQuan['top_chan'])): ?>
                            <tr><td colspan="2" class="text-secondary">Chua co du lieu bi chan.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Top domain truy van nhieu</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                        <tr><th>Domain</th><th class="text-end">So truy van</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ((array) ($tongQuan['top_truy_van'] ?? []) as $dong): ?>
                            <tr>
                                <td><?= bao_mat_chuoi((string) ($dong['domain'] ?? '')) ?></td>
                                <td class="text-end"><?= number_format((int) ($dong['queries'] ?? 0)) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($tongQuan['top_truy_van'])): ?>
                            <tr><td colspan="2" class="text-secondary">Chua co du lieu truy van.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
