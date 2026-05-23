<?php $ketQua = (array) ($tim_kiem_domain ?? []); $banGhiGanNhat = is_array($ketQua['ban_ghi_gan_nhat'] ?? null) ? $ketQua['ban_ghi_gan_nhat'] : null; $trangThaiHienThi = (string) ($ketQua['trang_thai_hien_thi'] ?? 'khong_tim_thay'); ?>
<div class="row g-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Tim kiem domain trong logs NextDNS</h3></div>
            <div class="card-body">
                <form method="get" class="row g-3 align-items-end">
                    <input type="hidden" name="tab" value="tim_kiem_domain">
                    <input type="hidden" name="cau_hinh_id" value="<?= (int) ($boLoc['cau_hinh_id'] ?? 0) ?>">
                    <input type="hidden" name="khoang_thoi_gian" value="<?= bao_mat_chuoi((string) ($boLoc['khoang_thoi_gian'] ?? '24h')) ?>">
                    <input type="hidden" name="tu_ngay" value="<?= bao_mat_chuoi((string) ($boLoc['tu_ngay'] ?? '')) ?>">
                    <input type="hidden" name="den_ngay" value="<?= bao_mat_chuoi((string) ($boLoc['den_ngay'] ?? '')) ?>">
                    <input type="hidden" name="gioi_han" value="<?= (int) ($boLoc['gioi_han'] ?? 20) ?>">
                    <div class="col-12 col-lg-8"><label class="form-label">Domain can kiem tra</label><input type="text" class="form-control" name="domain" value="<?= bao_mat_chuoi((string) ($boLoc['domain'] ?? '')) ?>" placeholder="Vi du: ads.example.com"></div>
                    <div class="col-12 col-lg-4"><button type="submit" class="btn btn-primary"><i class="ti ti-search me-1"></i>Kiem tra</button></div>
                </form>
            </div>
        </div>
    </div>
    <?php if (($ketQua['ok'] ?? null) === false): ?>
        <div class="col-12"><div class="alert alert-danger mb-0"><?= bao_mat_chuoi((string) ($ketQua['thong_bao'] ?? 'Khong the tim kiem domain.')) ?></div></div>
    <?php elseif (($ketQua['ok'] ?? null) === true): ?>
        <div class="col-12"><div class="card"><div class="card-body"><div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center"><div><div class="text-secondary small">Ket qua tim kiem</div><h3 class="mb-1"><?= bao_mat_chuoi((string) ($ketQua['domain'] ?? '')) ?></h3><div class="text-secondary"><?= $banGhiGanNhat !== null ? 'Lan truy van gan nhat: ' . bao_mat_chuoi((string) ($banGhiGanNhat['thoi_gian'] ?? '')) : 'Khong tim thay ban ghi phu hop trong khoang thoi gian da chon.' ?></div></div><div><?php if ($trangThaiHienThi === 'bi_chan'): ?><span class="badge bg-red-lt text-red fs-5">Bi chan</span><?php elseif ($trangThaiHienThi === 'cho_phep'): ?><span class="badge bg-green-lt text-green fs-5">Cho phep</span><?php else: ?><span class="badge bg-secondary-lt text-secondary fs-5">Khong tim thay</span><?php endif; ?></div></div></div></div></div>
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Lich su phu hop</h3></div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead><tr><th>Domain</th><th>Trang thai</th><th>Ly do chan</th><th>Thiet bi</th><th>Thoi gian truy van gan nhat</th></tr></thead>
                        <tbody>
                        <?php foreach ((array) ($ketQua['danh_sach'] ?? []) as $dong): ?>
                            <tr>
                                <td><?= bao_mat_chuoi((string) ($dong['domain'] ?? '')) ?></td>
                                <td><span class="badge <?= (($dong['trang_thai_api'] ?? '') === 'blocked') ? 'bg-red-lt text-red' : 'bg-green-lt text-green' ?>"><?= bao_mat_chuoi((string) ($dong['trang_thai_hien_thi'] ?? '')) ?></span></td>
                                <td><?= bao_mat_chuoi((string) (($dong['ly_do_chan'] ?? '') !== '' ? $dong['ly_do_chan'] : '-')) ?></td>
                                <td><?= bao_mat_chuoi((string) ($dong['thiet_bi'] ?? '')) ?></td>
                                <td><?= bao_mat_chuoi((string) ($dong['thoi_gian'] ?? '')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($ketQua['danh_sach'])): ?><tr><td colspan="5" class="text-secondary">Khong tim thay domain nay trong logs NextDNS.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
