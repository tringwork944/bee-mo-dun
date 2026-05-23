<?php
$boLoc = (array) ($ketQua['bo_loc'] ?? []);
$daCauHinh = !empty($cauHinh['da_cau_hinh']);
?>
<div class="d-flex flex-column gap-3">
    <?php if (!$daCauHinh): ?>
        <div class="alert alert-warning">
            Chua co cau hinh NextDNS. Vui long <a href="/nextdns/cau-hinh" class="alert-link">thiet lap ket noi</a> de tim kiem logs.
        </div>
    <?php elseif (empty($ketQua['ok']) && !empty($ketQua['thong_bao'])): ?>
        <div class="alert alert-danger"><?= bao_mat_chuoi((string) $ketQua['thong_bao']) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Tim kiem domain trong logs</h3>
        </div>
        <form method="get" action="/nextdns/logs">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label" for="tu_khoa">Domain</label>
                        <input class="form-control" id="tu_khoa" name="tu_khoa" value="<?= bao_mat_chuoi((string) ($boLoc['tu_khoa'] ?? '')) ?>" placeholder="example.com">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="trang_thai">Trang thai</label>
                        <select class="form-select" id="trang_thai" name="trang_thai">
                            <?php $trangThaiDaChon = (string) ($boLoc['trang_thai'] ?? 'tat_ca'); ?>
                            <option value="tat_ca" <?= $trangThaiDaChon === 'tat_ca' ? 'selected' : '' ?>>Tat ca</option>
                            <option value="blocked" <?= $trangThaiDaChon === 'blocked' ? 'selected' : '' ?>>Bi chan</option>
                            <option value="allowed" <?= $trangThaiDaChon === 'allowed' ? 'selected' : '' ?>>Duoc phep</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="gioi_han">So dong</label>
                        <select class="form-select" id="gioi_han" name="gioi_han">
                            <?php foreach ([25, 50, 100] as $gioiHan): ?>
                                <option value="<?= $gioiHan ?>" <?= (int) ($boLoc['gioi_han'] ?? 50) === $gioiHan ? 'selected' : '' ?>><?= $gioiHan ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary"><i class="ti ti-search me-1"></i>Tim kiem</button>
                <a href="/nextdns/logs" class="btn btn-outline-secondary">Xoa bo loc</a>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Ket qua logs</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                <tr>
                    <th>Domain</th>
                    <th>Trang thai</th>
                    <th>Thiet bi</th>
                    <th>Thoi gian</th>
                    <th>Ly do / danh sach chan</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ((array) ($ketQua['danh_sach'] ?? []) as $dong): ?>
                    <?php $trangThai = (string) ($dong['trang_thai'] ?? 'default'); ?>
                    <tr>
                        <td>
                            <div class="fw-bold"><?= bao_mat_chuoi((string) ($dong['domain'] ?? '')) ?></div>
                            <?php if (!empty($dong['root']) && $dong['root'] !== $dong['domain']): ?>
                                <small class="text-secondary">Root: <?= bao_mat_chuoi((string) $dong['root']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge <?= $trangThai === 'blocked' ? 'bg-danger-lt text-danger' : ($trangThai === 'allowed' ? 'bg-success-lt text-success' : ($trangThai === 'error' ? 'bg-yellow-lt text-yellow' : 'bg-blue-lt text-blue')) ?>">
                                <?= bao_mat_chuoi($trangThai) ?>
                            </span>
                        </td>
                        <td><?= bao_mat_chuoi((string) (($dong['thiet_bi'] ?? '') ?: 'Khong ro')) ?></td>
                        <td><?= bao_mat_chuoi((string) (($dong['thoi_gian'] ?? '') ?: 'Khong ro')) ?></td>
                        <td><?= bao_mat_chuoi((string) (($dong['ly_do'] ?? '') ?: 'Khong co')) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($ketQua['danh_sach'])): ?>
                    <tr><td colspan="5" class="text-secondary">Khong co log phu hop bo loc hien tai.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (!empty($ketQua['cursor_tiep'])): ?>
            <div class="card-footer">
                <form method="get" action="/nextdns/logs" class="d-flex flex-wrap gap-2">
                    <input type="hidden" name="tu_khoa" value="<?= bao_mat_chuoi((string) ($boLoc['tu_khoa'] ?? '')) ?>">
                    <input type="hidden" name="trang_thai" value="<?= bao_mat_chuoi((string) ($boLoc['trang_thai'] ?? 'tat_ca')) ?>">
                    <input type="hidden" name="gioi_han" value="<?= (int) ($boLoc['gioi_han'] ?? 50) ?>">
                    <input type="hidden" name="cursor" value="<?= bao_mat_chuoi((string) $ketQua['cursor_tiep']) ?>">
                    <button type="submit" class="btn btn-outline-primary"><i class="ti ti-database me-1"></i>Tai them log</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>
