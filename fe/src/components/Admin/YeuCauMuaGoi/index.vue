<template>
    <div class="row g-4">
        <!-- Main Panel: Purchase Requests List -->
        <div class="col-12">
            <div class="card luxury-panel border-0 shadow-sm">
                <div class="card-header bg-transparent py-4 border-0 border-bottom border-light-subtle d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <h5 class="mb-0 fw-bold panel-title text-dark">
                        <i class="bx bx-receipt me-2 text-warning fs-4"></i> Quản Lý Yêu Cầu Mua Gói Đối Tác
                    </h5>
                    
                    <!-- Filters and Actions Group -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="btn-group premium-btn-group" role="group">
                            <button 
                                v-for="status in statusFilters" 
                                :key="status.value"
                                type="button" 
                                class="btn btn-filter-pill"
                                :class="{'active': activeFilter === status.value}"
                                @click="activeFilter = status.value"
                            >
                                {{ status.label }}
                            </button>
                        </div>

                        <button 
                            class="btn btn-refresh-premium rounded-circle d-flex align-items-center justify-content-center" 
                            @click="loadData" 
                            :disabled="isLoading" 
                            title="Làm mới dữ liệu"
                        >
                            <i class="bx bx-sync fs-5 text-warning" :class="{'bx-spin': isLoading}"></i>
                        </button>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- Search Bar and Summary Stats -->
                    <div class="row g-3 mb-4 align-items-center">
                        <div class="col-md-6 col-12">
                            <div class="input-group radius-10 overflow-hidden border-2 search-box-premium shadow-sm">
                                <span class="input-group-text border-0 bg-transparent ps-3"><i class="bx bx-search text-secondary"></i></span>
                                <input 
                                    type="text" 
                                    class="form-control border-0 shadow-none ps-2 bg-transparent" 
                                    placeholder="Tìm theo tên khách hàng, email hoặc tên gói..." 
                                    v-model="searchQuery"
                                >
                            </div>
                        </div>
                        <div class="col-md-6 col-12 text-md-end text-start">
                            <span class="badge bg-gold-soft text-warning px-3 py-2 fw-semibold radius-10">
                                <i class="bx bx-info-circle me-1"></i> Tìm thấy {{ filteredRequests.length }} yêu cầu phù hợp
                            </span>
                        </div>
                    </div>

                    <!-- Requests Table -->
                    <div class="table-responsive rounded-3 border border-light-subtle premium-table-container">
                        <table class="table modern-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th width="5%" class="text-center">#</th>
                                    <th width="25%">Khách hàng</th>
                                    <th width="20%">Gói đăng ký</th>
                                    <th width="15%" class="text-end">Số tiền</th>
                                    <th width="15%" class="text-center">Trạng thái</th>
                                    <th width="20%" class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="isLoading">
                                    <td colspan="6" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center justify-content-center gap-2">
                                            <div class="spinner-border text-warning" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <span class="text-secondary small mt-2">Đang tải danh sách yêu cầu...</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-else-if="filteredRequests.length === 0">
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <div class="empty-state py-4">
                                            <i class="bx bx-receipt fs-1 mb-2 d-block text-warning opacity-50"></i>
                                            <p class="mb-0 fw-medium">Không tìm thấy yêu cầu mua gói nào</p>
                                            <span class="small text-secondary">Hệ thống chưa ghi nhận dữ liệu giao dịch phù hợp với bộ lọc này.</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-else v-for="(item, index) in filteredRequests" :key="item.id" class="premium-row">
                                    <td class="text-center fw-bold text-secondary">{{ index + 1 }}</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <strong class="text-dark customer-name">{{ item.nguoi_dung ? item.nguoi_dung.ho_ten : 'Khách hàng #' + item.id_nguoi_dung }}</strong>
                                            <span class="text-secondary small text-lowercase"><i class="bx bx-envelope me-1"></i>{{ item.nguoi_dung ? item.nguoi_dung.email : 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold text-gold-accent">{{ item.ten_goi || 'Gói Đối Tác' }}</span>
                                            <span class="text-secondary small text-muted">
                                                <i class="bx bx-time-five me-1"></i>{{ formatDate(item.created_at) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold text-gold fs-6">
                                        {{ formatCurrency(item.so_tien) }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-premium" :class="getStatusClass(item.trang_thai)" :title="item.trang_thai === 'REJECTED' ? 'Lý do: ' + (item.ly_do_tu_choi || 'Không có lý do') : ''">
                                            <span class="pulse-dot" v-if="item.trang_thai === 'PENDING'"></span>
                                            {{ getStatusText(item.trang_thai) }}
                                            <i class="bx bx-help-circle ms-1 small" v-if="item.trang_thai === 'REJECTED' && item.ly_do_tu_choi"></i>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2" v-if="item.trang_thai === 'PENDING'">
                                            <button 
                                                class="btn btn-premium-action btn-approve-glow d-flex align-items-center gap-1" 
                                                @click="approveRequest(item.id)" 
                                                :disabled="isProcessing"
                                                title="Phê duyệt kích hoạt"
                                            >
                                                <i class="bx bx-check fs-5"></i> Duyệt
                                            </button>
                                            <button 
                                                class="btn btn-premium-action btn-reject-glow d-flex align-items-center gap-1" 
                                                @click="openRejectModal(item)" 
                                                :disabled="isProcessing"
                                                title="Từ chối yêu cầu"
                                            >
                                                <i class="bx bx-x fs-5"></i> Từ chối
                                            </button>
                                        </div>
                                        <div class="d-flex justify-content-center gap-2" v-else>
                                            <button 
                                                class="btn btn-premium-delete" 
                                                @click="deleteRequest(item.id)" 
                                                :disabled="isProcessing"
                                                title="Xóa yêu cầu"
                                            >
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

    <!-- Reject Reason Modal (Luxury Glassmorphism Style) -->
    <div class="modal fade" id="rejectReasonModal" tabindex="-1" aria-hidden="true" ref="rejectModalRef">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content luxury-modal border-0 overflow-hidden">
                <div class="modal-header border-0 bg-transparent py-4 px-4 d-flex align-items-center justify-content-between">
                    <h5 class="modal-title fw-bold text-danger d-flex align-items-center">
                        <i class="bx bx-x-circle me-2 fs-4"></i> Từ Chối Yêu Cầu Mua Gói
                    </h5>
                    <button type="button" class="btn-close-luxury" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x fs-4"></i>
                    </button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <p class="text-secondary small mb-3">
                        Vui lòng nhập lý do từ chối yêu cầu mua gói đối tác của khách hàng 
                        <strong>{{ selectedRequest ? (selectedRequest.nguoi_dung ? selectedRequest.nguoi_dung.ho_ten : '') : '' }}</strong>. Lý do này sẽ được ghi vào nhật ký thao tác để đối chiếu.
                    </p>
                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary-custom">Lý do từ chối <span class="text-danger">*</span></label>
                        <textarea 
                            class="form-control premium-input radius-10 border-2 shadow-none" 
                            rows="4" 
                            placeholder="Nhập chi tiết lý do từ chối (ví dụ: Số tiền không khớp, sai nội dung chuyển khoản...)" 
                            v-model="rejectReason"
                            required
                        ></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary radius-30 px-4" data-bs-dismiss="modal">Hủy</button>
                        <button 
                            type="button" 
                            class="btn btn-danger-submit text-white radius-30 px-4 fw-bold shadow-sm"
                            @click="submitReject"
                            :disabled="!rejectReason.trim() || isProcessing"
                        >
                            <span v-if="!isProcessing">Xác nhận Từ Chối</span>
                            <span v-else><i class="bx bx-loader-alt bx-spin me-1"></i> Đang xử lý...</span>
                        </button>
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
    name: 'YeuCauMuaGoiManagement',
    data() {
        return {
            listRequests: [],
            isLoading: false,
            isProcessing: false,
            searchQuery: '',
            activeFilter: 'ALL',
            rejectReason: '',
            selectedRequest: null,
            rejectModal: null,
            statusFilters: [
                { value: 'ALL', label: 'Tất Cả' },
                { value: 'PENDING', label: 'Chờ Duyệt' },
                { value: 'APPROVED', label: 'Đã Duyệt' },
                { value: 'REJECTED', label: 'Đã Từ Chối' },
                { value: 'EXPIRED', label: 'Hết Hạn' }
            ]
        }
    },
    computed: {
        filteredRequests() {
            let data = this.listRequests;

            // 1. Filter by active status filter button
            if (this.activeFilter !== 'ALL') {
                data = data.filter(item => item.trang_thai === this.activeFilter);
            }

            // 2. Filter by search query
            if (this.searchQuery) {
                const q = this.searchQuery.toLowerCase();
                data = data.filter(item => {
                    const name = item.nguoi_dung && item.nguoi_dung.ho_ten ? item.nguoi_dung.ho_ten.toLowerCase() : '';
                    const email = item.nguoi_dung && item.nguoi_dung.email ? item.nguoi_dung.email.toLowerCase() : '';
                    const package_name = item.ten_goi ? item.ten_goi.toLowerCase() : '';
                    return name.includes(q) || email.includes(q) || package_name.includes(q);
                });
            }

            return data;
        }
    },
    mounted() {
        this.loadData();
        // Initialize bootstrap modal using window.bootstrap
        if (window.bootstrap && this.$refs.rejectModalRef) {
            this.rejectModal = new window.bootstrap.Modal(this.$refs.rejectModalRef);
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
        loadData() {
            this.isLoading = true;
            axios.get('http://127.0.0.1:8000/api/admin/yeu-cau-mua-goi/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.listRequests = res.data.data;
                    }
                })
                .catch(err => {
                    toastr.error('Lỗi tải dữ liệu yêu cầu mua gói!');
                    console.error(err);
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        approveRequest(id) {
            if (confirm('Bạn có chắc chắn muốn phê duyệt kích hoạt tài khoản đối tác này?')) {
                this.isProcessing = true;
                axios.post('http://127.0.0.1:8000/api/admin/yeu-cau-mua-goi/approve', { id }, this.getHeaders())
                    .then(res => {
                        if (res.data.status) {
                            toastr.success(res.data.message);
                            this.loadData();
                        } else {
                            toastr.error(res.data.message);
                        }
                    })
                    .catch(err => {
                        toastr.error(err.response?.data?.message || 'Có lỗi xảy ra khi phê duyệt!');
                    })
                    .finally(() => {
                        this.isProcessing = false;
                    });
            }
        },
        openRejectModal(request) {
            this.selectedRequest = request;
            this.rejectReason = '';
            if (this.rejectModal) {
                this.rejectModal.show();
            }
        },
        submitReject() {
            if (!this.rejectReason.trim()) {
                toastr.warning('Vui lòng điền lý do từ chối!');
                return;
            }
            this.isProcessing = true;
            axios.post('http://127.0.0.1:8000/api/admin/yeu-cau-mua-goi/reject', {
                id: this.selectedRequest.id,
                ly_do_tu_choi: this.rejectReason
            }, this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        toastr.success(res.data.message);
                        if (this.rejectModal) {
                            this.rejectModal.hide();
                        }
                        this.loadData();
                    } else {
                        toastr.error(res.data.message);
                    }
                })
                .catch(err => {
                    toastr.error(err.response?.data?.message || 'Có lỗi xảy ra khi từ chối!');
                })
                .finally(() => {
                    this.isProcessing = false;
                });
        },
        deleteRequest(id) {
            if (confirm('Bạn có chắc chắn muốn xóa (Soft Delete) yêu cầu mua gói này khỏi hệ thống?')) {
                this.isProcessing = true;
                axios.post('http://127.0.0.1:8000/api/admin/yeu-cau-mua-goi/delete', { id }, this.getHeaders())
                    .then(res => {
                        if (res.data.status) {
                            toastr.success(res.data.message);
                            this.loadData();
                        } else {
                            toastr.error(res.data.message);
                        }
                    })
                    .catch(err => {
                        toastr.error('Có lỗi xảy ra khi xóa!');
                    })
                    .finally(() => {
                        this.isProcessing = false;
                    });
            }
        },
        formatDate(dateStr) {
            if (!dateStr) return '---';
            const date = new Date(dateStr);
            return date.toLocaleString('vi-VN', { 
                day: '2-digit', 
                month: '2-digit', 
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        formatCurrency(val) {
            if (val === undefined || val === null) return '0 VNĐ';
            return Number(val).toLocaleString('vi-VN') + ' VNĐ';
        },
        getStatusClass(status) {
            switch(status) {
                case 'PENDING': 
                case '0': 
                case 0: 
                    return 'badge-pending';
                case 'APPROVED': 
                case '1': 
                case 1: 
                case 'Hoạt động': 
                    return 'badge-approved';
                case 'REJECTED': 
                case '2': 
                case 2: 
                case 'Từ chối': 
                    return 'badge-rejected';
                case 'EXPIRED': 
                case 'Hết hạn': 
                    return 'badge-expired';
                default: return '';
            }
        },
        getStatusText(status) {
            switch(status) {
                case 'PENDING': 
                case '0': 
                case 0: 
                    return 'Chờ duyệt';
                case 'APPROVED': 
                case '1': 
                case 1: 
                case 'Hoạt động': 
                    return 'Đã duyệt';
                case 'REJECTED': 
                case '2': 
                case 2: 
                case 'Từ chối': 
                    return 'Đã từ chối';
                case 'EXPIRED': 
                case 'Hết hạn': 
                    return 'Hết hạn';
                default: return status || '---';
            }
        }
    }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap');

.luxury-panel {
    background: #ffffff !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
    border-radius: 20px !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02) !important;
    transition: background-color 0.3s, border-color 0.3s;
}

.panel-title {
    font-size: 15.5px;
    letter-spacing: 0.3px;
    font-family: 'Outfit', sans-serif;
    background: linear-gradient(135deg, #d4af37 0%, #f39c12 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.radius-10 {
    border-radius: 10px !important;
}

.radius-30 {
    border-radius: 30px !important;
}

.text-gold {
    color: #b8860b !important;
}

.text-gold-accent {
    color: #c9961a;
}

.bg-gold-soft {
    background: rgba(243, 156, 18, 0.08) !important;
}

.search-box-premium {
    background: #f8fafc;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.search-box-premium input {
    font-size: 13.5px;
    color: #334155;
    font-family: 'Inter', sans-serif;
}

/* PREMIUM FILTER BUTTONS */
.premium-btn-group {
    background: #f1f5f9;
    padding: 4px;
    border-radius: 30px;
    border: 1px solid rgba(0,0,0,0.02);
}

.btn-filter-pill {
    background: transparent !important;
    border: none !important;
    font-size: 12.5px !important;
    font-weight: 600 !important;
    color: #475569 !important;
    padding: 8px 18px !important;
    border-radius: 30px !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
    box-shadow: none !important;
}

.btn-filter-pill:hover {
    color: #0f172a !important;
}

.btn-filter-pill.active {
    background: #ffffff !important;
    color: #b8860b !important;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04) !important;
}

/* TABLE LUXURY SYSTEM */
.premium-table-container {
    max-height: 520px;
    overflow-y: auto;
}

.modern-table {
    width: 100%;
}
.modern-table thead {
    background: #f8fafc;
    position: sticky;
    top: 0;
    z-index: 10;
}
.modern-table thead th {
    color: #475569;
    font-weight: 700;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    padding: 15px 16px;
    font-family: 'Outfit', sans-serif;
}
.modern-table tbody tr {
    transition: all 0.2s ease;
}
.modern-table tbody tr:hover {
    background-color: rgba(243, 156, 18, 0.015);
}
.modern-table tbody td {
    padding: 16px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.03);
    font-size: 13.5px;
    color: #1e293b;
    font-family: 'Inter', sans-serif;
}

.customer-name {
    font-size: 13.8px;
}

/* STATUS BADGES WITH GLOWS */
.badge-premium {
    display: inline-flex;
    align-items: center;
    padding: 6px 14px;
    font-size: 12px;
    font-weight: 700;
    border-radius: 30px;
    letter-spacing: 0.2px;
    gap: 6px;
    position: relative;
    cursor: default;
}

.badge-pending {
    background: rgba(217, 119, 6, 0.08);
    color: #d97706;
    border: 1px solid rgba(217, 119, 6, 0.2);
}

.badge-approved {
    background: rgba(16, 185, 129, 0.08);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.badge-rejected {
    background: rgba(239, 68, 68, 0.08);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.2);
    cursor: help;
}

.badge-expired {
    background: rgba(100, 116, 139, 0.08);
    color: #64748b;
    border: 1px solid rgba(100, 116, 139, 0.2);
}

/* Pulsing pending dot */
.pulse-dot {
    width: 6px;
    height: 6px;
    background: #d97706;
    border-radius: 50%;
    box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.7);
    animation: pulse 1.6s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.7);
    }
    70% {
        transform: scale(1);
        box-shadow: 0 0 0 6px rgba(217, 119, 6, 0);
    }
    100% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(217, 119, 6, 0);
    }
}

/* PREMIUM ACTION BUTTONS */
.btn-premium-action {
    border: none !important;
    font-weight: 700 !important;
    font-size: 12.5px !important;
    padding: 7px 15px !important;
    border-radius: 20px !important;
    transition: all 0.2s ease !important;
    box-shadow: 0 3px 8px rgba(0,0,0,0.03);
}

.btn-approve-glow {
    background: #10b981 !important;
    color: #ffffff !important;
}
.btn-approve-glow:hover:not(:disabled) {
    background: #059669 !important;
    transform: translateY(-1px);
    box-shadow: 0 6px 14px rgba(16, 185, 129, 0.25) !important;
}

.btn-reject-glow {
    background: rgba(239, 68, 68, 0.08) !important;
    color: #ef4444 !important;
    border: 1px solid rgba(239, 68, 68, 0.15) !important;
}
.btn-reject-glow:hover:not(:disabled) {
    background: #ef4444 !important;
    color: #ffffff !important;
    border-color: transparent !important;
    transform: translateY(-1px);
    box-shadow: 0 6px 14px rgba(239, 68, 68, 0.25) !important;
}

.btn-premium-delete {
    background: #ffffff;
    border: 1px solid rgba(0, 0, 0, 0.06);
    color: #dc3545;
    font-size: 13.5px;
    border-radius: 8px;
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}
.btn-premium-delete:hover:not(:disabled) {
    background: #dc3545;
    color: #fff;
    border-color: transparent;
    box-shadow: 0 4px 10px rgba(220, 53, 69, 0.15);
}

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

/* CUSTOM MODAL & INPUT */
.luxury-modal {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(0, 0, 0, 0.08) !important;
    border-radius: 24px !important;
}

.btn-close-luxury {
    background: transparent;
    border: none;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s ease;
}
.btn-close-luxury:hover {
    color: #0f172a;
    transform: rotate(90deg);
}

.premium-input {
    background-color: #f8fafc !important;
    border: 1px solid rgba(0, 0, 0, 0.06) !important;
    color: #334155 !important;
    padding: 12px 16px !important;
    font-size: 13.5px !important;
    font-family: 'Inter', sans-serif;
    transition: all 0.25s ease !important;
}

.premium-input:focus {
    border-color: #ef4444 !important;
    background-color: #ffffff !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
}

.btn-danger-submit {
    background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%) !important;
    border: none !important;
    font-weight: 700 !important;
    font-size: 13px !important;
    padding: 10px 24px !important;
    box-shadow: 0 4px 10px rgba(239, 68, 68, 0.15) !important;
    transition: all 0.2s ease !important;
}
.btn-danger-submit:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 6px 15px rgba(239, 68, 68, 0.25) !important;
}

.text-secondary-custom {
    color: #4b5563;
    font-size: 12.5px;
    letter-spacing: 0.3px;
    font-family: 'Outfit', sans-serif;
}
</style>
