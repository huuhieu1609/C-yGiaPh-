<template>
    <div class="container-fluid px-0">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 welcome-banner-card overflow-hidden" 
                     :style="partnerProfile && (partnerProfile.ten_goi === 'Gói Đối Tác' || !partnerProfile.ten_goi) ? 'background: linear-gradient(135deg, #f43f5e 0%, #f97316 100%); color: #ffffff;' : 'background: var(--card-bg); color: var(--text-main); border: 1px solid var(--border-color) !important;'">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="fw-bold mb-2 banner-title">
                                    {{ partnerProfile && (partnerProfile.ten_goi === 'Gói Đối Tác' || !partnerProfile.ten_goi) ? 'Chào mừng bạn đến với Hệ Thống Gia Phả!' : 'Quản Lý Thông Tin Dòng Họ' }}
                                </h4>
                                <p class="mb-0 opacity-75 banner-sub font-medium">Tên Dòng Họ sẽ được sử dụng làm tên Chi Nhánh chính của bạn trên hệ thống.</p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <div class="input-group search-pill-group">
                                    <input type="text" class="form-control premium-input border-1" v-model="newTenGoi" placeholder="Tên Dòng Họ (VD: Nguyễn Đức)">
                                    <button class="btn btn-action-orange fw-bold px-4 ms-2" @click="updatePartnerInfo">
                                        {{ partnerProfile && (partnerProfile.ten_goi === 'Gói Đối Tác' || !partnerProfile.ten_goi) ? 'Cập Nhật Ngay' : 'Đổi Tên' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card widget-kpi-card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-muted kpi-label fw-semibold">Thành Viên Dòng Họ</p>
                                <h3 class="my-1 fw-bold theme-text-main">{{ stats.total_members || 0 }}</h3>
                                <p class="mb-0 text-secondary kpi-sub">Tổng số thành viên đã thêm</p>
                            </div>
                            <div class="kpi-icon-box bg-light-orange text-orange ms-auto rounded-circle d-flex align-items-center justify-content-center">
                                <i class='bx bxs-group fs-3'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card widget-kpi-card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-muted kpi-label fw-semibold">Tính năng thông báo các vấn đề về hệ thống</p>
                                <h3 class="my-1 fw-bold theme-text-main">{{ stats.system_alerts_count || 0 }}</h3>
                                <p class="mb-0 text-secondary kpi-sub">Số thông báo hệ thống chưa xử lý</p>
                            </div>
                            <div class="kpi-icon-box bg-light-pink text-pink ms-auto rounded-circle d-flex align-items-center justify-content-center">
                                <i class='bx bxs-bell fs-3'></i>
                            </div>
                        </div>
                        <div class="mt-3 text-end">
                            <router-link to="/doi-tac/thong-bao" class="btn btn-sm btn-outline-secondary">Xem thông báo</router-link>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card widget-kpi-card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-muted kpi-label fw-semibold">Sự Kiện Sắp Tới</p>
                                <h3 class="my-1 fw-bold theme-text-main">{{ stats.upcoming_events || 0 }}</h3>
                                <p class="mb-0 text-secondary kpi-sub">Sự kiện trong dòng họ</p>
                            </div>
                            <div class="kpi-icon-box bg-light-amber text-amber ms-auto rounded-circle d-flex align-items-center justify-content-center">
                                <i class='bx bxs-calendar-event fs-3'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12 col-lg-7">
                <div class="card genealogy-main-card border-0 shadow-sm overflow-hidden h-100">
                    <div class="card-header py-3 px-4 border-0 mt-2 bg-transparent">
                        <div class="d-flex align-items-center">
                            <div>
                                <h5 class="mb-0 fw-bold d-flex align-items-center gap-2 theme-text-main">
                                    <i class="bx bx-line-chart text-orange fs-4"></i> Thống Kê Tăng Trưởng
                                </h5>
                                <p class="mb-0 text-secondary small mt-1">Biểu đồ thể hiện số lượng thành viên mới theo tháng — dùng để theo dõi tốc độ tăng trưởng và phát hiện biến động bất thường.</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="chart-container" style="position: relative; height: 320px;">
                            <LineChart :data="chartData" :options="chartOptions" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5">
                <div class="card genealogy-main-card border-0 shadow-sm overflow-hidden h-100">
                    <div class="card-header py-3 px-4 border-0 mt-2">
                        <div class="d-flex align-items-center">
                            <div>
                                <h5 class="mb-0 fw-bold d-flex align-items-center gap-2 theme-text-main">
                                    <i class="bx bx-time-five text-warning fs-4"></i> Cập Nhật Gần Đây
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4 pt-0">
                        <!-- Desktop / md+ table -->
                        <div class="table-responsive table-container-premium d-none d-md-block">
                            <table class="table align-middle mb-0 text-nowrap">
                                <thead>
                                    <tr>
                                        <th class="ps-3">Họ và Tên</th>
                                        <th>Mối Quan Hệ</th>
                                        <th>Ngày Cập Nhật</th>
                                        <th class="text-end pe-3">Trạng Thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="!stats.recent_members || stats.recent_members.length === 0">
                                        <td colspan="4" class="text-center py-5 text-muted fw-medium bg-transparent">Chưa có dữ liệu thành viên mới cập nhật.</td>
                                    </tr>
                                    <tr v-for="item in stats.recent_members" :key="item.id" class="table-row-premium">
                                        <td class="ps-3 bg-transparent">
                                            <div class="d-flex align-items-center">
                                                <img :src="item.avatar || 'https://ui-avatars.com/api/?name=' + item.ho_ten + '&background=f97316&color=fff'" class="rounded-circle me-3 border border-2 border-white shadow-sm" width="38" height="38" style="object-fit: cover;">
                                                <div class="fw-bold theme-text-main row-member-name">{{ item.ho_ten }}</div>
                                            </div>
                                        </td>
                                        <td class="fw-semibold text-secondary bg-transparent">{{ item.loai_quan_he }}</td>
                                        <td class="text-muted font-medium bg-transparent">{{ formatDate(item.updated_at) }}</td>
                                        <td class="text-end pe-3 bg-transparent">
                                            <span class="badge badge-premium-status px-3 py-2 fw-bold">Đã cập nhật</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile / small screens: stacked cards with details -->
                        <div class="d-md-none">
                            <div v-if="!stats.recent_members || stats.recent_members.length === 0" class="text-center py-4 text-muted">Chưa có dữ liệu thành viên mới cập nhật.</div>
                            <div v-for="item in stats.recent_members" :key="item.id" class="mobile-update-card p-3 mb-3 border rounded-3" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                                <div class="d-flex align-items-start">
                                    <img :src="item.avatar || 'https://ui-avatars.com/api/?name=' + item.ho_ten + '&background=f97316&color=fff'" class="rounded-circle me-3" width="48" height="48" style="object-fit: cover;">
                                    <div class="flex-fill">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="fw-bold theme-text-main">{{ item.ho_ten }}</div>
                                                <div class="small text-secondary">{{ item.loai_quan_he }}</div>
                                            </div>
                                            <div class="text-end small text-muted">{{ formatDate(item.updated_at) }}</div>
                                        </div>

                                        <div class="mt-2 small text-secondary">
                                            <div v-if="item.changes">
                                                <div v-for="(val, key) in item.changes" :key="key" class="mb-1">
                                                    <strong>{{ formatFieldName(key) }}:</strong> {{ val }}
                                                </div>
                                            </div>
                                            <div v-else-if="item.update_summary">{{ item.update_summary }}</div>
                                            <div v-else>Không có chi tiết cập nhật</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Line } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale, Filler } from 'chart.js';
import axios from 'axios';
import toastr from 'toastr';

ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale, Filler);

export default {
    name: 'PartnerDashboard',
    components: {
        LineChart: Line
    },
    data() {
        return {
            partnerProfile: null,
            stats: {
                total_members: 0,
                max_generation: 0,
                upcoming_events: 0,
                recent_members: []
            },
            newTenGoi: '',
            isLoading: false,
            chartData: {
                labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6'],
                datasets: [
                    {
                        label: 'Thành viên mới',
                        backgroundColor: 'rgba(249, 115, 22, 0.15)',
                        borderColor: '#f97316',
                        data: [5, 12, 8, 25, 16, 22],
                        tension: 0.4,
                        fill: true,
                    }
                ]
            },
            chartOptions: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#6b7280' }
                    }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { color: '#6b7280' } },
                    x: { grid: { display: false }, ticks: { color: '#6b7280' } }
                }
            }
        }
    },
    mounted() {
        this.loadProfile();
        this.loadStats();
    },
    methods: {
        getHeaders() {
            return { headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` } };
        },
        loadProfile() {
            axios.get('http://127.0.0.1:8000/api/doi-tac/get-profile', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.partnerProfile = res.data.data;
                        this.newTenGoi = this.partnerProfile.ten_goi !== 'Gói Đối Tác' ? this.partnerProfile.ten_goi : '';
                    }
                });
        },
        loadStats() {
            axios.get('http://127.0.0.1:8000/api/doi-tac/statistics', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.stats = res.data.data;
                        // Update chart with monthly data if available
                        const monthly = res.data.data.monthly_new_members;
                        if (monthly && monthly.labels && monthly.data) {
                            this.chartData.labels = monthly.labels;
                            if (this.chartData.datasets && this.chartData.datasets[0]) {
                                this.chartData.datasets[0].data = monthly.data;
                            }
                        }
                    }
                });
        },
        updatePartnerInfo() {
            if (!this.newTenGoi) {
                toastr.warning('Vui lòng nhập tên dòng họ/tổ chức!');
                return;
            }
            axios.post('http://127.0.0.1:8000/api/doi-tac/update-profile', { ten_goi: this.newTenGoi }, this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        toastr.success(res.data.message);
                        this.loadProfile();
                    }
                });
        },
        formatDate(dateString) {
            if (!dateString) return '---';
            return new Date(dateString).toLocaleDateString('vi-VN');
        }
        ,
        formatFieldName(key) {
            if (!key) return '';
            return String(key).replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
        }
    }
}
</script>

<style scoped>
/* BANNER CHÀO MỪNG */
.welcome-banner-card {
  border-radius: 16px !important;
}
.banner-title { font-family: 'Inter', sans-serif; font-weight: 700; }
.banner-sub { font-size: 13.5px; }

.premium-input {
  border-radius: 30px !important;
  border: 1px solid var(--border-color) !important;
  padding: 10px 18px !important;
  font-size: 14px;
  background-color: var(--app-bg) !important;
  box-shadow: none !important;
  color: var(--text-main) !important;
}
.premium-input:focus {
  background-color: var(--card-bg) !important;
  border-color: #f97316 !important;
}

.btn-action-orange {
  background: linear-gradient(135deg, #f43f5e 0%, #f97316 100%) !important;
  color: #ffffff !important; border: none !important; border-radius: 30px !important;
  font-weight: 600; box-shadow: 0 4px 12px rgba(244, 63, 94, 0.2) !important;
  transition: all 0.25s ease;
}
.btn-action-orange:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(244, 63, 94, 0.3) !important; }

/* 🌟 ÉP CHỮ THAY ĐỔI THEO THEME */
.theme-text-main {
  color: var(--text-main) !important;
}

/* WIDGET KPI CARD THÍCH ỨNG */
.widget-kpi-card {
  background-color: var(--card-bg) !important;
  border-radius: 16px !important;
  border: 1px solid var(--border-color) !important;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.01) !important;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.widget-kpi-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 24px rgba(249, 115, 22, 0.05) !important;
}


.kpi-label { font-size: 13px; color: var(--text-sub) !important; }
.kpi-sub { font-size: 11.5px; color: var(--text-sub) !important; opacity: 0.8; }

.kpi-icon-box { width: 48px; height: 48px; flex-shrink: 0; }
.bg-light-orange { background-color: rgba(249, 115, 22, 0.08) !important; }
.text-orange { color: #f97316 !important; }
.bg-light-pink { background-color: rgba(219, 39, 119, 0.08) !important; }
.text-pink { color: #db2777 !important; }
.bg-light-amber { background-color: rgba(245, 158, 11, 0.08) !important; }
.text-amber { color: #d97706 !important; }

/* BẢNG BIỂU PREMIUM */
.genealogy-main-card {
  background: var(--card-bg) !important;
  border: 1px solid var(--border-color) !important;
  border-radius: 16px !important;
}
.card-header {
  background: transparent !important;
  border-bottom: 1px solid var(--border-color) !important;
}
.table-container-premium {
  border: 1px solid var(--border-color);
  border-radius: 12px;
  overflow: hidden;
}
.table-row-premium {
  border-bottom: 1px solid var(--border-color) !important;
}
.table-row-premium:hover {
  background-color: var(--input-bg) !important;
}
.table-row-premium td {
  color: var(--text-main) !important;
}
.badge-premium-status {
  background-color: rgba(249, 115, 22, 0.08) !important;
  color: #ea580c !important;
  border: 1px solid rgba(234, 88, 12, 0.15);
  border-radius: 30px !important;
  font-size: 11px !important;
}
/* Mobile update card */
.mobile-update-card .fw-bold { font-size: 14px; }
.mobile-update-card .small { font-size: 12px; }
.mobile-update-card strong { color: var(--text-main); }
</style>