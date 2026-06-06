<template>
    <div class="row g-4">
        <!-- Main Panel: Transaction History -->
        <div class="col-12">
            <div class="card luxury-panel border-0 shadow-sm">
                <div class="card-header bg-transparent py-4 border-0 border-bottom border-light-subtle d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h5 class="mb-0 fw-bold panel-title">
                            <i class="bx bx-receipt me-2 fs-4"></i> Lịch Sử Giao Dịch Đối Tác
                        </h5>
                        <p class="text-secondary small mb-0 mt-1">
                            <i class="bx bx-bolt-circle text-success me-1"></i>
                            Hệ thống tự động kích hoạt tài khoản ngay khi xác nhận thanh toán đủ số tiền.
                        </p>
                    </div>

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
                                <span class="count-badge" v-if="getCountByStatus(status.value) > 0">
                                    {{ getCountByStatus(status.value) }}
                                </span>
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
                    <!-- Stats Row -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3 col-6">
                            <div class="stat-mini-card stat-approved">
                                <div class="stat-mini-icon"><i class="bx bx-check-circle"></i></div>
                                <div>
                                    <div class="stat-mini-num">{{ getCountByStatus('APPROVED') }}</div>
                                    <div class="stat-mini-label">Đã kích hoạt</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stat-mini-card stat-pending">
                                <div class="stat-mini-icon"><i class="bx bx-time-five"></i></div>
                                <div>
                                    <div class="stat-mini-num">{{ getCountByStatus('PENDING') }}</div>
                                    <div class="stat-mini-label">Chờ duyệt thủ công</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stat-mini-card stat-expired">
                                <div class="stat-mini-icon"><i class="bx bx-time"></i></div>
                                <div>
                                    <div class="stat-mini-num">{{ getCountByStatus('EXPIRED') }}</div>
                                    <div class="stat-mini-label">Hết hạn</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stat-mini-card stat-revenue">
                                <div class="stat-mini-icon"><i class="bx bx-money"></i></div>
                                <div>
                                    <div class="stat-mini-num">{{ totalRevenue }}</div>
                                    <div class="stat-mini-label">Tổng doanh thu</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search Bar -->
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
                                <i class="bx bx-info-circle me-1"></i> Tìm thấy {{ filteredRequests.length }} giao dịch phù hợp
                            </span>
                        </div>
                    </div>

                    <!-- Transactions Table -->
                    <div class="table-responsive rounded-3 border border-light-subtle premium-table-container">
                        <table class="table modern-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th width="4%" class="text-center">#</th>
                                    <th width="20%">Khách hàng</th>
                                    <th width="18%">Gói đăng ký</th>
                                    <th width="12%" class="text-end">Số tiền</th>
                                    <th width="14%" class="text-center">Ngày kích hoạt</th>
                                    <th width="14%" class="text-center">Hết hạn</th>
                                    <th width="10%" class="text-center">Trạng thái</th>
                                    <th width="8%" class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="isLoading">
                                    <td colspan="8" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center justify-content-center gap-2">
                                            <div class="spinner-border text-warning" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <span class="text-secondary small mt-2">Đang tải lịch sử giao dịch...</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-else-if="filteredRequests.length === 0">
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <div class="empty-state py-4">
                                            <i class="bx bx-receipt fs-1 mb-2 d-block text-warning opacity-50"></i>
                                            <p class="mb-0 fw-medium">Không tìm thấy giao dịch nào</p>
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
                                        <span class="small text-secondary" v-if="item.ngay_bat_dau">{{ formatDateOnly(item.ngay_bat_dau) }}</span>
                                        <span class="small text-muted opacity-50" v-else>—</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="small" :class="isExpiringSoon(item.ngay_ket_thuc) ? 'text-warning fw-semibold' : 'text-secondary'" v-if="item.ngay_ket_thuc">
                                            {{ formatDateOnly(item.ngay_ket_thuc) }}
                                            <i class="bx bx-error ms-1" v-if="isExpiringSoon(item.ngay_ket_thuc)" title="Sắp hết hạn"></i>
                                        </span>
                                        <span class="small text-muted opacity-50" v-else>—</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-premium" :class="getStatusClass(item.trang_thai)" :title="item.trang_thai === 'REJECTED' ? 'Lý do: ' + (item.ly_do_tu_choi || 'Không có lý do') : ''">
                                            <span class="pulse-dot" v-if="item.trang_thai === 'PENDING'"></span>
                                            {{ getStatusText(item.trang_thai) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <!-- PENDING: Cho phép Admin duyệt thủ công hoặc từ chối -->
                                        <div class="d-flex justify-content-center gap-1" v-if="item.trang_thai === 'PENDING'">
                                            <button
                                                class="btn btn-action-sm btn-approve-sm"
                                                @click="approveRequest(item.id)"
                                                :disabled="isProcessing"
                                                title="Duyệt thủ công"
                                            >
                                                <i class="bx bx-check fs-6"></i>
                                            </button>
                                            <button
                                                class="btn btn-action-sm btn-reject-sm"
                                                @click="openRejectModal(item)"
                                                :disabled="isProcessing"
                                                title="Từ chối"
                                            >
                                                <i class="bx bx-x fs-6"></i>
                                            </button>
                                        </div>
                                        <!-- APPROVED / khác: Chỉ có nút xóa -->
                                        <div class="d-flex justify-content-center" v-else>
                                            <button
                                                class="btn btn-premium-delete"
                                                @click="deleteRequest(item.id)"
                                                :disabled="isProcessing"
                                                title="Xóa giao dịch"
                                            >
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- PENDING notice -->
                    <div class="pending-notice mt-4 p-3 rounded-3 d-flex align-items-start gap-3" v-if="getCountByStatus('PENDING') > 0">
                        <i class="bx bx-info-circle text-warning fs-4 mt-1 flex-shrink-0"></i>
                        <div>
                            <strong class="text-dark d-block">Có {{ getCountByStatus('PENDING') }} giao dịch chờ duyệt thủ công</strong>
                            <span class="text-secondary small">
                                Các giao dịch PENDING thường là do Admin tạo trực tiếp hoặc giao dịch ngoài luồng thanh toán tự động. 
                                Từ khi hệ thống áp dụng <strong>tự động duyệt</strong>, các thanh toán online mới sẽ được kích hoạt ngay lập tức.
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Reason Modal -->
    <div class="modal fade" id="rejectReasonModal" tabindex="-1" aria-hidden="true" ref="rejectModalRef">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content luxury-modal border-0 overflow-hidden">
                <div class="modal-header border-0 bg-transparent py-4 px-4 d-flex align-items-center justify-content-between">
                    <h5 class="modal-title fw-bold text-danger d-flex align-items-center">
                        <i class="bx bx-x-circle me-2 fs-4"></i> Từ Chối Giao Dịch
                    </h5>
                    <button type="button" class="btn-close-luxury" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x fs-4"></i>
                    </button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <p class="text-secondary small mb-3">
                        Vui lòng nhập lý do từ chối giao dịch của khách hàng
                        <strong>{{ selectedRequest ? (selectedRequest.nguoi_dung ? selectedRequest.nguoi_dung.ho_ten : '') : '' }}</strong>.
                    </p>
                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary-custom">Lý do từ chối <span class="text-danger">*</span></label>
                        <textarea
                            class="form-control premium-input radius-10 border-2 shadow-none"
                            rows="4"
                            placeholder="Nhập chi tiết lý do từ chối..."
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
                { value: 'ALL',      label: 'Tất Cả' },
                { value: 'APPROVED', label: 'Đã Kích Hoạt' },
                { value: 'PENDING',  label: 'Chờ Duyệt' },
                { value: 'REJECTED', label: 'Đã Từ Chối' },
                { value: 'EXPIRED',  label: 'Hết Hạn' },
            ]
        };
    },
    computed: {
        filteredRequests() {
            let data = this.listRequests;
            if (this.activeFilter !== 'ALL') {
                data = data.filter(item => item.trang_thai === this.activeFilter);
            }
            if (this.searchQuery) {
                const q = this.searchQuery.toLowerCase();
                data = data.filter(item => {
                    const name    = item.nguoi_dung?.ho_ten?.toLowerCase() || '';
                    const email   = item.nguoi_dung?.email?.toLowerCase() || '';
                    const pkg     = item.ten_goi?.toLowerCase() || '';
                    return name.includes(q) || email.includes(q) || pkg.includes(q);
                });
            }
            return data;
        },
        totalRevenue() {
            const total = this.listRequests
                .filter(i => i.trang_thai === 'APPROVED')
                .reduce((sum, i) => sum + Number(i.so_tien || 0), 0);
            return total.toLocaleString('vi-VN') + ' VNĐ';
        }
    },
    mounted() {
        this.loadData();
        if (window.bootstrap && this.$refs.rejectModalRef) {
            this.rejectModal = new window.bootstrap.Modal(this.$refs.rejectModalRef);
        }
    },
    methods: {
        getHeaders() {
            return { headers: { Authorization: 'Bearer ' + localStorage.getItem('access_token') } };
        },
        getCountByStatus(status) {
            if (status === 'ALL') return this.listRequests.length;
            return this.listRequests.filter(i => i.trang_thai === status).length;
        },
        loadData() {
            this.isLoading = true;
            axios.get('http://127.0.0.1:8000/api/admin/yeu-cau-mua-goi/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.listRequests = res.data.data;
                    }
                })
                .catch(() => { toastr.error('Lỗi tải dữ liệu giao dịch!'); })
                .finally(() => { this.isLoading = false; });
        },
        approveRequest(id) {
            if (!confirm('Bạn có chắc chắn muốn duyệt thủ công giao dịch này?')) return;
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
                .catch(err => { toastr.error(err.response?.data?.message || 'Có lỗi xảy ra!'); })
                .finally(() => { this.isProcessing = false; });
        },
        openRejectModal(request) {
            this.selectedRequest = request;
            this.rejectReason = '';
            if (this.rejectModal) this.rejectModal.show();
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
                        if (this.rejectModal) this.rejectModal.hide();
                        this.loadData();
                    } else {
                        toastr.error(res.data.message);
                    }
                })
                .catch(err => { toastr.error(err.response?.data?.message || 'Có lỗi xảy ra!'); })
                .finally(() => { this.isProcessing = false; });
        },
        deleteRequest(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa giao dịch này?')) return;
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
                .catch(() => { toastr.error('Có lỗi xảy ra khi xóa!'); })
                .finally(() => { this.isProcessing = false; });
        },
        formatDate(dateStr) {
            if (!dateStr) return '---';
            const date = new Date(dateStr);
            return date.toLocaleString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        },
        formatDateOnly(dateStr) {
            if (!dateStr) return '---';
            const date = new Date(dateStr);
            return date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
        },
        isExpiringSoon(dateStr) {
            if (!dateStr) return false;
            const expiry = new Date(dateStr);
            const now = new Date();
            const diffDays = (expiry - now) / (1000 * 60 * 60 * 24);
            return diffDays >= 0 && diffDays <= 30;
        },
        formatCurrency(val) {
            if (val === undefined || val === null) return '0 VNĐ';
            return Number(val).toLocaleString('vi-VN') + ' VNĐ';
        },
        getStatusClass(status) {
            switch (status) {
                case 'PENDING':    return 'badge-pending';
                case 'APPROVED':   return 'badge-approved';
                case 'REJECTED':   return 'badge-rejected';
                case 'EXPIRED':    return 'badge-expired';
                default:           return '';
            }
        },
        getStatusText(status) {
            switch (status) {
                case 'PENDING':    return 'Chờ duyệt';
                case 'APPROVED':   return 'Đã kích hoạt';
                case 'REJECTED':   return 'Đã từ chối';
                case 'EXPIRED':    return 'Hết hạn';
                default:           return status || '---';
            }
        }
    }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap');

.luxury-panel {
    background: #ffffff !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
    border-radius: 20px !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02) !important;
}

.panel-title {
    font-size: 15.5px;
    letter-spacing: 0.3px;
    font-family: 'Outfit', sans-serif;
    background: linear-gradient(135deg, #d4af37 0%, #f39c12 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.radius-10 { border-radius: 10px !important; }
.radius-30 { border-radius: 30px !important; }
.text-gold { color: #b8860b !important; }
.text-gold-accent { color: #c9961a; }
.bg-gold-soft { background: rgba(243, 156, 18, 0.08) !important; }

/* Stats Mini Cards */
.stat-mini-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    border-radius: 14px;
    border: 1px solid;
}

.stat-mini-icon {
    font-size: 1.5rem;
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.stat-mini-num {
    font-weight: 800;
    font-size: 1.1rem;
    font-family: 'Outfit', sans-serif;
    line-height: 1;
}

.stat-mini-label {
    font-size: 11.5px;
    color: #64748b;
    margin-top: 2px;
    font-family: 'Inter', sans-serif;
}

.stat-approved { background: rgba(16,185,129,0.05); border-color: rgba(16,185,129,0.15); }
.stat-approved .stat-mini-icon { background: rgba(16,185,129,0.1); color: #10b981; }
.stat-approved .stat-mini-num { color: #059669; }

.stat-pending { background: rgba(217,119,6,0.05); border-color: rgba(217,119,6,0.15); }
.stat-pending .stat-mini-icon { background: rgba(217,119,6,0.1); color: #d97706; }
.stat-pending .stat-mini-num { color: #b45309; }

.stat-expired { background: rgba(100,116,139,0.05); border-color: rgba(100,116,139,0.15); }
.stat-expired .stat-mini-icon { background: rgba(100,116,139,0.1); color: #64748b; }
.stat-expired .stat-mini-num { color: #475569; }

.stat-revenue { background: rgba(184,134,11,0.05); border-color: rgba(184,134,11,0.15); }
.stat-revenue .stat-mini-icon { background: rgba(184,134,11,0.1); color: #b8860b; }
.stat-revenue .stat-mini-num { color: #92650a; font-size: 0.85rem; }

/* Search box */
.search-box-premium {
    background: #f8fafc;
    border: 1px solid rgba(0, 0, 0, 0.05);
}
.search-box-premium input {
    font-size: 13.5px;
    color: #334155;
    font-family: 'Inter', sans-serif;
}

/* Filter Buttons */
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
    padding: 6px 14px !important;
    border-radius: 30px !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
    box-shadow: none !important;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.btn-filter-pill:hover { color: #0f172a !important; }
.btn-filter-pill.active {
    background: #ffffff !important;
    color: #b8860b !important;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04) !important;
}

.count-badge {
    background: #e2e8f0;
    color: #475569;
    font-size: 10px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 10px;
    min-width: 18px;
    text-align: center;
}
.btn-filter-pill.active .count-badge {
    background: rgba(184,134,11,0.15);
    color: #b8860b;
}

/* Table */
.premium-table-container {
    max-height: 480px;
    overflow-y: auto;
}
.modern-table { width: 100%; }
.modern-table thead {
    background: #f8fafc;
    position: sticky;
    top: 0;
    z-index: 10;
}
.modern-table thead th {
    color: #475569;
    font-weight: 700;
    font-size: 11.5px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    padding: 13px 14px;
    font-family: 'Outfit', sans-serif;
}
.modern-table tbody tr { transition: all 0.2s ease; }
.modern-table tbody tr:hover { background-color: rgba(243, 156, 18, 0.015); }
.modern-table tbody td {
    padding: 14px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.03);
    font-size: 13px;
    color: #1e293b;
    font-family: 'Inter', sans-serif;
}

.customer-name { font-size: 13.5px; }

/* Status Badges */
.badge-premium {
    display: inline-flex;
    align-items: center;
    padding: 5px 12px;
    font-size: 11.5px;
    font-weight: 700;
    border-radius: 30px;
    letter-spacing: 0.2px;
    gap: 5px;
}
.badge-pending  { background: rgba(217,119,6,0.08); color: #d97706; border: 1px solid rgba(217,119,6,0.2); }
.badge-approved { background: rgba(16,185,129,0.08); color: #10b981; border: 1px solid rgba(16,185,129,0.2); }
.badge-rejected { background: rgba(239,68,68,0.08); color: #ef4444; border: 1px solid rgba(239,68,68,0.2); cursor: help; }
.badge-expired  { background: rgba(100,116,139,0.08); color: #64748b; border: 1px solid rgba(100,116,139,0.2); }

.pulse-dot {
    width: 6px; height: 6px;
    background: #d97706;
    border-radius: 50%;
    box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.7);
    animation: pulse 1.6s infinite;
}
@keyframes pulse {
    0%   { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.7); }
    70%  { transform: scale(1);    box-shadow: 0 0 0 6px rgba(217, 119, 6, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(217, 119, 6, 0); }
}

/* Action Buttons */
.btn-action-sm {
    width: 30px; height: 30px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    transition: all 0.2s ease;
    border: none;
    padding: 0;
}
.btn-approve-sm { background: rgba(16,185,129,0.1); color: #10b981; }
.btn-approve-sm:hover:not(:disabled) { background: #10b981; color: #fff; box-shadow: 0 4px 10px rgba(16,185,129,0.3); }
.btn-reject-sm { background: rgba(239,68,68,0.08); color: #ef4444; }
.btn-reject-sm:hover:not(:disabled) { background: #ef4444; color: #fff; box-shadow: 0 4px 10px rgba(239,68,68,0.25); }

.btn-premium-delete {
    background: #ffffff;
    border: 1px solid rgba(0,0,0,0.06);
    color: #dc3545;
    font-size: 13px;
    border-radius: 8px;
    width: 30px; height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}
.btn-premium-delete:hover:not(:disabled) { background: #dc3545; color: #fff; border-color: transparent; box-shadow: 0 4px 10px rgba(220,53,69,0.15); }

.btn-refresh-premium {
    background: rgba(0,0,0,0.03) !important;
    border: 1px solid rgba(0,0,0,0.05) !important;
    width: 36px; height: 36px;
    cursor: pointer;
    transition: all 0.25s ease;
}
.btn-refresh-premium:hover { transform: rotate(30deg) scale(1.05); border-color: #f39c12 !important; }

/* Pending Notice */
.pending-notice {
    background: rgba(217, 119, 6, 0.04);
    border: 1px solid rgba(217, 119, 6, 0.12);
}

/* Modal */
.luxury-modal {
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(0,0,0,0.08) !important;
    border-radius: 24px !important;
}
.btn-close-luxury {
    background: transparent;
    border: none;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s ease;
}
.btn-close-luxury:hover { color: #0f172a; transform: rotate(90deg); }
.premium-input {
    background-color: #f8fafc !important;
    border: 1px solid rgba(0,0,0,0.06) !important;
    color: #334155 !important;
    padding: 12px 16px !important;
    font-size: 13.5px !important;
    font-family: 'Inter', sans-serif;
    transition: all 0.25s ease !important;
}
.premium-input:focus {
    border-color: #ef4444 !important;
    background-color: #ffffff !important;
    box-shadow: 0 0 0 3px rgba(239,68,68,0.1) !important;
}
.btn-danger-submit {
    background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%) !important;
    border: none !important;
    font-weight: 700 !important;
    font-size: 13px !important;
    padding: 10px 24px !important;
    box-shadow: 0 4px 10px rgba(239,68,68,0.15) !important;
    transition: all 0.2s ease !important;
}
.btn-danger-submit:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 6px 15px rgba(239,68,68,0.25) !important;
}
.text-secondary-custom {
    color: #4b5563;
    font-size: 12.5px;
    letter-spacing: 0.3px;
    font-family: 'Outfit', sans-serif;
}
</style>
