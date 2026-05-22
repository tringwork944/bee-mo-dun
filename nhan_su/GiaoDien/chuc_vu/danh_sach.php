<?php
require_once __DIR__ . '/../thanh_phan/ui.php';
$action = co_quyen('chuc_vu.them') ? '<a class="btn btn-primary" href="/nhan-su/chuc-vu/them">Them chuc vu</a>' : '';
ns_ui_page_header('Chuc vu', 'Danh muc chuc vu', $action);
?>
<div class="card">
  <div class="table-responsive">
    <table class="table table-vcenter table-sm card-table">
      <thead><tr><th>Ma</th><th>Ten</th><th>Trang thai</th><th class="text-end">Thao tac</th></tr></thead>
      <tbody>
      <?php if (empty($ds)) { ns_ui_empty_state('Chua co chuc vu', '', 4); } ?>
      <?php foreach (($ds ?? []) as $item): ?>
        <tr>
          <td><?= bao_mat_chuoi((string)$item['code']) ?></td>
          <td><?= bao_mat_chuoi((string)$item['name']) ?></td>
          <td><?= (($item['status'] ?? 'hoat_dong') === 'hoat_dong') ? ns_ui_status_badge('Hoat dong', 'success') : ns_ui_status_badge('Khong hoat dong', 'secondary') ?></td>
          <td class="text-end">
            <?php
            $items = [];
            if (co_quyen('chuc_vu.sua')) $items[] = ['label' => 'Sua', 'href' => '/nhan-su/chuc-vu/sua/' . (int)$item['id']];
            if (co_quyen('chuc_vu.xoa')) $items[] = ['html' => '<form method="post" action="/nhan-su/chuc-vu/xoa/' . (int)$item['id'] . '"><input type="hidden" name="_csrf" value="' . bao_mat_chuoi(csrf_tao()) . '"><button type="submit" class="dropdown-item text-danger" onclick="return confirm(\'Xoa chuc vu nay?\')">Xoa</button></form>'];
            echo ns_ui_action_dropdown($items);
            ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
