<template>
    <div class="row g-4">
        <!-- Left Column: Add/Edit Form -->
        <div class="col-lg-4 col-md-12">
            <div class="card luxury-panel border-0 shadow-sm h-100">
                <div class="card-header bg-transparent py-4 border-0 border-bottom border-light-subtle d-flex align-items-center">
                    <h5 class="mb-0 fw-bold panel-title text-dark text-gradient-gold">
                        <i class="bx bx-plus-circle me-2"></i> {{ isEditing ? 'Cập Nhật Sự Kiện' : 'Thêm Sự Kiện Mới' }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form @submit.prevent="saveData">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary-custom">Tên Sự Kiện</label>
                            <input type="text" class="form-control premium-input radius-10 border-2 shadow-none" placeholder="Nhập tên sự kiện..." v-model="formData.tieu_de" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary-custom">Loại Sự Kiện</label>
                            <select class="form-select premium-input radius-10 border-2 shadow-none" v-model="formData.loai" required>
                                <option value="" disabled>-- Chọn Loại Sự Kiện --</option>
                                <option value="Giỗ tổ">Giỗ tổ</option>
                                <option value="Họp họ">Họp họ</option>
                                <option value="Cưới hỏi">Cưới hỏi</option>
                                <option value="Tang lễ">Tang lễ</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary-custom">Dòng Họ (Chi Nhánh)</label>
                            <select class="form-select premium-input radius-10 border-2 shadow-none" v-model="formData.chi_nhanh_id">
                                <option :value="null">-- Sự kiện chung toàn quốc --</option>
                                <option v-for="cn in chiNhanhList" :key="cn.id" :value="cn.id">
                                    {{ cn.ten_chi }}
                                </option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary-custom">Ngày Tổ Chức</label>
                            <input type="date" class="form-control premium-input radius-10 border-2 shadow-none" v-model="formData.ngay_to_chuc" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary-custom">Địa Điểm</label>
                            <input type="text" class="form-control premium-input radius-10 border-2 shadow-none" placeholder="Nhập địa điểm..." v-model="formData.dia_diem">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary-custom">Nội Dung Chi Tiết</label>
                            <textarea class="form-control premium-input radius-10 border-2 shadow-none" rows="4" placeholder="Nhập mô tả chi tiết chương trình sự kiện..." v-model="formData.noi_dung"></textarea>
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
                        <i class="bx bx-calendar-event me-2 text-warning"></i> Quản Lý Danh Sách Sự Kiện
                    </h5>
                    <button class="btn btn-refresh-premium rounded-circle d-flex align-items-center justify-content-center" @click="loadData" :disabled="isLoading" title="Làm mới dữ liệu">
                        <i class="bx bx-sync fs-5 text-warning" :class="{'bx-spin': isLoading}"></i>
                    </button>
                </div>
                <div class="card-body p-4">
                    <div class="input-group mb-4 radius-10 overflow-hidden border-2 search-box-premium shadow-sm">
                        <input type="text" class="form-control border-0 shadow-none ps-4 bg-transparent" placeholder="Tìm kiếm sự kiện..." v-model="searchQuery">
                        <span class="input-group-text border-0 bg-transparent pe-4"><i class="bx bx-search text-secondary"></i></span>
                    </div>

                    <div class="table-responsive rounded-3 border border-light-subtle">
                        <table class="table modern-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th width="5%" class="text-center">#</th>
                                    <th width="20%">Dòng Họ</th>
                                    <th width="25%">Sự Kiện</th>
                                    <th width="15%" class="text-center">Loại</th>
                                    <th width="15%" class="text-center">Ngày Tổ Chức</th>
                                    <th width="10%" class="text-center">Thao tác</th>
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
                                        Chưa có sự kiện nào được thiết lập
                                    </td>
                                </tr>
                                <tr v-for="(item, index) in filteredList" :key="item.id">
                                    <td class="text-center fw-bold">{{ index + 1 }}</td>
                                    <td class="fw-bold text-secondary small">
                                        {{ item.chi_nhanh_id ? (getBranchName(item.chi_nhanh_id)) : 'Chung toàn quốc' }}
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ item.tieu_de }}</div>
                                        <div class="text-muted small text-truncate" style="max-width: 200px;" v-if="item.dia_diem">
                                            <i class="bx bx-map me-1"></i>{{ item.dia_diem }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning-soft text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1 fw-bold">
                                            {{ item.loai }}
                                        </span>
                                    </td>
                                    <td class="text-center text-secondary small">{{ formatDate(item.ngay_to_chuc) }}</td>
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
    name: 'SuKienManagement',
    data() {
        return {
            listData: [],
            chiNhanhList: [],
            formData: {
                id: null,
                tieu_de: '',
                noi_dung: '',
                ngay_to_chuc: '',
                dia_diem: '',
                chi_nhanh_id: null,
                loai: ''
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
                (item.tieu_de && item.tieu_de.toLowerCase().includes(q)) ||
                (item.dia_diem && item.dia_diem.toLowerCase().includes(q)) ||
                (item.noi_dung && item.noi_dung.toLowerCase().includes(q)) ||
                (item.loai && item.loai.toLowerCase().includes(q))
            );
        }
    },
    mounted() {
        this.loadData();
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
            axios.get('http://127.0.0.1:8000/api/su-kien/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.listData = res.data.data;
                    }
                })
                .catch(err => {
                    toastr.error('Lỗi khi tải danh sách sự kiện!');
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        loadChiNhanh() {
            axios.get('http://127.0.0.1:8000/api/chi-nhanh/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.chiNhanhList = res.data.data;
                    }
                });
        },
        saveData() {
            this.saving = true;
            const url = this.isEditing 
                ? 'http://127.0.0.1:8000/api/su-kien/update'
                : 'http://127.0.0.1:8000/api/su-kien/create';
            
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
                tieu_de: item.tieu_de,
                noi_dung: item.noi_dung,
                ngay_to_chuc: item.ngay_to_chuc,
                dia_diem: item.dia_diem,
                chi_nhanh_id: item.chi_nhanh_id,
                loai: item.loai
            };
        },
        deleteItem(item) {
            if (confirm(`Bạn có chắc chắn muốn xóa sự kiện "${item.tieu_de}"?`)) {
                axios.post('http://127.0.0.1:8000/api/su-kien/delete', { id: item.id }, this.getHeaders())
                    .then(res => {
                        if (res.data.status) {
                            toastr.success(res.data.message);
                            this.loadData();
                        } else {
                            toastr.error(res.data.message);
                        }
                    })
                    .catch(err => {
                        toastr.error('Xóa sự kiện thất bại!');
                    });
            }
        },
        getBranchName(id) {
            const branch = this.chiNhanhList.find(b => b.id === id);
            return branch ? branch.ten_chi : 'Dòng họ #' + id;
        },
        formatDate(val) {
            if (!val) return '';
            const parts = val.split('-');
            if (parts.length === 3) {
                return `${parts[2]}/${parts[1]}/${parts[0]}`;
            }
            return val;
        },
        resetForm() {
            this.isEditing = false;
            this.formData = {
                id: null,
                tieu_de: '',
                noi_dung: '',
                ngay_to_chuc: '',
                dia_diem: '',
                chi_nhanh_id: null,
                loai: ''
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

.bg-warning-soft {
    background: rgba(243, 156, 18, 0.1) !important;
}
</style>