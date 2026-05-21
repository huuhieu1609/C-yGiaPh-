<template>
	<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
		<div class="col">
			<div class="card radius-10 border-start border-0 border-3 border-info">
				<div class="card-body">
					<div class="d-flex align-items-center">
						<div>
							<p class="mb-0 text-secondary">Tổng Thành Viên</p>
							<h4 class="my-1 text-info">2,564</h4>
							<p class="mb-0 font-13">+2.5% so với tháng trước</p>
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
							<h4 class="my-1 text-danger">425</h4>
							<p class="mb-0 font-13">+5.4% so với tháng trước</p>
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
							<h4 class="my-1 text-success">€34,652</h4>
							<p class="mb-0 font-13">-4.5% so với tháng trước</p>
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
							<p class="mb-0 text-secondary">Yêu Cầu Số Hóa</p>
							<h4 class="my-1 text-warning">132</h4>
							<p class="mb-0 font-13">+8.4% so với tháng trước</p>
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
						<div class="dropdown ms-auto">
							<a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown"><i
									class='bx bx-dots-horizontal-rounded font-22 text-option'></i>
							</a>
							<ul class="dropdown-menu">
								<li><a class="dropdown-item" href="javascript:;">Xem tất cả</a>
								</li>
								<li><a class="dropdown-item" href="javascript:;">Xuất báo cáo</a>
								</li>
							</ul>
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
								<tr>
									<td>Nguyễn Văn A</td>
									<td>Nguyễn Tộc</td>
									<td>12/05/2026</td>
									<td><span class="badge bg-gradient-quepal text-white shadow-sm w-100">Đã duyệt</span></td>
									<td><div class="d-flex order-actions">
										<a href="javascript:;" class=""><i class='bx bxs-edit'></i></a>
										<a href="javascript:;" class="ms-3"><i class='bx bxs-trash'></i></a>
									</div></td>
								</tr>
								<tr>
									<td>Trần Thị B</td>
									<td>Trần Gia</td>
									<td>11/05/2026</td>
									<td><span class="badge bg-gradient-blooker text-white shadow-sm w-100">Chờ duyệt</span></td>
									<td><div class="d-flex order-actions">
										<a href="javascript:;" class=""><i class='bx bxs-edit'></i></a>
										<a href="javascript:;" class="ms-3"><i class='bx bxs-trash'></i></a>
									</div></td>
								</tr>
								<tr>
									<td>Lê Văn C</td>
									<td>Lê Tộc</td>
									<td>10/05/2026</td>
									<td><span class="badge bg-gradient-bloody text-white shadow-sm w-100">Từ chối</span></td>
									<td><div class="d-flex order-actions">
										<a href="javascript:;" class=""><i class='bx bxs-edit'></i></a>
										<a href="javascript:;" class="ms-3"><i class='bx bxs-trash'></i></a>
									</div></td>
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
								<p class="mb-0 text-secondary">Người dùng mới</p>
								<h6 class="mb-0">15 tài khoản đăng ký mới hôm nay</h6>
							</div>
						</div>
						<div class="traffic-source d-flex align-items-center mb-3">
							<div class="widgets-icons-2 bg-light-success text-success rounded-circle"><i class='bx bxs-image-add'></i></div>
							<div class="ms-3">
								<p class="mb-0 text-secondary">Ảnh di sản</p>
								<h6 class="mb-0">45 tệp tin ảnh mới được tải lên</h6>
							</div>
						</div>
						<div class="traffic-source d-flex align-items-center mb-0">
							<div class="widgets-icons-2 bg-light-warning text-warning rounded-circle"><i class='bx bxs-bell'></i></div>
							<div class="ms-3">
								<p class="mb-0 text-secondary">Thông báo</p>
								<h6 class="mb-0">3 yêu cầu khôi phục mật khẩu</h6>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref } from 'vue';
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

const lineData = ref({
  labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6'],
  datasets: [
    {
      label: 'Thành viên mới',
      backgroundColor: 'rgba(23, 162, 184, 0.2)',
      borderColor: '#17a2b8',
      data: [40, 60, 45, 80, 55, 90],
      tension: 0.4,
      fill: true,
    },
    {
      label: 'Cây gia phả',
      backgroundColor: 'rgba(220, 53, 69, 0.2)',
      borderColor: '#dc3545',
      data: [15, 20, 30, 25, 45, 60],
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
      data: [75, 15, 10]
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
/* Bạn có thể thêm CSS tùy chỉnh ở đây nếu cần */
</style>
