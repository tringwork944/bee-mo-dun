(function () {
  var khungBieuDo = document.getElementById('nextdns-bieu-do-trang-thai');
  var nutDuLieu = document.getElementById('nextdns-du-lieu-bieu-do');

  if (!khungBieuDo || !nutDuLieu || typeof ApexCharts === 'undefined') {
    return;
  }

  var duLieuBieuDo;
  try {
    duLieuBieuDo = JSON.parse(nutDuLieu.textContent || '{}');
  } catch (loiTai) {
    return;
  }

  var danhSachNhan = Array.isArray(duLieuBieuDo.nhan) ? duLieuBieuDo.nhan : [];
  var duLieuTrangThai = duLieuBieuDo.du_lieu || {};

  var bieuDo = new ApexCharts(khungBieuDo, {
    chart: {
      type: 'area',
      height: 320,
      toolbar: { show: false },
      zoom: { enabled: false }
    },
    stroke: {
      curve: 'smooth',
      width: 2
    },
    fill: {
      type: 'gradient',
      gradient: {
        shadeIntensity: 0.15,
        opacityFrom: 0.35,
        opacityTo: 0.05
      }
    },
    dataLabels: { enabled: false },
    xaxis: {
      categories: danhSachNhan
    },
    yaxis: {
      labels: {
        formatter: function (giaTri) {
          return Math.round(giaTri);
        }
      }
    },
    tooltip: {
      shared: true
    },
    series: [
      {
        name: 'Blocked',
        data: Array.isArray(duLieuTrangThai.blocked) ? duLieuTrangThai.blocked : []
      },
      {
        name: 'Allowed',
        data: Array.isArray(duLieuTrangThai.allowed) ? duLieuTrangThai.allowed : []
      },
      {
        name: 'Default',
        data: Array.isArray(duLieuTrangThai.default) ? duLieuTrangThai.default : []
      }
    ],
    colors: ['#d63939', '#2fb344', '#206bc4'],
    legend: {
      position: 'top'
    }
  });

  bieuDo.render();
})();
