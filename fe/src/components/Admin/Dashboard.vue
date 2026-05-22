<template>
	<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
		<div class="col">
			<div class="card radius-10 border-start border-0 border-3 border-info">
				<div class="card-body">
					<div class="d-flex align-items-center">
						<div>
							<p class="mb-0 text-secondary">Tổng Thành Viên</p>
							<h4 class="my-1 text-info">{{ metrics.total_members }}</h4>
							<p class="mb-0 font-13">Cập nhật thời gian thực</p>
						</div>
						<div class="widgets-icons-2 rounded-circle bg-gradient-scooter text-white ms-auto">
							<i class='bx bxs-group'></i>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col">
			<div class="card radius-10 border-start border-0 border-3 border-danger">
				<div class="card-body">
					<div class="d-flex align-items-center">
						<div>
							<p class="mb-0 text-secondary">Cây Gia Phả</p>
							<h4 class="my-1 text-danger">{{ metrics.total_trees }}</h4>
							<p class="mb-0 font-13">Các chi nhánh dòng họ</p>
						</div>
						<div class="widgets-icons-2 rounded-circle bg-gradient-bloody text-white ms-auto">
							<i class='bx bxs-share-alt'></i>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col">
			<div class="card radius-10 border-start border-0 border-3 border-success">
				<div class="card-body">
					<div class="d-flex align-items-center">
						<div>
							<p class="mb-0 text-secondary">Đóng Góp Di Sản</p>
							<h4 class="my-1 text-success">{{ formatCurrency(metrics.total_contributions) }}</h4>
							<p class="mb-0 font-13">Quỹ dòng họ tự nguyện</p>
						</div>
						<div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto">
							<i class='bx bxs-wallet'></i>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col">
			<div class="card radius-10 border-start border-0 border-3 border-warning">
				<div class="card-body">
					<div class="d-flex align-items-center">
						<div>
							<p class="mb-0 text-secondary">Đóng Góp Quỹ</p>
							<h4 class="my-1 text-warning">{{ metrics.total_requests }}</h4>
							<p class="mb-0 font-13">Lượt đóng góp đã nhận</p>
						</div>
						<div class="widgets-icons-2 rounded-circle bg-gradient-blooker text-white ms-auto">
							<i class='bx bxs-cloud-upload'></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--end row-->

	<!-- Chart Row -->
	<div class="row">
		<div class="col-12 col-lg-8">
			<div class="card radius-10">
				<div class="card-header">
					<div class="d-flex align-items-center">
						<div>
							<h6 class="mb-0">Thống kê tăng trưởng Thành viên & Gia phả</h6>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="chart-container-1">
						<Line id="growth-chart" :options="lineOptions" :data="lineData" />
					</div>
				</div>
			</div>
		</div>
		<div class="col-12 col-lg-4">
			<div class="card radius-10">
				<div class="card-header">
					<div class="d-flex align-items-center">
						<div>
							<h6 class="mb-0">Tỷ lệ Trạng thái phê duyệt</h6>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="chart-container-2">
						<Doughnut id="status-chart" :options="doughnutOptions" :data="doughnutData" />
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end chart row -->

	<div class="row">
		<div class="col-12 col-lg-8">
			<div class="card radius-10">
				<div class="card-header">
					<div class="d-flex align-items-center">
						<div>
							<h6 class="mb-0">Cập nhật Gia Phả Gần Đây</h6>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table align-middle mb-0">
							<thead class="table-light">
								<tr>
									<th>Thành viên</th>
									<th>Dòng họ</th>
									<th>Ngày cập nhật</th>
									<th>Trạng thái</th>
									<th>Hành động</th>
								</tr>
							</thead>
							<tbody>
								<tr v-if="recentUpdates.length === 0">
									<td colspan="5" class="text-center text-secondary py-3">Chưa có dữ liệu thành viên mới nào được ghi nhận.</td>
								</tr>
								<tr v-for="item in recentUpdates" :key="item.id">
									<td>{{ item.ho_ten }}</td>
									<td>{{ item.dong_ho }}</td>
									<td>{{ item.ngay_cap_nhat }}</td>
									<td>
										<span :class="['badge shadow-sm w-100', getStatusBadgeClass(item.trang_thai)]">
											{{ item.trang_thai || 'Còn sống' }}
										</span>
									</td>
									<td>
										<div class="d-flex order-actions">
											<span class="text-info"><i class='bx bx-check-shield'></i> Đã đồng bộ</span>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="col-12 col-lg-4">
			<div class="card radius-10">
				<div class="card-header">
					<div class="d-flex align-items-center">
						<div>
							<h6 class="mb-0">Hoạt động Hệ thống</h6>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="traffic-source-container">
						<div class="traffic-source d-flex align-items-center mb-3">
							<div class="widgets-icons-2 bg-light-primary text-primary rounded-circle"><i class='bx bxs-user-plus'></i></div>
							<div class="ms-3">
								<p class="mb-0 text-secondary">Người dùng hoạt động</p>
								<h6 class="mb-0">{{ systemActivities.new_users_today }} tài khoản hoạt động/mới</h6>
							</div>
						</div>
						<div class="traffic-source d-flex align-items-center mb-3">
							<div class="widgets-icons-2 bg-light-success text-success rounded-circle"><i class='bx bxs-image-add'></i></div>
							<div class="ms-3">
								<p class="mb-0 text-secondary">Tư liệu & hình ảnh</p>
								<h6 class="mb-0">{{ systemActivities.images_uploaded }} hình ảnh được lưu giữ</h6>
							</div>
						</div>
						<div class="traffic-source d-flex align-items-center mb-0">
							<div class="widgets-icons-2 bg-light-warning text-warning rounded-circle"><i class='bx bxs-bell'></i></div>
							<div class="ms-3">
								<p class="mb-0 text-secondary">Thông báo</p>
								<h6 class="mb-0">{{ systemActivities.notifications }} thông báo hệ thống</h6>
							</div>
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
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  ArcElement
} from 'chart.js';
import { Line, Doughnut } from 'vue-chartjs';

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  ArcElement
);

const metrics = ref({
  total_members: 0,
  total_trees: 0,
  total_contributions: 0,
  total_requests: 0,
});

const recentUpdates = ref([]);
const systemActivities = ref({
  new_users_today: 0,
  images_uploaded: 0,
  notifications: 0,
});

const lineData = ref({
  labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6'],
  datasets: [
    {
      label: 'Thành viên mới',
      backgroundColor: 'rgba(23, 162, 184, 0.2)',
      borderColor: '#17a2b8',
      data: [0, 0, 0, 0, 0, 0],
      tension: 0.4,
      fill: true,
    },
    {
      label: 'Cây gia phả',
      backgroundColor: 'rgba(220, 53, 69, 0.2)',
      borderColor: '#dc3545',
      data: [0, 0, 0, 0, 0, 0],
      tension: 0.4,
      fill: true,
    }
  ]
});

const lineOptions = ref({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { position: 'bottom' }
  }
});

const doughnutData = ref({
  labels: ['Đã duyệt', 'Chờ duyệt', 'Từ chối'],
  datasets: [
    {
      backgroundColor: ['#17a2b8', '#ffc107', '#dc3545'],
      data: [0, 0, 0]
    }
  ]
});

const doughnutOptions = ref({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { position: 'bottom' }
  }
});

const formatCurrency = (value) => {
  if (value === undefined || value === null) return '0đ';
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
};

const getStatusBadgeClass = (status) => {
  if (status === 'Còn sống' || status === 'Đã duyệt' || status === 'Hoạt động') {
    return 'bg-gradient-quepal text-white';
  }
  if (status === 'Chờ duyệt') {
    return 'bg-gradient-blooker text-white';
  }
  return 'bg-gradient-bloody text-white';
};

const fetchDashboardData = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/admin/dashboard');
    if (res.data.status) {
      const dbData = res.data.data;
      metrics.value = dbData.metrics;
      recentUpdates.value = dbData.recent_updates;
      systemActivities.value = dbData.system_activities;

      // Update growth chart
      lineData.value = {
        labels: dbData.growth_chart.labels,
        datasets: [
          {
            label: 'Thành viên mới',
            backgroundColor: 'rgba(23, 162, 184, 0.2)',
            borderColor: '#17a2b8',
            data: dbData.growth_chart.members,
            tension: 0.4,
            fill: true,
          },
          {
            label: 'Cây gia phả',
            backgroundColor: 'rgba(220, 53, 69, 0.2)',
            borderColor: '#dc3545',
            data: dbData.growth_chart.trees,
            tension: 0.4,
            fill: true,
          }
        ]
      };

      // Update doughnut chart
      doughnutData.value = {
        labels: ['Đã duyệt', 'Chờ duyệt', 'Từ chối'],
        datasets: [
          {
            backgroundColor: ['#17a2b8', '#ffc107', '#dc3545'],
            data: [
              dbData.approval_chart.approved,
              dbData.approval_chart.pending,
              dbData.approval_chart.rejected
            ]
          }
        ]
      };
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
.chart-container-1 {
  position: relative;
  height: 280px;
}
.chart-container-2 {
  position: relative;
  height: 280px;
}
</style>
