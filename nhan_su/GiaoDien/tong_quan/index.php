<?php
require_once __DIR__ . '/../thanh_phan/ui.php';
ns_ui_page_header('Tong quan', 'So lieu nhan su quan trong');
?>

<div id="dashboard-loading" class="row row-cards mb-3">
  <?php for($i=0;$i<6;$i++): ?><div class="col-sm-6 col-lg-4"><div class="card card-sm"><div class="card-body placeholder-glow"><span class="placeholder col-8"></span><span class="placeholder col-6"></span></div></div></div><?php endfor; ?>
</div>

<div id="dashboard-content" class="d-none">
  <div class="row row-cards mb-3" id="kpi-cards"></div>
  <div class="row row-cards">
    <div class="col-lg-6"><div class="card"><div class="card-header"><h3 class="card-title">Nhan su theo phong ban</h3></div><div class="card-body"><div id="chart-phong-ban" class="chart-lg"></div></div></div></div>
    <div class="col-lg-6"><div class="card"><div class="card-header"><h3 class="card-title">Trang thai nhan vien</h3></div><div class="card-body"><div id="chart-trang-thai" class="chart-lg"></div></div></div></div>
    <div class="col-lg-4"><div class="card"><div class="card-header"><h3 class="card-title">Nhan vien moi</h3></div><div class="list-group list-group-flush" id="widget-nhan-vien-moi"></div></div></div>
    <div class="col-lg-4"><div class="card"><div class="card-header"><h3 class="card-title">Cham cong bat thuong hom nay</h3></div><div class="list-group list-group-flush" id="widget-bat-thuong"></div></div></div>
    <div class="col-lg-4"><div class="card"><div class="card-header"><h3 class="card-title">Sinh nhat sap toi</h3></div><div class="list-group list-group-flush" id="widget-sinh-nhat"></div></div></div>
  </div>
</div>

<div id="dashboard-empty" class="d-none">
  <div class="empty"><div class="empty-title">Khong co du lieu dashboard</div></div>
</div>
