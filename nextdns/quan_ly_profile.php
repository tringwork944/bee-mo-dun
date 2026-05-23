<?php
require_once __DIR__ . '/giao_dien/header_trang.php';

$danhSachProfile = (array) ($danh_sach_profile ?? []);

ob_start();
if (!empty($co_quan_ly_profile)):
?>
    <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#nextdns-modal-them-profile">
        <i class="ti ti-plus"></i>
        <span>Them profile</span>
    </button>
<?php
endif;
$headerHanhDong = ob_get_clean();
?>
<div class="d-flex flex-column gap-3 nextdns-trang">
    <?php render_header_nextdns([
        'tieu_de' => 'Quan ly profile',
        'mo_ta' => 'Quan ly cac profile NextDNS dang su dung.',
        'icon' => 'ti ti-user-cog',
        'hanh_dong' => $headerHanhDong,
    ]); ?>

    <div class="card">
        <div class="card-header nextdns-card-header-tight"><h3 class="card-title">Danh sach profile</h3></div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead><tr><th>Ten profile</th><th>Ma profile</th><th>API key</th><th>Trang thai</th><th>Cap nhat cuoi</th><th class="text-end">Hanh dong</th></tr></thead>
                <tbody>
                <?php foreach ($danhSachProfile as $profile): ?>
                    <tr>
                        <td><div class="fw-semibold"><?= bao_mat_chuoi((string) $profile['ten_profile']) ?></div><?php if ((string) $profile['ghi_chu'] !== ''): ?><div class="text-secondary small nextdns-note-cell"><?= bao_mat_chuoi((string) $profile['ghi_chu']) ?></div><?php endif; ?></td>
                        <td><code class="nextdns-code-inline"><?= bao_mat_chuoi((string) $profile['ma_profile_nextdns']) ?></code></td>
                        <td><code class="nextdns-code-inline"><?= bao_mat_chuoi((string) $profile['api_key_an']) ?></code></td>
                        <td><span class="badge <?= (int) $profile['trang_thai'] === 1 ? 'bg-green-lt text-green' : 'bg-secondary-lt text-secondary' ?>"><?= (int) $profile['trang_thai'] === 1 ? 'Hoat dong' : 'Tam tat' ?></span></td>
                        <td><?= bao_mat_chuoi((string) (($profile['ngay_cap_nhat_cache'] ?? '') !== '' ? $profile['ngay_cap_nhat_cache'] : 'Chua co')) ?></td>
                        <td class="text-end nextdns-actions-cell">
                            <div class="btn-list justify-content-end flex-nowrap">
                                <button type="button" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1 btn-sua-profile" data-bs-toggle="modal" data-bs-target="#nextdns-modal-sua-profile" data-id="<?= (int) $profile['id'] ?>" data-ten-profile="<?= bao_mat_chuoi((string) $profile['ten_profile']) ?>" data-ma-profile="<?= bao_mat_chuoi((string) $profile['ma_profile_nextdns']) ?>" data-trang-thai="<?= (int) $profile['trang_thai'] ?>" data-ghi-chu="<?= bao_mat_chuoi((string) $profile['ghi_chu']) ?>">
                                    <i class="ti ti-edit"></i>
                                    <span>Sua</span>
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#nextdns-modal-xoa-profile-<?= (int) $profile['id'] ?>">
                                    <span class="nextdns-btn-icon"><i class="ti ti-trash"></i><span>Xoa</span></span>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($danhSachProfile === []): ?><tr><td colspan="6"><div class="nextdns-empty my-2"><i class="ti ti-user-plus"></i><div class="fw-semibold">Chua co profile nao.</div></div></td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if (!empty($co_quan_ly_profile)): ?>
    <div class="modal modal-blur fade" id="nextdns-modal-them-profile" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="post" action="/nextdns/them-profile" class="modal-content">
                <input type="hidden" name="_csrf" value="<?= bao_mat_chuoi(csrf_tao()) ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Them profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Dong"></button>
                </div>
                <div class="modal-body d-flex flex-column gap-3">
                    <div><label class="form-label">Ten profile</label><input type="text" class="form-control" name="ten_profile" maxlength="150" required></div>
                    <div><label class="form-label">Ma profile NextDNS</label><input type="text" class="form-control" name="ma_profile_nextdns" pattern="[A-Za-z0-9]+" required></div>
                    <div><label class="form-label">API key</label><input type="password" class="form-control" name="api_key" required autocomplete="off" placeholder="Nhap API key NextDNS"></div>
                    <div><label class="form-label">Trang thai</label><select class="form-select" name="trang_thai"><option value="1" selected>Hoat dong</option><option value="0">Tam tat</option></select></div>
                    <div><label class="form-label">Ghi chu</label><textarea class="form-control" rows="3" name="ghi_chu"></textarea></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Huy</button>
                    <button type="submit" class="btn btn-primary"><span class="nextdns-btn-icon"><i class="ti ti-plus"></i><span>Them profile</span></span></button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal modal-blur fade" id="nextdns-modal-sua-profile" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="post" action="/nextdns/cap-nhat-profile/0" class="modal-content" id="nextdns-form-sua-profile">
                <input type="hidden" name="_csrf" value="<?= bao_mat_chuoi(csrf_tao()) ?>">
                <input type="hidden" name="id" value="">
                <div class="modal-header">
                    <h5 class="modal-title">Cap nhat profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Dong"></button>
                </div>
                <div class="modal-body d-flex flex-column gap-3">
                    <div><label class="form-label">Ten profile</label><input type="text" class="form-control" name="ten_profile" maxlength="150" required></div>
                    <div><label class="form-label">Ma profile NextDNS</label><input type="text" class="form-control" name="ma_profile_nextdns" pattern="[A-Za-z0-9]+" required></div>
                    <div><label class="form-label">API key</label><input type="password" class="form-control" name="api_key" autocomplete="off" placeholder="De trong neu khong doi API key"><div class="form-hint">De trong neu khong doi API key.</div></div>
                    <div><label class="form-label">Trang thai</label><select class="form-select" name="trang_thai"><option value="1">Hoat dong</option><option value="0">Tam tat</option></select></div>
                    <div><label class="form-label">Ghi chu</label><textarea class="form-control" rows="3" name="ghi_chu"></textarea></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Huy</button>
                    <button type="submit" class="btn btn-primary"><span class="nextdns-btn-icon"><i class="ti ti-edit"></i><span>Luu thay doi</span></span></button>
                </div>
            </form>
        </div>
    </div>
    <?php foreach ($danhSachProfile as $profile): ?>
        <div class="modal modal-blur fade" id="nextdns-modal-xoa-profile-<?= (int) $profile['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form method="post" action="/nextdns/xoa-profile/<?= (int) $profile['id'] ?>" class="modal-content">
                    <input type="hidden" name="_csrf" value="<?= bao_mat_chuoi(csrf_tao()) ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Xoa profile</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Dong"></button>
                    </div>
                    <div class="modal-body d-flex flex-column gap-3">
                        <p class="mb-0">Ban co chac chan muon xoa profile nay khong?</p>
                        <div class="nextdns-modal-summary">
                            <div class="fw-semibold"><?= bao_mat_chuoi((string) $profile['ten_profile']) ?></div>
                            <div class="text-secondary small">Ma profile: <code class="nextdns-code-inline"><?= bao_mat_chuoi((string) $profile['ma_profile_nextdns']) ?></code></div>
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modalSuaProfile = document.getElementById('nextdns-modal-sua-profile');
            if (!modalSuaProfile) {
                return;
            }

            modalSuaProfile.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                if (!button) {
                    return;
                }

                var id = button.getAttribute('data-id') || '';
                var tenProfile = button.getAttribute('data-ten-profile') || '';
                var maProfile = button.getAttribute('data-ma-profile') || '';
                var trangThai = button.getAttribute('data-trang-thai') || '1';
                var ghiChu = button.getAttribute('data-ghi-chu') || '';
                var form = document.getElementById('nextdns-form-sua-profile');

                if (form) {
                    form.setAttribute('action', '/nextdns/cap-nhat-profile/' + id);
                }

                modalSuaProfile.querySelector('[name="id"]').value = id;
                modalSuaProfile.querySelector('[name="ten_profile"]').value = tenProfile;
                modalSuaProfile.querySelector('[name="ma_profile_nextdns"]').value = maProfile;
                modalSuaProfile.querySelector('[name="trang_thai"]').value = trangThai;
                modalSuaProfile.querySelector('[name="ghi_chu"]').value = ghiChu;

                var apiKeyInput = modalSuaProfile.querySelector('[name="api_key"]');
                if (apiKeyInput) {
                    apiKeyInput.value = '';
                    apiKeyInput.placeholder = 'De trong neu khong doi API key';
                }
            });
        });
    </script>
<?php endif; ?>
