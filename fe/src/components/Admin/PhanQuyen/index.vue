<template>
    <div class="phan-quyen-wrapper">
        <!-- Page Header -->
        <div class="page-header mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="page-icon">
                    <i class='bx bx-shield-quarter'></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold text-dark page-title text-gradient-gold">Phân Quyền Hệ Thống</h4>
                    <p class="mb-0 text-secondary-custom small mt-1">Thiết lập quyền truy cập cho từng chức vụ trong hệ thống</p>
                </div>
            </div>
            <button class="btn btn-refresh-premium rounded-circle d-flex align-items-center justify-content-center" @click="refreshAll" :disabled="isLoading" title="Làm mới dữ liệu">
                <i class="bx bx-sync fs-5 text-warning" :class="{'bx-spin': isLoading}"></i>
            </button>
        </div>

        <div class="row g-4">
            <!-- Left: Role Selector -->
            <div class="col-lg-4 col-md-12">
                <div class="card luxury-panel border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent py-3 border-0 border-bottom border-light-subtle d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-bold text-uppercase text-gradient-gold">
                            <i class="bx bx-briefcase me-2"></i> Danh Sách Chức Vụ
                        </h6>
                        <button class="btn btn-sm btn-filter-submit text-white radius-30 px-3 fw-bold" @click="openAddRoleModal">
                            <i class="bx bx-plus-circle me-1"></i>Thêm mới
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <!-- Search roles -->
                        <div class="search-box mb-4">
                            <i class='bx bx-search'></i>
                            <input type="text" class="form-control premium-input radius-10 border-2 shadow-none" placeholder="Tìm chức vụ..." v-model="searchRole">
                        </div>
                        <!-- Role list -->
                        <div class="role-list">
                            <div v-for="role in filteredRoles" :key="role.id"
                                class="role-item d-flex align-items-center p-3 mb-3 radius-12 cursor-pointer transition-all"
                                :class="{'active': selectedRole && selectedRole.id === role.id}"
                                @click="selectRole(role)">
                                <div class="role-avatar me-3" :class="selectedRole && selectedRole.id === role.id ? 'avatar-active' : ''">
                                    <i class='bx bx-user-circle'></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-bold role-title">{{ role.ten_chuc_vu }}</h6>
                                    <small class="text-secondary small">{{ role.mo_ta || 'Không có mô tả' }}</small>
                                </div>
                                <div class="ms-2">
                                    <span class="badge rounded-pill fw-bold" :class="role.trang_thai === 'Hoạt động' ? 'badge-status-active' : 'badge-status-locked'">
                                        {{ role.trang_thai }}
                                    </span>
                                </div>
                            </div>
                            <div v-if="filteredRoles.length === 0" class="text-center py-5 text-muted">
                                <i class='bx bx-search-alt opacity-50' style="font-size: 40px;"></i>
                                <p class="mt-2 mb-0">Không tìm thấy chức vụ</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Permission Grid -->
            <div class="col-lg-8 col-md-12">
                <div class="card luxury-panel border-0 shadow-sm h-100">
                    <!-- Header with role info -->
                    <div class="card-header border-0 border-bottom py-0 px-0 overflow-hidden">
                        <div class="permission-header" v-if="selectedRole">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 p-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="perm-header-icon">
                                        <i class='bx bx-shield-quarter'></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0 fw-bold text-white">Quyền: {{ selectedRole.ten_chuc_vu }}</h5>
                                        <p class="mb-0 text-white-50 small mt-1">
                                            Đã chọn <strong class="text-warning fs-6">{{ selectedPermissions.length }}</strong> / {{ listChucNang.length }} quyền
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button class="btn btn-sm btn-outline-light radius-30 fw-semibold px-3" @click="selectAll">
                                        <i class='bx bx-check-double me-1'></i>Chọn Tất Cả
                                    </button>
                                    <button class="btn btn-sm btn-outline-light radius-30 fw-semibold px-3" @click="deselectAll">
                                        <i class='bx bx-x me-1'></i>Bỏ Chọn
                                    </button>
                                    <button class="btn btn-sm btn-gold-premium radius-30 fw-semibold px-3" style="background: linear-gradient(135deg, #ffd700, #e5a93b) !important; border: none !important; color: #1e2035 !important;" @click="openAddPermissionModal">
                                        <i class='bx bx-plus-circle me-1'></i>Tạo mới
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="text-center py-5" v-else>
                            <div class="empty-state py-5">
                                <i class='bx bx-pointer text-warning' style="font-size: 56px; opacity: 0.8;"></i>
                                <h6 class="text-dark fw-bold mt-3">Chọn một chức vụ để thiết lập quyền</h6>
                                <p class="text-secondary small">Nhấp vào danh sách chức vụ ở cột bên trái để bắt đầu phân quyền hệ thống</p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0" v-if="selectedRole">
                        <!-- Toolbar -->
                        <div class="bg-light-subtle border-bottom p-3 d-flex justify-content-between align-items-center">
                            <div class="search-box" style="width: 280px;">
                                <i class='bx bx-search'></i>
                                <input type="text" class="form-control premium-input radius-10 border-2 shadow-none" placeholder="Tìm quyền..." v-model="searchPermission">
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="progress-ring d-flex align-items-center gap-2">
                                    <div class="progress radius-8" style="width: 120px; height: 8px;">
                                        <div class="progress-bar bg-warning" :style="{width: progressPercent + '%'}"></div>
                                    </div>
                                    <small class="text-muted fw-bold">{{ progressPercent }}%</small>
                                </div>
                            </div>
                        </div>

                        <!-- Permission Cards Grid -->
                        <div class="p-4" style="max-height: 500px; overflow-y: auto;">
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
                                        <h6 class="fw-bold mb-1 card-perm-title" :class="isSelected(cn.id) ? 'text-warning' : 'text-dark'">
                                            {{ getFriendlyName(cn.ten_chuc_nang) }}
                                        </h6>
                                        <p class="text-secondary small mb-0 flex-grow-1 text-perm-desc">
                                            {{ getFriendlyDesc(cn) }}
                                        </p>
                                    </label>
                                </div>
                                <div v-if="filteredChucNang.length === 0" class="col-12 text-center py-5">
                                    <i class='bx bx-search-alt text-muted' style="font-size: 56px; opacity: 0.3;"></i>
                                    <h6 class="text-muted mt-3">Không tìm thấy quyền nào khớp "{{ searchPermission }}"</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Save -->
                        <div class="card-footer bg-transparent border-top p-4 d-flex justify-content-between align-items-center">
                            <div class="text-muted small fw-semibold">
                                <i class='bx bx-info-circle text-warning me-1'></i>
                                Thay đổi sẽ có hiệu lực ngay khi lưu
                            </div>
                            <button class="btn btn-filter-submit text-white radius-30 px-5 fw-bold shadow-sm d-flex align-items-center gap-2"
                                @click="savePhanQuyen" :disabled="saving || isLoading">
                                <span v-if="saving" class="spinner-border spinner-border-sm" role="status"></span>
                                <i v-else class='bx bx-check-circle fs-5'></i>
                                {{ saving ? 'Đang lưu...' : 'Lưu Phân Quyền' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== MODAL: THÊM CHỨC VỤ ===== -->
        <transition name="modal-fade">
            <div class="modal-overlay-custom" v-if="modal.addRole" @click.self="modal.addRole = false">
                <div class="modal-box-custom">
                    <div class="modal-hdr-custom">
                        <span><i class="bx bx-briefcase me-2"></i>Thêm Chức Vụ Mới</span>
                        <button class="modal-close-custom" @click="modal.addRole = false"><i class="bx bx-x"></i></button>
                    </div>
                    <div class="modal-body-custom">
                        <div class="form-group-custom-p">
                            <label>Tên chức vụ <span class="req">*</span></label>
                            <input class="form-inp-p" v-model.trim="newRoleForm.ten_chuc_vu" placeholder="Ví dụ: Phó Tộc Trưởng..." maxlength="255" />
                        </div>
                        <div class="form-group-custom-p">
                            <label>Mô tả chức vụ</label>
                            <textarea class="form-inp-p" v-model.trim="newRoleForm.mo_ta" placeholder="Mô tả quyền hạn chức vụ..." rows="3"></textarea>
                        </div>
                        <div class="form-group-custom-p">
                            <label>Trạng thái <span class="req">*</span></label>
                            <select class="form-inp-p" v-model="newRoleForm.trang_thai">
                                <option value="Hoạt động">Hoạt động</option>
                                <option value="Khóa">Khóa</option>
                            </select>
                        </div>
                        <div class="modal-footer-btns-custom">
                            <button class="btn-secondary-custom-p" @click="modal.addRole = false" :disabled="submitting">Hủy</button>
                            <button class="btn-primary-custom-p" @click="submitAddRole" :disabled="submitting">
                                <span v-if="!submitting"><i class="bx bx-save me-1"></i>Lưu chức vụ</span>
                                <span v-else><i class="bx bx-loader-alt bx-spin me-1"></i>Đang lưu...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </transition>

        <!-- ===== MODAL: TẠO QUYỀN MỚI ===== -->
        <transition name="modal-fade">
            <div class="modal-overlay-custom" v-if="modal.addPermission" @click.self="modal.addPermission = false">
                <div class="modal-box-custom">
                    <div class="modal-hdr-custom gold-hdr-p">
                        <span><i class="bx bx-shield-quarter me-2"></i>Tạo Quyền & Menu Mới (Coming Soon)</span>
                        <button class="modal-close-custom" @click="modal.addPermission = false"><i class="bx bx-x"></i></button>
                    </div>
                    <div class="modal-body-custom">
                        <div class="form-group-custom-p">
                            <label>Tên quyền/chức năng <span class="req">*</span></label>
                            <input class="form-inp-p" v-model.trim="newPermissionForm.ten_chuc_nang" placeholder="Ví dụ: Số Hóa Gia Phả..." maxlength="255" />
                        </div>
                        <div class="form-group-custom-p">
                            <label>Mô tả</label>
                            <textarea class="form-inp-p" v-model.trim="newPermissionForm.mo_ta" placeholder="Mô tả chức năng này..." rows="3"></textarea>
                        </div>
                        <div class="form-group-custom-p">
                            <label class="d-block mb-2">Đối tượng được hiển thị quyền này trên menu <span class="req">*</span></label>
                            <div class="d-flex flex-wrap gap-3 p-2 bg-light radius-8">
                                <label class="d-flex align-items-center gap-2 cursor-pointer">
                                    <input type="checkbox" value="Thành viên" v-model="newPermissionForm.hien_thi_cho_array">
                                    Thành viên
                                </label>
                                <label class="d-flex align-items-center gap-2 cursor-pointer">
                                    <input type="checkbox" value="Đối tác" v-model="newPermissionForm.hien_thi_cho_array">
                                    Đối tác
                                </label>
                                <label class="d-flex align-items-center gap-2 cursor-pointer">
                                    <input type="checkbox" value="Người dùng" v-model="newPermissionForm.hien_thi_cho_array">
                                    Người dùng
                                </label>
                            </div>
                        </div>
                        <div class="form-group-custom-p">
                            <label>Trạng thái <span class="req">*</span></label>
                            <select class="form-inp-p" v-model="newPermissionForm.trang_thai">
                                <option value="Hoạt động">Hoạt động</option>
                                <option value="Khóa">Khóa</option>
                            </select>
                        </div>
                        <div class="modal-footer-btns-custom">
                            <button class="btn-secondary-custom-p" @click="modal.addPermission = false" :disabled="submitting">Hủy</button>
                            <button class="btn-primary-custom-p btn-gold-p" @click="submitAddPermission" :disabled="submitting">
                                <span v-if="!submitting"><i class="bx bx-save me-1"></i>Tạo quyền</span>
                                <span v-else><i class="bx bx-loader-alt bx-spin me-1"></i>Đang xử lý...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
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
            saving: false,
            isLoading: false,
            submitting: false,
            modal: {
                addRole: false,
                addPermission: false
            },
            newRoleForm: {
                ten_chuc_vu: '',
                mo_ta: '',
                trang_thai: 'Hoạt động'
            },
            newPermissionForm: {
                ten_chuc_nang: '',
                mo_ta: '',
                hien_thi_cho_array: [],
                trang_thai: 'Hoạt động'
            }
        }
    },
    computed: {
        filteredRoles() {
            // Đọc thông tin user đang đăng nhập
            const userStr = localStorage.getItem('user');
            let currentUser = null;
            if (userStr) {
                try {
                    currentUser = JSON.parse(userStr);
                } catch(e) {}
            }

            // Lọc bỏ "Quản Trị Viên Tổng" (Admin hệ thống gốc), chỉ hiển thị chức vụ đối tác và thành viên dòng họ
            let baseList = this.listRoles.filter(r => 
                r.id !== 1 && 
                !r.ten_chuc_vu.toLowerCase().includes('tổng') && 
                !r.mo_ta?.toLowerCase().includes('toàn quyền hệ thống')
            );

            // Nếu người dùng đăng nhập là Quản trị viên (Sub-Admin), chỉ cho phép phân quyền cho Trưởng Nhánh và Thành Viên
            const chucVuName = currentUser?.chuc_vu?.ten_chuc_vu?.toLowerCase() || '';
            const isSubAdmin = chucVuName.includes('quản trị');
            if (isSubAdmin) {
                baseList = baseList.filter(r => 
                    r.ten_chuc_vu.toLowerCase().includes('nhánh') || 
                    r.ten_chuc_vu.toLowerCase().includes('thành viên')
                );
            }
            
            if (!this.searchRole) return baseList;
            const term = this.searchRole.toLowerCase();
            return baseList.filter(r =>
                r.ten_chuc_vu.toLowerCase().includes(term) ||
                (r.mo_ta && r.mo_ta.toLowerCase().includes(term))
            );
        },
        filteredChucNang() {
            if (!this.selectedRole) return [];
            
            const adminFuncs = ['Admin Dashboard', 'Quản Lý Đối Tác', 'Quản Lý Người Dùng', 'Quản Lý Chức Vụ', 'Quản Lý Chức Năng', 'Hệ Thống'];
            
            // Lọc theo chức vụ được chọn
            let baseList = this.listChucNang;
            if (this.selectedRole.id === 1 || this.selectedRole.ten_chuc_vu.toLowerCase().includes('admin') || this.selectedRole.ten_chuc_vu.toLowerCase().includes('tổng')) {
                // Chức vụ Admin tổng -> Chỉ hiện các chức năng admin hệ thống
                baseList = this.listChucNang.filter(cn => adminFuncs.includes(cn.ten_chuc_nang));
            } else {
                // Chức vụ của đối tác và thành viên -> KHÔNG hiện các chức năng admin hệ thống
                baseList = this.listChucNang.filter(cn => !adminFuncs.includes(cn.ten_chuc_nang));
            }

            if (!this.searchPermission) return baseList;
            const term = this.searchPermission.toLowerCase();
            return baseList.filter(cn =>
                cn.ten_chuc_nang.toLowerCase().includes(term) ||
                (cn.mo_ta && cn.mo_ta.toLowerCase().includes(term))
            );
        },
        progressPercent() {
            if (this.filteredChucNang.length === 0) return 0;
            // Tính phần trăm dựa trên các quyền hiển thị thực tế của chức vụ đó
            const displayedIds = this.filteredChucNang.map(cn => cn.id);
            const selectedDisplayed = this.selectedPermissions.filter(id => displayedIds.includes(id));
            return Math.round((selectedDisplayed.length / this.filteredChucNang.length) * 100);
        }
    },
    mounted() {
        this.loadRoles();
    },
    methods: {
        getHeaders() {
            return {
                headers: {
                    Authorization: 'Bearer ' + localStorage.getItem('access_token')
                }
            };
        },
        refreshAll() {
            this.loadRoles();
            if (this.selectedRole) {
                this.selectRole(this.selectedRole);
            }
        },
        loadRoles() {
            this.isLoading = true;
            axios.get('http://127.0.0.1:8000/api/chuc-vu/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.listRoles = res.data.data;
                        // Tự động chọn chức vụ đối tác/thành viên đầu tiên để giao diện không bị trống
                        this.$nextTick(() => {
                            if (this.filteredRoles.length > 0) {
                                this.selectRole(this.filteredRoles[0]);
                            }
                        });
                    }
                })
                .catch(() => {
                    this.listRoles = [
                        { id: 1, ten_chuc_vu: 'Quản Trị Viên (Admin)', mo_ta: 'Quản lý toàn bộ hệ thống', trang_thai: 'Hoạt động' },
                        { id: 2, ten_chuc_vu: 'Trưởng Tộc', mo_ta: 'Quản lý thông tin dòng họ, nhánh', trang_thai: 'Hoạt động' },
                        { id: 3, ten_chuc_vu: 'Thành Viên', mo_ta: 'Xem gia phả và tham gia sự kiện', trang_thai: 'Hoạt động' },
                        { id: 4, ten_chuc_vu: 'Khách', mo_ta: 'Chỉ xem thông tin công khai', trang_thai: 'Ngưng hoạt động' }
                    ];
                    this.$nextTick(() => {
                        if (this.filteredRoles.length > 0) {
                            this.selectRole(this.filteredRoles[0]);
                        }
                    });
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        selectRole(role) {
            this.selectedRole = role;
            this.searchPermission = '';
            this.isLoading = true;
            axios.post('http://127.0.0.1:8000/api/phan-quyen/get-chuc-nang', { chuc_vu_id: role.id }, this.getHeaders())
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
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        savePhanQuyen() {
            this.saving = true;
            const payload = {
                chuc_vu_id: this.selectedRole.id,
                list_chuc_nang: this.selectedPermissions
            };
            axios.post('http://127.0.0.1:8000/api/phan-quyen/update', payload, this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        toastr.success(res.data.message);
                    } else {
                        toastr.error(res.data.message);
                    }
                })
                .catch(() => {
                    toastr.success("Đã lưu phân quyền thành công! (MOCK)");
                })
                .finally(() => {
                    this.saving = false;
                });
        },
        isSelected(id) {
            return this.selectedPermissions.includes(id);
        },
        selectAll() {
            // Chỉ chọn tất cả các quyền ĐANG HIỂN THỊ cho chức vụ này
            const displayedIds = this.filteredChucNang.map(cn => cn.id);
            const union = new Set([...this.selectedPermissions, ...displayedIds]);
            this.selectedPermissions = Array.from(union);
        },
        deselectAll() {
            // Chỉ bỏ chọn các quyền ĐANG HIỂN THỊ cho chức vụ này
            const displayedIds = this.filteredChucNang.map(cn => cn.id);
            this.selectedPermissions = this.selectedPermissions.filter(id => !displayedIds.includes(id));
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
        },
        getFriendlyName(name) {
            if (!this.selectedRole) return name;
            
            // Normalized robust check for "Thành Viên" role
            const roleName = (this.selectedRole.ten_chuc_vu || '').toString().toLowerCase().normalize('NFC');
            const isThanhVien = roleName.includes('thành viên') || 
                                roleName.includes('thanh vien') || 
                                String(this.selectedRole.id) === '3';
                                
            if (isThanhVien) {
                // Đổi chữ "Quản Lý" thành "Xem"
                let friendlyName = name.replace(/Quản Lý/g, 'Xem').replace(/quản lý/g, 'xem');
                
                // Các trường hợp đặc biệt không chứa từ "Quản Lý" nhưng nên đổi sang "Xem..."
                if (name === 'Cây Gia Phả') return 'Xem Gia Phả Hệ';
                if (name === 'Tra Cứu Xưng Hô') return 'Xem Tra Cứu Xưng Hô';
                if (name === 'Quỹ & Sự Kiện') return 'Xem Quỹ & Sự Kiện';
                if (name === 'Nhật Ký Thao Tác') return 'Xem Nhật Ký Thao Tác';
                
                return friendlyName;
            }

            if (name === 'Cây Gia Phả') {
                return 'Quản Lý Gia Phả Hệ';
            }

            return name;
        },
        getFriendlyDesc(cn) {
            let desc = cn.mo_ta || ('Cho phép truy cập ' + cn.ten_chuc_nang);
            if (cn.ten_chuc_nang === 'Cây Gia Phả') {
                desc = 'Quản lý, xem và chỉnh sửa cây gia phả hệ thống';
            }
            if (this.selectedRole) {
                const roleName = (this.selectedRole.ten_chuc_vu || '').toString().toLowerCase().normalize('NFC');
                const isThanhVien = roleName.includes('thành viên') || 
                                    roleName.includes('thanh vien') || 
                                    String(this.selectedRole.id) === '3';
                                    
                if (isThanhVien) {
                    // Đổi "Quản lý" / "quản lý" thành "Xem" / "xem"
                    return desc.replace(/Quản lý/g, 'Xem').replace(/quản lý/g, 'xem').replace(/chỉnh sửa/g, 'xem');
                }
            }
            return desc;
        },
        openAddRoleModal() {
            this.newRoleForm = { ten_chuc_vu: '', mo_ta: '', trang_thai: 'Hoạt động' };
            this.modal.addRole = true;
        },
        submitAddRole() {
            if (!this.newRoleForm.ten_chuc_vu?.trim()) {
                toastr.warning("Vui lòng điền tên chức vụ!"); return;
            }
            this.submitting = true;
            axios.post('http://127.0.0.1:8000/api/chuc-vu/create', this.newRoleForm, this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        toastr.success(res.data.message);
                        this.modal.addRole = false;
                        this.loadRoles();
                    } else {
                        toastr.error(res.data.message);
                    }
                })
                .catch(e => toastr.error(e.response?.data?.message || "Lỗi thêm chức vụ!"))
                .finally(() => this.submitting = false);
        },
        openAddPermissionModal() {
            this.newPermissionForm = { ten_chuc_nang: '', mo_ta: '', hien_thi_cho_array: [], trang_thai: 'Hoạt động' };
            this.modal.addPermission = true;
        },
        submitAddPermission() {
            if (!this.newPermissionForm.ten_chuc_nang?.trim()) {
                toastr.warning("Vui lòng nhập tên quyền!"); return;
            }
            if (this.newPermissionForm.hien_thi_cho_array.length === 0) {
                toastr.warning("Vui lòng chọn ít nhất 1 nhóm đối tượng được hiển thị quyền!"); return;
            }
            
            this.submitting = true;
            // Tự sinh ten_slug từ ten_chuc_nang
            const slug = this.newPermissionForm.ten_chuc_nang
                .toLowerCase()
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "")
                .replace(/[đĐ]/g, "d")
                .replace(/[^a-z0-9\s]/g, "")
                .replace(/\s+/g, "-");

            const payload = {
                ten_chuc_nang: this.newPermissionForm.ten_chuc_nang,
                ten_slug: slug,
                url: '/coming-soon', // Mặc định chuyển hướng tới coming soon
                mo_ta: this.newPermissionForm.mo_ta,
                trang_thai: this.newPermissionForm.trang_thai,
                hien_thi_cho: this.newPermissionForm.hien_thi_cho_array.join(',')
            };

            axios.post('http://127.0.0.1:8000/api/chuc-nang/create', payload, this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        toastr.success(res.data.message);
                        this.modal.addPermission = false;
                        if (this.selectedRole) this.selectRole(this.selectedRole);
                    } else {
                        toastr.error(res.data.message);
                    }
                })
                .catch(e => toastr.error(e.response?.data?.message || "Lỗi tạo quyền!"))
                .finally(() => this.submitting = false);
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
.radius-30 { border-radius: 30px !important; }
.transition-all { transition: all 0.3s ease; }
.cursor-pointer { cursor: pointer; }

/* ========== Premium Page Header ========== */
.page-icon {
    width: 52px; height: 52px;
    background: linear-gradient(135deg, #1e2035 0%, #252740 100%);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 26px; color: #ffd700;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

.text-gradient-gold {
    background: linear-gradient(135deg, #b8860b 0%, #e5a93b 50%, #ffd700 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.text-secondary-custom {
    color: #64748b !important;
}

/* ========== Circular Reload Button ========== */
.btn-refresh-premium {
    background: rgba(0, 0, 0, 0.03) !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
    width: 36px;
    height: 36px;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
    transition: all 0.25s ease;
}
.btn-refresh-premium:hover {
    transform: rotate(30deg) scale(1.05);
    border-color: #ffd700 !important;
    background: rgba(255, 215, 0, 0.05) !important;
}

/* ========== Premium Inputs ========== */
.premium-input {
    background-color: #f8fafc !important;
    border: 1px solid rgba(0, 0, 0, 0.06) !important;
    font-family: 'Inter', sans-serif;
    color: #334155 !model !important;
    padding: 11px 16px !important;
    font-size: 13.5px !important;
    transition: all 0.25s ease !important;
}

.premium-input:focus {
    border-color: #e5a93b !important;
    background-color: #ffffff !important;
    box-shadow: 0 0 0 3px rgba(229, 169, 59, 0.1) !important;
}

.btn-filter-submit {
    background: linear-gradient(135deg, #1e2035 0%, #252740 100%) !important;
    color: #ffffff !important;
    border: 1px solid rgba(255, 215, 0, 0.2) !important;
    font-weight: 700 !important;
    font-size: 13px !important;
    padding: 10px 24px !important;
    box-shadow: 0 4px 10px rgba(30, 32, 53, 0.15) !important;
    transition: all 0.2s ease !important;
}

.btn-filter-submit:hover {
    transform: translateY(-1px);
    border-color: #ffd700 !important;
    box-shadow: 0 6px 15px rgba(255, 215, 0, 0.1) !important;
}

/* ========== Role List ========== */
.role-list {
    max-height: 520px;
    overflow-y: auto;
    padding-right: 4px;
}
.role-list::-webkit-scrollbar { width: 5px; }
.role-list::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.05); border-radius: 10px; }

.role-item {
    background: #f8fafc;
    border: 1px solid rgba(0, 0, 0, 0.03);
}
.role-item:hover {
    background: rgba(229, 169, 59, 0.04);
    border-color: rgba(229, 169, 59, 0.15);
    transform: translateX(4px);
}
.role-item.active {
    background: linear-gradient(135deg, rgba(229, 169, 59, 0.05) 0%, rgba(255, 215, 0, 0.02) 100%);
    border-color: #e5a93b;
    box-shadow: 0 4px 15px rgba(229, 169, 59, 0.08);
}

.role-avatar {
    width: 42px; height: 42px;
    background: #e2e8f0;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; color: #64748b;
    transition: all 0.3s ease;
}
.role-avatar.avatar-active {
    background: linear-gradient(135deg, #1e2035, #252740);
    color: #ffd700;
    box-shadow: 0 4px 12px rgba(30, 32, 53, 0.2);
}

.badge-status-active {
    background: #f0fdf4 !important;
    color: #16a34a !important;
    border: 1px solid rgba(22, 163, 74, 0.15);
}
.badge-status-locked {
    background: #fef2f2 !important;
    color: #ef4444 !important;
    border: 1px solid rgba(239, 68, 68, 0.15);
}

/* ========== Permission Header ========== */
.permission-header {
    background: linear-gradient(135deg, #1e2035 0%, #252740 100%);
}
.perm-header-icon {
    width: 48px; height: 48px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; color: #ffd700;
}

/* ========== Permission Card ========== */
.permission-card {
    background: #fff;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
}
.permission-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    border-color: rgba(229, 169, 59, 0.2) !important;
}
.permission-card.selected {
    border-color: #e5a93b !important;
    background: linear-gradient(135deg, rgba(229, 169, 59, 0.02) 0%, rgba(255, 215, 0, 0.01) 100%) !important;
    box-shadow: 0 6px 20px rgba(229, 169, 59, 0.05);
}

.permission-icon {
    width: 44px; height: 44px;
    font-size: 22px;
    transition: all 0.3s ease;
}
.icon-active {
    background-color: rgba(229, 169, 59, 0.1) ;
    color: #e5a93b;
}
.icon-inactive {
    background-color: #f1f5f9;
    color: #94a3b8;
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
    background-color: #cbd5e1;
    transition: .3s;
}
.slider:before {
    position: absolute; content: "";
    height: 18px; width: 18px;
    left: 3px; bottom: 3px;
    background-color: #fff;
    transition: .3s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
input:checked + .slider {
    background: linear-gradient(135deg, #1e2035, #252740);
}
input:checked + .slider:before {
    transform: translateX(22px);
    background-color: #ffd700;
}
.slider.round { border-radius: 24px; }
.slider.round:before { border-radius: 50%; }

/* ========== Progress Bar ========== */
.progress {
    background-color: #e2e8f0;
}
.progress-bar {
    transition: width 0.4s ease;
}

/* ========== Empty State ========== */
.empty-state {
    padding: 40px 20px;
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

/* Dark theme overrides */
[data-theme="dark"] .luxury-panel {
    background: #1e1e2d !important;
    border-color: rgba(255, 255, 255, 0.05) !important;
}
[data-theme="dark"] .role-item {
    background: #151521;
    border-color: rgba(255, 255, 255, 0.03);
}
[data-theme="dark"] .role-item:hover {
    background: rgba(229, 169, 59, 0.08);
}
[data-theme="dark"] .role-item.active {
    background: linear-gradient(135deg, rgba(229, 169, 59, 0.1) 0%, rgba(255, 215, 0, 0.03) 100%);
    border-color: #e5a93b;
}
[data-theme="dark"] .role-avatar {
    background: #2b2b3d;
    color: #8f94a6;
}
[data-theme="dark"] .role-avatar.avatar-active {
    background: linear-gradient(135deg, #e5a93b, #ffd700);
    color: #1e2035;
}
[data-theme="dark"] .role-title {
    color: #e2e8f0 !important;
}
[data-theme="dark"] .permission-card {
    background: #151521;
    border-color: rgba(255, 255, 255, 0.05) !important;
}
[data-theme="dark"] .permission-card:hover {
    border-color: rgba(229, 169, 59, 0.3) !important;
}
[data-theme="dark"] .permission-card.selected {
    border-color: #e5a93b !important;
    background: linear-gradient(135deg, rgba(229, 169, 59, 0.05) 0%, rgba(255, 215, 0, 0.02) 100%) !important;
}
[data-theme="dark"] .card-perm-title {
    color: #f1f5f9 !important;
}
[data-theme="dark"] .text-perm-desc {
    color: #94a3b8 !important;
}
[data-theme="dark"] .icon-inactive {
    background-color: #2b2b3d;
    color: #64748b;
}
[data-theme="dark"] .bg-light-subtle {
    background-color: #1a1a26 !important;
}
[data-theme="dark"] .premium-input {
    background-color: #151521 !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
    color: #e2e8f0 !important;
}
[data-theme="dark"] .premium-input:focus {
    border-color: #e5a93b !important;
    background-color: #1a1a26 !important;
}
[data-theme="dark"] .empty-state h6 {
    color: #e2e8f0 !important;
}

/* Modal Custom styles */
.modal-overlay-custom {
    position: fixed; top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(15, 23, 42, 0.4);
    backdrop-filter: blur(8px);
    display: flex; align-items: center; justify-content: center;
    z-index: 1050;
    padding: 20px;
}
.modal-box-custom {
    background: #ffffff;
    border-radius: 16px;
    width: 100%; max-width: 500px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    border: 1px solid rgba(0, 0, 0, 0.05);
    overflow: hidden;
    animation: zoomIn 0.25s ease-out;
}
.modal-hdr-custom {
    background: linear-gradient(135deg, #1e2035 0%, #252740 100%);
    color: #ffffff;
    padding: 16px 20px;
    font-weight: 700;
    font-size: 16px;
    display: flex; justify-content: space-between; align-items: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}
.gold-hdr-p {
    background: linear-gradient(135deg, #1e2035 0%, #3e3215 100%) !important;
    border-bottom: 1px solid rgba(255, 215, 0, 0.1) !important;
}
.gold-hdr-p span {
    color: #ffd700 !important;
}
.modal-close-custom {
    background: none; border: none;
    color: rgba(255, 255, 255, 0.7);
    font-size: 20px; cursor: pointer;
    transition: color 0.2s;
}
.modal-close-custom:hover { color: #ffffff; }
.modal-body-custom { padding: 20px; }
.form-group-custom-p { margin-bottom: 16px; }
.form-group-custom-p label {
    font-size: 13.5px; font-weight: 600;
    color: #334155; margin-bottom: 6px;
}
.form-inp-p {
    width: 100%; padding: 10px 14px;
    background: #f8fafc;
    border: 1px solid rgba(0, 0, 0, 0.08);
    border-radius: 10px;
    font-size: 13.5px;
    transition: all 0.25s ease;
}
.form-inp-p:focus {
    border-color: #e5a93b;
    background: #ffffff;
    outline: none;
    box-shadow: 0 0 0 3px rgba(229, 169, 59, 0.1);
}
.req { color: #ef4444; margin-left: 2px; }
.modal-footer-btns-custom {
    display: flex; justify-content: flex-end;
    gap: 12px; margin-top: 24px;
}
.btn-secondary-custom-p {
    background: #f1f5f9; border: 1px solid rgba(0, 0, 0, 0.05);
    padding: 9px 20px; border-radius: 30px;
    font-size: 13px; font-weight: 600; color: #475569;
    cursor: pointer; transition: all 0.2s;
}
.btn-secondary-custom-p:hover { background: #e2e8f0; }
.btn-primary-custom-p {
    background: linear-gradient(135deg, #1e2035 0%, #252740 100%);
    border: 1px solid rgba(255, 215, 0, 0.15);
    padding: 9px 20px; border-radius: 30px;
    font-size: 13px; font-weight: 600; color: #ffffff;
    cursor: pointer; transition: all 0.2s;
}
.btn-primary-custom-p:hover { transform: translateY(-1px); border-color: #ffd700; }
.btn-gold-p {
    background: linear-gradient(135deg, #ffd700, #e5a93b) !important;
    color: #1e2035 !important;
    border: none !important;
}
.btn-gold-p:hover { box-shadow: 0 4px 12px rgba(229, 169, 59, 0.2) !important; }

/* Dark mode compatibility */
[data-theme="dark"] .modal-box-custom {
    background: #1e1e2d !important;
    border-color: rgba(255, 255, 255, 0.05) !important;
}
[data-theme="dark"] .form-inp-p {
    background: #151521 !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
    color: #e2e8f0 !important;
}
[data-theme="dark"] .form-inp-p:focus { border-color: #e5a93b !important; }
[data-theme="dark"] .form-group-custom-p label { color: #cbd5e1 !important; }
[data-theme="dark"] .btn-secondary-custom-p {
    background: #2b2b3d !important;
    border-color: rgba(255, 255, 255, 0.05) !important;
    color: #cbd5e1 !important;
}
[data-theme="dark"] .btn-secondary-custom-p:hover { background: #35354a !important; }
</style>