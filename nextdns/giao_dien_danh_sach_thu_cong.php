<?php
require_once __DIR__ . '/giao_dien/header_trang.php';

$danhSachProfile = (array) ($danh_sach_profile ?? []);
$profileDangChon = is_array($profile_dang_chon ?? null) ? $profile_dang_chon : null;
$danhSach = (array) ($danh_sach ?? []);
$moTaDanhSach = (string) ($moTaDanhSach ?? 'Quan ly danh sach ten mien.');
$iconHeader = (string) ($iconHeader ?? 'ti ti-shield-x');

ob_start();
if (!empty($co_quan_ly) && $profileDangChon !== null):
?>
    <button class="btn btn-primary d-inline-flex align-items-center gap-1" type="button" data-bs-toggle="modal" data-bs-target="#nextdns-modal-domain">
        <i class="ti ti-plus"></i>
        <span>Them domain</span>
    </button>
    <button class="btn btn-primary d-inline-flex align-items-center gap-1" type="submit" form="nextdns-form-dong-bo">
        <i class="ti ti-refresh"></i>
        <span>Dong bo</span>
    </button>
<?php
endif;
$headerHanhDong = ob_get_clean();
?>
<div class="d-flex flex-column gap-3 nextdns-trang">
    <?php render_header_nextdns([
        'tieu_de' => $tieuDeDanhSach,
        'mo_ta' => $moTaDanhSach,
        'icon' => $iconHeader,
        'hanh_dong' => $headerHanhDong,
    ]); ?>
    <?php if (!empty($co_quan_ly) && $profileDangChon !== null): ?>
        <form method="post" action="<?= bao_mat_chuoi($duongDanDongBo) ?>" id="nextdns-form-dong-bo" class="d-none"><input type="hidden" name="_csrf" value="<?= bao_mat_chuoi(csrf_tao()) ?>"><input type="hidden" name="profile_id" value="<?= (int) $profileDangChon['id'] ?>"></form>
    <?php endif; ?>

    <div class="card nextdns-filter-card">
        <div class="card-body p-3 p-lg-4">
            <form method="get" action="<?= bao_mat_chuoi($duongDan) ?>" class="row g-3 align-items-end nextdns-control-row">
                <div class="col-12 col-md-3">
                    <label class="form-label">Profile</label>
                    <select class="form-select" name="profile_id">
                        <?php foreach ($danhSachProfile as $profile): ?>
                            <option value="<?= (int) $profile['id'] ?>" <?= $profileDangChon !== null && (int) $profileDangChon['id'] === (int) $profile['id'] ? 'selected' : '' ?>>
                                <?= bao_mat_chuoi((string) $profile['ten_profile']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-7">
                    <label class="form-label">Tim kiem domain</label>
                    <input type="text" class="form-control" name="tu_khoa" value="<?= bao_mat_chuoi((string) ($tu_khoa ?? '')) ?>" placeholder="example.com">
                </div>
                <div class="col-12 col-md-2 d-grid">
                    <button class="btn btn-outline-primary w-100" type="submit"><span class="nextdns-btn-icon"><i class="ti ti-search"></i><span>Tim kiem</span></span></button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header nextdns-card-header-tight"><h3 class="card-title"><?= bao_mat_chuoi($tieuDeDanhSach) ?></h3></div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead><tr><th>Domain</th><th>Ghi chu</th><th>Trang thai</th><th>Cap nhat cuoi</th><th class="text-end">Hanh dong</th></tr></thead>
                <tbody>
                <?php foreach ($danhSach as $dong): ?>
                    <tr>
                        <td class="w-50"><span class="nextdns-table-domain"><?= bao_mat_chuoi((string) $dong['domain']) ?></span></td>
                        <td class="nextdns-note-cell"><?= bao_mat_chuoi((string) ($dong['ghi_chu'] ?? '')) ?></td>
                        <td><span class="badge <?= (int) $dong['trang_thai'] === 1 ? 'bg-green-lt text-green' : 'bg-secondary-lt text-secondary' ?>"><?= (int) $dong['trang_thai'] === 1 ? 'Hoat dong' : 'Tam tat' ?></span></td>
                        <td><?= bao_mat_chuoi((string) (($dong['cap_nhat_cuoi'] ?? '') ?: ($dong['ngay_sua'] ?? ''))) ?></td>
                        <td class="text-end nextdns-actions-cell"><div class="btn-list justify-content-end flex-nowrap"><form method="post" action="<?= bao_mat_chuoi($duongDanChuyenTrangThai . (int) $dong['id']) ?>" class="d-inline"><input type="hidden" name="_csrf" value="<?= bao_mat_chuoi(csrf_tao()) ?>"><input type="hidden" name="profile_id" value="<?= (int) $dong['profile_id'] ?>"><button type="submit" class="btn <?= (int) $dong['trang_thai'] === 1 ? 'btn-outline-warning' : 'btn-outline-success' ?> btn-sm d-inline-flex align-items-center gap-1"><i class="ti <?= (int) $dong['trang_thai'] === 1 ? 'ti-toggle-right' : 'ti-toggle-left' ?>"></i><span><?= (int) $dong['trang_thai'] === 1 ? 'Tat' : 'Bat' ?></span></button></form><button type="button" class="btn btn-outline-danger btn-sm d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#nextdns-modal-xoa-domain-<?= (int) $dong['id'] ?>"><i class="ti ti-trash"></i><span>Xoa</span></button></div></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($danhSach === []): ?><tr><td colspan="5"><div class="nextdns-empty my-2"><i class="ti ti-list-search"></i><div class="fw-semibold">Chua co domain nao.</div></div></td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if (!empty($co_quan_ly) && $profileDangChon !== null): ?>
    <div class="modal modal-blur fade" id="nextdns-modal-domain" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="post" action="<?= bao_mat_chuoi($duongDanThem) ?>" class="modal-content">
                <input type="hidden" name="_csrf" value="<?= bao_mat_chuoi(csrf_tao()) ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Them domain</h5>
                    <a class="btn-close" href="<?= bao_mat_chuoi($duongDan . '?profile_id=' . (int) ($profileDangChon['id'] ?? 0)) ?>" aria-label="Dong"></a>
                </div>
                <div class="modal-body d-flex flex-column gap-3">
                    <div><label class="form-label">Profile</label><select class="form-select" name="profile_id"><?php foreach ($danhSachProfile as $profile): ?><option value="<?= (int) $profile['id'] ?>" <?= $profileDangChon !== null && (int) $profileDangChon['id'] === (int) $profile['id'] ? 'selected' : '' ?>><?= bao_mat_chuoi((string) $profile['ten_profile']) ?></option><?php endforeach; ?></select></div>
                    <div><label class="form-label">Domain</label><input type="text" class="form-control" name="domain" required maxlength="253" placeholder="example.com"></div>
                    <div><label class="form-label">Ghi chu</label><textarea class="form-control" name="ghi_chu" rows="3" maxlength="1000"></textarea></div>
                    <div><label class="form-label">Trang thai</label><select class="form-select" name="trang_thai"><option value="1" selected>Hoat dong</option><option value="0">Tam tat</option></select></div>
                </div>
                <div class="modal-footer"><a class="btn btn-outline-secondary" href="<?= bao_mat_chuoi($duongDan . '?profile_id=' . (int) ($profileDangChon['id'] ?? 0)) ?>">Huy</a><button type="submit" class="btn btn-primary"><span class="nextdns-btn-icon"><i class="ti ti-plus"></i><span>Them domain</span></span></button></div>
            </form>
        </div>
    </div>
    <?php foreach ($danhSach as $dong): ?>
        <div class="modal modal-blur fade" id="nextdns-modal-xoa-domain-<?= (int) $dong['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form method="post" action="<?= bao_mat_chuoi($duongDanXoa . (int) $dong['id']) ?>" class="modal-content">
                    <input type="hidden" name="_csrf" value="<?= bao_mat_chuoi(csrf_tao()) ?>">
                    <input type="hidden" name="profile_id" value="<?= (int) $dong['profile_id'] ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Xoa domain</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Dong"></button>
                    </div>
                    <div class="modal-body d-flex flex-column gap-3">
                        <p class="mb-0">Ban co chac chan muon xoa domain nay khoi danh sach khong?</p>
                        <div class="nextdns-modal-summary d-flex flex-column gap-2">
                            <div><span class="text-secondary small d-block mb-1">Domain</span><span class="nextdns-table-domain"><?= bao_mat_chuoi((string) $dong['domain']) ?></span></div>
                            <?php if ($profileDangChon !== null): ?>
                                <div><span class="text-secondary small d-block mb-1">Profile</span><span class="fw-semibold"><?= bao_mat_chuoi((string) $profileDangChon['ten_profile']) ?></span></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Huy</button>
                        <button type="submit" class="btn btn-danger"><span class="nextdns-btn-icon"><i class="ti ti-trash"></i><span>Xac nhan xoa</span></span></button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
