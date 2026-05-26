<template>
  <div class="admin-dashboard-premium">
    <h4 class="fw-bold mb-4 page-title">Báo cáo Tổng quan</h4>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 mb-4">
      <div class="col">
        <div class="card h-100 border-0 shadow-sm flat-card">
          <div class="card-body">
            <p class="mb-2 text-muted font-13 fw-medium">Tổng Thành Viên</p>
            <h3 class="my-0 d-flex align-items-center gap-2 text-purple fw-bold">
              <i class='bx bx-group'></i> {{ metrics.total_members }}
            </h3>
            <p class="mb-0 mt-3 font-12 text-success fw-medium"><i class='bx bx-up-arrow-alt'></i> 12.5% so với tháng trước</p>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card h-100 border-0 shadow-sm flat-card">
          <div class="card-body">
            <p class="mb-2 text-muted font-13 fw-medium">Cây Gia Phả</p>
            <h3 class="my-0 d-flex align-items-center gap-2 text-blue fw-bold">
              <i class='bx bx-git-branch'></i> {{ metrics.total_trees }}
            </h3>
            <p class="mb-0 mt-3 font-12 text-success fw-medium"><i class='bx bx-up-arrow-alt'></i> 8.2% so với tháng trước</p>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card h-100 border-0 shadow-sm flat-card">
          <div class="card-body">
            <p class="mb-2 text-muted font-13 fw-medium">Đóng Góp Di Sản</p>
            <h3 class="my-0 d-flex align-items-center gap-2 text-red fw-bold">
              <i class='bx bx-wallet'></i> {{ formatCurrencyShort(metrics.total_contributions) }}
            </h3>
            <p class="mb-0 mt-3 font-12 text-muted fw-medium">Tăng 5 khoản đóng góp mới</p>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card h-100 border-0 shadow-sm flat-card">
          <div class="card-body">
            <p class="mb-2 text-muted font-13 fw-medium">Lượt Đóng Góp</p>
            <h3 class="my-0 d-flex align-items-center gap-2 text-green fw-bold">
              <i class='bx bx-user'></i> {{ metrics.total_requests }}
            </h3>
            <p class="mb-0 mt-3 font-12 text-success fw-medium"><i class='bx bx-up-arrow-alt'></i> 15% tỷ lệ tăng trưởng</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Middle Chart Row -->
    <div class="row g-3 mb-4">
      <div class="col-12 col-lg-8">
        <div class="card h-100 border-0 shadow-sm flat-card">
          <div class="card-header bg-transparent border-0 pt-3 pb-0">
            <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
              <i class='bx bx-line-chart text-muted'></i> Biểu đồ tăng trưởng hệ thống
            </h6>
          </div>
          <div class="card-body">
            <div class="chart-container-1" style="height: 300px;">
              <Line id="growth-chart" :options="lineOptions" :data="lineData" />
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-4">
        <div class="card h-100 border-0 shadow-sm flat-card">
          <div class="card-header bg-transparent border-0 pt-3 pb-0">
            <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
              <i class='bx bx-pie-chart-alt-2 text-muted'></i> Tỷ lệ trạng thái duyệt
            </h6>
          </div>
          <div class="card-body d-flex flex-column align-items-center justify-content-center">
            <div class="chart-container-2 w-100" style="height: 200px;">
              <Doughnut id="status-chart" :options="doughnutOptions" :data="doughnutData" />
            </div>
            <!-- Custom Legend -->
            <div class="custom-legend mt-4 w-100 text-center">
               <div class="d-flex flex-wrap justify-content-center gap-3">
                 <div class="legend-item"><span class="legend-color" style="background-color: #06b6d4;"></span>Đã duyệt</div>
                 <div class="legend-item"><span class="legend-color" style="background-color: #f59e0b;"></span>Chờ duyệt</div>
                 <div class="legend-item"><span class="legend-color" style="background-color: #ef4444;"></span>Từ chối</div>
               </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Row (Bar Chart) -->
    <div class="row g-3">
      <div class="col-12">
        <div class="card border-0 shadow-sm flat-card">
          <div class="card-header bg-transparent border-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
              <i class='bx bx-bar-chart-alt-2 text-muted'></i> Thống kê Hoạt động (Gia phả - Thành viên)
            </h6>
            <select class="form-select form-select-sm w-auto shadow-none bg-light border-0" style="border-radius: 6px;">
              <option>Xem Tuần</option>
              <option>Xem Tháng</option>
            </select>
          </div>
          <div class="card-body">
             <div class="d-flex justify-content-end gap-3 mb-2 font-12 fw-medium text-muted">
               <div><span class="legend-color d-inline-block me-1 rounded-1" style="width:10px;height:10px;background-color:#22c55e;"></span>Gia phả mới</div>
               <div><span class="legend-color d-inline-block me-1 rounded-1" style="width:10px;height:10px;background-color:#3b82f6;"></span>Thành viên (Bán ra)</div>
             </div>
             <div class="chart-container-3" style="height: 250px;">
                <Bar id="activity-chart" :options="barOptions" :data="barData" />
             </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import {
  Chart as ChartJS, Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale, ArcElement, BarElement, Filler
} from 'chart.js';
import { Line, Doughnut, Bar } from 'vue-chartjs';

ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale, ArcElement, BarElement, Filler);

const metrics = ref({
  total_members: 0,
  total_trees: 0,
  total_contributions: 0,
  total_requests: 0,
});

const recentUpdates = ref([]);
const systemActivities = ref({});

const lineData = ref({
  labels: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'],
  datasets: [
    {
      label: 'Tăng trưởng',
      backgroundColor: 'rgba(59, 130, 246, 0.1)', // Light blue
      borderColor: '#3b82f6', // Blue
      data: [0, 0, 0, 0, 0, 0, 0],
      tension: 0.4,
      fill: true,
      pointBackgroundColor: '#fff',
      pointBorderColor: '#3b82f6',
      pointBorderWidth: 2,
      pointRadius: 3
    }
  ]
});

const lineOptions = ref({
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false } },
  scales: {
    y: { beginAtZero: true, grid: { borderDash: [5, 5], color: 'rgba(0,0,0,0.05)' }, border: { display: false } },
    x: { grid: { display: false }, border: { display: false } }
  }
});

const doughnutData = ref({
  labels: ['Đã duyệt', 'Chờ duyệt', 'Từ chối'],
  datasets: [
    {
      backgroundColor: ['#06b6d4', '#f59e0b', '#ef4444'],
      borderWidth: 3,
      borderColor: '#fff',
      data: [1, 1, 1], // Placeholder, loaded later
      cutout: '70%'
    }
  ]
});

const doughnutOptions = ref({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: { enabled: true }
  }
});

const barData = ref({
  labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
  datasets: [
    {
      label: 'Gia phả mới',
      backgroundColor: '#22c55e', // Green
      data: [120, 200, 150, 80, 210, 180, 240],
      borderRadius: 4,
      barPercentage: 0.4,
      categoryPercentage: 0.5
    },
    {
      label: 'Thành viên',
      backgroundColor: '#3b82f6', // Blue
      data: [10, 25, 15, 5, 20, 30, 15],
      borderRadius: 4,
      barPercentage: 0.4,
      categoryPercentage: 0.5
    }
  ]
});

const barOptions = ref({
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false } },
  scales: {
    y: { beginAtZero: true, grid: { borderDash: [5, 5], color: 'rgba(0,0,0,0.05)' }, border: { display: false } },
    x: { grid: { display: false }, border: { display: false } }
  }
});

const formatCurrencyShort = (value) => {
  if (value === undefined || value === null) return '0 đ';
  if (value >= 1000000) return (value / 1000000).toFixed(1) + 'Tr đ';
  return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
};

const fetchDashboardData = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/admin/dashboard', {
        headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` }
    });
    if (res.data.status) {
      const dbData = res.data.data;
      metrics.value = dbData.metrics;

      if (dbData.growth_chart && dbData.growth_chart.labels) {
          lineData.value = {
            labels: dbData.growth_chart.labels,
            datasets: [
              {
                label: 'Thành viên mới',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderColor: '#3b82f6',
                data: dbData.growth_chart.members,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#3b82f6',
                pointBorderWidth: 2,
                pointRadius: 3
              }
            ]
          };
      }

      if (dbData.approval_chart) {
          doughnutData.value = {
            labels: ['Đã duyệt', 'Chờ duyệt', 'Từ chối'],
            datasets: [
              {
                backgroundColor: ['#06b6d4', '#f59e0b', '#ef4444'],
                borderWidth: 3,
                borderColor: '#fff',
                data: [
                  dbData.approval_chart.approved || 0,
                  dbData.approval_chart.pending || 0,
                  dbData.approval_chart.rejected || 0
                ],
                cutout: '70%'
              }
            ]
          };
      }
    }
  } catch (error) {
    console.error('Error fetching dashboard statistics:', error);
  }
};

onMounted(() => {
  fetchDashboardData();
});
</script>

<style scoped>
.admin-dashboard-premium {
  padding: 5px;
  color: var(--text-main, #333);
}

.page-title {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  color: var(--text-main, #111827);
  letter-spacing: -0.5px;
}

.flat-card {
  border-radius: 12px !important;
  background-color: var(--card-bg, #ffffff);
  border: 1px solid var(--border-color, rgba(0,0,0,0.05)) !important;
}

.text-purple { color: #8b5cf6 !important; }
.text-blue { color: #3b82f6 !important; }
.text-red { color: #ef4444 !important; }
.text-green { color: #10b981 !important; }

.font-12 { font-size: 12px; }
.font-13 { font-size: 13px; }

.legend-item {
  font-size: 12px;
  color: var(--text-sub, #6b7280);
  display: flex;
  align-items: center;
  gap: 6px;
  font-weight: 500;
}
.legend-color {
  width: 12px;
  height: 12px;
  border-radius: 3px;
  display: inline-block;
}

[data-theme="dark"] .admin-dashboard-premium {
  color: var(--text-main);
}
[data-theme="dark"] .flat-card {
  background-color: var(--card-bg);
  border-color: var(--border-color) !important;
}
[data-theme="dark"] .page-title {
  color: var(--text-main);
}
[data-theme="dark"] .bg-light {
  background-color: var(--input-bg) !important;
  color: var(--text-main) !important;
}
</style>
