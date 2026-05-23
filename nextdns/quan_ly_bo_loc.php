<?php
require_once __DIR__ . '/giao_dien/header_trang.php';

$danhSachProfile = (array) ($danh_sach_profile ?? []);
$profileDangChon = is_array($profile_dang_chon ?? null) ? $profile_dang_chon : null;
$danhSachBoLoc = (array) ($danh_sach_bo_loc ?? []);
$capNhatCuoiHienThi = (string) (($cap_nhat_cuoi ?? '') !== '' ? $cap_nhat_cuoi : 'Chua co du lieu');

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
        <button type="submit" form="nextdns-form-cap-nhat-bo-loc" class="btn btn-primary d-inline-flex align-items-center gap-1">
            <i class="ti ti-refresh"></i>
            <span>Dong bo</span>
        </button>
    <?php endif; ?>
</form>
<?php
$headerHanhDong = ob_get_clean();
?>
<div class="d-flex flex-column gap-3 nextdns-trang">
    <?php render_header_nextdns([
        'tieu_de' => 'Quan ly bo loc',
        'mo_ta' => 'Theo doi cac bo loc dang ap dung cho tung profile.',
        'icon' => 'ti ti-filter-cog',
        'hanh_dong' => $headerHanhDong,
    ]); ?>
    <?php if (!empty($co_cap_nhat_du_lieu) && $profileDangChon !== null): ?>
        <form method="post" action="/nextdns/quan-ly-bo-loc/cap-nhat" id="nextdns-form-cap-nhat-bo-loc" class="d-none">
            <input type="hidden" name="_csrf" value="<?= bao_mat_chuoi(csrf_tao()) ?>">
            <input type="hidden" name="profile_id" value="<?= (int) $profileDangChon['id'] ?>">
        </form>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 w-100">
                <h3 class="card-title mb-0">Danh sach bo loc</h3>
                <div class="text-secondary small d-inline-flex align-items-center gap-1">
                    <i class="ti ti-clock"></i>
                    <span>Cap nhat lan cuoi: <span class="fw-semibold"><?= bao_mat_chuoi($capNhatCuoiHienThi) ?></span></span>
                </div>
            </div>
        </div>
        <?php if ($danhSachBoLoc === []): ?>
            <div class="card-body p-3 p-lg-4">
                <div class="nextdns-empty">
                    <i class="ti ti-filter"></i>
                    <div class="fw-semibold">Chua co du lieu bo loc.</div>
                    <?php if (!empty($co_cap_nhat_du_lieu) && $profileDangChon !== null): ?>
                        <button type="submit" form="nextdns-form-cap-nhat-bo-loc" class="btn btn-primary d-inline-flex align-items-center gap-1 mt-2">
                            <i class="ti ti-refresh"></i>
                            <span>Dong bo ngay</span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Bo loc</th>
                        <th>Loai</th>
                        <th>Trang thai</th>
                        <th>Cap nhat cuoi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($danhSachBoLoc as $boLoc): ?>
                        <tr>
                            <td class="fw-semibold"><?= bao_mat_chuoi((string) ($boLoc['ten'] ?? '')) ?></td>
                            <td><?= bao_mat_chuoi((string) ($boLoc['loai'] ?? '')) ?></td>
                            <td><span class="badge <?= (int) ($boLoc['trang_thai'] ?? 0) === 1 ? 'bg-green-lt text-green' : 'bg-secondary-lt text-secondary' ?>"><?= (int) ($boLoc['trang_thai'] ?? 0) === 1 ? 'Hoat dong' : 'Tam tat' ?></span></td>
                            <td><?= bao_mat_chuoi((string) (($boLoc['cap_nhat_cuoi'] ?? '') !== '' ? $boLoc['cap_nhat_cuoi'] : $capNhatCuoiHienThi)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
