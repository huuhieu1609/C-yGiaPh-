<template>
    <div class="dashboard-wrapper">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card luxury-panel border-0 overflow-hidden shadow-sm position-relative">
                    <div class="banner-bg-pattern"></div>
                    <div class="card-body p-4 position-relative z-2">
                        <div class="row align-items-center g-3">
                            <div class="col-md-7">
                                <h4 class="fw-bold mb-2 main-title text-dark">
                                    {{ partnerProfile && (partnerProfile.ten_goi === 'Gói Đối Tác' || !partnerProfile.ten_goi) ? 'Chào mừng bạn đến với Hệ Thống Gia Phả!' : 'Quản Lý Thông Tin Dòng Họ' }}
                                </h4>
                                <p class="mb-0 text-secondary small">Tên Dòng Họ sẽ được sử dụng làm tên Chi Nhánh gốc của bạn trên bản đồ hệ thống tộc phả.</p>
                            </div>
                            <div class="col-md-5 text-md-end">
                                <div class="modern-input-group d-flex align-items-center">
                                    <input type="text" class="form-control modern-input-inner fw-semibold" v-model="newTenGoi" placeholder="Tên Dòng Họ (VD: Trần Gia)">
                                    <button class="btn btn-luxury-primary-pill text-nowrap" @click="updatePartnerInfo">
                                        {{ partnerProfile && (partnerProfile.ten_goi === 'Gói Đối Tác' || !partnerProfile.ten_goi) ? 'Cập Nhật' : 'Đổi Tên' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 row-cols-1 row-cols-md-2 row-cols-xl-3 mb-4">
            <div class="col">
                <div class="card luxury-stat-card border-0 shadow-sm item-members">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="stat-content flex-grow-1">
                                <p class="stat-label mb-1">Thành Viên Dòng Họ</p>
                                <h2 class="stat-number my-1 fw-bold">{{ stats.total_members || 0 }}</h2>
                                <p class="stat-desc mb-0">Tổng số nhân khẩu đã ghi nhận</p>
                            </div>
                            <div class="stat-icon-box rounded-circle">
                                <i class='bx bx-group'></i>
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
                                <p class="stat-label mb-1">Số Đời Tộc Họ</p>
                                <h2 class="stat-number my-1 fw-bold">{{ stats.max_generation || 0 }}</h2>
                                <p class="stat-desc mb-0">Thế hệ cao nhất trên cây</p>
                            </div>
                            <div class="stat-icon-box rounded-circle">
                                <i class='bx bx-git-branch'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card luxury-stat-card border-0 shadow-sm item-clan">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="stat-content flex-grow-1">
                                <p class="stat-label mb-1">Sự Kiện Sắp Tới</p>
                                <h2 class="stat-number my-1 fw-bold">{{ stats.upcoming_events || 0 }}</h2>
                                <p class="stat-desc mb-0">Lịch giỗ chạp, hội họp dòng tộc</p>
                            </div>
                            <div class="stat-icon-box rounded-circle">
                                <i class='bx bx-calendar-event'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card luxury-panel border-0 shadow-sm">
                    <div class="card-header bg-transparent py-4">
                        <h6 class="mb-0 fw-bold panel-sub-title text-dark">
                            <i class="bx bx-time-five me-1 text-rose"></i> Cập Nhật Thành Viên Gần Đây
                        </h6>
                    </div>
                    <div class="card-body p-4 pt-2">
                        <div class="table-responsive rounded-3 border border-light-subtle">
                            <table class="table modern-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th width="35%">Họ và Tên</th>
                                        <th width="25%">Mối Quan Hệ</th>
                                        <th width="25%">Ngày Cập Nhật</th>
                                        <th width="15%" class="text-center">Trạng Thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="!stats.recent_members || stats.recent_members.length === 0">
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="bx bx-file-blank fs-2 mb-2 d-block opacity-20"></i>
                                            Chưa có dữ liệu thành viên mới cập nhật.
                                        </td>
                                    </tr>
                                    <tr v-for="item in stats.recent_members" :key="item.id">
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar-table-wrapper">
                                                    <img :src="item.avatar || 'https://ui-avatars.com/api/?name=' + item.ho_ten + '&background=f3f4f6&color=4b5563'" class="rounded-circle shadow-sm" width="38" height="38">
                                                    <span class="avatar-status-dot" :class="item.trang_thai === 'Còn sống' ? 'alive' : 'dead'"></span>
                                                </div>
                                                <div class="fw-bold text-dark fs-6">{{ item.ho_ten }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge-relationship" :class="item.loai_quan_he === 'Chính' ? 'rel-main' : 'rel-spouse'">
                                                {{ item.loai_quan_he === 'Chính' ? 'Thành viên chính' : 'Người phối ngẫu' }}
                                            </span>
                                        </td>
                                        <td class="text-secondary small fw-medium">{{ formatDate(item.updated_at) }}</td>
                                        <td class="text-center">
                                            <span class="badge btn-modern-success">Đã đồng bộ</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import toastr from 'toastr';

export default {
    name: 'PartnerDashboard',
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
            isLoading: false
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
    }
}
</script>

<style scoped>
/* ─── SYSTEM LUXURY BLOCKS ─── */
.luxury-panel {
    background: #ffffff !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
    border-radius: 20px !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
}

.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.04) !important;
}

.main-title {
    font-size: 18px;
    letter-spacing: 0.2px;
}
.panel-sub-title {
    font-size: 14px;
    letter-spacing: 0.3px;
}
.text-rose { color: #db2777; }

/* ─── BANNER INPUT GROUP VIÊN THUỐC ─── */
.banner-bg-pattern {
    position: absolute;
    inset: 0;
    pointer-events: none;
    background: radial-gradient(circle at 90% 10%, rgba(219, 39, 119, 0.02) 0%, transparent 60%);
}
.modern-input-group {
    background: #f9fafb;
    border: 1px solid rgba(0, 0, 0, 0.06);
    border-radius: 30px;
    padding: 3px;
    width: 100%;
}
.modern-input-inner {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    padding-left: 16px;
    color: #111827;
    font-size: 14px;
}
.modern-input-inner::placeholder { color: #9ca3af; }

/* Nút bấm viên thuốc rực rỡ đồng bộ toàn cục FE */
.btn-luxury-primary-pill {
    background: linear-gradient(135deg, #db2777 0%, #f97316 50%, #f59e0b 100%);
    border: none;
    color: #ffffff;
    font-weight: 600;
    font-size: 13.5px;
    border-radius: 30px;
    padding: 8px 24px;
    box-shadow: 0 4px 10px rgba(219, 39, 119, 0.15);
    transition: all 0.25s ease;
}
.btn-luxury-primary-pill:hover {
    box-shadow: 0 6px 16px rgba(219, 39, 119, 0.3);
    filter: brightness(1.05);
}

/* ─── CARD STATS LUXURY CO-ALIGNMENT ─── */
.luxury-stat-card {
    background: #ffffff !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
    border-radius: 18px !important;
    box-shadow: 0 6px 20px rgba(0,0,0,0.015);
    position: relative;
    overflow: hidden;
}
.stat-label {
    font-size: 13px;
    font-weight: 600;
    color: #6b7280;
}
.stat-number {
    font-size: 26px;
    color: #111827;
}
.stat-desc {
    font-size: 11.5px;
    color: #9ca3af;
}

/* Định vị gán dải sắc cho icon vòng tròn mờ */
.stat-icon-box {
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
    border: 1px solid transparent;
}

/* Khối màu kén mờ Pastel sáng cho các mục con */
.item-members .stat-icon-box { background: #f5f3ff; color: #4f46e5; border-color: rgba(79, 70, 229, 0.1); }
.item-tree .stat-icon-box { background: #fff5f7; color: #db2777; border-color: rgba(219, 39, 119, 0.1); }
.item-clan .stat-icon-box { background: #f0fdf4; color: #059669; border-color: rgba(5, 150, 105, 0.1); }

/* ─── MODERN LIGHT TABLE PRESET ─── */
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
}
.modern-table tbody tr {
    transition: background var(--transition-fast);
}
.modern-table tbody tr:hover {
    background-color: #fafafb;
}
.modern-table tbody td {
    padding: 14px 16px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.03);
    color: #374151;
    font-size: 14px;
}

/* Avatar bàn bảng kèm chấm sinh tử */
.avatar-table-wrapper {
    position: relative;
    width: 38px;
    height: 38px;
}
.avatar-status-dot {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    border: 1.5px solid #ffffff;
}
.avatar-status-dot.alive { background-color: #10b981; box-shadow: 0 0 6px #10b981; }
.avatar-status-dot.dead { background-color: #9ca3af; }

/* Thẻ badge mối quan hệ */
.badge-relationship {
    font-size: 12px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 20px;
}
.rel-main { background: #eef2ff; color: #4f46e5; }
.rel-spouse { background: #fff7ed; color: #ea580c; }

/* Badge gán trạng thái */
.btn-modern-success {
    background-color: #f0fdf4 !important;
    border: 1px solid rgba(5, 150, 105, 0.15) !important;
    color: #059669 !important;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 30px;
    font-size: 12px;
}
</style>    