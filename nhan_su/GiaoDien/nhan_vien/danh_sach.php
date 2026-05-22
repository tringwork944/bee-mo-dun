<?php
require_once __DIR__ . '/../thanh_phan/ui.php';
$tongTrang = max(1, (int)ceil(($tong ?? 0) / max(1, (int)($moiTrang ?? 10))));
$trangHienTai = (int)($trang ?? 1);
$boLoc = $boLoc ?? ['tu_khoa' => '', 'trang_thai' => '', 'department_id' => 0, 'position_id' => 0];
$thongKe = $thongKe ?? [];
$phongBan = $phongBan ?? [];
$chucVu = $chucVu ?? [];
$mapTrangThai = [
  'dang_lam' => ['label' => 'Dang lam', 'tone' => 'success'],
  'tam_nghi' => ['label' => 'Tam nghi', 'tone' => 'warning'],
  'nghi_viec' => ['label' => 'Nghi viec', 'tone' => 'secondary'],
];
?>
<?php ns_ui_page_header('Nhan vien', 'Danh sach nhan vien', co_quyen('nhan_vien.them') ? '<a class="btn btn-primary" href="/nhan-su/nhan-vien/them"><i class="ti ti-user-plus me-1"></i>Them nhan vien</a>' : ''); ?>

<div class="row row-cards mb-3">
  <div class="col-sm-6 col-lg-3"><?= ns_ui_stat_card('Tong NV', (string)(int)($thongKe['tong_nhan_vien'] ?? 0), 'dark') ?></div>
  <div class="col-sm-6 col-lg-3"><?= ns_ui_stat_card('Dang lam', (string)(int)($thongKe['dang_lam'] ?? 0), 'success') ?></div>
  <div class="col-sm-6 col-lg-3"><?= ns_ui_stat_card('Tam nghi', (string)(int)($thongKe['tam_nghi'] ?? 0), 'warning') ?></div>
  <div class="col-sm-6 col-lg-3"><?= ns_ui_stat_card('Nghi viec', (string)(int)($thongKe['nghi_viec'] ?? 0), 'secondary') ?></div>
</div>

<div class="card">
  <div class="card-body py-3 border-bottom">
    <form method="get" class="row g-2 align-items-end">
      <div class="col-12 col-lg-4">
        <label class="form-label mb-1">Tim kiem</label>
        <input class="form-control" name="tu_khoa" placeholder="Ma NV, ho ten, email..." value="<?= bao_mat_chuoi((string)$boLoc['tu_khoa']) ?>">
      </div>
      <div class="col-6 col-lg-2">
        <label class="form-label mb-1">Phong ban</label>
        <select class="form-select" name="department_id">
          <option value="0">Tat ca</option>
          <?php foreach ($phongBan as $pb): ?>
            <option value="<?= (int)$pb['id'] ?>" <?= (int)$boLoc['department_id'] === (int)$pb['id'] ? 'selected' : '' ?>><?= bao_mat_chuoi((string)$pb['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-6 col-lg-2">
        <label class="form-label mb-1">Chuc vu</label>
        <select class="form-select" name="position_id">
          <option value="0">Tat ca</option>
          <?php foreach ($chucVu as $cv): ?>
            <option value="<?= (int)$cv['id'] ?>" <?= (int)$boLoc['position_id'] === (int)$cv['id'] ? 'selected' : '' ?>><?= bao_mat_chuoi((string)$cv['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-6 col-lg-2">
        <label class="form-label mb-1">Trang thai</label>
        <select class="form-select" name="trang_thai">
          <option value="">Tat ca trang thai</option>
          <option value="dang_lam" <?= ($boLoc['trang_thai'] === 'dang_lam') ? 'selected' : '' ?>>Dang lam</option>
          <option value="tam_nghi" <?= ($boLoc['trang_thai'] === 'tam_nghi') ? 'selected' : '' ?>>Tam nghi</option>
          <option value="nghi_viec" <?= ($boLoc['trang_thai'] === 'nghi_viec') ? 'selected' : '' ?>>Nghi viec</option>
        </select>
      </div>
      <div class="col-6 col-lg-2 d-flex gap-2">
        <button class="btn btn-outline-secondary flex-fill" type="submit">Loc</button>
        <a class="btn btn-outline-secondary flex-fill" href="/nhan-su/nhan-vien">Dat lai</a>
      </div>
    </form>
  </div>
  <div class="table-responsive">
    <table class="table table-vcenter table-sm card-table">
      <thead>
        <tr><th>Ma NV</th><th>Ho ten</th><th>Phong ban</th><th>Chuc vu</th><th>Trang thai</th><th>Lien ket tai khoan</th><th class="text-end"></th></tr>
      </thead>
      <tbody>
      <?php if (empty($ds)) { ns_ui_empty_state('Chua co nhan vien', 'Them nhan vien moi de bat dau.', 7); } ?>
      <?php foreach (($ds ?? []) as $item): ?>
        <tr>
          <td><?= bao_mat_chuoi((string)$item['employee_code']) ?></td>
          <td><?= bao_mat_chuoi((string)$item['full_name']) ?></td>
          <td><?= bao_mat_chuoi((string)($item['department_name'] ?? '')) ?></td>
          <td><?= bao_mat_chuoi((string)($item['position_name'] ?? '')) ?></td>
          <?php $tt = $mapTrangThai[(string)$item['status']] ?? ['label' => (string)$item['status'], 'tone' => 'secondary']; ?>
          <td><?= ns_ui_status_badge((string)$tt['label'], (string)$tt['tone']) ?></td>
          <td><?= !empty($item['account_email']) ? ns_ui_status_badge('Da lien ket', 'success') : ns_ui_status_badge('Chua lien ket', 'secondary') ?></td>
          <td class="text-end">
            <?php
            $items = [['label' => 'Chi tiet', 'href' => '/nhan-su/nhan-vien/' . (int)$item['id']]];
            if (co_quyen('nhan_vien.sua')) $items[] = ['label' => 'Chinh sua', 'href' => '/nhan-su/nhan-vien/sua/' . (int)$item['id']];
            if (co_quyen('nhan_vien.xoa')) $items[] = ['html' => '<form method="post" action="/nhan-su/nhan-vien/xoa/' . (int)$item['id'] . '"><input type="hidden" name="_csrf" value="' . bao_mat_chuoi(csrf_tao()) . '"><button type="submit" class="dropdown-item text-danger" onclick="return confirm(\'Xoa nhan vien nay?\')">Xoa</button></form>'];
            echo ns_ui_action_dropdown($items);
            ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="card-footer d-flex justify-content-between align-items-center">
    <div>Tong: <?= (int)($tong ?? 0) ?></div>
    <div class="btn-list">
      <?php for ($i = 1; $i <= $tongTrang; $i++): ?>
        <a class="btn btn-sm <?= $i === $trangHienTai ? 'btn-primary' : 'btn-outline-secondary' ?>" href="/nhan-su/nhan-vien?tu_khoa=<?= urlencode((string)$boLoc['tu_khoa']) ?>&trang_thai=<?= urlencode((string)$boLoc['trang_thai']) ?>&department_id=<?= (int)$boLoc['department_id'] ?>&position_id=<?= (int)$boLoc['position_id'] ?>&trang=<?= $i ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  </div>
</div>
