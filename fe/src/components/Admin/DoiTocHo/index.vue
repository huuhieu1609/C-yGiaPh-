<template>
    <div class="doi-toc-ho-management-wrapper">
        <!-- Page Header -->
        <div class="page-header mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="page-icon">
                    <i class='bx bx-layer' style="color: #db2777;"></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold theme-text-main page-title text-gradient-gold">Quản Lý Đời Họ</h4>
                    <p class="mb-0 text-secondary small mt-1">Cấu hình hệ thống các thế hệ dòng tộc và thiết lập tên gọi đời tương ứng</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <!-- Partner Selector Button -->
                <button class="btn btn-outline-warning radius-30 px-3 fw-bold d-flex align-items-center gap-2 shadow-sm" style="font-size: 13px; height: 34px; padding-top: 5px; padding-bottom: 5px;" @click="showPartnerSelectorModal = true">
                    <i class="bx bx-user-circle fs-5"></i>
                    <span>{{ selectedPartner ? (selectedPartner.nguoi_dung?.ho_ten || selectedPartner.ten_goi) + ' (' + selectedPartner.dong_ho + ')' : 'Chọn Đối Tác' }}</span>
                </button>

                <button class="btn btn-refresh-premium rounded-circle d-flex align-items-center justify-content-center" @click="refreshAll" :disabled="isLoading" title="Làm mới dữ liệu">
                    <i class="bx bx-sync fs-5 text-warning" :class="{'bx-spin': isLoading}"></i>
                </button>
            </div>
        </div>

        <!-- State 1: No partner chosen yet -->
        <div v-if="!selectedPartner" class="card shadow-sm border-0 radius-10 p-5 text-center bg-white">
            <div class="empty-state-icon mb-4">
                <i class="bx bx-layer fs-1 text-warning opacity-75 animate__animated animate__pulse animate__infinite" style="font-size: 60px;"></i>
            </div>
            <h4 class="fw-bold text-dark mb-3">Chưa Chọn Đối Tác Quản Lý Đời Họ</h4>
            <p class="text-muted small mx-auto mb-4" style="max-width: 480px; font-size: 14px; line-height: 1.6;">
                Vui lòng lựa chọn một tài khoản đối tác từ danh sách để tải cấu trúc đời tộc họ và thực hiện quản lý tương ứng.
            </p>
            <div>
                <button class="btn btn-warning radius-30 px-4 py-2 fw-bold shadow-sm" @click="showPartnerSelectorModal = true">
                    <i class="bx bx-list-ul me-1"></i> Chọn Đối Tác Ngay
                </button>
            </div>
        </div>

        <!-- State 2: Partner chosen -->
        <div class="row g-4" v-else>
            <!-- Left Column: Add/Edit Form -->
            <div class="col-lg-4 col-md-12">
                <div class="card luxury-panel border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent py-4 border-0 border-bottom border-light-subtle d-flex align-items-center">
                        <h5 class="mb-0 fw-bold panel-title text-dark text-gradient-gold">
                            <i class="bx bx-plus-circle me-2"></i> {{ isEditing ? 'Cập Nhật Đời Họ' : 'Thêm Đời Họ Mới' }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form @submit.prevent="saveData">
                            <div class="mb-4">
                                <label class="form-label fw-bold text-secondary-custom">Số Đời (Thế Thế Hệ)</label>
                                <input type="number" class="form-control premium-input radius-10 border-2 shadow-none" placeholder="Nhập số đời (VD: 1, 2, 3...)" v-model="formData.so_doi" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold text-secondary-custom">Tên Đời</label>
                                <input type="text" class="form-control premium-input radius-10 border-2 shadow-none" placeholder="Nhập tên đời (VD: Đời thứ nhất)" v-model="formData.ten_doi" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold text-secondary-custom">Tình Trạng</label>
                                <select class="form-select premium-input radius-10 border-2 shadow-none" v-model="formData.trang_thai">
                                    <option value="1">Hoạt động</option>
                                    <option value="0">Tạm dừng</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold text-secondary-custom">Mô Tả</label>
                                <textarea class="form-control premium-input radius-10 border-2 shadow-none" rows="4" placeholder="Nhập mô tả chi tiết..." v-model="formData.mo_ta"></textarea>
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5">
                                <button type="button" class="btn btn-outline-secondary radius-30 px-4" v-if="isEditing" @click="resetForm">Hủy</button>
                                <button type="submit" class="btn btn-filter-submit text-white radius-30 px-4 fw-bold shadow-sm">
                                    {{ isEditing ? 'Cập Nhật' : 'Thêm Mới' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Column: Data Table -->
            <div class="col-lg-8 col-md-12">
                <div class="card luxury-panel border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent py-4 border-0 border-bottom border-light-subtle d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <h5 class="mb-0 fw-bold panel-title text-dark">
                            <i class="bx bx-layer me-2 text-warning"></i> Cấu Trúc Đời Tộc Họ
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="input-group mb-4 radius-10 overflow-hidden border-2 search-box-premium shadow-sm">
                            <input type="text" class="form-control border-0 shadow-none ps-4 bg-transparent" placeholder="Tìm kiếm đời tộc họ..." v-model="searchQuery">
                            <span class="input-group-text border-0 bg-transparent pe-4"><i class="bx bx-search text-secondary"></i></span>
                        </div>

                        <div class="table-responsive rounded-3 border border-light-subtle">
                            <table class="table modern-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th width="8%" class="text-center">#</th>
                                        <th width="22%">Tên Đời</th>
                                        <th width="15%" class="text-center">Số Đời</th>
                                        <th width="35%">Mô Tả</th>
                                        <th width="15%" class="text-center">Tình Trạng</th>
                                        <th width="15%" class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="isLoading">
                                        <td colspan="6" class="text-center py-5">
                                            <div class="spinner-border text-warning" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-else-if="filteredList.length === 0">
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="bx bx-folder-open fs-1 mb-2 d-block opacity-50"></i>
                                            Chưa có cấu hình đời nào cho dòng họ này
                                        </td>
                                    </tr>
                                    <tr v-for="(item, index) in filteredList" :key="item.id">
                                        <td class="text-center fw-bold">{{ index + 1 }}</td>
                                        <td class="fw-bold text-dark">{{ item.ten_doi }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-warning-soft text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1 fw-bold">
                                                Đời thứ {{ item.so_doi }}
                                            </span>
                                        </td>
                                        <td class="text-secondary small">{{ item.mo_ta || '---' }}</td>
                                        <td class="text-center">
                                            <span v-if="item.trang_thai == 1" class="badge badge-active rounded-pill px-3 py-1">Hoạt động</span>
                                            <span v-else class="badge badge-paused rounded-pill px-3 py-1">Tạm dừng</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn btn-action-edit" @click="editItem(item)" title="Sửa">
                                                    <i class="bx bx-edit-alt"></i>
                                                </button>
                                                <button class="btn btn-action-delete" @click="deleteItem(item.id)" title="Xóa">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Chọn Đối Tác -->
        <div v-if="showPartnerSelectorModal" class="custom-modal-backdrop animate__animated animate__fadeIn" @click.self="showPartnerSelectorModal = false">
            <div class="custom-modal-content animate__animated animate__zoomIn p-4 rounded-4 shadow-2xl bg-white position-relative" style="max-width: 650px; z-index: 1060;">
                <button class="btn-close-custom position-absolute top-0 end-0 m-3 border-0 bg-transparent" @click="showPartnerSelectorModal = false">
                    <i class="bx bx-x fs-2 text-muted"></i>
                </button>
                
                <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                    <i class="bx bx-user-circle text-warning fs-3"></i> Danh Sách Tài Khoản Đối Tác
                </h5>
                <p class="text-muted small mb-4">Lựa chọn đối tác quản lý để hiển thị và biên tập cây gia phả tương ứng.</p>

                <!-- Search inside modal -->
                <div class="position-relative mb-3">
                    <input type="text" class="form-control ps-5 radius-30 border-2 shadow-none" v-model="partnerSearchQuery" placeholder="Tìm kiếm đối tác theo tên, email hoặc dòng họ...">
                    <span class="position-absolute top-50 translate-middle-y start-0 ms-3 text-secondary"><i class="bx bx-search"></i></span>
                </div>

                <!-- Partners List Container -->
                <div class="partner-list-scroll overflow-auto pe-1" style="max-height: 380px;">
                    <div class="row g-3" v-if="filteredPartners.length">
                        <div class="col-md-6" v-for="item in filteredPartners" :key="item.id">
                            <!-- Active Partner (Has Chi Nhanh / Clan Tree) -->
                            <div v-if="item.id_chi_nhanh" 
                                 class="partner-select-card p-3 rounded-3 border border-2 cursor-pointer transition-all d-flex align-items-center gap-3 animate__animated animate__fadeIn"
                                 :class="selectedPartner && selectedPartner.id === item.id ? 'border-warning bg-light-gold' : 'border-light bg-white'"
                                 @click="selectPartner(item)">
                                
                                <div class="partner-avatar-circle d-flex align-items-center justify-content-center text-white font-weight-bold"
                                     :style="{ background: getAvatarBg(item.nguoi_dung?.ho_ten || item.ten_goi) }">
                                    {{ getAvatarInitials(item.nguoi_dung?.ho_ten || item.ten_goi) }}
                                </div>
                                
                                <div class="overflow-hidden flex-grow-1">
                                    <div class="fw-bold text-dark text-truncate">{{ item.nguoi_dung?.ho_ten || item.ten_goi }}</div>
                                    <div class="text-muted small text-truncate">{{ item.nguoi_dung?.email || 'Chưa liên kết email' }}</div>
                                    <div class="mt-2">
                                        <span class="badge badge-gold-soft font-9 px-2 py-1 text-dark animate__animated animate__fadeIn" style="background: rgba(212, 175, 55, 0.15); color: #8a6d1c !important; border: 1px solid rgba(212, 175, 55, 0.25);">
                                            <i class="bx bx-home-alt me-1"></i>{{ item.dong_ho }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div v-if="selectedPartner && selectedPartner.id === item.id" class="text-warning">
                                    <i class="bx bxs-check-circle fs-4 animate__animated animate__scaleIn"></i>
                                </div>
                            </div>

                            <!-- Inactive Partner -->
                            <div v-else 
                                 class="partner-select-card partner-card-disabled p-3 rounded-3 border border-2 border-dashed border-light-secondary bg-light bg-opacity-70 text-muted d-flex align-items-center gap-3 animate__animated animate__fadeIn"
                                 style="opacity: 0.65; cursor: not-allowed;"
                                 title="Đối tác này chưa tạo cây gia phả">
                                
                                <div class="partner-avatar-circle d-flex align-items-center justify-content-center text-white font-weight-bold"
                                     style="background: #94a3b8; filter: grayscale(0.5); box-shadow: none;">
                                    {{ getAvatarInitials(item.nguoi_dung?.ho_ten || item.ten_goi) }}
                                </div>
                                
                                <div class="overflow-hidden flex-grow-1">
                                    <div class="fw-bold text-secondary text-truncate" style="text-decoration: line-through; opacity: 0.85;">{{ item.nguoi_dung?.ho_ten || item.ten_goi }}</div>
                                    <div class="text-muted small text-truncate" style="opacity: 0.85;">{{ item.nguoi_dung?.email || 'Chưa liên kết email' }}</div>
                                    <div class="mt-2 text-danger small fw-bold d-flex align-items-center gap-1">
                                        <i class="bx bx-error-circle fs-6"></i>
                                        <span>Đối tác này chưa tạo cây gia phả</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Search Empty State -->
                    <div v-else class="text-center py-5">
                        <i class="bx bx-user-x fs-1 text-muted opacity-25 mb-2"></i>
                        <h6 class="text-muted fw-bold">Không tìm thấy đối tác nào</h6>
                        <p class="text-muted small mb-0">Vui lòng thử từ khóa tìm kiếm khác.</p>
                    </div>
                </div>
                
                <div class="modal-footer border-0 p-0 mt-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary radius-30 px-4" @click="showPartnerSelectorModal = false">Đóng</button>
                    <button v-if="selectedPartner" type="button" class="btn btn-danger radius-30 px-4 text-white" @click="clearPartnerSelection">Bỏ chọn</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import toastr from 'toastr';

export default {
    name: 'DoiTocHoManagement',
    data() {
        return {
            listData: [],
            listPartners: [],
            selectedPartner: null,
            selectedChiNhanh: null,
            showPartnerSelectorModal: false,
            partnerSearchQuery: '',
            formData: {
                id: null,
                so_doi: '',
                ten_doi: '',
                mo_ta: '',
                trang_thai: 1,
                chi_nhanh_id: null
            },
            isEditing: false,
            isLoading: false,
            searchQuery: ''
        }
    },
    computed: {
        filteredPartners() {
            if (!this.partnerSearchQuery) {
                return this.listPartners;
            }
            const q = this.partnerSearchQuery.toLowerCase();
            return this.listPartners.filter(item => {
                const partnerName = (item.nguoi_dung?.ho_ten || item.ten_goi || '').toLowerCase();
                const partnerEmail = (item.nguoi_dung?.email || '').toLowerCase();
                const clanName = (item.dong_ho || '').toLowerCase();
                return partnerName.includes(q) || partnerEmail.includes(q) || clanName.includes(q);
            });
        },
        filteredList() {
            if (!this.selectedPartner || !this.selectedChiNhanh) return [];
            
            // Filter list by selected partner's Chi Nhanh
            let base = this.listData.filter(item => item.chi_nhanh_id == this.selectedChiNhanh);
            
            if (!this.searchQuery) return base;
            const q = this.searchQuery.toLowerCase();
            return base.filter(item => 
                (item.ten_doi && item.ten_doi.toLowerCase().includes(q)) ||
                (item.so_doi && item.so_doi.toString().includes(q)) ||
                (item.mo_ta && item.mo_ta.toLowerCase().includes(q))
            );
        }
    },
    mounted() {
        const saved = localStorage.getItem('selected_admin_partner');
        if (saved) {
            const item = JSON.parse(saved);
            this.selectedPartner = item;
            this.selectedChiNhanh = item.id_chi_nhanh;
            this.formData.chi_nhanh_id = item.id_chi_nhanh;
        }
        this.loadData();
        this.loadPartners();
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
            this.loadData();
            this.loadPartners();
        },
        loadData() {
            this.isLoading = true;
            axios.get('http://127.0.0.1:8000/api/doi-toc-ho/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.listData = res.data.data;
                    }
                })
                .catch(err => {
                    toastr.error('Lỗi khi tải danh sách đời tộc họ!');
                    console.log(err);
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        loadPartners() {
            axios.get('http://127.0.0.1:8000/api/admin/doi-tac/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.listPartners = res.data.data;
                        const saved = localStorage.getItem('selected_admin_partner');
                        if (saved) {
                            const item = JSON.parse(saved);
                            const fresh = this.listPartners.find(p => p.id === item.id);
                            if (fresh) {
                                this.selectedPartner = fresh;
                                this.selectedChiNhanh = fresh.id_chi_nhanh;
                                this.formData.chi_nhanh_id = fresh.id_chi_nhanh;
                                localStorage.setItem('selected_admin_partner', JSON.stringify(fresh));
                            }
                        }
                    }
                })
                .catch(err => console.error(err));
        },
        selectPartner(item) {
            this.selectedPartner = item;
            this.selectedChiNhanh = item.id_chi_nhanh;
            this.formData.chi_nhanh_id = item.id_chi_nhanh;
            this.showPartnerSelectorModal = false;
            localStorage.setItem('selected_admin_partner', JSON.stringify(item));
            toastr.success(`Đã chọn đối tác: ${item.nguoi_dung?.ho_ten || item.ten_goi}`);
        },
        clearPartnerSelection() {
            this.selectedPartner = null;
            this.selectedChiNhanh = null;
            this.formData.chi_nhanh_id = null;
            this.showPartnerSelectorModal = false;
            localStorage.removeItem('selected_admin_partner');
            toastr.info('Đã gỡ chọn đối tác.');
        },
        getAvatarInitials(name) {
            if (!name) return 'DT';
            const parts = name.trim().split(/\s+/);
            if (parts.length >= 2) {
                return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
            }
            return name.substring(0, 2).toUpperCase();
        },
        getAvatarBg(name) {
            if (!name) return '#d4af37';
            let hash = 0;
            for (let i = 0; i < name.length; i++) {
                hash = name.charCodeAt(i) + ((hash << 5) - hash);
            }
            const colors = [
                'linear-gradient(135deg, #d4af37 0%, #aa8c2c 100%)',
                'linear-gradient(135deg, #1e3c72 0%, #2a5298 100%)',
                'linear-gradient(135deg, #0f2027 0%, #203a43 100%)',
                'linear-gradient(135deg, #56ab2f 0%, #a8e063 100%)',
                'linear-gradient(135deg, #e65c00 0%, #f9d423 100%)',
                'linear-gradient(135deg, #833ab4 0%, #fd1d1d 100%)'
            ];
            const index = Math.abs(hash) % colors.length;
            return colors[index];
        },
        saveData() {
            if (!this.selectedPartner) {
                toastr.warning('Vui lòng chọn đối tác quản lý trước!');
                return;
            }
            this.formData.chi_nhanh_id = this.selectedChiNhanh;

            const url = this.isEditing 
                ? 'http://127.0.0.1:8000/api/doi-toc-ho/update'
                : 'http://127.0.0.1:8000/api/doi-toc-ho/create';
            
            axios.post(url, this.formData, this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        toastr.success(res.data.message);
                        this.loadData();
                        this.resetForm();
                    } else {
                        toastr.error(res.data.message);
                    }
                })
                .catch(err => {
                    toastr.error('Có lỗi xảy ra, vui lòng thử lại!');
                });
        },
        editItem(item) {
            this.isEditing = true;
            this.formData = { ...item };
        },
        deleteItem(id) {
            if (confirm('Bạn có chắc chắn muốn xóa đời họ này?')) {
                axios.post('http://127.0.0.1:8000/api/doi-toc-ho/delete', { id }, this.getHeaders())
                    .then(res => {
                        if (res.data.status) {
                            toastr.success(res.data.message);
                            this.loadData();
                        } else {
                            toastr.error(res.data.message);
                        }
                    })
                    .catch(err => {
                        toastr.error('Có lỗi xảy ra!');
                    });
            }
        },
        resetForm() {
            this.isEditing = false;
            this.formData = {
                id: null,
                so_doi: '',
                ten_doi: '',
                mo_ta: '',
                trang_thai: 1,
                chi_nhanh_id: this.selectedChiNhanh
            };
        }
    }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap');

.doi-toc-ho-management-wrapper {
    width: 100%;
}

.page-icon {
    width: 52px; height: 52px;
    background: linear-gradient(135deg, #1e2035 0%, #252740 100%);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 26px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

.luxury-panel {
    background: #ffffff !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
    border-radius: 20px !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02) !important;
    transition: background-color 0.3s, border-color 0.3s;
}

.panel-title {
    font-size: 14.5px;
    letter-spacing: 0.3px;
    font-family: 'Outfit', sans-serif;
}

.text-gradient-gold {
    background: linear-gradient(135deg, #d4af37 0%, #f39c12 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.text-secondary-custom {
    color: #4b5563;
    font-size: 12.5px;
    letter-spacing: 0.3px;
    font-family: 'Outfit', sans-serif;
}

.radius-10 {
    border-radius: 10px !important;
}

.radius-30 {
    border-radius: 30px !important;
}

.premium-input {
    background-color: #f8fafc !important;
    border: 1px solid rgba(0, 0, 0, 0.06) !important;
    font-family: 'Inter', sans-serif;
    color: #334155 !important;
    padding: 11px 16px !important;
    font-size: 13.5px !important;
    transition: all 0.25s ease !important;
}

.premium-input:focus {
    border-color: #f39c12 !important;
    background-color: #ffffff !important;
    box-shadow: 0 0 0 3px rgba(243, 156, 18, 0.1) !important;
}

.btn-filter-submit {
    background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%) !important;
    color: #ffffff !important;
    border: none !important;
    font-weight: 700 !important;
    font-size: 13px !important;
    padding: 10px 24px !important;
    box-shadow: 0 4px 10px rgba(79, 70, 229, 0.15) !important;
    transition: all 0.2s ease !important;
}

.btn-filter-submit:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 15px rgba(79, 70, 229, 0.25) !important;
}

/* TABLE LUXURY SYSTEM */
.modern-table {
    width: 100%;
}
.modern-table thead {
    background: #f8fafc;
}
.modern-table thead th {
    color: #475569;
    font-weight: 700;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    padding: 14px 16px;
    font-family: 'Outfit', sans-serif;
}
.modern-table tbody tr {
    transition: background 0.2s ease;
}
.modern-table tbody tr:hover {
    background-color: #f8fafc;
}
.modern-table tbody td {
    padding: 14px 16px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.03);
    font-size: 13.5px;
    color: #1e293b;
}

.search-box-premium {
    background: #f8fafc;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.search-box-premium input {
    font-size: 13.5px;
    color: #334155;
}

/* Circular reload button */
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
    border-color: #f39c12 !important;
    background: rgba(243, 156, 18, 0.05) !important;
}

.btn-action-edit, .btn-action-delete {
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

.bg-warning-soft {
    background: rgba(243, 156, 18, 0.1) !important;
}

.badge-active {
    background: #f0fdf4 !important;
    color: #16a34a !important;
    border: 1px solid rgba(22, 163, 74, 0.15);
}

.badge-paused {
    background: #fef2f2 !important;
    color: #ef4444 !important;
    border: 1px solid rgba(239, 68, 68, 0.15);
}

/* Custom Vue Modal Styling for Partner Selector */
.custom-modal-backdrop {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(8px);
    z-index: 1050;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.custom-modal-content {
    width: 100%;
    max-width: 650px;
    background: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
}
.btn-close-custom {
    transition: all 0.2s ease;
}
.btn-close-custom:hover {
    transform: rotate(90deg);
}

/* Partner Selector Premium Styling */
.partner-list-scroll {
    scrollbar-width: thin;
    scrollbar-color: #d4af37 transparent;
}
.partner-list-scroll::-webkit-scrollbar {
    width: 6px;
}
.partner-list-scroll::-webkit-scrollbar-thumb {
    background-color: #d4af37;
    border-radius: 10px;
}
.partner-select-card {
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.partner-select-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(212, 175, 55, 0.15);
    border-color: #ffd700 !important;
}
.partner-select-card.border-warning {
    box-shadow: 0 4px 12px rgba(212, 175, 55, 0.25);
}
.bg-light-gold {
    background-color: rgba(212, 175, 55, 0.05) !important;
}
.partner-avatar-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    font-size: 16px;
    font-weight: 700;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    flex-shrink: 0;
}
.badge-gold-soft {
    background: rgba(212, 175, 55, 0.15) !important;
    color: #8a6d1c !important;
    border: 1px solid rgba(212, 175, 55, 0.25) !important;
}
</style>
