<?php
$duLieu = $duLieu ?? [];
$loiTheoTruong = $loiTheoTruong ?? [];
$cheDo = $cheDo ?? 'them';
$laSua = $cheDo === 'sua';
$action = $laSua ? '/nhan-su/nhan-vien/cap-nhat/' . (int)($duLieu['id'] ?? 0) : '/nhan-su/nhan-vien/luu';
?>
<form method="post" action="<?= bao_mat_chuoi($action) ?>">
  <input type="hidden" name="_csrf" value="<?= bao_mat_chuoi(csrf_tao()) ?>">
  <div class="page-header d-print-none mb-3">
    <div class="row align-items-center">
      <div class="col">
        <h2 class="page-title"><?= $laSua ? 'Cap nhat nhan vien' : 'Them nhan vien' ?></h2>
        <div class="text-secondary">Form lien mach, toi uu thao tac cho nguoi dung.</div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <h3 class="card-title mb-3">Thong tin ca nhan</h3>
      <div class="row g-3 mb-4">
        <div class="col-md-4">
        <label class="form-label">Ma nhan vien</label>
        <input class="form-control<?= isset($loiTheoTruong['employee_code']) ? ' is-invalid' : '' ?>" name="employee_code" value="<?= bao_mat_chuoi((string)($duLieu['employee_code'] ?? '')) ?>">
        </div>
        <div class="col-md-8">
        <label class="form-label">Ho ten</label>
        <input class="form-control<?= isset($loiTheoTruong['full_name']) ? ' is-invalid' : '' ?>" name="full_name" value="<?= bao_mat_chuoi((string)($duLieu['full_name'] ?? '')) ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Ngay sinh</label>
          <input type="date" class="form-control<?= isset($loiTheoTruong['birth_date']) ? ' is-invalid' : '' ?>" name="birth_date" value="<?= bao_mat_chuoi((string)($duLieu['birth_date'] ?? '')) ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Gioi tinh</label>
          <select class="form-select<?= isset($loiTheoTruong['gender']) ? ' is-invalid' : '' ?>" name="gender">
            <option value="">Chon</option>
            <option value="nam" <?= (($duLieu['gender'] ?? '') === 'nam') ? 'selected' : '' ?>>Nam</option>
            <option value="nu" <?= (($duLieu['gender'] ?? '') === 'nu') ? 'selected' : '' ?>>Nu</option>
            <option value="khac" <?= (($duLieu['gender'] ?? '') === 'khac') ? 'selected' : '' ?>>Khac</option>
          </select>
        </div>
      </div>

      <h3 class="card-title mb-3">Thong tin lien he</h3>
      <div class="row g-3 mb-4">
        <div class="col-md-4">
        <label class="form-label">Email</label>
        <input type="email" class="form-control<?= isset($loiTheoTruong['email']) ? ' is-invalid' : '' ?>" name="email" value="<?= bao_mat_chuoi((string)($duLieu['email'] ?? '')) ?>">
        </div>
        <div class="col-md-4">
        <label class="form-label">So dien thoai</label>
        <input class="form-control<?= isset($loiTheoTruong['phone']) ? ' is-invalid' : '' ?>" name="phone" value="<?= bao_mat_chuoi((string)($duLieu['phone'] ?? '')) ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Dia chi</label>
          <input class="form-control" name="address" value="<?= bao_mat_chuoi((string)($duLieu['address'] ?? '')) ?>">
        </div>
      </div>

      <h3 class="card-title mb-3">Thong tin cong viec</h3>
      <div class="row g-3 mb-4">
        <div class="col-md-4">
        <label class="form-label">Phong ban</label>
        <select class="form-select" name="department_id">
          <option value="">Chon phong ban</option>
          <?php foreach (($phongBan ?? []) as $pb): ?>
            <option value="<?= (int)$pb['id'] ?>" <?= ((int)($duLieu['department_id'] ?? 0) === (int)$pb['id']) ? 'selected' : '' ?>><?= bao_mat_chuoi((string)$pb['name']) ?></option>
          <?php endforeach; ?>
        </select>
        </div>
        <div class="col-md-4">
        <label class="form-label">Chuc vu</label>
        <select class="form-select" name="position_id">
          <option value="">Chon chuc vu</option>
          <?php foreach (($chucVu ?? []) as $cv): ?>
            <option value="<?= (int)$cv['id'] ?>" <?= ((int)($duLieu['position_id'] ?? 0) === (int)$cv['id']) ? 'selected' : '' ?>><?= bao_mat_chuoi((string)$cv['name']) ?></option>
          <?php endforeach; ?>
        </select>
        </div>
        <div class="col-md-4">
        <label class="form-label">Lien ket tai khoan</label>
        <select class="form-select<?= isset($loiTheoTruong['account_id']) ? ' is-invalid' : '' ?>" name="account_id">
          <option value="0">Chua lien ket</option>
          <?php if (!empty($duLieu['account_id']) && !empty($duLieu['account_email'])): ?>
            <option value="<?= (int)$duLieu['account_id'] ?>" selected><?= bao_mat_chuoi((string)$duLieu['account_email']) ?> (dang lien ket)</option>
          <?php endif; ?>
          <?php foreach (($taiKhoan ?? []) as $tk): ?>
            <option value="<?= (int)$tk['id'] ?>" <?= ((int)($duLieu['account_id'] ?? 0) === (int)$tk['id']) ? 'selected' : '' ?>><?= bao_mat_chuoi((string)$tk['email']) ?> - <?= bao_mat_chuoi((string)$tk['ho_ten']) ?></option>
          <?php endforeach; ?>
        </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
          <?php if ($laSua && empty($duLieu['account_id']) && co_quyen('tai_khoan.them')): ?>
          <form method="post" action="/nhan-su/nhan-vien/tao-tai-khoan/<?= (int)$duLieu['id'] ?>">
            <input type="hidden" name="_csrf" value="<?= bao_mat_chuoi(csrf_tao()) ?>">
            <button class="btn btn-outline-primary" type="submit">Tao tai khoan nhanh</button>
          </form>
          <?php endif; ?>
        </div>
        <div class="col-md-4">
        <label class="form-label">Ngay vao lam</label>
        <input type="date" class="form-control<?= isset($loiTheoTruong['join_date']) ? ' is-invalid' : '' ?>" name="join_date" value="<?= bao_mat_chuoi((string)($duLieu['join_date'] ?? '')) ?>">
        </div>
      </div>

      <h3 class="card-title mb-3">Trang thai va ghi chu</h3>
      <div class="row g-3">
        <div class="col-md-4">
        <label class="form-label">Trang thai</label>
        <select class="form-select<?= isset($loiTheoTruong['status']) ? ' is-invalid' : '' ?>" name="status">
          <option value="dang_lam" <?= (($duLieu['status'] ?? 'dang_lam') === 'dang_lam') ? 'selected' : '' ?>>Dang lam</option>
          <option value="tam_nghi" <?= (($duLieu['status'] ?? '') === 'tam_nghi') ? 'selected' : '' ?>>Tam nghi</option>
          <option value="nghi_viec" <?= (($duLieu['status'] ?? '') === 'nghi_viec') ? 'selected' : '' ?>>Nghi viec</option>
        </select>
        </div>
        <div class="col-12">
        <label class="form-label">Ghi chu</label>
        <textarea class="form-control" name="note" rows="3"><?= bao_mat_chuoi((string)($duLieu['note'] ?? '')) ?></textarea>
        </div>
      </div>
    </div>
    <div class="card-footer d-flex gap-2">
      <button class="btn btn-primary" type="submit">Luu</button>
      <a class="btn btn-outline-secondary" href="/nhan-su/nhan-vien">Huy</a>
    </div>
  </div>
</form>
