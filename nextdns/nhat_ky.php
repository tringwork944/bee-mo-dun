<div class="card">
    <div class="card-header"><h3 class="card-title">Nhat ky truy van NextDNS</h3></div>
    <div class="card-body border-bottom">
        <form method="get" class="row g-3 align-items-end">
            <input type="hidden" name="tab" value="nhat_ky">
            <input type="hidden" name="cau_hinh_id" value="<?= (int) ($boLoc['cau_hinh_id'] ?? 0) ?>">
            <input type="hidden" name="khoang_thoi_gian" value="<?= bao_mat_chuoi((string) ($boLoc['khoang_thoi_gian'] ?? '24h')) ?>">
            <input type="hidden" name="tu_ngay" value="<?= bao_mat_chuoi((string) ($boLoc['tu_ngay'] ?? '')) ?>">
            <input type="hidden" name="den_ngay" value="<?= bao_mat_chuoi((string) ($boLoc['den_ngay'] ?? '')) ?>">
            <input type="hidden" name="gioi_han" value="<?= (int) ($boLoc['gioi_han'] ?? 20) ?>">
            <div class="col-12 col-lg-5"><label class="form-label">Tim trong logs</label><input type="text" class="form-control" name="tu_khoa_nhat_ky" value="<?= bao_mat_chuoi((string) ($boLoc['tu_khoa_nhat_ky'] ?? '')) ?>" placeholder="Nhap domain hoac tu khoa"></div>
            <div class="col-6 col-lg-3"><label class="form-label">Trang thai</label><select class="form-select" name="trang_thai_nhat_ky"><?php foreach (['tat_ca' => 'Tat ca', 'blocked' => 'Bi chan', 'allowed' => 'Cho phep', 'default' => 'Mac dinh', 'error' => 'Loi'] as $giaTri => $nhan): ?><option value="<?= bao_mat_chuoi($giaTri) ?>" <?= ($boLoc['trang_thai_nhat_ky'] ?? 'tat_ca') === $giaTri ? 'selected' : '' ?>><?= bao_mat_chuoi($nhan) ?></option><?php endforeach; ?></select></div>
            <div class="col-6 col-lg-4"><button type="submit" class="btn btn-primary"><i class="ti ti-search me-1"></i>Loc nhat ky</button></div>
        </form>
    </div>
    <?php if (!empty($nhatKy['ok'])): ?>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead><tr><th>Domain</th><th>Trang thai</th><th>Ly do chan</th><th>Thiet bi</th><th>Thoi gian</th></tr></thead>
                <tbody>
                <?php foreach ((array) ($nhatKy['danh_sach'] ?? []) as $dong): ?>
                    <tr>
                        <td><div class="fw-semibold"><?= bao_mat_chuoi((string) ($dong['domain'] ?? '')) ?></div><?php if ((string) ($dong['root'] ?? '') !== '' && (string) ($dong['root'] ?? '') !== (string) ($dong['domain'] ?? '')): ?><div class="text-secondary small"><?= bao_mat_chuoi((string) ($dong['root'] ?? '')) ?></div><?php endif; ?></td>
                        <td><span class="badge <?= (($dong['trang_thai_api'] ?? '') === 'blocked') ? 'bg-red-lt text-red' : 'bg-green-lt text-green' ?>"><?= bao_mat_chuoi((string) ($dong['trang_thai_hien_thi'] ?? '')) ?></span></td>
                        <td><?= bao_mat_chuoi((string) (($dong['ly_do_chan'] ?? '') !== '' ? $dong['ly_do_chan'] : '-')) ?></td>
                        <td><?= bao_mat_chuoi((string) ($dong['thiet_bi'] ?? '')) ?></td>
                        <td><?= bao_mat_chuoi((string) ($dong['thoi_gian'] ?? '')) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($nhatKy['danh_sach'])): ?><tr><td colspan="5" class="text-secondary">Khong co nhat ky nao phu hop bo loc hien tai.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (!empty($nhatKy['cursor_tiep'])): ?><div class="card-footer"><a class="btn btn-outline-primary" href="/nextdns?<?= bao_mat_chuoi(http_build_query(['tab' => 'nhat_ky', 'cau_hinh_id' => (int) ($boLoc['cau_hinh_id'] ?? 0), 'khoang_thoi_gian' => (string) ($boLoc['khoang_thoi_gian'] ?? '24h'), 'tu_ngay' => (string) ($boLoc['tu_ngay'] ?? ''), 'den_ngay' => (string) ($boLoc['den_ngay'] ?? ''), 'gioi_han' => (int) ($boLoc['gioi_han'] ?? 20), 'trang_thai_nhat_ky' => (string) ($boLoc['trang_thai_nhat_ky'] ?? 'tat_ca'), 'tu_khoa_nhat_ky' => (string) ($boLoc['tu_khoa_nhat_ky'] ?? ''), 'cursor' => (string) ($nhatKy['cursor_tiep'] ?? '')])) ?>"><i class="ti ti-database me-1"></i>Tai them logs</a></div><?php endif; ?>
    <?php else: ?>
        <div class="card-body"><div class="alert alert-danger mb-0"><?= bao_mat_chuoi((string) ($nhatKy['thong_bao'] ?? 'Khong the lay nhat ky NextDNS.')) ?></div></div>
    <?php endif; ?>
</div>
