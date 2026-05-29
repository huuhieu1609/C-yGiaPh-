<template>
    <div class="row g-4">
        <!-- Left Column: Add/Edit Form -->
        <div class="col-lg-4 col-md-12">
            <div class="card luxury-panel border-0 shadow-sm">
                <div class="card-header bg-transparent py-4 border-0 border-bottom border-light-subtle d-flex align-items-center">
                    <h5 class="mb-0 fw-bold panel-title text-dark text-gradient-gold">
                        <i class="bx bx-user-plus me-2"></i> {{ isEditing ? 'Cập Nhật Người Dùng' : 'Thêm Người Dùng Mới' }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form @submit.prevent="saveData">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary-custom">Họ Và Tên</label>
                            <input type="text" class="form-control premium-input radius-10 border-2 shadow-none" placeholder="Nhập họ tên đầy đủ..." v-model="formData.ho_ten" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary-custom">Email</label>
                            <input type="email" class="form-control premium-input radius-10 border-2 shadow-none" placeholder="Nhập địa chỉ email..." v-model="formData.email" required>
                        </div>
                        <div class="mb-4" v-if="!isEditing">
                            <label class="form-label fw-bold text-secondary-custom">Mật Khẩu</label>
                            <input type="password" class="form-control premium-input radius-10 border-2 shadow-none" placeholder="Nhập mật khẩu..." v-model="formData.mat_khau" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary-custom">Số Điện Thoại</label>
                            <input type="text" class="form-control premium-input radius-10 border-2 shadow-none" placeholder="Nhập số điện thoại..." v-model="formData.so_dien_thoai">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary-custom">Chức Vụ</label>
                            <select class="form-select premium-input radius-10 border-2 shadow-none" v-model="formData.id_chuc_vu">
                                <option :value="null">-- Chọn Chức Vụ --</option>
                                <option v-for="cv in listChucVu" :key="cv.id" :value="cv.id">{{ cv.ten_chuc_vu }}</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary-custom">Chi Nhánh</label>
                            <select class="form-select premium-input radius-10 border-2 shadow-none" v-model="formData.chi_nhanh_id">
                                <option :value="null">-- Không liên kết --</option>
                                <option v-for="cn in listChiNhanh" :key="cn.id" :value="cn.id">{{ cn.ten_chi }}</option>
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
                        <i class="bx bx-group me-2 text-warning"></i> Quản Lý Tài Khoản Người Dùng
                    </h5>
                    <button class="btn btn-refresh-premium rounded-circle d-flex align-items-center justify-content-center" @click="loadData" :disabled="isLoading" title="Làm mới dữ liệu">
                        <i class="bx bx-sync fs-5 text-warning" :class="{'bx-spin': isLoading}"></i>
                    </button>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive rounded-3 border border-light-subtle">
                        <table class="table modern-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th width="5%" class="text-center">#</th>
                                    <th width="20%">Họ Tên</th>
                                    <th width="20%">Email</th>
                                    <th width="15%" class="text-center">Chức Vụ</th>
                                    <th width="20%" class="text-center">Chi Nhánh</th>
                                    <th width="12%" class="text-center">Trạng Thái</th>
                                    <th width="8%" class="text-center">Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="isLoading">
                                    <td colspan="7" class="text-center py-5">
                                        <div class="spinner-border text-warning" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-else-if="listData.length === 0">
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="bx bx-folder-open fs-1 mb-2 d-block opacity-50"></i>
                                        Chưa có người dùng nào
                                    </td>
                                </tr>
                                <tr v-for="(item, index) in listData" :key="item.id">
                                    <td class="text-center fw-bold">{{ index + 1 }}</td>
                                    <td class="fw-bold text-dark">{{ item.ho_ten }}</td>
                                    <td class="text-secondary small">{{ item.email }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-chucvu px-3 py-1.5 fw-bold">{{ getTenChucVu(item.id_chuc_vu) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-chinhanh px-3 py-1.5 fw-bold">{{ getTenChiNhanh(item.chi_nhanh_id) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <button @click="changeStatus(item.id)" :class="item.trang_thai == 'Hoạt động' ? 'btn-status-active' : 'btn-status-locked'" class="btn-status-toggle w-100 fw-bold">
                                            {{ item.trang_thai }}
                                        </button>
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
</template>

<script>
import axios from 'axios';
import toastr from 'toastr';

export default {
    name: 'NguoiDungManagement',
    data() {
        return {
            listData: [],
            listChucVu: [],
            listChiNhanh: [],
            formData: {
                id: null,
                ho_ten: '',
                email: '',
                mat_khau: '',
                so_dien_thoai: '',
                id_chuc_vu: null,
                chi_nhanh_id: null,
                trang_thai: 'Hoạt động'
            },
            isEditing: false,
            isLoading: false
        }
    },
    mounted() {
        this.loadData();
        this.loadChucVu();
        this.loadChiNhanh();
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
            axios.get('http://127.0.0.1:8000/api/nguoi-dung/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.listData = res.data.data;
                    }
                })
                .catch(err => {
                    toastr.error('Lỗi khi tải dữ liệu người dùng!');
                    console.error(err);
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        loadChucVu() {
            axios.get('http://127.0.0.1:8000/api/chuc-vu/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.listChucVu = res.data.data;
                    }
                });
        },
        loadChiNhanh() {
            axios.get('http://127.0.0.1:8000/api/chi-nhanh/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.listChiNhanh = res.data.data;
                    }
                });
        },
        getTenChucVu(id) {
            const cv = this.listChucVu.find(c => c.id === id);
            return cv ? cv.ten_chuc_vu : 'Chưa gán';
        },
        getTenChiNhanh(id) {
            const cn = this.listChiNhanh.find(c => c.id === id);
            return cn ? cn.ten_chi : 'Không liên kết';
        },
        saveData() {
            const url = this.isEditing 
                ? 'http://127.0.0.1:8000/api/nguoi-dung/update'
                : 'http://127.0.0.1:8000/api/nguoi-dung/create';
            
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
            this.formData.mat_khau = ''; // Don't show password
            if (!this.formData.hasOwnProperty('chi_nhanh_id')) {
                this.formData.chi_nhanh_id = null;
            }
        },
        deleteItem(id) {
            if (confirm('Bạn có chắc chắn muốn xóa?')) {
                axios.post('http://127.0.0.1:8000/api/nguoi-dung/delete', { id }, this.getHeaders())
                    .then(res => {
                        if (res.data.status) {
                            toastr.success(res.data.message);
                            this.loadData();
                        }
                    });
            }
        },
        changeStatus(id) {
            axios.post('http://127.0.0.1:8000/api/nguoi-dung/status', { id }, this.getHeaders())
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
                ho_ten: '',
                email: '',
                mat_khau: '',
                so_dien_thoai: '',
                id_chuc_vu: null,
                chi_nhanh_id: null,
                trang_thai: 'Hoạt động'
            };
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

.badge-chucvu {
    background: rgba(79, 70, 229, 0.08) !important;
    color: #4f46e5 !important;
    border: 1px solid rgba(79, 70, 229, 0.15);
    border-radius: 30px;
}

.badge-chinhanh {
    background: rgba(6, 182, 212, 0.08) !important;
    color: #0891b2 !important;
    border: 1px solid rgba(6, 182, 212, 0.15);
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
</style>

