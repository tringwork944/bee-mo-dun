<?php
$daCauHinh = !empty($cauHinh['da_cau_hinh']);
?>
<div class="d-flex flex-column gap-3">
    <?php if (!$daCauHinh): ?>
        <div class="alert alert-warning">
            Chua co cau hinh NextDNS. Vui long <a href="/nextdns/cau-hinh" class="alert-link">thiet lap ket noi</a> truoc khi quan ly denylist.
        </div>
    <?php elseif (empty($ketQua['ok']) && !empty($ketQua['thong_bao'])): ?>
        <div class="alert alert-danger"><?= bao_mat_chuoi((string) $ketQua['thong_bao']) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Them domain vao denylist</h3>
        </div>
        <form method="post" action="/nextdns/denylist/them">
            <input type="hidden" name="_csrf" value="<?= bao_mat_chuoi(csrf_tao()) ?>">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-9">
                        <label class="form-label" for="mien">Domain</label>
                        <input class="form-control" id="mien" name="mien" placeholder="ads.example.com" required>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100" <?= $daCauHinh ? '' : 'disabled' ?>><i class="ti ti-plus me-1"></i>Them vao denylist</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Danh sach denylist hien tai</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                <tr>
                    <th>Domain</th>
                    <th>Trang thai</th>
                    <th class="text-end">Thao tac</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ((array) ($ketQua['danh_sach'] ?? []) as $dong): ?>
                    <tr>
                        <td><?= bao_mat_chuoi((string) ($dong['id'] ?? '')) ?></td>
                        <td>
                            <span class="badge <?= !empty($dong['active']) ? 'bg-danger-lt text-danger' : 'bg-secondary-lt text-secondary' ?>">
                                <?= !empty($dong['active']) ? 'Dang chan' : 'Dang tat' ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <form method="post" action="/nextdns/denylist/xoa" class="d-inline">
                                <input type="hidden" name="_csrf" value="<?= bao_mat_chuoi(csrf_tao()) ?>">
                                <input type="hidden" name="mien" value="<?= bao_mat_chuoi((string) ($dong['id'] ?? '')) ?>">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Xoa domain khoi denylist?')"><i class="ti ti-trash me-1"></i>Xoa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($ketQua['danh_sach'])): ?>
                    <tr><td colspan="3" class="text-secondary">Chua co domain nao trong denylist.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (!empty($ketQua['cursor_tiep'])): ?>
            <div class="card-footer">
                <form method="get" action="/nextdns/denylist">
                    <input type="hidden" name="cursor" value="<?= bao_mat_chuoi((string) $ketQua['cursor_tiep']) ?>">
                    <button type="submit" class="btn btn-outline-primary"><i class="ti ti-database me-1"></i>Tai them</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>
