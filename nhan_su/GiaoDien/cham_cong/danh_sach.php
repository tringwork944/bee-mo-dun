<?php
require_once __DIR__ . '/../thanh_phan/ui.php';
$boLoc = $boLoc ?? [];
$cheDoXem = ($cheDoXem ?? 'employee_calendar') === 'list' ? 'list' : 'employee_calendar';
$maTran = $duLieuMaTran ?? ['month' => (int)date('n'), 'year' => (int)date('Y'), 'days' => [], 'employees' => []];
$days = $maTran['days'] ?? [];
$employees = $maTran['employees'] ?? [];
$tongTrang = max(1, (int)ceil(((int)($tong ?? 0)) / max(1, (int)($moiTrang ?? 15))));
$trang = (int)($trang ?? 1);

$statusMeta = [
    'ngay_le' => ['label' => 'Ngay le', 'icon' => 'ti ti-star', 'class' => 'text-yellow'],
    'ngay_nghi' => ['label' => 'Ngay nghi', 'icon' => 'ti ti-calendar', 'class' => 'text-blue'],
    'co_mat' => ['label' => 'Co mat', 'icon' => 'ti ti-check', 'class' => 'text-success'],
    'lam_o_nha' => ['label' => 'Lam o nha', 'icon' => 'ti ti-home', 'class' => 'text-primary'],
    'nua_ngay' => ['label' => 'Nua ngay', 'icon' => 'ti ti-star-half', 'class' => 'text-orange'],
    'di_muon' => ['label' => 'Di muon', 'icon' => 'ti ti-clock', 'class' => 'text-warning'],
    'vang' => ['label' => 'Vang', 'icon' => 'ti ti-x', 'class' => 'text-danger'],
    'nghi_phep' => ['label' => 'Nghi phep', 'icon' => 'ti ti-plane', 'class' => 'text-azure'],
    'tang_ca' => ['label' => 'Tang ca', 'icon' => 'ti ti-bolt', 'class' => 'text-purple'],
    'chua_co_du_lieu' => ['label' => '-', 'icon' => 'ti ti-minus', 'class' => 'text-secondary'],
];

$queryBase = [
    'month' => (int)($maTran['month'] ?? date('n')),
    'year' => (int)($maTran['year'] ?? date('Y')),
    'department_id' => (int)($boLoc['department_id'] ?? 0),
    'status' => (string)($boLoc['status'] ?? ''),
    'keyword' => (string)($boLoc['keyword'] ?? ''),
    'tu_ngay' => (string)($boLoc['tu_ngay'] ?? ''),
    'den_ngay' => (string)($boLoc['den_ngay'] ?? ''),
];
$taoUrl = static function(array $add = []) use ($queryBase): string {
    return '/nhan-su/cham-cong?' . http_build_query(array_merge($queryBase, $add));
};
$thangTruoc = strtotime(sprintf('%04d-%02d-01 -1 month', $queryBase['year'], $queryBase['month']));
$thangSau = strtotime(sprintf('%04d-%02d-01 +1 month', $queryBase['year'], $queryBase['month']));
$quayLai = $taoUrl(['view' => 'employee_calendar']);
?>

<?php ns_ui_page_header('Cham cong', 'Lich cham cong theo nhan vien', co_quyen('cham_cong.them') ? '<a class="btn btn-primary" href="/nhan-su/cham-cong/them?quay_lai=' . urlencode($quayLai) . '">Cham cong thu cong</a>' : ''); ?>

<div class="card attendance-card">
  <div class="card-header py-2">
    <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
      <div class="attendance-legend">
        <span class="badge bg-secondary-lt text-secondary">☆ Ngay le</span>
        <span class="badge bg-secondary-lt text-secondary">📅 Ngay nghi</span>
        <span class="badge bg-secondary-lt text-secondary">✓ Co mat</span>
        <span class="badge bg-secondary-lt text-secondary">⌂ Lam o nha</span>
        <span class="badge bg-secondary-lt text-secondary">◐ Nua ngay</span>
        <span class="badge bg-secondary-lt text-secondary">◷ Di muon</span>
        <span class="badge bg-secondary-lt text-secondary">× Vang</span>
        <span class="badge bg-secondary-lt text-secondary">✈ Nghi phep</span>
      </div>
      <div class="btn-group btn-group-sm">
        <a class="btn <?= $cheDoXem === 'employee_calendar' ? 'btn-secondary' : 'btn-outline-secondary' ?>" href="<?= bao_mat_chuoi($taoUrl(['view' => 'employee_calendar'])) ?>">Lich nhan vien</a>
        <a class="btn <?= $cheDoXem === 'list' ? 'btn-secondary' : 'btn-outline-secondary' ?>" href="<?= bao_mat_chuoi($taoUrl(['view' => 'list'])) ?>">Danh sach</a>
      </div>
    </div>
  </div>
  <div class="card-body border-bottom py-2 attendance-toolbar">
    <form method="get" class="row g-2 align-items-end">
      <input type="hidden" name="view" value="<?= bao_mat_chuoi($cheDoXem) ?>">
      <?php if ($cheDoXem === 'employee_calendar'): ?>
      <div class="col-6 col-lg-1"><label class="form-label mb-1">Thang</label><input class="form-control form-control-sm" type="number" min="1" max="12" name="month" value="<?= (int)$queryBase['month'] ?>"></div>
      <div class="col-6 col-lg-1"><label class="form-label mb-1">Nam</label><input class="form-control form-control-sm" type="number" min="1970" max="2099" name="year" value="<?= (int)$queryBase['year'] ?>"></div>
      <?php endif; ?>
      <?php if ($cheDoXem === 'list'): ?>
      <div class="col-6 col-lg-2"><label class="form-label mb-1">Tu ngay</label><input class="form-control form-control-sm" type="date" name="tu_ngay" value="<?= bao_mat_chuoi((string)$queryBase['tu_ngay']) ?>"></div>
      <div class="col-6 col-lg-2"><label class="form-label mb-1">Den ngay</label><input class="form-control form-control-sm" type="date" name="den_ngay" value="<?= bao_mat_chuoi((string)$queryBase['den_ngay']) ?>"></div>
      <?php endif; ?>
      <div class="col-6 col-lg-2"><label class="form-label mb-1">Phong ban</label><select class="form-select form-select-sm" name="department_id"><option value="0">Tat ca</option><?php foreach(($phongBan ?? []) as $pb): ?><option value="<?= (int)$pb['id'] ?>" <?= (int)$queryBase['department_id']===(int)$pb['id']?'selected':'' ?>><?= bao_mat_chuoi((string)$pb['name']) ?></option><?php endforeach; ?></select></div>
      <div class="col-6 col-lg-2"><label class="form-label mb-1">Trang thai</label><select class="form-select form-select-sm" name="status"><option value="">Tat ca</option><?php foreach ($statusMeta as $k => $m): if ($k === 'chua_co_du_lieu') continue; ?><option value="<?= bao_mat_chuoi($k) ?>" <?= $queryBase['status'] === $k ? 'selected' : '' ?>><?= bao_mat_chuoi($m['label']) ?></option><?php endforeach; ?></select></div>
      <div class="col-12 col-lg-<?= $cheDoXem === 'employee_calendar' ? '4' : '2' ?>"><label class="form-label mb-1">Tim nhan vien</label><input class="form-control form-control-sm" name="keyword" value="<?= bao_mat_chuoi((string)$queryBase['keyword']) ?>" placeholder="Ten hoac ma"></div>
      <div class="col-12 col-lg-2 d-flex gap-2">
        <button class="btn btn-primary flex-fill btn-sm" type="submit">Loc</button>
        <a class="btn btn-outline-secondary flex-fill" href="/nhan-su/cham-cong?view=<?= bao_mat_chuoi($cheDoXem) ?>">Dat lai</a>
      </div>
    </form>
  </div>

  <?php if ($cheDoXem === 'employee_calendar'): ?>
  <div class="card-body py-2 border-bottom d-flex justify-content-end">
    <div class="btn-group btn-group-sm">
      <a class="btn btn-outline-secondary" href="<?= bao_mat_chuoi($taoUrl(['view' => 'employee_calendar', 'month' => (int)date('n', $thangTruoc), 'year' => (int)date('Y', $thangTruoc)])) ?>">Thang truoc</a>
      <a class="btn btn-outline-secondary" href="<?= bao_mat_chuoi($taoUrl(['view' => 'employee_calendar', 'month' => (int)date('n'), 'year' => (int)date('Y')])) ?>">Hom nay</a>
      <a class="btn btn-outline-secondary" href="<?= bao_mat_chuoi($taoUrl(['view' => 'employee_calendar', 'month' => (int)date('n', $thangSau), 'year' => (int)date('Y', $thangSau)])) ?>">Thang sau</a>
    </div>
  </div>
  <div class="attendance-matrix-wrap">
    <table class="table table-sm table-vcenter attendance-table m-0">
      <thead>
        <tr>
          <th rowspan="2" class="attendance-col-employee attendance-head-left">Nhan vien</th>
          <?php foreach ($days as $d):
            $weekendClass = ((string)$d['weekday'] === 'T7') ? 'weekend-saturday' : (((string)$d['weekday'] === 'CN') ? 'weekend-sunday' : '');
            $todayClass = !empty($d['is_today']) ? 'today-column' : '';
          ?><th class="attendance-day-col <?= $weekendClass ?> <?= $todayClass ?>"><?= (int)$d['day'] ?></th><?php endforeach; ?>
          <th rowspan="2" class="attendance-col-total attendance-head-right">Tong</th>
        </tr>
        <tr>
          <?php foreach ($days as $d):
            $weekendClass = ((string)$d['weekday'] === 'T7') ? 'weekend-saturday' : (((string)$d['weekday'] === 'CN') ? 'weekend-sunday' : '');
            $todayClass = !empty($d['is_today']) ? 'today-column' : '';
          ?><th class="small text-secondary attendance-day-col <?= $weekendClass ?> <?= $todayClass ?>"><?= bao_mat_chuoi((string)$d['weekday']) ?></th><?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
      <?php if (empty($employees)) { ns_ui_empty_state('Chua co nhan vien', 'Them nhan vien hoac thay doi bo loc.', count($days) + 2); } ?>
      <?php foreach ($employees as $nv):
        $initials = strtoupper(substr((string)$nv['ho_ten'], 0, 1));
      ?>
        <tr class="attendance-row">
          <td class="attendance-col-employee">
            <div class="d-flex align-items-center gap-2">
              <span class="employee-avatar"><?= bao_mat_chuoi($initials) ?></span>
              <div>
                <div><a href="/nhan-su/nhan-vien/<?= (int)$nv['id'] ?>" class="text-reset"><?= bao_mat_chuoi((string)$nv['ho_ten']) ?></a> <?php if (!empty($nv['is_current_user'])): ?><span class="badge bg-primary-lt text-primary">La ban</span><?php endif; ?></div>
                <div class="small text-secondary"><?= bao_mat_chuoi((string)($nv['chuc_vu'] ?: $nv['phong_ban'])) ?></div>
              </div>
            </div>
          </td>
          <?php foreach ($days as $d):
            $date = (string)$d['date'];
            $weekendClass = ((string)$d['weekday'] === 'T7') ? 'weekend-saturday' : (((string)$d['weekday'] === 'CN') ? 'weekend-sunday' : '');
            $todayClass = !empty($d['is_today']) ? 'today-column' : '';
            $item = $nv['days'][$date] ?? ['trang_thai' => 'chua_co_du_lieu', 'gio_vao' => null, 'gio_ra' => null, 'tong_gio' => 0, 'id' => null];
            $st = (string)($item['trang_thai'] ?? 'chua_co_du_lieu');
            $meta = $statusMeta[$st] ?? $statusMeta['chua_co_du_lieu'];
            $tooltip = sprintf('%s | %s | %s | %s - %s | %.2f gio', $nv['ho_ten'], $date, $meta['label'], (string)($item['gio_vao'] ?: '-'), (string)($item['gio_ra'] ?: '-'), (float)($item['tong_gio'] ?? 0));
            $link = '#';
            if (!empty($item['id']) && co_quyen('cham_cong.sua')) $link = '/nhan-su/cham-cong/sua/' . (int)$item['id'] . '?quay_lai=' . urlencode($quayLai);
            if (empty($item['id']) && co_quyen('cham_cong.them')) $link = '/nhan-su/cham-cong/them?employee_id=' . (int)$nv['id'] . '&attendance_date=' . urlencode($date) . '&quay_lai=' . urlencode($quayLai);
          ?>
          <td class="attendance-cell attendance-day-col <?= $weekendClass ?> <?= $todayClass ?>">
            <?php if ($link === '#'): ?>
              <span class="<?= $meta['class'] ?>" title="<?= bao_mat_chuoi($tooltip) ?>"><i class="<?= $meta['icon'] ?>"></i></span>
            <?php else: ?>
              <a href="<?= bao_mat_chuoi($link) ?>" class="<?= $meta['class'] ?>" title="<?= bao_mat_chuoi($tooltip) ?>"><i class="<?= $meta['icon'] ?>"></i></a>
            <?php endif; ?>
          </td>
          <?php endforeach; ?>
          <td class="attendance-col-total"><strong><?= rtrim(rtrim(number_format((float)$nv['tong_cong'], 1, '.', ''), '0'), '.') ?> / <?= (int)$nv['tong_ngay_lam_viec'] ?></strong></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <div class="table-responsive">
    <table class="table table-vcenter table-sm card-table">
      <thead><tr><th>Ngay</th><th>Nhan vien</th><th>Gio vao</th><th>Gio ra</th><th>Tong gio</th><th>Trang thai</th><th class="text-end"></th></tr></thead>
      <tbody>
      <?php if (empty($ds)) { ns_ui_empty_state('Khong co du lieu cham cong', '', 7); } ?>
      <?php foreach(($ds ?? []) as $r): $statusHien = ($r['status'] ?? '') === 'di_lam' ? 'co_mat' : (string)($r['status'] ?? ''); $meta=$statusMeta[$statusHien] ?? $statusMeta['chua_co_du_lieu']; $thu = (int)date('N', strtotime((string)$r['attendance_date'])); $weekendClass = $thu === 6 ? 'weekend-saturday' : ($thu === 7 ? 'weekend-sunday' : ''); ?>
        <tr>
          <td class="<?= $weekendClass ?>"><?= bao_mat_chuoi((string)$r['attendance_date']) ?></td>
          <td><?= bao_mat_chuoi((string)$r['full_name']) ?></td>
          <td><?= bao_mat_chuoi((string)($r['check_in'] ?? '')) ?></td>
          <td><?= bao_mat_chuoi((string)($r['check_out'] ?? '')) ?></td>
          <td><?= (float)$r['total_hours'] ?></td>
          <td><?= ns_ui_status_badge((string)$meta['label'], str_replace('text-', '', (string)$meta['class'])) ?></td>
          <td class="text-end">
            <a class="btn btn-sm" href="/nhan-su/cham-cong/<?= (int)$r['id'] ?>">Chi tiet</a>
            <?php if (co_quyen('cham_cong.sua')): ?><a class="btn btn-sm" href="/nhan-su/cham-cong/sua/<?= (int)$r['id'] ?>?quay_lai=<?= urlencode($taoUrl(['view' => 'list'])) ?>">Sua</a><?php endif; ?>
            <?php if (co_quyen('cham_cong.xoa')): ?><form class="d-inline" method="post" action="/nhan-su/cham-cong/xoa/<?= (int)$r['id'] ?>"><input type="hidden" name="_csrf" value="<?= bao_mat_chuoi(csrf_tao()) ?>"><button class="btn btn-sm btn-danger" onclick="return confirm('Xoa ban ghi nay?')">Xoa</button></form><?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php if (($tong ?? 0) > 0 && $tongTrang > 1): ?>
  <div class="card-footer"><div class="btn-list"><?php for($i=1;$i<=$tongTrang;$i++): ?><a class="btn btn-sm <?= $i===$trang?'btn-primary':'btn-outline-secondary' ?>" href="<?= bao_mat_chuoi($taoUrl(['view' => 'list', 'trang' => $i])) ?>"><?= $i ?></a><?php endfor; ?></div></div>
  <?php endif; ?>
  <?php endif; ?>
</div>
