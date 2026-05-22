<?php
$duLieu = $duLieu ?? [];
$loiTheoTruong = $loiTheoTruong ?? [];
$laSua = ($cheDo ?? 'them') === 'sua';
$action = $laSua ? '/nhan-su/phong-ban/cap-nhat/' . (int)($duLieu['id'] ?? 0) : '/nhan-su/phong-ban/luu';
?>
<form method="post" action="<?= bao_mat_chuoi($action) ?>">
  <input type="hidden" name="_csrf" value="<?= bao_mat_chuoi(csrf_tao()) ?>">
  <div class="card">
    <div class="card-header"><h3 class="card-title"><?= $laSua ? 'Cap nhat phong ban' : 'Them phong ban' ?></h3></div>
    <div class="card-body row g-3">
      <div class="col-md-4">
        <label class="form-label">Ma phong ban</label>
        <input class="form-control<?= isset($loiTheoTruong['code']) ? ' is-invalid' : '' ?>" name="code" value="<?= bao_mat_chuoi((string)($duLieu['code'] ?? '')) ?>">
      </div>
      <div class="col-md-8">
        <label class="form-label">Ten phong ban</label>
        <input class="form-control<?= isset($loiTheoTruong['name']) ? ' is-invalid' : '' ?>" name="name" value="<?= bao_mat_chuoi((string)($duLieu['name'] ?? '')) ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Trang thai</label>
        <select class="form-select<?= isset($loiTheoTruong['status']) ? ' is-invalid' : '' ?>" name="status">
          <option value="hoat_dong" <?= (($duLieu['status'] ?? 'hoat_dong') === 'hoat_dong') ? 'selected' : '' ?>>Hoat dong</option>
          <option value="khong_hoat_dong" <?= (($duLieu['status'] ?? 'hoat_dong') === 'khong_hoat_dong') ? 'selected' : '' ?>>Khong hoat dong</option>
        </select>
      </div>
      <div class="col-12">
        <label class="form-label">Mo ta</label>
        <textarea class="form-control" rows="3" name="description"><?= bao_mat_chuoi((string)($duLieu['description'] ?? '')) ?></textarea>
      </div>
    </div>
    <div class="card-footer d-flex gap-2">
      <button class="btn btn-primary" type="submit">Luu</button>
      <a class="btn btn-outline-secondary" href="/nhan-su/phong-ban">Huy</a>
    </div>
  </div>
</form>
