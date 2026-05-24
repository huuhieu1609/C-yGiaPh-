<template>
    <div class="row">
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="card shadow-sm border-0 radius-10">
                <div class="card-header bg-white py-3 border-0 border-bottom">
                    <h5 class="mb-0 fw-bold text-uppercase" style="color: #00b4d8;">
                        <i class="bx bx-plus-circle me-1"></i> {{ isEditing ? 'Cập Nhật Chức Vụ' : 'Thêm Chức Vụ Mới' }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form @submit.prevent="saveData">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tên Chức Vụ</label>
                            <input type="text" class="form-control radius-8 border-2 shadow-none" placeholder="Nhập tên chức vụ" v-model="formData.ten_chuc_vu" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mô Tả</label>
                            <textarea class="form-control radius-8 border-2 shadow-none" rows="4" placeholder="Nhập mô tả..." v-model="formData.mo_ta"></textarea>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="button" class="btn btn-light radius-8 px-4" v-if="isEditing" @click="resetForm">Hủy</button>
                            <button type="submit" class="btn text-white radius-8 px-4 fw-bold shadow-sm" style="background-color: #008bf8; border-color: #008bf8;">
                                {{ isEditing ? 'Cập Nhật' : 'Thêm Mới' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-md-12">
            <div class="card shadow-sm border-0 radius-10 h-100">
                <div class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold text-uppercase" style="color: #333;">
                        <i class="bx bx-list-ul me-1"></i> Danh Sách Chức Vụ
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="text-center text-white" style="background-color: #008bf8;">
                                <tr>
                                    <th width="5%" class="py-3">#</th>
                                    <th width="25%" class="py-3">Tên Chức Vụ</th>
                                    <th width="35%" class="py-3">Mô Tả</th>
                                    <th width="10%" class="py-3">Trạng Thái</th>
                                    <th width="25%" class="py-3">Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in listData" :key="item.id">
                                    <td class="text-center fw-bold">{{ index + 1 }}</td>
                                    <td class="fw-semibold">{{ item.ten_chuc_vu }}</td>
                                    <td>{{ item.mo_ta || '---' }}</td>
                                    <td class="text-center">
                                        <button @click="changeStatus(item.id)" :class="item.trang_thai == 'Hoạt động' ? 'btn-success' : 'btn-danger'" class="btn btn-sm radius-8 w-100">
                                            {{ item.trang_thai }}
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-sm btn-info text-white radius-8 shadow-sm" @click="openPhanQuyen(item)" title="Phân Quyền">
                                                <i class="bx bx-shield-quarter m-0"></i> Phân Quyền
                                            </button>
                                            <button class="btn btn-sm btn-outline-primary radius-8" @click="editItem(item)" title="Sửa">
                                                <i class="bx bx-edit-alt m-0"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger radius-8" @click="deleteItem(item.id)" title="Xóa">
                                                <i class="bx bx-trash m-0"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="listData.length === 0">
                                    <td colspan="5" class="text-center py-4 text-muted">Không có dữ liệu chức vụ</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Phân Quyền Premium -->
        <div class="modal fade custom-modal" id="phanQuyenModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 radius-15 shadow-xl overflow-hidden">
                    <!-- Header -->
                    <div class="modal-header premium-header border-0">
                        <div class="d-flex align-items-center gap-3">
                            <div class="header-icon">
                                <i class='bx bx-shield-quarter'></i>
                            </div>
                            <div>
                                <h4 class="modal-title fw-bold text-white mb-0">PHÂN QUYỀN HỆ THỐNG</h4>
                                <p class="mb-0 text-white-50 mt-1">Đang thiết lập quyền cho chức vụ: <span class="badge bg-warning text-dark px-2 py-1 ms-1 rounded-pill fw-bold">{{ selectedRole.ten_chuc_vu }}</span></p>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <!-- Body -->
                    <div class="modal-body p-0 bg-light">
                        <!-- Toolbar -->
                        <div class="bg-white border-bottom p-3 d-flex justify-content-between align-items-center sticky-top shadow-sm z-index-1">
                            <div class="d-flex align-items-center gap-2">
                                <div class="search-box">
                                    <i class='bx bx-search'></i>
                                    <input type="text" class="form-control border-0 bg-light radius-8" placeholder="Tìm quyền..." v-model="searchPermission">
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary radius-8 fw-semibold btn-sm px-3" @click="selectAll">Chọn Tất Cả</button>
                                <button class="btn btn-outline-secondary radius-8 fw-semibold btn-sm px-3" @click="deselectAll">Bỏ Chọn Tất Cả</button>
                            </div>
                        </div>

                        <!-- Permissions Grid -->
                        <div class="p-4">
                            <div class="row g-4">
                                <div class="col-lg-4 col-md-6" v-for="cn in filteredChucNang" :key="cn.id">
                                    <label class="permission-card bg-white p-3 radius-12 border transition-all h-100 d-flex flex-column cursor-pointer" :class="{'selected': isSelected(cn.id)}">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="permission-icon d-flex align-items-center justify-content-center radius-10" :class="isSelected(cn.id) ? 'bg-primary-subtle text-primary' : 'bg-light text-secondary'">
                                                <i class='bx' :class="getIconForPermission(cn.ten_chuc_nang)"></i>
                                            </div>
                                            <div class="custom-switch">
                                                <input type="checkbox" :value="cn.id" v-model="selectedPermissions">
                                                <span class="slider round"></span>
                                            </div>
                                        </div>
                                        <h6 class="fw-bold mb-1" :class="isSelected(cn.id) ? 'text-primary' : 'text-dark'">{{ cn.ten_chuc_nang }}</h6>
                                        <p class="text-muted small mb-0 flex-grow-1">{{ cn.mo_ta || 'Cho phép truy cập và thao tác với ' + cn.ten_chuc_nang }}</p>
                                    </label>
                                </div>
                                <div v-if="filteredChucNang.length === 0" class="col-12 text-center py-5">
                                    <i class='bx bx-search-alt text-muted" style="font-size: 64px; opacity: 0.3;'></i>
                                    <h6 class="text-muted mt-3">Không tìm thấy quyền nào khớp với từ khóa "{{ searchPermission }}".</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="modal-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Đã chọn: <strong class="text-primary fs-6">{{ selectedPermissions.length }}</strong> / {{ listChucNang.length }} quyền
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-light radius-8 px-4 fw-semibold border" data-bs-dismiss="modal">Hủy</button>
                            <button type="button" class="btn btn-primary radius-8 px-5 fw-bold shadow-sm d-flex align-items-center gap-2" @click="savePhanQuyen">
                                <i class='bx bx-check-circle fs-5'></i> Lưu Phân Quyền
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
    name: 'ChucVuManagement',
    data() {
        return {
            listData: [],
            formData: {
                id: null,
                ten_chuc_vu: '',
                mo_ta: '',
                trang_thai: 'Hoạt động'
            },
            isEditing: false,
            // Phan Quyen
            selectedRole: {},
            listChucNang: [],
            selectedPermissions: [],
            modalPhanQuyen: null,
            searchPermission: ''
        }
    },
    computed: {
        filteredChucNang() {
            if (!this.searchPermission) return this.listChucNang;
            const term = this.searchPermission.toLowerCase();
            return this.listChucNang.filter(cn => 
                cn.ten_chuc_nang.toLowerCase().includes(term) || 
                (cn.mo_ta && cn.mo_ta.toLowerCase().includes(term))
            );
        }
    },
    mounted() {
        this.loadData();
        if (window.bootstrap) {
            this.modalPhanQuyen = new window.bootstrap.Modal(document.getElementById('phanQuyenModal'));
        }
    },
    methods: {
        loadData() {
            axios.get('http://127.0.0.1:8000/api/chuc-vu/get-data')
                .then(res => {
                    if (res.data.status) {
                        this.listData = res.data.data;
                    }
                })
                .catch(() => {
                    // MOCK DATA FOR DEMO IF BACKEND IS DOWN
                    this.listData = [
                        { id: 1, ten_chuc_vu: 'Quản Trị Viên (Admin)', mo_ta: 'Quản lý toàn bộ hệ thống', trang_thai: 'Hoạt động' },
                        { id: 2, ten_chuc_vu: 'Trưởng Tộc', mo_ta: 'Quản lý thông tin dòng họ, nhánh', trang_thai: 'Hoạt động' },
                        { id: 3, ten_chuc_vu: 'Thành Viên', mo_ta: 'Xem gia phả và tham gia sự kiện', trang_thai: 'Hoạt động' }
                    ];
                });
        },
        saveData() {
            const url = this.isEditing 
                ? 'http://127.0.0.1:8000/api/chuc-vu/update'
                : 'http://127.0.0.1:8000/api/chuc-vu/create';
            
            axios.post(url, this.formData)
                .then(res => {
                    if (res.data.status) {
                        toastr.success(res.data.message);
                        this.loadData();
                        this.resetForm();
                    } else {
                        toastr.error(res.data.message);
                    }
                })
                .catch(() => toastr.error("Không thể kết nối đến máy chủ"));
        },
        editItem(item) {
            this.isEditing = true;
            this.formData = { ...item };
        },
        deleteItem(id) {
            if (confirm('Bạn có chắc chắn muốn xóa?')) {
                axios.post('http://127.0.0.1:8000/api/chuc-vu/delete', { id })
                    .then(res => {
                        if (res.data.status) {
                            toastr.success(res.data.message);
                            this.loadData();
                        }
                    });
            }
        },
        changeStatus(id) {
            axios.post('http://127.0.0.1:8000/api/chuc-vu/status', { id })
                .then(res => {
                    if (res.data.status) {
                        toastr.success(res.data.message);
                        this.loadData();
                    }
                });
        },
        resetForm() {
            this.isEditing = false;
            this.formData = {
                id: null,
                ten_chuc_vu: '',
                mo_ta: '',
                trang_thai: 'Hoạt động'
            };
        },
        // Phan Quyen methods
        openPhanQuyen(item) {
            this.selectedRole = item;
            this.searchPermission = '';
            axios.post('http://127.0.0.1:8000/api/phan-quyen/get-chuc-nang', { chuc_vu_id: item.id })
                .then(res => {
                    if (res.data.status) {
                        this.listChucNang = res.data.data;
                        this.selectedPermissions = res.data.da_chon;
                        this.modalPhanQuyen.show();
                    }
                })
                .catch(() => {
                    // MOCK DATA FOR DEMO IF BACKEND IS DOWN
                    this.listChucNang = [
                        { id: 1, ten_chuc_nang: 'Admin Dashboard', mo_ta: 'Truy cập trang tổng quan thống kê hệ thống' },
                        { id: 2, ten_chuc_nang: 'Cây Gia Phả', mo_ta: 'Xem và chỉnh sửa bản đồ cây gia phả gia đình' },
                        { id: 3, ten_chuc_nang: 'Quản Lý Dòng Họ', mo_ta: 'Quản lý gốc rễ dòng họ, tổ tiên' },
                        { id: 4, ten_chuc_nang: 'Quản Lý Chi Nhánh', mo_ta: 'Tổ chức các chi nhánh, phân nhánh trong họ' },
                        { id: 5, ten_chuc_nang: 'Quản Lý Đời Tộc Họ', mo_ta: 'Phân loại các thế hệ, các đời trong gia phả' },
                        { id: 6, ten_chuc_nang: 'Quản Lý Mộ Phần', mo_ta: 'Sắp xếp, định vị và quản lý mộ phần tổ tiên' },
                        { id: 7, ten_chuc_nang: 'Quản Lý Sự Kiện', mo_ta: 'Lên lịch giỗ chạp, họp mặt, thông báo' },
                        { id: 8, ten_chuc_nang: 'Quản Lý Đóng Góp', mo_ta: 'Theo dõi thu chi, đóng góp quỹ dòng họ' },
                        { id: 9, ten_chuc_nang: 'Quản Lý Người Dùng', mo_ta: 'Quản lý tài khoản đăng nhập của thành viên' },
                        { id: 10, ten_chuc_nang: 'Quản Lý Chức Vụ', mo_ta: 'Thiết lập vai trò, quyền hạn trong hệ thống' },
                        { id: 11, ten_chuc_nang: 'Tra Cứu Xưng Hô', mo_ta: 'Công cụ tra cứu mối quan hệ, cách xưng hô' }
                    ];
                    this.selectedPermissions = item.id === 1 ? [1,2,3,4,5,6,7,8,9,10,11] : [2, 11];
                    this.modalPhanQuyen.show();
                });
        },
        savePhanQuyen() {
            const payload = {
                chuc_vu_id: this.selectedRole.id,
                list_chuc_nang: this.selectedPermissions
            };
            axios.post('http://127.0.0.1:8000/api/phan-quyen/update', payload)
                .then(res => {
                    if (res.data.status) {
                        toastr.success(res.data.message);
                        this.modalPhanQuyen.hide();
                    } else {
                        toastr.error(res.data.message);
                    }
                })
                .catch(() => {
                    toastr.success("Đã lưu phân quyền thành công! (MOCK)");
                    this.modalPhanQuyen.hide();
                });
        },
        isSelected(id) {
            return this.selectedPermissions.includes(id);
        },
        selectAll() {
            this.selectedPermissions = this.filteredChucNang.map(cn => cn.id);
        },
        deselectAll() {
            this.selectedPermissions = [];
        },
        getIconForPermission(name) {
            name = name.toLowerCase();
            if(name.includes('dashboard') || name.includes('tổng quan')) return 'bx-home-circle';
            if(name.includes('gia phả')) return 'bx-git-branch';
            if(name.includes('dòng họ')) return 'bx-sitemap';
            if(name.includes('chi nhánh')) return 'bx-network-chart';
            if(name.includes('đời')) return 'bx-layer';
            if(name.includes('nhà thờ')) return 'bx-building-house';
            if(name.includes('mộ')) return 'bx-ghost';
            if(name.includes('sự kiện')) return 'bx-calendar-event';
            if(name.includes('đóng góp') || name.includes('quỹ')) return 'bx-wallet';
            if(name.includes('người dùng') || name.includes('thành viên')) return 'bx-user';
            if(name.includes('chức vụ')) return 'bx-briefcase';
            if(name.includes('chức năng') || name.includes('quyền')) return 'bx-shield-quarter';
            if(name.includes('nhật ký')) return 'bx-history';
            if(name.includes('tra cứu')) return 'bx-search-alt';
            return 'bx-check-square';
        }
    }
}
</script>

<style scoped>
/* Premium UI Utilities */
.radius-15 { border-radius: 15px !important; }
.radius-12 { border-radius: 12px !important; }
.radius-10 { border-radius: 10px !important; }
.radius-8 { border-radius: 8px !important; }
.shadow-xl { box-shadow: 0 20px 50px rgba(0,0,0,0.15) !important; }
.z-index-1 { z-index: 1; }
.transition-all { transition: all 0.25s ease; }
.cursor-pointer { cursor: pointer; }

/* Premium Header */
.premium-header {
    background: linear-gradient(135deg, #1e2035 0%, #252740 100%);
    padding: 24px 28px;
}
.header-icon {
    width: 52px; height: 52px;
    background: rgba(255,255,255,0.1);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 26px; color: #fff;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

/* Search Box */
.search-box {
    position: relative;
    width: 280px;
}
.search-box i {
    position: absolute;
    left: 14px; top: 50%; transform: translateY(-50%);
    color: #888; font-size: 18px;
}
.search-box input {
    padding-left: 40px;
    height: 42px;
}
.search-box input:focus {
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
}

/* Permission Card */
.permission-card {
    border-color: #eee !important;
}
.permission-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    border-color: #dee2e6 !important;
}
.permission-card.selected {
    border-color: #0d6efd !important;
    background-color: #f8fbff !important;
    box-shadow: 0 6px 20px rgba(13, 110, 253, 0.1);
}
.permission-icon {
    width: 44px; height: 44px;
    font-size: 22px;
}

/* Custom Switch Toggle */
.custom-switch {
    position: relative;
    display: inline-block;
    width: 48px;
    height: 26px;
}
.custom-switch input { 
    opacity: 0;
    width: 0;
    height: 0;
}
.slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #d1d5db;
    transition: .3s;
}
.slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .3s;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}
input:checked + .slider {
    background-color: #0d6efd;
}
input:checked + .slider:before {
    transform: translateX(22px);
}
.slider.round {
    border-radius: 26px;
}
.slider.round:before {
    border-radius: 50%;
}
</style>

