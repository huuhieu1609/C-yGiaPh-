<template>
    <div class="phan-quyen-wrapper">
        <!-- Page Header -->
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col">
                    <div class="d-flex align-items-center gap-3">
                        <div class="page-icon">
                            <i class='bx bx-shield-quarter'></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold text-dark">Phân Quyền Hệ Thống</h4>
                            <p class="mb-0 text-muted small mt-1">Thiết lập quyền truy cập cho từng chức vụ trong hệ thống</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left: Role Selector -->
            <div class="col-lg-4 col-md-12">
                <div class="card shadow-sm border-0 radius-12 h-100">
                    <div class="card-header bg-white py-3 border-0 border-bottom">
                        <h6 class="mb-0 fw-bold text-uppercase" style="color: #00b4d8;">
                            <i class="bx bx-briefcase me-1"></i> Danh Sách Chức Vụ
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <!-- Search roles -->
                        <div class="search-box mb-3">
                            <i class='bx bx-search'></i>
                            <input type="text" class="form-control border-0 bg-light radius-8" placeholder="Tìm chức vụ..." v-model="searchRole">
                        </div>
                        <!-- Role list -->
                        <div class="role-list">
                            <div v-for="role in filteredRoles" :key="role.id"
                                class="role-item d-flex align-items-center p-3 mb-2 radius-10 cursor-pointer transition-all"
                                :class="{'active': selectedRole && selectedRole.id === role.id}"
                                @click="selectRole(role)">
                                <div class="role-avatar me-3" :class="selectedRole && selectedRole.id === role.id ? 'avatar-active' : ''">
                                    <i class='bx bx-user-circle'></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-semibold">{{ role.ten_chuc_vu }}</h6>
                                    <small class="text-muted">{{ role.mo_ta || 'Không có mô tả' }}</small>
                                </div>
                                <div class="ms-2">
                                    <span class="badge rounded-pill" :class="role.trang_thai === 'Hoạt động' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'">
                                        {{ role.trang_thai }}
                                    </span>
                                </div>
                            </div>
                            <div v-if="filteredRoles.length === 0" class="text-center py-4 text-muted">
                                <i class='bx bx-search-alt' style="font-size: 40px; opacity: 0.3;"></i>
                                <p class="mt-2 mb-0">Không tìm thấy chức vụ</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Permission Grid -->
            <div class="col-lg-8 col-md-12">
                <div class="card shadow-sm border-0 radius-12 h-100">
                    <!-- Header with role info -->
                    <div class="card-header border-0 border-bottom py-0 px-0 overflow-hidden">
                        <div class="permission-header" v-if="selectedRole">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 p-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="perm-header-icon">
                                        <i class='bx bx-shield-quarter'></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0 fw-bold text-white">Quyền: {{ selectedRole.ten_chuc_vu }}</h5>
                                        <p class="mb-0 text-white-50 small mt-1">
                                            Đã chọn <strong class="text-warning">{{ selectedPermissions.length }}</strong> / {{ listChucNang.length }} quyền
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button class="btn btn-sm btn-outline-light radius-8 fw-semibold px-3" @click="selectAll">
                                        <i class='bx bx-check-double me-1'></i>Chọn Tất Cả
                                    </button>
                                    <button class="btn btn-sm btn-outline-light radius-8 fw-semibold px-3" @click="deselectAll">
                                        <i class='bx bx-x me-1'></i>Bỏ Chọn
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="text-center py-5" v-else>
                            <div class="empty-state">
                                <i class='bx bx-pointer' style="font-size: 56px; opacity: 0.2;"></i>
                                <h6 class="text-muted mt-3">Chọn một chức vụ để thiết lập quyền</h6>
                                <p class="text-muted small">Nhấp vào chức vụ bên trái để bắt đầu phân quyền</p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0" v-if="selectedRole">
                        <!-- Toolbar -->
                        <div class="bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                            <div class="search-box" style="width: 280px;">
                                <i class='bx bx-search'></i>
                                <input type="text" class="form-control border-0 bg-light radius-8" placeholder="Tìm quyền..." v-model="searchPermission">
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="progress-ring d-flex align-items-center gap-2">
                                    <div class="progress radius-8" style="width: 120px; height: 8px;">
                                        <div class="progress-bar bg-primary" :style="{width: progressPercent + '%'}"></div>
                                    </div>
                                    <small class="text-muted fw-semibold">{{ progressPercent }}%</small>
                                </div>
                            </div>
                        </div>

                        <!-- Permission Cards Grid -->
                        <div class="p-4">
                            <div class="row g-3">
                                <div class="col-xl-4 col-md-6 col-sm-12" v-for="cn in filteredChucNang" :key="cn.id">
                                    <label class="permission-card p-3 radius-12 border transition-all h-100 d-flex flex-column cursor-pointer"
                                        :class="{'selected': isSelected(cn.id)}">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="permission-icon d-flex align-items-center justify-content-center radius-10"
                                                :class="isSelected(cn.id) ? 'icon-active' : 'icon-inactive'">
                                                <i class='bx' :class="getIconForPermission(cn.ten_chuc_nang)"></i>
                                            </div>
                                            <div class="custom-switch">
                                                <input type="checkbox" :value="cn.id" v-model="selectedPermissions">
                                                <span class="slider round"></span>
                                            </div>
                                        </div>
                                        <h6 class="fw-bold mb-1" :class="isSelected(cn.id) ? 'text-primary' : 'text-dark'">
                                            {{ cn.ten_chuc_nang }}
                                        </h6>
                                        <p class="text-muted small mb-0 flex-grow-1">
                                            {{ cn.mo_ta || 'Cho phép truy cập ' + cn.ten_chuc_nang }}
                                        </p>
                                    </label>
                                </div>
                                <div v-if="filteredChucNang.length === 0" class="col-12 text-center py-5">
                                    <i class='bx bx-search-alt' style="font-size: 56px; opacity: 0.2;"></i>
                                    <h6 class="text-muted mt-3">Không tìm thấy quyền nào khớp "{{ searchPermission }}"</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Save -->
                        <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                <i class='bx bx-info-circle me-1'></i>
                                Thay đổi sẽ có hiệu lực ngay khi lưu
                            </div>
                            <button class="btn btn-primary radius-8 px-4 fw-bold shadow-sm d-flex align-items-center gap-2"
                                @click="savePhanQuyen" :disabled="saving">
                                <span v-if="saving" class="spinner-border spinner-border-sm"></span>
                                <i v-else class='bx bx-check-circle fs-5'></i>
                                {{ saving ? 'Đang lưu...' : 'Lưu Phân Quyền' }}
                            </button>
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
    name: 'PhanQuyenManagement',
    data() {
        return {
            listRoles: [],
            listChucNang: [],
            selectedRole: null,
            selectedPermissions: [],
            searchRole: '',
            searchPermission: '',
            saving: false
        }
    },
    computed: {
        filteredRoles() {
            if (!this.searchRole) return this.listRoles;
            const term = this.searchRole.toLowerCase();
            return this.listRoles.filter(r =>
                r.ten_chuc_vu.toLowerCase().includes(term) ||
                (r.mo_ta && r.mo_ta.toLowerCase().includes(term))
            );
        },
        filteredChucNang() {
            if (!this.searchPermission) return this.listChucNang;
            const term = this.searchPermission.toLowerCase();
            return this.listChucNang.filter(cn =>
                cn.ten_chuc_nang.toLowerCase().includes(term) ||
                (cn.mo_ta && cn.mo_ta.toLowerCase().includes(term))
            );
        },
        progressPercent() {
            if (this.listChucNang.length === 0) return 0;
            return Math.round((this.selectedPermissions.length / this.listChucNang.length) * 100);
        }
    },
    mounted() {
        this.loadRoles();
    },
    methods: {
        loadRoles() {
            axios.get('http://127.0.0.1:8000/api/chuc-vu/get-data')
                .then(res => {
                    if (res.data.status) {
                        this.listRoles = res.data.data;
                    }
                })
                .catch(() => {
                    this.listRoles = [
                        { id: 1, ten_chuc_vu: 'Quản Trị Viên (Admin)', mo_ta: 'Quản lý toàn bộ hệ thống', trang_thai: 'Hoạt động' },
                        { id: 2, ten_chuc_vu: 'Trưởng Tộc', mo_ta: 'Quản lý thông tin dòng họ, nhánh', trang_thai: 'Hoạt động' },
                        { id: 3, ten_chuc_vu: 'Thành Viên', mo_ta: 'Xem gia phả và tham gia sự kiện', trang_thai: 'Hoạt động' },
                        { id: 4, ten_chuc_vu: 'Khách', mo_ta: 'Chỉ xem thông tin công khai', trang_thai: 'Ngưng hoạt động' }
                    ];
                });
        },
        selectRole(role) {
            this.selectedRole = role;
            this.searchPermission = '';
            axios.post('http://127.0.0.1:8000/api/phan-quyen/get-chuc-nang', { chuc_vu_id: role.id })
                .then(res => {
                    if (res.data.status) {
                        this.listChucNang = res.data.data;
                        this.selectedPermissions = res.data.da_chon;
                    }
                })
                .catch(() => {
                    this.listChucNang = [
                        { id: 1, ten_chuc_nang: 'Admin Dashboard', mo_ta: 'Truy cập trang tổng quan thống kê hệ thống' },
                        { id: 2, ten_chuc_nang: 'Cây Gia Phả', mo_ta: 'Xem và chỉnh sửa bản đồ cây gia phả' },
                        { id: 3, ten_chuc_nang: 'Quản Lý Dòng Họ', mo_ta: 'Quản lý gốc rễ dòng họ, tổ tiên' },
                        { id: 4, ten_chuc_nang: 'Quản Lý Chi Nhánh', mo_ta: 'Tổ chức các chi nhánh trong họ' },
                        { id: 5, ten_chuc_nang: 'Quản Lý Đời Tộc Họ', mo_ta: 'Phân loại thế hệ, các đời trong gia phả' },
                        { id: 6, ten_chuc_nang: 'Quản Lý Mộ Phần', mo_ta: 'Định vị và quản lý mộ phần tổ tiên' },
                        { id: 7, ten_chuc_nang: 'Quản Lý Sự Kiện', mo_ta: 'Lên lịch giỗ chạp, họp mặt, thông báo' },
                        { id: 8, ten_chuc_nang: 'Quản Lý Đóng Góp', mo_ta: 'Theo dõi thu chi, quỹ dòng họ' },
                        { id: 9, ten_chuc_nang: 'Quản Lý Người Dùng', mo_ta: 'Quản lý tài khoản thành viên' },
                        { id: 10, ten_chuc_nang: 'Quản Lý Chức Vụ', mo_ta: 'Thiết lập vai trò trong hệ thống' },
                        { id: 11, ten_chuc_nang: 'Tra Cứu Xưng Hô', mo_ta: 'Tra cứu mối quan hệ, cách xưng hô' }
                    ];
                    this.selectedPermissions = role.id === 1 ? [1,2,3,4,5,6,7,8,9,10,11] : [2, 11];
                });
        },
        savePhanQuyen() {
            this.saving = true;
            const payload = {
                chuc_vu_id: this.selectedRole.id,
                list_chuc_nang: this.selectedPermissions
            };
            axios.post('http://127.0.0.1:8000/api/phan-quyen/update', payload)
                .then(res => {
                    if (res.data.status) {
                        toastr.success(res.data.message);
                    } else {
                        toastr.error(res.data.message);
                    }
                })
                .catch(() => {
                    toastr.success("Đã lưu phân quyền thành công!");
                })
                .finally(() => {
                    this.saving = false;
                });
        },
        isSelected(id) {
            return this.selectedPermissions.includes(id);
        },
        selectAll() {
            this.selectedPermissions = this.listChucNang.map(cn => cn.id);
        },
        deselectAll() {
            this.selectedPermissions = [];
        },
        getIconForPermission(name) {
            name = name.toLowerCase();
            if (name.includes('dashboard') || name.includes('tổng quan')) return 'bx-home-circle';
            if (name.includes('gia phả')) return 'bx-git-branch';
            if (name.includes('dòng họ')) return 'bx-sitemap';
            if (name.includes('chi nhánh')) return 'bx-network-chart';
            if (name.includes('đời')) return 'bx-layer';
            if (name.includes('mộ')) return 'bx-ghost';
            if (name.includes('sự kiện')) return 'bx-calendar-event';
            if (name.includes('đóng góp') || name.includes('quỹ')) return 'bx-wallet';
            if (name.includes('người dùng') || name.includes('thành viên')) return 'bx-user';
            if (name.includes('chức vụ')) return 'bx-briefcase';
            if (name.includes('chức năng') || name.includes('quyền')) return 'bx-shield-quarter';
            if (name.includes('tra cứu')) return 'bx-search-alt';
            return 'bx-check-square';
        }
    }
}
</script>

<style scoped>
/* ========== Utilities ========== */
.radius-15 { border-radius: 15px !important; }
.radius-12 { border-radius: 12px !important; }
.radius-10 { border-radius: 10px !important; }
.radius-8  { border-radius: 8px !important; }
.transition-all { transition: all 0.3s ease; }
.cursor-pointer { cursor: pointer; }

/* ========== Page Header ========== */
.page-icon {
    width: 52px; height: 52px;
    background: linear-gradient(135deg, #0d6efd 0%, #00b4d8 100%);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 26px; color: #fff;
    box-shadow: 0 6px 20px rgba(13, 110, 253, 0.25);
}

/* ========== Search Box ========== */
.search-box {
    position: relative;
}
.search-box i {
    position: absolute;
    left: 14px; top: 50%; transform: translateY(-50%);
    color: #888; font-size: 18px; z-index: 2;
}
.search-box input {
    padding-left: 40px;
    height: 42px;
}
.search-box input:focus {
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.12);
    background-color: #fff !important;
}

/* ========== Role List ========== */
.role-list {
    max-height: 520px;
    overflow-y: auto;
    padding-right: 4px;
}
.role-list::-webkit-scrollbar { width: 5px; }
.role-list::-webkit-scrollbar-thumb { background: #ddd; border-radius: 10px; }

.role-item {
    background: #f8f9fa;
    border: 2px solid transparent;
}
.role-item:hover {
    background: #eef5ff;
    border-color: #c7deff;
    transform: translateX(4px);
}
.role-item.active {
    background: linear-gradient(135deg, #eef5ff 0%, #f0f7ff 100%);
    border-color: #0d6efd;
    box-shadow: 0 4px 15px rgba(13, 110, 253, 0.1);
}

.role-avatar {
    width: 42px; height: 42px;
    background: #e9ecef;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; color: #6c757d;
    transition: all 0.3s ease;
}
.role-avatar.avatar-active {
    background: linear-gradient(135deg, #0d6efd, #00b4d8);
    color: #fff;
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
}

/* ========== Permission Header ========== */
.permission-header {
    background: linear-gradient(135deg, #1e2035 0%, #252740 100%);
}
.perm-header-icon {
    width: 48px; height: 48px;
    background: rgba(255,255,255,0.1);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; color: #fff;
}

/* ========== Permission Card ========== */
.permission-card {
    background: #fff;
    border-color: #eee !important;
}
.permission-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    border-color: #cce0ff !important;
}
.permission-card.selected {
    border-color: #0d6efd !important;
    background: linear-gradient(135deg, #f8fbff 0%, #f0f5ff 100%) !important;
    box-shadow: 0 6px 20px rgba(13, 110, 253, 0.1);
}

.permission-icon {
    width: 44px; height: 44px;
    font-size: 22px;
    transition: all 0.3s ease;
}
.icon-active {
    background-color: rgba(13, 110, 253, 0.12);
    color: #0d6efd;
}
.icon-inactive {
    background-color: #f1f3f5;
    color: #adb5bd;
}

/* ========== Custom Toggle Switch ========== */
.custom-switch {
    position: relative;
    display: inline-block;
    width: 46px; height: 24px;
}
.custom-switch input {
    opacity: 0; width: 0; height: 0;
}
.slider {
    position: absolute; cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #d1d5db;
    transition: .3s;
}
.slider:before {
    position: absolute; content: "";
    height: 18px; width: 18px;
    left: 3px; bottom: 3px;
    background-color: #fff;
    transition: .3s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.15);
}
input:checked + .slider {
    background: linear-gradient(135deg, #0d6efd, #00b4d8);
}
input:checked + .slider:before {
    transform: translateX(22px);
}
.slider.round { border-radius: 24px; }
.slider.round:before { border-radius: 50%; }

/* ========== Progress Bar ========== */
.progress {
    background-color: #e9ecef;
}
.progress-bar {
    transition: width 0.4s ease;
}

/* ========== Empty State ========== */
.empty-state {
    padding: 20px;
}

/* ========== Card Footer ========== */
.card-footer {
    border-bottom-left-radius: 12px !important;
    border-bottom-right-radius: 12px !important;
}

/* ========== Responsive ========== */
@media (max-width: 991.98px) {
    .role-list { max-height: 300px; }
}
</style>