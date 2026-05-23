document.addEventListener('DOMContentLoaded', function () {
  if (!window.nextdnsDuLieuBieuDo) {
    hienThiTrangThaiRong();
    return;
  }

  renderTatCaBieuDo(window.nextdnsDuLieuBieuDo);
});

function hienThiTrangThaiRong() {
  [
    'nextdns_bieu_do_xu_huong',
    'nextdns_bieu_do_bo_loc_chan',
    'nextdns_bieu_do_top_domain',
    'nextdns_bieu_do_domain_bi_chan'
  ].forEach(function (id) {
    hienThongBao(id, 'Chua co du lieu de hien thi.');
  });
}

function renderTatCaBieuDo(duLieu) {
  if (duLieu.debug && window.console) {
    console.log('NextDNS chart data', duLieu);
  }

  var xuHuong = duLieu.xu_huong_7_ngay || {};
  renderChart(
    'nextdns_bieu_do_xu_huong',
    { labels: xuHuong.labels || [], values: xuHuong.tong || [] },
    function () {
      return {
        chart: { type: 'area', height: 320, toolbar: { show: false } },
        stroke: { curve: 'smooth', width: 3 },
        dataLabels: { enabled: false },
        series: [
          { name: 'Tong truy van', data: xuHuong.tong || [] },
          { name: 'Bi chan', data: xuHuong.bi_chan || [] }
        ],
        colors: ['#206bc4', '#d63939'],
        xaxis: { categories: xuHuong.labels || [] },
        legend: { position: 'top' },
        fill: { opacity: 0.18 }
      };
    }
  );

  var boLocChan = duLieu.bo_loc_chan || {};
  renderChart(
    'nextdns_bieu_do_bo_loc_chan',
    { labels: boLocChan.labels || [], values: boLocChan.values || [] },
    function () {
      return {
        chart: { type: 'donut', height: 320, toolbar: { show: false } },
        series: boLocChan.values || [],
        labels: boLocChan.labels || [],
        colors: ['#206bc4', '#2fb344', '#f59f00', '#d63939', '#6f42c1', '#0ca678'],
        legend: { position: 'bottom' }
      };
    },
    'Chua co du lieu de hien thi.'
  );

  var topDomain = duLieu.top_domain || {};
  renderChart(
    'nextdns_bieu_do_top_domain',
    { labels: topDomain.labels || [], values: topDomain.values || [] },
    function () {
      return {
        chart: { type: 'bar', height: 280, toolbar: { show: false } },
        plotOptions: { bar: { borderRadius: 6, horizontal: true } },
        series: [{ name: 'Truy van', data: topDomain.values || [] }],
        xaxis: { categories: topDomain.labels || [] },
        colors: ['#206bc4'],
        dataLabels: { enabled: false }
      };
    }
  );

  var topBiChan = duLieu.top_domain_bi_chan || {};
  renderChart(
    'nextdns_bieu_do_domain_bi_chan',
    { labels: topBiChan.labels || [], values: topBiChan.values || [] },
    function () {
      return {
        chart: { type: 'bar', height: 280, toolbar: { show: false } },
        plotOptions: { bar: { borderRadius: 6, horizontal: true } },
        series: [{ name: 'Bi chan', data: topBiChan.values || [] }],
        xaxis: { categories: topBiChan.labels || [] },
        colors: ['#d63939'],
        dataLabels: { enabled: false }
      };
    }
  );
}

function layPhanTu(id) {
  return document.getElementById(id);
}

function hienThongBao(id, noiDung) {
  var el = layPhanTu(id);
  if (!el) return;
  el.innerHTML = '<div class="nextdns-bieu-do-thong-bao">' + noiDung + '</div>';
}

function coDuLieu(labels, values) {
  return Array.isArray(labels) && Array.isArray(values) && labels.length > 0 && values.length > 0;
}

function renderChart(id, duLieuKiemTra, taoTuyChon, thongBaoRong) {
  var el = layPhanTu(id);
  if (!el) return;

  if (typeof ApexCharts === 'undefined') {
    hienThongBao(id, 'ApexCharts chua duoc tai. Vui long kiem tra asset cuc bo.');
    return;
  }

  if (!duLieuKiemTra || !coDuLieu(duLieuKiemTra.labels, duLieuKiemTra.values)) {
    hienThongBao(id, thongBaoRong || 'Chua co du lieu de hien thi.');
    return;
  }

  try {
    el.innerHTML = '';
    var chart = new ApexCharts(el, taoTuyChon());
    chart.render();
  } catch (error) {
    console.error('NextDNS chart render error', id, error);
    hienThongBao(id, 'Khong the hien thi bieu do');
  }
}
