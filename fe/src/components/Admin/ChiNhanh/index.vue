<template>
    <div class="row g-4">
        <!-- Left Column: Add/Edit Form -->
        <div class="col-lg-4 col-md-12">
            <div class="card luxury-panel border-0 shadow-sm h-100">
                <div class="card-header bg-transparent py-4 border-0 border-bottom border-light-subtle d-flex align-items-center">
                    <h5 class="mb-0 fw-bold panel-title text-dark text-gradient-gold">
                        <i class="bx bx-plus-circle me-2"></i> {{ isEditing ? 'Cập Nhật Dòng Họ' : 'Thêm Dòng Họ Mới' }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form @submit.prevent="saveData">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary-custom">Tên Dòng Họ (Chi Nhánh)</label>
                            <input type="text" class="form-control premium-input radius-10 border-2 shadow-none" placeholder="Nhập tên dòng họ (VD: Chi Nhánh Họ Nguyễn)" v-model="formData.ten_chi" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary-custom">Mô Tả</label>
                            <textarea class="form-control premium-input radius-10 border-2 shadow-none" rows="4" placeholder="Nhập mô tả chi tiết dòng họ..." v-model="formData.mo_ta" required></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary-custom">Chủ Sở Hữu (Quản Trị Viên/Đối Tác)</label>
                            <select class="form-select premium-input radius-10 border-2 shadow-none" v-model="formData.id_nguoi_dung">
                                <option :value="null">-- Chưa chỉ định (Hệ thống quản lý) --</option>
                                <option v-for="user in userList" :key="user.id" :value="user.id">
                                    {{ user.ho_ten }} ({{ user.email || 'Không có email' }})
                                </option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary-custom">Tự Động Duyệt Đề Xuất</label>
                            <select class="form-select premium-input radius-10 border-2 shadow-none" v-model="formData.is_auto_approve">
                                <option value="1">Bật (Tự động phê duyệt đề xuất từ thành viên)</option>
                                <option value="0">Tắt (Trưởng nhánh/Admin duyệt thủ công)</option>
                            </select>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5">
                            <button type="button" class="btn btn-outline-secondary radius-30 px-4" v-if="isEditing" @click="resetForm">Hủy</button>
                            <button type="submit" class="btn btn-filter-submit text-white radius-30 px-4 fw-bold shadow-sm" :disabled="saving">
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
                        <i class="bx bx-sitemap me-2 text-warning"></i> Quản Lý Danh Sách Dòng Họ
                    </h5>
                    <button class="btn btn-refresh-premium rounded-circle d-flex align-items-center justify-content-center" @click="loadData" :disabled="isLoading" title="Làm mới dữ liệu">
                        <i class="bx bx-sync fs-5 text-warning" :class="{'bx-spin': isLoading}"></i>
                    </button>
                </div>
                <div class="card-body p-4">
                    <div class="input-group mb-4 radius-10 overflow-hidden border-2 search-box-premium shadow-sm">
                        <input type="text" class="form-control border-0 shadow-none ps-4 bg-transparent" placeholder="Tìm kiếm dòng họ..." v-model="searchQuery">
                        <span class="input-group-text border-0 bg-transparent pe-4"><i class="bx bx-search text-secondary"></i></span>
                    </div>

                    <div class="table-responsive rounded-3 border border-light-subtle">
                        <table class="table modern-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th width="8%" class="text-center">#</th>
                                    <th width="25%">Tên Dòng Họ</th>
                                    <th width="32%">Mô Tả</th>
                                    <th width="15%" class="text-center">Tự Động Duyệt</th>
                                    <th width="20%" class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="isLoading">
                                    <td colspan="5" class="text-center py-5">
                                        <div class="spinner-border text-warning" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-else-if="filteredList.length === 0">
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bx bx-folder-open fs-1 mb-2 d-block opacity-50"></i>
                                        Chưa có thông tin dòng họ
                                    </td>
                                </tr>
                                <tr v-for="(item, index) in filteredList" :key="item.id">
                                    <td class="text-center fw-bold">{{ index + 1 }}</td>
                                    <td class="fw-bold text-dark">{{ item.ten_chi }}</td>
                                    <td class="text-secondary small">{{ item.mo_ta }}</td>
                                    <td class="text-center">
                                        <span v-if="item.is_auto_approve == 1" class="badge bg-success-soft text-success border border-success border-opacity-25 rounded-pill px-3 py-1 fw-bold">
                                            Có
                                        </span>
                                        <span v-else class="badge bg-secondary-soft text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1 fw-bold">
                                            Không
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-action-edit" @click="editItem(item)" title="Sửa">
                                                <i class="bx bx-edit-alt"></i>
                                            </button>
                                            <button class="btn btn-action-delete" @click="deleteItem(item)" title="Xóa">
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
    name: 'ChiNhanhManagement',
    data() {
        return {
            listData: [],
            userList: [],
            formData: {
                id: null,
                ten_chi: '',
                mo_ta: '',
                id_nguoi_dung: null,
                is_auto_approve: 0
            },
            isEditing: false,
            isLoading: false,
            saving: false,
            searchQuery: ''
        }
    },
    computed: {
        filteredList() {
            if (!this.searchQuery) return this.listData;
            const q = this.searchQuery.toLowerCase();
            return this.listData.filter(item => 
                (item.ten_chi && item.ten_chi.toLowerCase().includes(q)) ||
                (item.mo_ta && item.mo_ta.toLowerCase().includes(q))
            );
        }
    },
    mounted() {
        this.loadData();
        this.loadUsers();
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
            axios.get('http://127.0.0.1:8000/api/chi-nhanh/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.listData = res.data.data;
                    }
                })
                .catch(err => {
                    toastr.error('Lỗi khi tải danh sách dòng họ!');
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        loadUsers() {
            axios.get('http://127.0.0.1:8000/api/nguoi-dung/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.userList = res.data.data;
                    }
                })
                .catch(err => {
                    console.log('Lỗi tải danh sách người dùng:', err);
                });
        },
        saveData() {
            this.saving = true;
            const url = this.isEditing 
                ? 'http://127.0.0.1:8000/api/chi-nhanh/update'
                : 'http://127.0.0.1:8000/api/chi-nhanh/create';
            
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
                    toastr.error(err.response?.data?.message || 'Có lỗi xảy ra, vui lòng thử lại!');
                })
                .finally(() => {
                    this.saving = false;
                });
        },
        editItem(item) {
            this.isEditing = true;
            this.formData = { 
                id: item.id,
                ten_chi: item.ten_chi,
                mo_ta: item.mo_ta,
                id_nguoi_dung: item.id_nguoi_dung,
                is_auto_approve: item.is_auto_approve
            };
        },
        deleteItem(item) {
            if (confirm(`Bạn có chắc chắn muốn xóa dòng họ "${item.ten_chi}"?`)) {
                axios.post('http://127.0.0.1:8000/api/chi-nhanh/delete', { id: item.id }, this.getHeaders())
                    .then(res => {
                        if (res.data.status) {
                            toastr.success(res.data.message);
                            this.loadData();
                        } else {
                            toastr.error(res.data.message);
                        }
                    })
                    .catch(err => {
                        toastr.error('Xóa dòng họ thất bại!');
                    });
            }
        },
        resetForm() {
            this.isEditing = false;
            this.formData = {
                id: null,
                ten_chi: '',
                mo_ta: '',
                id_nguoi_dung: null,
                is_auto_approve: 0
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

.bg-success-soft {
    background: rgba(22, 163, 74, 0.1) !important;
}
.bg-secondary-soft {
    background: rgba(108, 117, 125, 0.1) !important;
}
</style>
