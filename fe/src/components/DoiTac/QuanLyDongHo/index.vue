<template>
    <div class="partner-phan-quyen-wrapper">
        <!-- Page Header -->
        <div class="page-header mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="page-icon">
                    <i class='bx bx-shield-quarter'></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold theme-text-main page-title text-gradient-gold">Phân Quyền Thành Viên</h4>
                    <p class="mb-0 text-secondary small mt-1">Điều chỉnh bật/tắt các tính năng chi tiết cho từng thành viên trong chi nhánh quản lý</p>
                </div>
            </div>
            <button class="btn btn-refresh-premium rounded-circle d-flex align-items-center justify-content-center" @click="refreshAll" :disabled="isLoadingMembers || isLoadingPermissions" title="Làm mới dữ liệu">
                <i class="bx bx-sync fs-5 text-warning" :class="{'bx-spin': isLoadingMembers || isLoadingPermissions}"></i>
            </button>
        </div>

        <div class="row g-4">
            <!-- Left Panel: Member List -->
            <div class="col-lg-4 col-md-12">
                <div class="card genealogy-main-card shadow-sm border-0 h-100">
                    <div class="card-header bg-transparent py-3 border-0 border-bottom border-light-subtle d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-bold text-uppercase theme-text-main">
                            <i class="bx bx-group me-2 text-warning"></i> Danh Sách Thành Viên
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <!-- Search Box -->
                        <div class="search-box mb-4">
                            <i class='bx bx-search'></i>
                            <input type="text" class="form-control premium-input border-2 shadow-none" placeholder="Tìm thành viên..." v-model="searchMember">
                        </div>

                        <!-- Member List Scrollable -->
                        <div class="member-list">
                            <div v-if="isLoadingMembers" class="text-center py-5 text-muted">
                                <div class="spinner-border spinner-border-sm text-warning me-2" role="status"></div>
                                <span>Đang tải thành viên...</span>
                            </div>

                            <div v-else-if="filteredMembers.length === 0" class="text-center py-5 text-muted">
                                <i class='bx bx-search-alt opacity-50' style="font-size: 40px;"></i>
                                <p class="mt-2 mb-0">Không tìm thấy thành viên</p>
                            </div>

                            <div v-else v-for="m in filteredMembers" :key="m.id"
                                class="member-item d-flex align-items-center p-3 mb-3 radius-12 cursor-pointer transition-all"
                                :class="{'active': selectedMember && selectedMember.id === m.id}"
                                @click="selectMember(m)">
                                <div class="member-avatar me-3" :class="selectedMember && selectedMember.id === m.id ? 'avatar-active' : ''">
                                    <img v-if="m.avatar" :src="m.avatar" alt="Avatar" class="avatar-img">
                                    <i v-else class='bx bx-user-circle'></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <h6 class="mb-0 fw-bold member-title text-truncate theme-text-main">{{ m.ho_ten }}</h6>
                                    <small class="text-secondary small d-block text-truncate mt-0.5">
                                        Đời {{ m.doi_thu }} • {{ m.loai_quan_he || 'Thành viên' }}
                                    </small>
                                </div>
                                <div class="ms-2" v-if="m.email">
                                    <span class="badge badge-email-status rounded-pill fw-medium">
                                        Đã liên kết
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Permissions Grid -->
            <div class="col-lg-8 col-md-12">
                <div class="card genealogy-main-card shadow-sm border-0 h-100">
                    <!-- Header -->
                    <div class="card-header border-0 border-bottom py-0 px-0 overflow-hidden">
                        <div class="permission-header p-4" v-if="selectedMember">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="perm-header-icon">
                                        <i class='bx bx-shield-quarter'></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0 fw-bold text-white">Quyền: {{ selectedMember.ho_ten }}</h5>
                                        <p class="mb-0 text-white-50 small mt-1">
                                            Chức vụ mặc định: <strong class="text-warning fw-bold">{{ roleName }}</strong> | Bật <strong class="text-warning">{{ activePermissionsCount }}</strong> / {{ filteredChucNang.length }} quyền hoạt động
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-light radius-30 fw-semibold px-3" @click="selectAll">
                                        <i class='bx bx-check-double me-1'></i>Chọn Tất Cả
                                    </button>
                                    <button class="btn btn-sm btn-outline-light radius-30 fw-semibold px-3" @click="deselectAll">
                                        <i class='bx bx-x me-1'></i>Bỏ Chọn
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="text-center py-5" v-else>
                            <div class="empty-state py-5">
                                <i class='bx bx-pointer text-warning' style="font-size: 56px; opacity: 0.8;"></i>
                                <h6 class="theme-text-main fw-bold mt-3">Chọn một thành viên để bắt đầu phân quyền</h6>
                                <p class="text-secondary small">Nhấp vào danh sách thành viên ở cột bên trái để tải và điều chỉnh quyền truy cập</p>
                            </div>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="card-body p-0" v-if="selectedMember">
                        <!-- Account Linking Section -->
                        <div class="account-link-banner p-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-3" style="background: rgba(249, 115, 22, 0.02);">
                            <div class="d-flex align-items-center gap-3">
                                <div class="link-status-icon d-flex align-items-center justify-content-center rounded-3" 
                                    :class="selectedMember.email ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning'"
                                    style="width: 40px; height: 40px; font-size: 20px;">
                                    <i class="bx" :class="selectedMember.email ? 'bx-check-shield' : 'bx-link-alt'"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold theme-text-main">Liên kết tài khoản hệ thống</h6>
                                    <p class="mb-0 text-secondary small" v-if="selectedMember.email">
                                        Đã liên kết với email: <strong class="theme-text-main fw-bold">{{ selectedMember.email }}</strong>
                                    </p>
                                    <p class="mb-0 text-secondary small" v-else>
                                        Chưa có tài khoản liên kết trong hệ thống dòng họ
                                    </p>
                                </div>
                            </div>
                            
                            <!-- If linked: show unlink button -->
                            <div v-if="selectedMember.email" class="d-flex align-items-center gap-2">
                                <button class="btn btn-outline-danger btn-sm radius-30 px-3 fw-semibold transition-all d-flex align-items-center gap-1"
                                    @click="unlinkAccount" :disabled="linking">
                                    <span v-if="linking" class="spinner-border spinner-border-sm" role="status"></span>
                                    <i v-else class="bx bx-unlink"></i>
                                    Hủy liên kết
                                </button>
                            </div>
                            
                            <!-- If not linked: show email input and link button -->
                            <div v-else class="d-flex align-items-center gap-2 flex-grow-1 flex-sm-grow-0" style="max-width: 400px; width: 100%;">
                                <input type="email" class="form-control premium-input border-2 shadow-none flex-grow-1 py-1.5" 
                                    placeholder="Nhập email tài khoản..." v-model="linkEmail" :disabled="linking" style="font-size: 13px !important;">
                                <button class="btn btn-gradient-orange btn-sm text-white radius-30 px-3 fw-bold transition-all d-flex align-items-center gap-1 flex-shrink-0"
                                    @click="linkAccount" :disabled="linking || !linkEmail">
                                    <span v-if="linking" class="spinner-border spinner-border-sm" role="status"></span>
                                    <i v-else class="bx bx-link"></i>
                                    Liên kết
                                </button>
                            </div>
                        </div>

                        <!-- Toolbar -->
                        <div class="bg-light-subtle border-bottom p-3 d-flex justify-content-between align-items-center gap-3 flex-wrap">
                            <div class="search-box" style="width: 280px; max-width: 100%;">
                                <i class='bx bx-search'></i>
                                <input type="text" class="form-control premium-input border-2 shadow-none" placeholder="Tìm quyền..." v-model="searchPermission">
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="progress-ring d-flex align-items-center gap-2">
                                    <div class="progress radius-8" style="width: 120px; height: 8px;">
                                        <div class="progress-bar bg-warning" :style="{width: progressPercent + '%'}"></div>
                                    </div>
                                    <small class="text-secondary fw-bold">{{ progressPercent }}%</small>
                                </div>
                            </div>
                        </div>

                        <!-- Grid -->
                        <div class="p-4" style="max-height: 500px; overflow-y: auto;">
                            <div v-if="isLoadingPermissions" class="text-center py-5 text-muted">
                                <div class="spinner-border text-warning mb-3" role="status"></div>
                                <h6>Đang tải dữ liệu phân quyền thành viên...</h6>
                            </div>

                            <div v-else class="row g-3">
                                <div class="col-xl-4 col-md-6 col-sm-12" v-for="cn in filteredChucNang" :key="cn.id">
                                    <label class="permission-card p-3 radius-12 border transition-all h-100 d-flex flex-column cursor-pointer"
                                        :class="{'selected': isSelected(cn.id), 'disabled-card': !isGlobalEnabled(cn.id)}">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="permission-icon d-flex align-items-center justify-content-center radius-10"
                                                :class="isGlobalEnabled(cn.id) && isSelected(cn.id) ? 'icon-active' : 'icon-inactive'">
                                                <i class='bx' :class="getIconForPermission(cn.ten_chuc_nang)"></i>
                                            </div>
                                            <div class="custom-switch">
                                                <input type="checkbox" :value="cn.id" v-model="selectedPermissions" :disabled="!isGlobalEnabled(cn.id)">
                                                <span class="slider round"></span>
                                            </div>
                                        </div>
                                        <h6 class="fw-bold mb-1 card-perm-title" :class="isGlobalEnabled(cn.id) && isSelected(cn.id) ? 'text-warning' : 'theme-text-main'">
                                            {{ getFriendlyName(cn.ten_chuc_nang) }}
                                        </h6>
                                        <p class="text-secondary small mb-0 flex-grow-1 text-perm-desc">
                                            {{ getFriendlyDesc(cn) }}
                                        </p>
                                        
                                        <!-- Lock Label (Admin Turned Off) -->
                                        <div class="mt-2 text-danger small fw-semibold d-flex align-items-center gap-1 border-top pt-2" v-if="!isGlobalEnabled(cn.id)">
                                            <i class="bx bx-lock-alt"></i>
                                            <span>Hệ thống đã tắt</span>
                                        </div>
                                    </label>
                                </div>
                                <div v-if="filteredChucNang.length === 0" class="col-12 text-center py-5">
                                    <i class='bx bx-search-alt text-muted' style="font-size: 56px; opacity: 0.3;"></i>
                                    <h6 class="text-muted mt-3">Không tìm thấy quyền nào khớp "{{ searchPermission }}"</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="card-footer bg-transparent border-top p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div class="text-secondary small fw-semibold">
                                <i class='bx bx-info-circle text-warning me-1'></i>
                                Thay đổi chỉ áp dụng cho thành viên này
                            </div>
                            <button class="btn btn-gradient-orange text-white radius-30 px-5 fw-bold shadow-sm d-flex align-items-center gap-2"
                                @click="savePhanQuyen" :disabled="saving || isLoadingPermissions">
                                <span v-if="saving" class="spinner-border spinner-border-sm" role="status"></span>
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
    name: 'PartnerPhanQuyen',
    data() {
        return {
            allMembers: [],
            selectedMember: null,
            roleName: '',
            listChucNang: [],
            globalEnabledIds: [],
            selectedPermissions: [],
            searchMember: '',
            searchPermission: '',
            saving: false,
            isLoadingMembers: false,
            isLoadingPermissions: false,
            linkEmail: '',
            linking: false
        }
    },
    computed: {
        filteredMembers() {
            if (!this.searchMember) return this.allMembers;
            const term = this.searchMember.toLowerCase();
            return this.allMembers.filter(m =>
                m.ho_ten.toLowerCase().includes(term) ||
                (m.email && m.email.toLowerCase().includes(term))
            );
        },
        filteredChucNang() {
            if (!this.selectedMember) return [];
            if (!this.searchPermission) return this.listChucNang;
            const term = this.searchPermission.toLowerCase();
            return this.listChucNang.filter(cn =>
                cn.ten_chuc_nang.toLowerCase().includes(term) ||
                (cn.mo_ta && cn.mo_ta.toLowerCase().includes(term))
            );
        },
        activePermissionsCount() {
            // Chỉ tính những quyền thực tế được bật (thỏa mãn cả global & partner lựa chọn)
            return this.selectedPermissions.filter(id => this.globalEnabledIds.includes(id)).length;
        },
        progressPercent() {
            if (this.filteredChucNang.length === 0) return 0;
            return Math.round((this.activePermissionsCount / this.filteredChucNang.length) * 100);
        }
    },
    mounted() {
        this.loadMembers();
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
            this.loadMembers();
            if (this.selectedMember) {
                this.selectMember(this.selectedMember);
            }
        },
        loadMembers() {
            this.isLoadingMembers = true;
            axios.get('http://127.0.0.1:8000/api/thanh-vien/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.allMembers = res.data.data;
                    }
                })
                .catch(() => {
                    toastr.error('Không thể tải danh sách thành viên');
                })
                .finally(() => {
                    this.isLoadingMembers = false;
                });
        },
        selectMember(member) {
            this.selectedMember = member;
            this.searchPermission = '';
            this.linkEmail = '';
            this.isLoadingPermissions = true;
            axios.post('http://127.0.0.1:8000/api/phan-quyen/get-member-chuc-nang', { thanh_vien_id: member.id }, this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.listChucNang = res.data.data;
                        this.globalEnabledIds = res.data.global_enabled_ids;
                        this.roleName = res.data.role_name;

                        // Nếu đã được cấu hình tùy chỉnh
                        if (res.data.has_customized) {
                            this.selectedPermissions = res.data.custom_enabled_ids;
                        } else {
                            // Nếu chưa cấu hình: mặc định gán toàn bộ quyền của Admin
                            this.selectedPermissions = [...res.data.global_enabled_ids];
                        }
                    }
                })
                .catch(() => {
                    toastr.error('Lỗi khi tải thông tin phân quyền thành viên');
                })
                .finally(() => {
                    this.isLoadingPermissions = false;
                });
        },
        savePhanQuyen() {
            if (!this.selectedMember) return;
            this.saving = true;

            // Chỉ lưu những ID thuộc globalEnabledIds mà được bật (hoặc lưu đầy đủ theo danh sách partner chọn)
            const payload = {
                thanh_vien_id: this.selectedMember.id,
                list_chuc_nang: this.selectedPermissions
            };

            axios.post('http://127.0.0.1:8000/api/phan-quyen/update-member', payload, this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        toastr.success(res.data.message);
                        // Cập nhật lại giao diện
                        this.selectMember(this.selectedMember);
                    } else {
                        toastr.error(res.data.message);
                    }
                })
                .catch(() => {
                    toastr.error('Lưu phân quyền thất bại!');
                })
                .finally(() => {
                    this.saving = false;
                });
        },
        linkAccount() {
            if (!this.selectedMember || !this.linkEmail) return;
            this.linking = true;
            const payload = {
                thanh_vien_id: this.selectedMember.id,
                email: this.linkEmail
            };
            axios.post('http://127.0.0.1:8000/api/phan-quyen/link-member', payload, this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        toastr.success(res.data.message);
                        this.linkEmail = '';
                        this.loadMembers();
                        
                        // Cập nhật email trực tiếp cho member đang chọn để tránh delay
                        const updatedMember = { ...this.selectedMember, email: payload.email };
                        this.selectMember(updatedMember);
                    } else {
                        toastr.error(res.data.message);
                    }
                })
                .catch(err => {
                    toastr.error(err.response?.data?.message || 'Liên kết tài khoản thất bại!');
                })
                .finally(() => {
                    this.linking = false;
                });
        },
        unlinkAccount() {
            if (!this.selectedMember) return;
            this.linking = true;
            const payload = {
                thanh_vien_id: this.selectedMember.id
            };
            axios.post('http://127.0.0.1:8000/api/phan-quyen/unlink-member', payload, this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        toastr.success(res.data.message);
                        this.linkEmail = '';
                        this.loadMembers();
                        
                        // Cập nhật email trực tiếp cho member đang chọn để tránh delay
                        const updatedMember = { ...this.selectedMember, email: null };
                        this.selectMember(updatedMember);
                    } else {
                        toastr.error(res.data.message);
                    }
                })
                .catch(err => {
                    toastr.error(err.response?.data?.message || 'Hủy liên kết thất bại!');
                })
                .finally(() => {
                    this.linking = false;
                });
        },
        isSelected(id) {
            return this.selectedPermissions.includes(id);
        },
        isGlobalEnabled(id) {
            return this.globalEnabledIds.includes(id);
        },
        selectAll() {
            // Bật toàn bộ quyền có trong danh sách được hiển thị và đang được Admin cho phép
            const allowedIds = this.filteredChucNang
                .filter(cn => this.isGlobalEnabled(cn.id))
                .map(cn => cn.id);
            const union = new Set([...this.selectedPermissions, ...allowedIds]);
            this.selectedPermissions = Array.from(union);
        },
        deselectAll() {
            // Bỏ chọn tất cả quyền đang hiển thị cho thành viên này
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
            // Thay thế "Quản Lý" thành "Xem" để phù hợp cho cấp thành viên
            let friendlyName = name.replace(/Quản Lý/g, 'Xem').replace(/quản lý/g, 'xem');
            if (name === 'Cây Gia Phả') return 'Xem Cây Gia Phả';
            if (name === 'Tra Cứu Xưng Hô') return 'Xem Tra Cứu Xưng Hô';
            if (name === 'Quỹ & Sự Kiện') return 'Xem Quỹ & Sự Kiện';
            if (name === 'Nhật Ký Thao Tác') return 'Xem Nhật Ký Thao Tác';
            return friendlyName;
        },
        getFriendlyDesc(cn) {
            let desc = cn.mo_ta || ('Cho phép truy cập ' + cn.ten_chuc_nang);
            return desc.replace(/Quản lý/g, 'Xem').replace(/quản lý/g, 'xem');
        }
    }
}
</script>

<style scoped>
/* ========== Utilities ========== */
.radius-12 { border-radius: 12px !important; }
.radius-10 { border-radius: 10px !important; }
.radius-8  { border-radius: 8px !important; }
.radius-30 { border-radius: 30px !important; }
.transition-all { transition: all 0.3s ease; }
.cursor-pointer { cursor: pointer; }
.min-w-0 { min-width: 0; }

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

/* ========== Circular Reload Button ========== */
.btn-refresh-premium {
    background: var(--input-bg) !important;
    border: 1px solid var(--border-color) !important;
    width: 36px;
    height: 36px;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    transition: all 0.25s ease;
}
.btn-refresh-premium:hover {
    transform: rotate(30deg) scale(1.05);
    border-color: #f97316 !important;
    box-shadow: 0 4px 12px rgba(249, 115, 22, 0.15);
}

/* ========== Premium Inputs & Containers ========== */
.genealogy-main-card {
    background: var(--card-bg) !important;
    border: 1px solid var(--border-color) !important;
    border-radius: 16px !important;
}

.premium-input {
    background-color: var(--input-bg) !important;
    border: 1px solid var(--border-color) !important;
    color: var(--text-main) !important;
    padding: 11px 16px !important;
    border-radius: 12px !important;
    font-size: 13.5px !important;
    transition: all 0.25s ease !important;
}
.premium-input:focus {
    border-color: #f97316 !important;
    background-color: var(--card-bg) !important;
    box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1) !important;
}

.search-box {
    position: relative;
}
.search-box i {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-sub);
    font-size: 18px;
}

/* ========== Member List Scrollable ========== */
.member-list {
    max-height: 520px;
    overflow-y: auto;
    padding-right: 4px;
}
.member-list::-webkit-scrollbar { width: 5px; }
.member-list::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.05); border-radius: 10px; }

.member-item {
    background: var(--input-bg);
    border: 1px solid var(--border-color);
    border-radius: 12px;
}
.member-item:hover {
    background: rgba(249, 115, 22, 0.04);
    border-color: rgba(249, 115, 22, 0.15);
    transform: translateX(4px);
}
.member-item.active {
    background: linear-gradient(135deg, rgba(249, 115, 22, 0.05) 0%, rgba(255, 215, 0, 0.02) 100%);
    border-color: #f97316;
    box-shadow: 0 4px 15px rgba(249, 115, 22, 0.08);
}

.member-avatar {
    width: 42px; height: 42px;
    background: var(--border-color);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; color: var(--text-sub);
    transition: all 0.3s ease;
    overflow: hidden;
}
.member-avatar.avatar-active {
    background: linear-gradient(135deg, #1e2035, #252740);
    color: #ffd700;
    box-shadow: 0 4px 12px rgba(30, 32, 53, 0.2);
}
.avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.badge-email-status {
    background: rgba(22, 163, 74, 0.1) !important;
    color: #16a34a !important;
    font-size: 11px;
    border: 1px solid rgba(22, 163, 74, 0.2);
}

/* ========== Account Link Banner ========== */
.account-link-banner {
    border-bottom: 1px solid var(--border-color);
}
.bg-success-subtle {
    background-color: rgba(22, 163, 74, 0.1) !important;
}
.bg-warning-subtle {
    background-color: rgba(249, 115, 22, 0.1) !important;
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

/* ========== Permission Card Grid ========== */
.permission-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color) !important;
}
.permission-card:hover:not(.disabled-card) {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    border-color: rgba(249, 115, 22, 0.2) !important;
}
.permission-card.selected:not(.disabled-card) {
    border-color: #f97316 !important;
    background: linear-gradient(135deg, rgba(249, 115, 22, 0.02) 0%, rgba(255, 215, 0, 0.01) 100%) !important;
    box-shadow: 0 6px 20px rgba(249, 115, 22, 0.05);
}
.disabled-card {
    opacity: 0.65;
    background: var(--input-bg) !important;
    cursor: not-allowed;
    border-color: var(--border-color) !important;
}

.permission-icon {
    width: 44px; height: 44px;
    font-size: 22px;
    transition: all 0.3s ease;
}
.icon-active {
    background-color: rgba(249, 115, 22, 0.1) ;
    color: #f97316;
}
.icon-inactive {
    background-color: var(--input-bg);
    color: var(--text-sub);
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
    background-color: var(--border-color);
}
.progress-bar {
    transition: width 0.4s ease;
}

/* ========== Empty State ========== */
.empty-state {
    padding: 40px 20px;
}

/* ========== Orange Gradient Button ========== */
.btn-gradient-orange {
    background: linear-gradient(135deg, #f43f5e 0%, #f97316 100%) !important;
    border: none !important;
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(244, 63, 94, 0.15) !important;
    transition: all 0.25s ease;
}
.btn-gradient-orange:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(244, 63, 94, 0.25) !important;
}
</style>