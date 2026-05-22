<div class="page-header d-print-none mb-3">
  <div class="row align-items-center">
    <div class="col"><h2 class="page-title">Chi tiet cham cong</h2></div>
    <div class="col-auto d-flex gap-2"><a class="btn btn-outline-primary" href="/nhan-su/cham-cong/sua/<?= (int)$duLieu['id'] ?>">Chinh sua</a><a class="btn btn-outline-secondary" href="/nhan-su/cham-cong">Quay lai</a></div>
  </div>
</div>
<div class="card">
  <div class="card-body row g-2">
    <div class="col-md-4 text-secondary">Nhan vien</div><div class="col-md-8"><?= bao_mat_chuoi((string)$duLieu['full_name']) ?> (<?= bao_mat_chuoi((string)$duLieu['employee_code']) ?>)</div>
    <div class="col-md-4 text-secondary">Ngay</div><div class="col-md-8"><?= bao_mat_chuoi((string)$duLieu['attendance_date']) ?></div>
    <div class="col-md-4 text-secondary">Phong ban</div><div class="col-md-8"><?= bao_mat_chuoi((string)($duLieu['department_name'] ?? '')) ?></div>
    <div class="col-md-4 text-secondary">Gio vao</div><div class="col-md-8"><?= bao_mat_chuoi((string)($duLieu['check_in'] ?? '')) ?></div>
    <div class="col-md-4 text-secondary">Gio ra</div><div class="col-md-8"><?= bao_mat_chuoi((string)($duLieu['check_out'] ?? '')) ?></div>
    <div class="col-md-4 text-secondary">Tong gio</div><div class="col-md-8"><?= (float)$duLieu['total_hours'] ?></div>
    <div class="col-md-4 text-secondary">Trang thai</div><div class="col-md-8"><span class="badge bg-blue-lt text-blue"><?= bao_mat_chuoi((string)$duLieu['status']) ?></span></div>
    <div class="col-md-4 text-secondary">Ghi chu</div><div class="col-md-8"><?= bao_mat_chuoi((string)($duLieu['note'] ?? '')) ?></div>
  </div>
</div>
