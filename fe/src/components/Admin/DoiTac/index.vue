<template>
    <div class="row g-4">
        <!-- Left Column: Add/Edit Form -->
        <div class="col-lg-4 col-md-12">
            <div class="card luxury-panel border-0 shadow-sm">
                <div class="card-header bg-transparent py-4 border-0 border-bottom border-light-subtle d-flex align-items-center">
                    <h5 class="mb-0 fw-bold panel-title text-dark text-gradient-gold">
                        <i class="bx bx-plus-circle me-2"></i> {{ isEditing ? 'Cập Nhật Đối Tác' : 'Thêm Đối Tác Mới' }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form @submit.prevent="saveData">
                        <div class="mb-4" v-if="!isEditing">
                            <label class="form-label fw-bold text-secondary-custom">Người Dùng Liên Kết</label>
                            <select class="form-select premium-input radius-10 border-2 shadow-none" v-model="formData.id_nguoi_dung" required>
                                <option :value="null">-- Chọn Người Dùng --</option>
                                <option v-for="user in listUsers" :key="user.id" :value="user.id">
                                    {{ user.ho_ten }} ({{ user.email }})
                                </option>
                            </select>
                        </div>
                        <div class="mb-4" v-else>
                            <label class="form-label fw-bold text-secondary-custom">Đối Tác</label>
                            <input type="text" class="form-control premium-input radius-10 border-2 shadow-none bg-light-subtle" :value="formData.nguoi_dung ? formData.nguoi_dung.ho_ten : 'Đang tải...'" readonly>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary-custom">Gói Dịch Vụ</label>
                            <select class="form-select premium-input radius-10 border-2 shadow-none" v-model="formData.ten_goi" @change="onPackageChange" required>
                                <option v-for="pkg in listPackages" :key="pkg.id" :value="pkg.ten_goi">
                                    {{ pkg.ten_goi }} ({{ formatCurrency(pkg.gia_ca) }})
                                </option>
                                <option value="Gói Đối Tác Custom">Gói Tùy Chỉnh (Nhập tay)</option>
                            </select>
                        </div>

                        <div class="mb-4" v-if="formData.ten_goi === 'Gói Đối Tác Custom'">
                            <label class="form-label fw-bold text-secondary-custom">Tên Gói Tùy Chỉnh</label>
                            <input type="text" class="form-control premium-input radius-10 border-2 shadow-none" placeholder="Nhập tên gói tự định nghĩa..." v-model="customPackageName" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary-custom">Số Tiền (VNĐ)</label>
                            <input type="number" class="form-control premium-input radius-10 border-2 shadow-none" placeholder="Nhập số tiền đăng ký..." v-model="formData.so_tien" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold text-secondary-custom">Ngày Bắt Đầu</label>
                                <input type="date" class="form-control premium-input radius-10 border-2 shadow-none" v-model="formData.ngay_bat_dau" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold text-secondary-custom">Ngày Kết Thúc</label>
                                <input type="date" class="form-control premium-input radius-10 border-2 shadow-none" v-model="formData.ngay_ket_thuc" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary-custom">Trạng Thái Kích Hoạt</label>
                            <select class="form-select premium-input radius-10 border-2 shadow-none" v-model="formData.trang_thai">
                                <option value="1">Hoạt động (Active)</option>
                                <option value="0">Khóa (Inactive)</option>
                            </select>
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
                        <i class="bx bx-list-ul me-2 text-warning"></i> Danh Sách Đối Tác Hệ Thống
                    </h5>
                    <button class="btn btn-refresh-premium rounded-circle d-flex align-items-center justify-content-center" @click="loadData" :disabled="isLoading" title="Làm mới dữ liệu">
                        <i class="bx bx-sync fs-5 text-warning" :class="{'bx-spin': isLoading}"></i>
                    </button>
                </div>
                <div class="card-body p-4">
                    <div class="input-group mb-4 radius-10 overflow-hidden border-2 search-box-premium shadow-sm">
                        <input type="text" class="form-control border-0 shadow-none ps-4 bg-transparent" placeholder="Tìm kiếm theo tên đối tác, dòng họ hoặc gói dịch vụ..." v-model="searchQuery">
                        <span class="input-group-text border-0 bg-transparent pe-4"><i class="bx bx-search text-secondary"></i></span>
                    </div>

                    <div class="table-responsive rounded-3 border border-light-subtle">
                        <table class="table modern-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th width="5%" class="text-center">#</th>
                                    <th width="20%">Đối Tác</th>
                                    <th width="20%" class="text-center">Dòng Họ / Chi Nhánh</th>
                                    <th width="15%" class="text-center">Tên Gói</th>
                                    <th width="10%" class="text-end">Số Tiền</th>
                                    <th width="13%" class="text-center">Thời Hạn Gói</th>
                                    <th width="12%" class="text-center">Trạng Thái</th>
                                    <th width="15%" class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="isLoading">
                                    <td colspan="8" class="text-center py-5">
                                        <div class="spinner-border text-warning" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-else-if="filteredList.length === 0">
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bx bx-folder-open fs-1 mb-2 d-block opacity-50"></i>
                                        Chưa có đối tác nào đăng ký
                                    </td>
                                </tr>
                                <tr v-for="(item, index) in filteredList" :key="item.id">
                                    <td class="text-center fw-bold">{{ index + 1 }}</td>
                                    <td class="fw-bold text-dark">
                                        {{ item.nguoi_dung ? item.nguoi_dung.ho_ten : 'Chưa có thông tin' }}
                                        <div class="small text-secondary fw-normal mt-1" style="font-size: 11px;">
                                            {{ item.nguoi_dung ? item.nguoi_dung.email : '' }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-chinhanh px-3 py-1.5 fw-bold">
                                            <i class="bx bx-git-branch me-1"></i>{{ item.dong_ho || 'Chưa liên kết' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-goi px-2.5 py-1.5 fw-bold">
                                            {{ item.ten_goi }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-success">{{ formatCurrency(item.so_tien) }}</td>
                                    <td class="small text-center text-secondary">
                                        <div><strong>Từ:</strong> {{ formatDate(item.ngay_bat_dau) }}</div>
                                        <div class="mt-1"><strong>Đến:</strong> {{ formatDate(item.ngay_ket_thuc) }}</div>
                                    </td>
                                    <td class="text-center">
                                        <button @click="changeStatus(item.id)" :class="item.trang_thai == 1 ? 'btn-status-active' : 'btn-status-locked'" class="btn-status-toggle w-100 fw-bold">
                                            {{ item.trang_thai == 1 ? 'Hoạt động' : 'Đang Khóa' }}
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-action-activity" @click="viewPartnerActivity(item)" title="Xem lịch sử cập nhật phả hệ của dòng họ">
                                                <i class="bx bx-history"></i>
                                            </button>
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

        <!-- Modal Hoạt Động Trưởng Nhánh Premium -->
        <div class="modal fade custom-modal" id="partnerActivityModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 radius-15 shadow-xl overflow-hidden">
                    <div class="modal-header premium-header border-0">
                        <div class="d-flex align-items-center gap-3">
                            <div class="header-icon">
                                <i class='bx bx-history'></i>
                            </div>
                            <div>
                                <h4 class="modal-title fw-bold text-white mb-0">LỊCH SỬ CẬP NHẬT GIA PHẢ</h4>
                                <p class="mb-0 text-white-50 mt-1">Trưởng nhánh: <span class="badge bg-warning text-dark px-2 py-1 ms-1 rounded-pill fw-bold">{{ activePartner.nguoi_dung ? activePartner.nguoi_dung.ho_ten : '' }}</span></p>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body p-4 bg-light">
                        <div v-if="loadingActivity" class="text-center py-5">
                            <div class="spinner-border text-warning" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <div v-else-if="partnerLogs.length === 0" class="text-center py-5 text-muted">
                            <i class="bx bx-folder-open fs-1 mb-2 d-block opacity-50"></i>
                            Trưởng tộc nhánh này chưa thực hiện thao tác cập nhật nào trên phả hệ
                        </div>
                        <div v-else class="timeline-premium">
                            <div v-for="log in partnerLogs" :key="log.id" class="timeline-item-premium d-flex gap-3 mb-4">
                                <div class="timeline-marker d-flex flex-column align-items-center">
                                    <div class="marker-dot bg-warning shadow-sm"></div>
                                    <div class="marker-line flex-grow-1"></div>
                                </div>
                                <div class="timeline-content bg-white p-3 radius-12 shadow-sm border border-light-subtle flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge bg-light text-secondary-custom font-outfit">{{ log.thoi_gian || formatDate(log.created_at) }}</span>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-0">{{ log.hanh_dong }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer bg-white border-top p-3">
                        <button type="button" class="btn btn-light radius-8 px-4 fw-semibold border" data-bs-dismiss="modal">Đóng</button>
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
    name: 'DoiTacManagement',
    data() {
        return {
            listData: [],
            listUsers: [],
            listPackages: [],
            formData: {
                id: null,
                id_nguoi_dung: null,
                ten_goi: '',
                so_tien: 0,
                ngay_bat_dau: '',
                ngay_ket_thuc: '',
                trang_thai: 1
            },
            customPackageName: '',
            isEditing: false,
            isLoading: false,
            searchQuery: '',
            // Partner update history modal
            activePartner: {},
            partnerLogs: [],
            loadingActivity: false,
            modalActivity: null
        };
    },
    computed: {
        filteredList() {
            if (!this.searchQuery) return this.listData;
            const q = this.searchQuery.toLowerCase();
            return this.listData.filter(item => {
                const partnerName = item.nguoi_dung ? item.nguoi_dung.ho_ten.toLowerCase() : '';
                const partnerEmail = item.nguoi_dung ? item.nguoi_dung.email.toLowerCase() : '';
                const packageName = item.ten_goi ? item.ten_goi.toLowerCase() : '';
                const lineageName = item.dong_ho ? item.dong_ho.toLowerCase() : '';
                return partnerName.includes(q) || partnerEmail.includes(q) || packageName.includes(q) || lineageName.includes(q);
            });
        }
    },
    mounted() {
        this.loadData();
        this.loadUsers();
        this.loadPackages();
        this.setDefaultDates();
        if (window.bootstrap) {
            this.modalActivity = new window.bootstrap.Modal(document.getElementById('partnerActivityModal'));
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
        setDefaultDates() {
            const today = new Date();
            const nextYear = new Date();
            nextYear.setFullYear(today.getFullYear() + 1);

            this.formData.ngay_bat_dau = today.toISOString().split('T')[0];
            this.formData.ngay_ket_thuc = nextYear.toISOString().split('T')[0];
        },
        loadPackages() {
            axios.get('http://127.0.0.1:8000/api/goi-dich-vu/get-data', this.getHeaders())
            .then(res => {
                if (res.data.status) {
                    this.listPackages = res.data.data.filter(p => p.trang_thai === 'Hoạt động');
                    if (this.listPackages.length > 0 && !this.isEditing) {
                        this.formData.ten_goi = this.listPackages[0].ten_goi;
                        this.formData.so_tien = this.listPackages[0].gia_ca;
                    }
                }
            })
            .catch(err => console.error(err));
        },
        onPackageChange() {
            const pkg = this.listPackages.find(p => p.ten_goi === this.formData.ten_goi);
            if (pkg) {
                this.formData.so_tien = pkg.gia_ca;
            } else if (this.formData.ten_goi !== 'Gói Đối Tác Custom') {
                this.formData.so_tien = 0;
            }
        },
        loadData() {
            this.isLoading = true;
            axios.get('http://127.0.0.1:8000/api/admin/doi-tac/get-data', this.getHeaders())
            .then(res => {
                if (res.data.status) {
                    this.listData = res.data.data;
                }
            })
            .catch(err => {
                toastr.error('Lỗi khi tải dữ liệu đối tác!');
                console.error(err);
            })
            .finally(() => {
                this.isLoading = false;
            });
        },
        loadUsers() {
            axios.get('http://127.0.0.1:8000/api/nguoi-dung/get-data', this.getHeaders())
            .then(res => {
                if (res.data.status) {
                    this.listUsers = res.data.data;
                }
            })
            .catch(err => console.error(err));
        },
        saveData() {
            const url = this.isEditing 
                ? 'http://127.0.0.1:8000/api/admin/doi-tac/update'
                : 'http://127.0.0.1:8000/api/admin/doi-tac/create';
            
            const payload = { ...this.formData };
            if (payload.ten_goi === 'Gói Đối Tác Custom') {
                payload.ten_goi = this.customPackageName || 'Gói Đối Tác Custom';
            }

            axios.post(url, payload, this.getHeaders())
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
                const msg = err.response && err.response.data && err.response.data.message
                    ? err.response.data.message
                    : 'Đã có lỗi xảy ra, vui lòng thử lại!';
                toastr.error(msg);
            });
        },
        editItem(item) {
            this.isEditing = true;
            this.formData = { ...item };
            
            // Nếu không thuộc các gói chuẩn thì set là Custom
            if (!this.listPackages.some(pkg => pkg.ten_goi === item.ten_goi)) {
                this.formData.ten_goi = 'Gói Đối Tác Custom';
                this.customPackageName = item.ten_goi;
            }
        },
        deleteItem(id) {
            if (confirm('Bạn có chắc chắn muốn xóa đối tác này? Mọi quyền lợi đối tác của tài khoản liên kết sẽ bị thu hồi.')) {
                axios.post('http://127.0.0.1:8000/api/admin/doi-tac/delete', { id }, this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        toastr.success(res.data.message);
                        this.loadData();
                    } else {
                        toastr.error(res.data.message);
                    }
                })
                .catch(err => {
                    toastr.error('Lỗi khi xóa đối tác!');
                });
            }
        },
        changeStatus(id) {
            axios.post('http://127.0.0.1:8000/api/admin/doi-tac/status', { id }, this.getHeaders())
            .then(res => {
                if (res.data.status) {
                    toastr.success(res.data.message);
                    this.loadData();
                }
            })
            .catch(err => {
                toastr.error('Lỗi khi đổi trạng thái!');
            });
        },
        viewPartnerActivity(item) {
            this.activePartner = item;
            this.partnerLogs = [];
            this.loadingActivity = true;
            this.modalActivity.show();
            
            axios.get('http://127.0.0.1:8000/api/nhat-ky-hoat-dong/get-data', this.getHeaders())
            .then(res => {
                if (res.data.status) {
                    // Filter logs that belong to this partner's user ID
                    this.partnerLogs = res.data.data.filter(log => log.nguoi_dung_id === item.id_nguoi_dung);
                }
            })
            .catch(err => {
                toastr.error('Lỗi khi lấy lịch sử hoạt động!');
                console.error(err);
            })
            .finally(() => {
                this.loadingActivity = false;
            });
        },
        resetForm() {
            this.isEditing = false;
            this.formData = {
                id: null,
                id_nguoi_dung: null,
                ten_goi: 'Gói Khởi Tạo',
                so_tien: 100000,
                ngay_bat_dau: '',
                ngay_ket_thuc: '',
                trang_thai: 1
            };
            this.customPackageName = '';
            this.setDefaultDates();
        },
        formatCurrency(value) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value || 0);
        },
        formatDate(value) {
            if (!value) return '';
            const d = new Date(value);
            return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`;
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

.radius-15 {
    border-radius: 15px !important;
}

.radius-30 {
    border-radius: 30px !important;
}

.shadow-xl {
    box-shadow: 0 20px 50px rgba(0,0,0,0.15) !important;
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

.btn-action-activity {
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
    color: #0d6efd;
}
.btn-action-activity:hover {
    background: #0d6efd;
    color: #fff;
    border-color: transparent;
}

.badge-chinhanh {
    background: rgba(16, 185, 129, 0.08) !important;
    color: #10b981 !important;
    border: 1px solid rgba(16, 185, 129, 0.15);
    border-radius: 30px;
}

.badge-goi {
    background: rgba(79, 70, 229, 0.08) !important;
    color: #4f46e5 !important;
    border: 1px solid rgba(79, 70, 229, 0.15);
    border-radius: 30px;
}

.btn-status-toggle {
    border-radius: 30px;
    padding: 6px 14px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.btn-status-active {
    background: #f0fdf4 !important;
    color: #16a34a !important;
    border-color: rgba(22, 163, 74, 0.15) !important;
}
.btn-status-active:hover {
    background: #dcfce7 !important;
}

.btn-status-locked {
    background: #fef2f2 !important;
    color: #ef4444 !important;
    border-color: rgba(239, 68, 68, 0.15) !important;
}
.btn-status-locked:hover {
    background: #fee2e2 !important;
}

/* Premium Header and Timelines */
.premium-header {
    background: linear-gradient(135deg, #1e2035 0%, #252740 100%);
    padding: 24px 28px;
}
.header-icon {
    width: 52px; height: 52px;
    background: rgba(255,255,255,0.1);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 26px; color: #ffd700;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.timeline-premium {
    position: relative;
    padding-left: 10px;
}
.timeline-item-premium {
    position: relative;
}
.timeline-marker {
    width: 20px;
}
.marker-dot {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #e5a93b !important;
    border: 3px solid #fff;
    z-index: 1;
}
.marker-line {
    width: 2px;
    background: #e2e8f0;
    margin-top: 4px;
    margin-bottom: -15px;
}
.timeline-item-premium:last-child .marker-line {
    display: none;
}
.timeline-content {
    transition: all 0.25s ease;
}
.timeline-content:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.05) !important;
}
</style>
