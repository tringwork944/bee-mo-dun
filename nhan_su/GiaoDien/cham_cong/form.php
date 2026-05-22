<?php
$duLieu = $duLieu ?? [];
$loiTheoTruong = $loiTheoTruong ?? [];
$laSua = ($cheDo ?? 'them') === 'sua';
$action = $laSua ? '/nhan-su/cham-cong/cap-nhat/' . (int)($duLieu['id'] ?? 0) : '/nhan-su/cham-cong/luu';
$quayLai = (string)($quayLai ?? '/nhan-su/cham-cong');
?>
<div class="page-header d-print-none mb-3">
  <div class="row align-items-center">
    <div class="col"><h2 class="page-title"><?= $laSua ? 'Cap nhat cham cong' : 'Cham cong thu cong' ?></h2></div>
  </div>
</div>
<form method="post" action="<?= bao_mat_chuoi($action) ?>">
  <input type="hidden" name="_csrf" value="<?= bao_mat_chuoi(csrf_tao()) ?>">
  <input type="hidden" name="quay_lai" value="<?= bao_mat_chuoi($quayLai) ?>">
  <div class="card">
    <div class="card-body row g-3">
      <div class="col-md-6"><label class="form-label">Nhan vien</label><select class="form-select<?= isset($loiTheoTruong['employee_id']) ? ' is-invalid' : '' ?>" name="employee_id"><option value="0">Chon nhan vien</option><?php foreach(($nhanVien ?? []) as $nv): ?><option value="<?= (int)$nv['id'] ?>" <?= (int)($duLieu['employee_id'] ?? 0)===(int)$nv['id']?'selected':'' ?>><?= bao_mat_chuoi((string)$nv['full_name']) ?> (<?= bao_mat_chuoi((string)$nv['employee_code']) ?>)</option><?php endforeach; ?></select></div>
      <div class="col-md-3"><label class="form-label">Ngay</label><input type="date" class="form-control<?= isset($loiTheoTruong['attendance_date']) ? ' is-invalid' : '' ?>" name="attendance_date" value="<?= bao_mat_chuoi((string)($duLieu['attendance_date'] ?? '')) ?>"></div>
      <div class="col-md-3"><label class="form-label">Trang thai</label><select class="form-select<?= isset($loiTheoTruong['status']) ? ' is-invalid' : '' ?>" name="status"><option value="di_lam" <?= (($duLieu['status'] ?? '')==='di_lam')?'selected':'' ?>>Co mat</option><option value="di_muon" <?= (($duLieu['status'] ?? '')==='di_muon')?'selected':'' ?>>Di muon</option><option value="nghi_phep" <?= (($duLieu['status'] ?? '')==='nghi_phep')?'selected':'' ?>>Nghi phep</option><option value="vang" <?= (($duLieu['status'] ?? '')==='vang')?'selected':'' ?>>Vang</option><option value="tang_ca" <?= (($duLieu['status'] ?? '')==='tang_ca')?'selected':'' ?>>Tang ca</option><option value="lam_o_nha" <?= (($duLieu['status'] ?? '')==='lam_o_nha')?'selected':'' ?>>Lam o nha</option><option value="nua_ngay" <?= (($duLieu['status'] ?? '')==='nua_ngay')?'selected':'' ?>>Nua ngay</option><option value="ngay_le" <?= (($duLieu['status'] ?? '')==='ngay_le')?'selected':'' ?>>Ngay le</option><option value="ngay_nghi" <?= (($duLieu['status'] ?? '')==='ngay_nghi')?'selected':'' ?>>Ngay nghi</option></select></div>
      <div class="col-md-3"><label class="form-label">Gio vao</label><input type="time" class="form-control" name="check_in" value="<?= bao_mat_chuoi((string)($duLieu['check_in'] ?? '')) ?>"></div>
      <div class="col-md-3"><label class="form-label">Gio ra</label><input type="time" class="form-control" name="check_out" value="<?= bao_mat_chuoi((string)($duLieu['check_out'] ?? '')) ?>"></div>
      <div class="col-12"><label class="form-label">Ghi chu</label><textarea class="form-control" name="note" rows="3"><?= bao_mat_chuoi((string)($duLieu['note'] ?? '')) ?></textarea></div>
    </div>
    <div class="card-footer d-flex gap-2"><button class="btn btn-primary" type="submit">Luu</button><a class="btn btn-outline-secondary" href="<?= bao_mat_chuoi($quayLai) ?>">Huy</a></div>
  </div>
</form>
