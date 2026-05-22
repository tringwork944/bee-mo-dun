<div class="page-header d-print-none mb-3">
  <div class="row align-items-center">
    <div class="col">
      <h2 class="page-title">Chi tiet nhan vien</h2>
      <div class="text-secondary">Thong tin tong hop theo tung nhom nghiep vu.</div>
    </div>
    <div class="col-auto d-flex gap-2">
      <a class="btn btn-outline-primary" href="/nhan-su/nhan-vien/sua/<?= (int)$nv['id'] ?>">Chinh sua</a>
      <a class="btn btn-outline-secondary" href="/nhan-su/nhan-vien">Quay lai</a>
    </div>
  </div>
</div>

<div class="row row-cards">
  <div class="col-12 col-lg-6">
    <div class="card">
      <div class="card-header"><h3 class="card-title">Thong tin ca nhan</h3></div>
      <div class="card-body row g-2">
        <div class="col-5 text-secondary">Ma nhan vien</div><div class="col-7"><?= bao_mat_chuoi((string)$nv['employee_code']) ?></div>
        <div class="col-5 text-secondary">Ho ten</div><div class="col-7"><?= bao_mat_chuoi((string)$nv['full_name']) ?></div>
        <div class="col-5 text-secondary">Ngay sinh</div><div class="col-7"><?= bao_mat_chuoi((string)($nv['birth_date'] ?? '')) ?></div>
        <div class="col-5 text-secondary">Gioi tinh</div><div class="col-7"><?= bao_mat_chuoi((string)($nv['gender'] ?? '')) ?></div>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-6">
    <div class="card">
      <div class="card-header"><h3 class="card-title">Thong tin lien he va cong viec</h3></div>
      <div class="card-body row g-2">
        <div class="col-5 text-secondary">Email</div><div class="col-7"><?= bao_mat_chuoi((string)$nv['email']) ?></div>
        <div class="col-5 text-secondary">So dien thoai</div><div class="col-7"><?= bao_mat_chuoi((string)($nv['phone'] ?? '')) ?></div>
        <div class="col-5 text-secondary">Dia chi</div><div class="col-7"><?= bao_mat_chuoi((string)($nv['address'] ?? '')) ?></div>
        <div class="col-5 text-secondary">Phong ban</div><div class="col-7"><?= bao_mat_chuoi((string)($nv['department_name'] ?? '')) ?></div>
        <div class="col-5 text-secondary">Chuc vu</div><div class="col-7"><?= bao_mat_chuoi((string)($nv['position_name'] ?? '')) ?></div>
        <div class="col-5 text-secondary">Tai khoan</div><div class="col-7"><?php if (!empty($nv['account_email'])): ?><span class="badge bg-success-lt text-success">Da lien ket</span> <?= bao_mat_chuoi((string)$nv['account_email']) ?><?php else: ?><span class="badge bg-secondary-lt text-secondary">Chua lien ket</span><?php endif; ?></div>
        <div class="col-5 text-secondary">Ngay vao lam</div><div class="col-7"><?= bao_mat_chuoi((string)($nv['join_date'] ?? '')) ?></div>
        <div class="col-5 text-secondary">Trang thai</div><div class="col-7"><span class="badge bg-blue-lt text-blue"><?= bao_mat_chuoi((string)$nv['status']) ?></span></div>
        <div class="col-5 text-secondary">Ghi chu</div><div class="col-7"><?= bao_mat_chuoi((string)($nv['note'] ?? '')) ?></div>
      </div>
    </div>
  </div>
</div>
