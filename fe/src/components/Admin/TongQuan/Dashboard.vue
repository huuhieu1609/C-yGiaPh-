<template>
    <div>
        <!-- Metrics Row -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 mb-4">
            <div class="col">
                <div class="card luxury-stat-card border-0 shadow-sm item-total">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="stat-content flex-grow-1">
                                <p class="stat-label mb-1">Tổng Nhân Khẩu</p>
                                <h3 class="stat-number my-1 fw-bold text-dark">{{ formatNumber(metrics.total_members) }}
                                </h3>
                                <p class="stat-desc mb-0 text-success"><i class="bx bx-check-circle"></i> Đã đồng bộ hệ
                                    thống</p>
                            </div>
                            <div class="stat-icon-box rounded-circle">
                                <i class='bx bxs-group'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card luxury-stat-card border-0 shadow-sm item-tree">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="stat-content flex-grow-1">
                                <p class="stat-label mb-1">Chi Nhánh & Dòng Họ</p>
                                <h3 class="stat-number my-1 fw-bold text-dark">{{ formatNumber(metrics.total_trees) }}
                                </h3>
                                <p class="stat-desc mb-0 text-success"><i class="bx bx-git-branch"></i> Hoạt động ổn
                                    định</p>
                            </div>
                            <div class="stat-icon-box rounded-circle">
                                <i class='bx bxs-share-alt'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card luxury-stat-card border-0 shadow-sm item-donation">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="stat-content flex-grow-1">
                                <p class="stat-label mb-1">Doanh Thu Gói Dịch Vụ</p>
                                <h3 class="stat-number my-1 fw-bold text-dark" style="font-size: 20px;">{{
                                    formatCurrency(metrics.total_contributions) }}</h3>
                                <p class="stat-desc mb-0 text-success"><i class="bx bx-trending-up"></i> Tăng trưởng bền
                                    vững</p>
                            </div>
                            <div class="stat-icon-box rounded-circle">
                                <i class='bx bxs-wallet'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card luxury-stat-card border-0 shadow-sm item-digitize">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="stat-content flex-grow-1">
                                <p class="stat-label mb-1">Tài Khoản Đăng Ký</p>
                                <h3 class="stat-number my-1 fw-bold text-dark">{{ formatNumber(metrics.total_users) }}
                                </h3>
                                <p class="stat-desc mb-0 text-success"><i class="bx bx-user-check"></i> Đang được quản
                                    lý</p>
                            </div>
                            <div class="stat-icon-box rounded-circle">
                                <i class='bx bxs-user-detail'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Growth Chart Row -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card luxury-panel border-0 shadow-sm">
                    <div
                        class="card-header bg-transparent py-4 border-0 d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <h6 class="mb-0 fw-bold panel-title text-dark">
                            <i class="bx bx-analyse me-2 text-primary-gradient"></i>{{ chartTitle }}
                        </h6>
                        <!-- Interactive Premium Legend -->
                        <div class="custom-chart-legend d-flex align-items-center gap-3">
                            <div class="legend-item" :class="{ 'legend-item-disabled': datasetVisibility[0] === false }"
                                @click="toggleDatasetVisibility(0)">
                                <span class="legend-color-dot dot-blue"></span>
                                <span class="legend-label-text">Tổng Thành Viên</span>
                                <span class="legend-value-badge bg-blue-subtle">{{ formatNumber(metrics.total_members)
                                }}</span>
                            </div>
                            <div class="legend-item" :class="{ 'legend-item-disabled': datasetVisibility[1] === false }"
                                @click="toggleDatasetVisibility(1)">
                                <span class="legend-color-dot dot-pink"></span>
                                <span class="legend-label-text">Cây Gia Phả</span>
                                <span class="legend-value-badge bg-pink-subtle">{{ formatNumber(metrics.total_trees)
                                }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <!-- Premium Filter Toolbar -->
                        <div
                            class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 pb-3 border-bottom border-light-subtle">
                            <!-- Quick Range Buttons -->
                            <div class="btn-group premium-btn-group" role="group">
                                <button type="button" class="btn premium-filter-btn"
                                    :class="{ 'active': currentFilter === 'week' }" @click="setFilter('week')">
                                    <i class="bx bx-calendar-event me-1"></i> Tuần
                                </button>
                                <button type="button" class="btn premium-filter-btn"
                                    :class="{ 'active': currentFilter === 'month' }" @click="setFilter('month')">
                                    <i class="bx bx-calendar me-1"></i> Tháng
                                </button>
                                <button type="button" class="btn premium-filter-btn"
                                    :class="{ 'active': currentFilter === 'year' }" @click="setFilter('year')">
                                    <i class="bx bx-calendar-star me-1"></i> Năm
                                </button>
                                <button type="button" class="btn premium-filter-btn"
                                    :class="{ 'active': currentFilter === 'custom' }" @click="setFilter('custom')">
                                    <i class="bx bx-slider-alt me-1"></i> Tùy chọn khoảng
                                </button>
                            </div>

                            <!-- Custom Date Inputs -->
                            <div v-if="currentFilter === 'custom'"
                                class="custom-date-inputs d-flex align-items-center gap-2 animate-fade-in">
                                <div class="input-group input-group-sm">
                                    <span
                                        class="input-group-text bg-light border-light-subtle text-secondary small-label">Từ</span>
                                    <input type="date" v-model="startDate" class="form-control premium-date-input"
                                        :max="endDate || todayDate">
                                </div>
                                <div class="input-group input-group-sm">
                                    <span
                                        class="input-group-text bg-light border-light-subtle text-secondary small-label">Đến</span>
                                    <input type="date" v-model="endDate" class="form-control premium-date-input"
                                        :min="startDate" :max="todayDate">
                                </div>
                                <button class="btn btn-sm btn-filter-submit" @click="applyCustomDateRange"
                                    :disabled="!startDate || !endDate">
                                    <i class="bx bx-filter-alt me-1"></i> Lọc
                                </button>
                            </div>
                        </div>

                        <!-- Premium Analytics Summary Badges -->
                        <div
                            class="chart-summary-stats d-flex gap-4 mb-4 pb-3 border-bottom border-light-subtle flex-wrap">
                            <div class="summary-stat-box">
                                <span class="summary-label text-muted small d-block mb-1">Quy Mô Hệ Thống</span>
                                <strong class="summary-val text-dark font-outfit" style="font-size: 15px;">
                                    <i class="bx bx-sitemap text-success"></i> {{ formatNumber(metrics.total_members) }}
                                    nhân khẩu
                                </strong>
                            </div>
                            <div class="summary-stat-box border-start ps-4">
                                <span class="summary-label text-muted small d-block mb-1">Tài Khoản Đăng Ký</span>
                                <strong class="summary-val text-dark font-outfit" style="font-size: 15px;">
                                    <i class="bx bx-user text-info"></i> {{ formatNumber(metrics.total_users) }} tài
                                    khoản
                                </strong>
                            </div>
                            <div class="summary-stat-box border-start ps-4">
                                <span class="summary-label text-muted small d-block mb-1">Cập nhật cuối</span>
                                <strong class="summary-val text-secondary small"
                                    style="font-size: 13px; line-height: 24px;">
                                    {{ recentUpdates.length > 0 ? recentUpdates[0].ngay_cap_nhat : 'Hôm nay' }}
                                </strong>
                            </div>
                        </div>
                        <div style="height: 300px; position: relative;">
                            <canvas id="growthChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Row -->
        <div class="row g-4">
            <div class="col-12 col-lg-8">
                <div class="card luxury-panel border-0 shadow-sm h-100">
                    <div
                        class="card-header bg-transparent py-4 border-0 d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <h6 class="mb-0 fw-bold panel-title text-dark">
                            <i class="bx bx-time-five me-1 text-emerald"></i> Cập nhật Gia Phả Gần Đây
                        </h6>
                        <div class="d-flex align-items-center gap-3 ms-auto">
                            <!-- Premium Select Dropdown for Clan selection -->
                            <select class="form-select form-select-sm premium-select border-2 shadow-none"
                                v-model="selectedClanId" style="width: 350px; font-weight: 600; border-radius: 8px;">
                                <option :value="null">Tất Cả Gia Tộc</option>
                                <option v-for="clan in listClans" :key="clan.id" :value="clan.id">
                                    {{ clan.dong_ho }} ({{ clan.nguoi_dung ? clan.nguoi_dung.ho_ten : '' }})
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="table-responsive rounded-3 border border-light-subtle recent-updates-scroll">
                            <table class="table modern-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Thành viên</th>
                                        <th>Dòng họ</th>
                                        <th>Ngày cập nhật</th>
                                        <th class="text-center">Trạng thái</th>
                                        <th class="text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="filteredUpdates.length === 0">
                                        <td colspan="5" class="text-center py-4 text-muted">Chưa có cập nhật thành viên
                                            nào gần đây của gia tộc này</td>
                                    </tr>
                                    <tr v-for="item in filteredUpdates" :key="item.id">
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <img :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(item.ho_ten)}&background=eef2ff&color=4f46e5`"
                                                    class="rounded-circle shadow-sm" width="36" height="36">
                                                <span class="fw-bold text-dark">{{ item.ho_ten }}</span>
                                            </div>
                                        </td>
                                        <td class="text-secondary fw-semibold">{{ item.dong_ho }}</td>
                                        <td class="text-muted small fw-medium">{{ item.ngay_cap_nhat }}</td>
                                        <td class="text-center">
                                            <span class="badge status-badge"
                                                :class="item.trang_thai === 'Còn sống' ? 'badge-approved' : 'badge-rejected'">
                                                {{ item.trang_thai || 'Chưa rõ' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <router-link to="/admin/gia-pha" class="btn btn-action-edit"
                                                    title="Đi tới Quản lý"><i class='bx bx-cog'></i></router-link>
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
                <div class="card luxury-panel border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent py-4 border-0">
                        <h6 class="mb-0 fw-bold panel-title text-dark">
                            <i class="bx bx-pulse me-1 text-emerald"></i> Hoạt động Hệ thống
                        </h6>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="traffic-source-container d-flex flex-column gap-4">
                            <div class="traffic-source d-flex align-items-center">
                                <div class="activity-icon-box box-indigo rounded-circle">
                                    <i class='bx bxs-user-plus'></i>
                                </div>
                                <div class="ms-3">
                                    <p class="activity-label mb-0">Thành viên đăng ký mới</p>
                                    <h6 class="activity-desc mb-0 fw-bold text-dark">{{
                                        formatNumber(systemActivities.new_users_today) }} tài khoản hệ thống</h6>
                                </div>
                            </div>
                            <div class="traffic-source d-flex align-items-center">
                                <div class="activity-icon-box box-emerald rounded-circle">
                                    <i class='bx bxs-image-add'></i>
                                </div>
                                <div class="ms-3">
                                    <p class="activity-label mb-0">Ảnh tư liệu & di sản</p>
                                    <h6 class="activity-desc mb-0 fw-bold text-dark">{{
                                        formatNumber(systemActivities.images_uploaded) }} tệp tin ảnh lưu trữ</h6>
                                </div>
                            </div>
                            <div class="traffic-source d-flex align-items-center">
                                <div class="activity-icon-box box-amber rounded-circle">
                                    <i class='bx bxs-bell'></i>
                                </div>
                                <div class="ms-3">
                                    <p class="activity-label mb-0">Thông báo hệ thống</p>
                                    <h6 class="activity-desc mb-0 fw-bold text-dark">{{
                                        formatNumber(systemActivities.notifications) }} bản tin được phát đi</h6>
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
import axios from 'axios';
import { Chart } from 'chart.js/auto';

export default {
    name: 'AdminDashboard',
    data() {
        return {
            metrics: {
                total_members: 0,
                total_trees: 0,
                total_contributions: 0,
                total_requests: 0,
                total_users: 0
            },
            recentUpdates: [],
            systemActivities: {
                new_users_today: 0,
                images_uploaded: 0,
                notifications: 0
            },
            growthChartData: null,
            chartInstance: null,
            datasetVisibility: [true, true],
            currentFilter: 'month',
            startDate: '',
            endDate: '',
            todayDate: '',
            // Clan selection variables
            selectedClanId: null,
            listClans: []
        };
    },
    mounted() {
        const today = new Date();
        this.todayDate = this.formatDateYYYYMMDD(today);
        this.fetchDashboardData();
        this.fetchClans();
    },
    computed: {
        chartTitle() {
            if (this.currentFilter === 'week') {
                return 'Thống Kê Tăng Trưởng Hệ Thống (7 Ngày Gần Đây)';
            } else if (this.currentFilter === 'year') {
                return 'Thống Kê Tăng Trưởng Hệ Thống (5 Năm Gần Đây)';
            } else if (this.currentFilter === 'custom') {
                if (this.startDate && this.endDate) {
                    return `Thống Kê Tăng Trưởng Hệ Thống (${this.formatDateDMY(this.startDate)} - ${this.formatDateDMY(this.endDate)})`;
                }
                return 'Thống Kê Tăng Trưởng Hệ Thống (Khoảng Thời Gian Tùy Chọn)';
            }
            return 'Thống Kê Tăng Trưởng Hệ Thống (6 Tháng Gần Đây)';
        },
        filteredUpdates() {
            if (!this.selectedClanId) {
                return this.recentUpdates;
            }
            const clan = this.listClans.find(c => c.id === this.selectedClanId);
            if (!clan || !clan.dong_ho) return [];

            return this.recentUpdates.filter(item =>
                item.dong_ho && item.dong_ho.toLowerCase() === clan.dong_ho.toLowerCase()
            );
        }
    },
    methods: {
        getHeaders() {
            return {
                headers: {
                    Authorization: 'Bearer ' + localStorage.getItem('access_token')
                }
            };
        },
        fetchClans() {
            axios.get('http://127.0.0.1:8000/api/admin/doi-tac/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        // Filter partners who have associated clan names
                        this.listClans = res.data.data.filter(c => c.dong_ho);
                    }
                })
                .catch(err => console.error('Lỗi tải danh sách gia tộc:', err));
        },
        fetchDashboardData() {
            const params = {
                filter_type: this.currentFilter
            };
            if (this.currentFilter === 'custom' && this.startDate && this.endDate) {
                params.start_date = this.startDate;
                params.end_date = this.endDate;
            }

            axios.get('http://127.0.0.1:8000/api/admin/dashboard', {
                params: params,
                headers: {
                    Authorization: 'Bearer ' + localStorage.getItem('access_token')
                }
            })
                .then(res => {
                    if (res.data.status) {
                        const data = res.data.data;
                        this.metrics = data.metrics;
                        this.recentUpdates = data.recent_updates;
                        this.systemActivities = data.system_activities;
                        this.growthChartData = data.growth_chart;

                        this.$nextTick(() => {
                            this.initChart();
                        });
                    }
                })
                .catch(err => {
                    console.error('Lỗi tải dữ liệu dashboard:', err);
                });
        },
        initChart() {
            if (!this.growthChartData) return;

            const ctx = document.getElementById('growthChart');
            if (!ctx) return;

            const canvasContext = ctx.getContext('2d');

            // Create gradients for Bar charts (vertical gradient)
            const gradientBlue = canvasContext.createLinearGradient(0, 0, 0, 300);
            gradientBlue.addColorStop(0, '#4f46e5');
            gradientBlue.addColorStop(1, '#818cf8');

            const gradientPink = canvasContext.createLinearGradient(0, 0, 0, 300);
            gradientPink.addColorStop(0, '#f43f5e');
            gradientPink.addColorStop(1, '#fb7185');

            if (this.chartInstance) {
                this.chartInstance.destroy();
            }

            this.chartInstance = new Chart(ctx, {
                type: 'bar', // Dynamic request: Column/Bar chart!
                data: {
                    labels: this.growthChartData.labels,
                    datasets: [
                        {
                            label: 'Tổng Thành Viên',
                            data: this.growthChartData.members,
                            backgroundColor: gradientBlue,
                            hoverBackgroundColor: '#3730a3',
                            borderRadius: 6, // Sleek modern rounded top corners
                            borderWidth: 0,
                            barPercentage: 0.55,
                            categoryPercentage: 0.7
                        },
                        {
                            label: 'Cây Gia Phả',
                            data: this.growthChartData.trees,
                            backgroundColor: gradientPink,
                            hoverBackgroundColor: '#be123c',
                            borderRadius: 6, // Sleek modern rounded top corners
                            borderWidth: 0,
                            barPercentage: 0.55,
                            categoryPercentage: 0.7
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            padding: 14,
                            cornerRadius: 12,
                            backgroundColor: 'rgba(15, 23, 42, 0.95)',
                            titleColor: '#f8fafc',
                            titleFont: {
                                family: "'Outfit', sans-serif",
                                weight: '700',
                                size: 13
                            },
                            bodyFont: {
                                family: "'Inter', sans-serif",
                                size: 12
                            },
                            multiKeyBackground: 'transparent',
                            borderColor: 'rgba(255, 255, 255, 0.08)',
                            borderWidth: 1,
                            usePointStyle: true,
                            boxWidth: 8,
                            boxHeight: 8,
                            boxPadding: 4,
                            callbacks: {
                                labelColor: function (context) {
                                    return {
                                        borderColor: context.dataset.backgroundColor,
                                        backgroundColor: context.dataset.backgroundColor,
                                        borderWidth: 0,
                                        borderRadius: 2
                                    };
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.04)',
                                borderDash: [5, 5],
                                drawTicks: false
                            },
                            border: {
                                display: false
                            },
                            ticks: {
                                padding: 12,
                                color: '#64748b',
                                font: {
                                    family: "'Inter', sans-serif",
                                    size: 11
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            border: {
                                display: false
                            },
                            ticks: {
                                padding: 12,
                                color: '#64748b',
                                font: {
                                    family: "'Outfit', 'Inter', sans-serif",
                                    weight: '600',
                                    size: 12
                                }
                            }
                        }
                    }
                }
            });

            // Set visibility based on reactive state
            this.chartInstance.setDatasetVisibility(0, this.datasetVisibility[0]);
            this.chartInstance.setDatasetVisibility(1, this.datasetVisibility[1]);
            this.chartInstance.update();
        },
        toggleDatasetVisibility(index) {
            if (!this.chartInstance) return;
            const isVisible = this.chartInstance.isDatasetVisible(index);
            if (isVisible) {
                this.chartInstance.hide(index);
                this.datasetVisibility[index] = false;
            } else {
                this.chartInstance.show(index);
                this.datasetVisibility[index] = true;
            }
        },
        setFilter(filter) {
            this.currentFilter = filter;
            if (filter !== 'custom') {
                this.fetchDashboardData();
            } else {
                // Default pre-fill date range: 2 weeks
                if (!this.startDate || !this.endDate) {
                    const end = new Date();
                    const start = new Date();
                    start.setDate(end.getDate() - 14);

                    this.endDate = this.formatDateYYYYMMDD(end);
                    this.startDate = this.formatDateYYYYMMDD(start);
                }
                this.fetchDashboardData();
            }
        },
        applyCustomDateRange() {
            if (this.startDate && this.endDate) {
                this.fetchDashboardData();
            }
        },
        formatDateYYYYMMDD(date) {
            const yyyy = date.getFullYear();
            const mm = String(date.getMonth() + 1).padStart(2, '0');
            const dd = String(date.getDate()).padStart(2, '0');
            return `${yyyy}-${mm}-${dd}`;
        },
        formatDateDMY(dateStr) {
            if (!dateStr) return '';
            const parts = dateStr.split('-');
            if (parts.length !== 3) return dateStr;
            return `${parts[2]}/${parts[1]}/${parts[0]}`;
        },
        formatNumber(val) {
            return new Intl.NumberFormat('vi-VN').format(val || 0);
        },
        formatCurrency(val) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val || 0);
        }
    },
    beforeUnmount() {
        if (this.chartInstance) {
            this.chartInstance.destroy();
        }
    }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap');

/* ─── CONTEMPORARY SYSTEM LUXURY CONTROLS ─── */
.luxury-panel {
    background: #ffffff !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
    border-radius: 20px !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
}

.panel-title {
    font-size: 14.5px;
    letter-spacing: 0.3px;
    font-family: 'Outfit', sans-serif;
}

.text-emerald {
    color: #059669 !important;
}

/* ─── CARD THỐNG KÊ PHỒNG KHỐI TRẮNG VIP ─── */
.luxury-stat-card {
    background: #ffffff !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
    border-radius: 18px !important;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.015);
    font-family: 'Inter', sans-serif;
}

.stat-label {
    font-size: 13px;
    font-weight: 600;
    color: #6b7280;
    font-family: 'Outfit', sans-serif;
}

.stat-number {
    font-size: 26px;
    color: #111827;
    font-family: 'Outfit', sans-serif;
}

.stat-desc {
    font-size: 11.5px;
    font-weight: 500;
}

.stat-desc i {
    font-size: 13px;
    vertical-align: -1px;
}

/* Ô tròn icon bọc kén mờ phát sáng nhẹ */
.stat-icon-box {
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    border: 1px solid transparent;
}

.item-total .stat-icon-box {
    background: #e0f2fe;
    color: #0284c7;
    border-color: rgba(2, 132, 199, 0.1);
}

.item-tree .stat-icon-box {
    background: #fff5f7;
    color: #db2777;
    border-color: rgba(219, 39, 119, 0.1);
}

.item-donation .stat-icon-box {
    background: #f0fdf4;
    color: #059669;
    border-color: rgba(5, 150, 105, 0.1);
}

.item-digitize .stat-icon-box {
    background: #fef3c7;
    color: #d97706;
    border-color: rgba(217, 119, 6, 0.1);
}

/* ─── LUXURY LIGHT TABLE SYSTEM ─── */
.modern-table {
    width: 100%;
}

.modern-table thead {
    background: #f4f5f7;
}

.modern-table thead th {
    color: #4b5563;
    font-weight: 700;
    font-size: 12.5px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.06);
    padding: 14px 16px;
    font-family: 'Outfit', sans-serif;
}

.modern-table tbody tr {
    transition: background 0.2s ease;
}

.modern-table tbody tr:hover {
    background-color: #fdfdfd;
}

.modern-table tbody td {
    padding: 14px 16px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.03);
    font-size: 14px;
}

/* Kiểu dáng thuốc nhuộm hạt kén cho Trạng thái duyệt */
.status-badge {
    font-size: 11.5px;
    font-weight: 600;
    padding: 5px 14px;
    border-radius: 30px;
    display: inline-block;
    width: 95px;
}

.badge-approved {
    background-color: #f0fdf4 !important;
    color: #16a34a !important;
    border: 1px solid rgba(22, 163, 74, 0.15);
}

.badge-pending {
    background-color: #fff7ed !important;
    color: #ea580c !important;
    border: 1px solid rgba(234, 88, 12, 0.15);
}

.badge-rejected {
    background-color: #fef2f2 !important;
    color: #ef4444 !important;
    border: 1px solid rgba(239, 68, 68, 0.15);
}

/* Nút hành động mini tinh xảo */
.btn-action-edit,
.btn-action-delete {
    background: #ffffff;
    border: 1px solid rgba(0, 0, 0, 0.06);
    font-size: 14px;
    border-radius: 6px;
    width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.btn-action-edit {
    color: #d97706;
}

.btn-action-edit:hover {
    background: #d97706;
    color: #fff;
    border-color: transparent;
}

.btn-action-delete {
    color: #dc3545;
}

.btn-action-delete:hover {
    background: #dc3545;
    color: #fff;
    border-color: transparent;
}

/* ─── DROPDOWN & THREE DOTS LUXURY ─── */
.custom-dropdown-trigger {
    color: #9ca3af;
    font-size: 20px;
    transition: color 0.2s ease;
}

.custom-dropdown-trigger:hover {
    color: #111827;
}

.luxury-dropdown {
    background: #ffffff;
    border-radius: 12px;
    padding: 6px;
}

.luxury-dropdown .dropdown-item {
    font-size: 13.5px;
    font-weight: 500;
    color: #4b5563;
    padding: 8px 14px;
    border-radius: 8px;
}

.luxury-dropdown .dropdown-item:hover {
    background: #f9fafb;
    color: #111827;
}

/* ─── TRAFFIC SYSTEM ACTIVITY (Hoạt động hệ thống) ─── */
.activity-icon-box {
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.box-indigo {
    background: #eef2ff;
    color: #4f46e5;
}

.box-emerald {
    background: #f0fdf4;
    color: #059669;
}

.box-amber {
    background: #fffbeb;
    color: #d97706;
}

.activity-label {
    font-size: 11.5px;
    font-weight: 600;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    font-family: 'Outfit', sans-serif;
}

.activity-desc {
    font-size: 13.5px;
    color: #111827;
    margin-top: 2px;
}

/* ─── CUSTOM PREMIUM CHART LEGEND & ANALYTICS ─── */
.text-primary-gradient {
    background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 800;
}

.custom-chart-legend {
    display: flex;
    align-items: center;
    gap: 12px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 14px;
    background: #f8fafc;
    border: 1px solid rgba(0, 0, 0, 0.04);
    border-radius: 30px;
    cursor: pointer;
    user-select: none;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.legend-item:hover {
    background: #f1f5f9;
    transform: translateY(-1.5px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
}

.legend-item-disabled {
    opacity: 0.45;
    background: #f8fafc !important;
    transform: none !important;
    box-shadow: none !important;
}

.legend-item-disabled .legend-label-text {
    text-decoration: line-through;
    color: #94a3b8;
}

.legend-color-dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    display: inline-block;
}

.dot-blue {
    background-color: #4f46e5;
    box-shadow: 0 0 8px rgba(79, 70, 229, 0.55);
}

.dot-pink {
    background-color: #f43f5e;
    box-shadow: 0 0 8px rgba(244, 63, 94, 0.55);
}

.legend-label-text {
    font-size: 13px;
    font-weight: 700;
    color: #475569;
    font-family: 'Outfit', sans-serif;
}

.legend-value-badge {
    font-size: 11px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 20px;
    font-family: 'Inter', sans-serif;
}

.bg-blue-subtle {
    background-color: #eef2ff;
    color: #4338ca;
}

.bg-pink-subtle {
    background-color: #fff1f2;
    color: #be123c;
}

/* Sub header summary stats */
.chart-summary-stats {
    display: flex;
    align-items: center;
}

.summary-stat-box {
    display: flex;
    flex-direction: column;
}

.summary-label {
    letter-spacing: 0.5px;
    text-transform: uppercase;
    font-weight: 700;
    font-size: 9.5px;
    color: #94a3b8 !important;
}

.font-outfit {
    font-family: 'Outfit', sans-serif;
}

/* ─── PREMIUM FILTER TOOLBAR ─── */
.premium-btn-group {
    background: #f1f5f9;
    padding: 4px;
    border-radius: 12px;
    border: 1px solid rgba(0, 0, 0, 0.03);
}

.premium-filter-btn {
    border: none !important;
    background: transparent !important;
    color: #475569 !important;
    font-size: 12.5px !important;
    font-weight: 600 !important;
    padding: 6px 14px !important;
    border-radius: 9px !important;
    font-family: 'Outfit', sans-serif !important;
    transition: all 0.2s ease !important;
}

.premium-filter-btn:hover {
    color: #0f172a !important;
    background: rgba(255, 255, 255, 0.45) !important;
}

.premium-filter-btn.active {
    background: #ffffff !important;
    color: #4f46e5 !important;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05) !important;
}

.custom-date-inputs {
    background: #ffffff;
    border: 1px solid rgba(0, 0, 0, 0.05);
    padding: 4px 8px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.01);
}

.premium-date-input {
    border: 1px solid rgba(0, 0, 0, 0.06) !important;
    border-radius: 8px !important;
    font-size: 11.5px !important;
    color: #334155 !important;
    padding: 4px 8px !important;
    font-family: 'Inter', sans-serif !important;
    background-color: #f8fafc !important;
}

.premium-date-input:focus {
    border-color: #4f46e5 !important;
    background-color: #ffffff !important;
    box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.1) !important;
}

.small-label {
    font-size: 11px !important;
    font-weight: 700 !important;
    color: #64748b !important;
    font-family: 'Outfit', sans-serif !important;
    border-color: rgba(0, 0, 0, 0.05) !important;
}

.btn-filter-submit {
    background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%) !important;
    color: #ffffff !important;
    border: none !important;
    font-weight: 700 !important;
    font-size: 12px !important;
    padding: 6px 14px !important;
    border-radius: 8px !important;
    font-family: 'Outfit', sans-serif !important;
    box-shadow: 0 4px 10px rgba(79, 70, 229, 0.15) !important;
    transition: all 0.2s ease !important;
}

.btn-filter-submit:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 6px 15px rgba(79, 70, 229, 0.22) !important;
}

.btn-filter-submit:disabled {
    opacity: 0.45;
    cursor: not-allowed;
    transform: none !important;
    box-shadow: none !important;
}

.animate-fade-in {
    animation: fadeIn 0.25s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(4px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.premium-select {
    background-color: #f8fafc !important;
    border: 1px solid rgba(0, 0, 0, 0.06) !important;
    font-family: 'Outfit', sans-serif;
    color: #334155 !important;
    padding: 6px 12px !important;
    font-size: 13px !important;
    transition: all 0.25s ease !important;
}

.premium-select:focus {
    border-color: #ffd700 !important;
    box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.15) !important;
}

[data-theme="dark"] .premium-select {
    background-color: #1e1e2d !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
    color: #e2e8f0 !important;
}

[data-theme="dark"] .premium-select:focus {
    border-color: #e5a93b !important;
}

.recent-updates-scroll {
    max-height: 400px;
    overflow-y: auto;
}

.recent-updates-scroll::-webkit-scrollbar {
    width: 6px;
}

.recent-updates-scroll::-webkit-scrollbar-thumb {
    background-color: rgba(0, 0, 0, 0.08);
    border-radius: 10px;
}

[data-theme="dark"] .recent-updates-scroll::-webkit-scrollbar-thumb {
    background-color: rgba(255, 255, 255, 0.15);
}
</style>