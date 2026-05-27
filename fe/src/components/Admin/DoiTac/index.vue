<template>
    <div class="row">
        <!-- Left Column: Add/Edit Form -->
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="card shadow-sm border-0 radius-10 h-100">
                <div class="card-header bg-white py-3 border-0 border-bottom">
                    <h5 class="mb-0 fw-bold text-uppercase" style="color: #00b4d8;">
                        <i class="bx bx-plus-circle me-1"></i> {{ isEditing ? 'Cập Nhật Đối Tác' : 'Thêm Đối Tác Mới' }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form @submit.prevent="saveData">
                        <div class="mb-3" v-if="!isEditing">
                            <label class="form-label fw-semibold">Người Dùng Liên Kết</label>
                            <select class="form-select radius-8 border-2 shadow-none" v-model="formData.id_nguoi_dung" required>
                                <option :value="null">-- Chọn Người Dùng --</option>
                                <option v-for="user in listUsers" :key="user.id" :value="user.id">
                                    {{ user.ho_ten }} ({{ user.email }})
                                </option>
                            </select>
                        </div>
                        <div class="mb-3" v-else>
                            <label class="form-label fw-semibold">Đối Tác</label>
                            <input type="text" class="form-control radius-8 border-2 shadow-none bg-light" :value="formData.nguoi_dung ? formData.nguoi_dung.ho_ten : 'Đang tải...'" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Gói Dịch Vụ</label>
                            <select class="form-select radius-8 border-2 shadow-none" v-model="formData.ten_goi" @change="onPackageChange" required>
                                <option v-for="pkg in listPackages" :key="pkg.id" :value="pkg.ten_goi">
                                    {{ pkg.ten_goi }} ({{ formatCurrency(pkg.gia_ca) }})
                                </option>
                                <option value="Gói Đối Tác Custom">Gói Tùy Chỉnh (Nhập tay)</option>
                            </select>
                        </div>

                        <div class="mb-3" v-if="formData.ten_goi === 'Gói Đối Tác Custom'">
                            <label class="form-label fw-semibold">Tên Gói Tùy Chỉnh</label>
                            <input type="text" class="form-control radius-8 border-2 shadow-none" placeholder="Nhập tên gói tự định nghĩa..." v-model="customPackageName" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Số Tiền (VNĐ)</label>
                            <input type="number" class="form-control radius-8 border-2 shadow-none" placeholder="Nhập số tiền đăng ký..." v-model="formData.so_tien" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Ngày Bắt Đầu</label>
                                <input type="date" class="form-control radius-8 border-2 shadow-none" v-model="formData.ngay_bat_dau" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Ngày Kết Thúc</label>
                                <input type="date" class="form-control radius-8 border-2 shadow-none" v-model="formData.ngay_ket_thuc" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Trạng Thái Kích Hoạt</label>
                            <select class="form-select radius-8 border-2 shadow-none" v-model="formData.trang_thai">
                                <option value="1">Hoạt động (Active)</option>
                                <option value="0">Khóa (Inactive)</option>
                            </select>
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

        <!-- Right Column: Data Table -->
        <div class="col-lg-8 col-md-12">
            <div class="card shadow-sm border-0 radius-10 h-100">
                <div class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold text-uppercase" style="color: #333;">
                        <i class="bx bx-list-ul me-1"></i> Danh Sách Đối Tác Đăng Ký
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="input-group mb-4 radius-8 overflow-hidden border-2">
                        <input type="text" class="form-control border-0 shadow-none ps-4" placeholder="Tìm kiếm theo tên đối tác, dòng họ hoặc gói..." v-model="searchQuery">
                        <button class="btn btn-success px-5 fw-bold" type="button" style="background-color: #00c853; border-color: #00c853;">Tìm kiếm</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="text-center text-white" style="background-color: #008bf8;">
                                <tr>
                                    <th width="5%" class="py-3">#</th>
                                    <th width="18%" class="py-3">Đối Tác</th>
                                    <th width="20%" class="py-3">Dòng Họ / Chi Nhánh</th>
                                    <th width="15%" class="py-3">Tên Gói</th>
                                    <th width="12%" class="py-3">Số Tiền</th>
                                    <th width="18%" class="py-3">Thời Hạn Gói</th>
                                    <th width="12%" class="py-3">Trạng Thái</th>
                                    <th width="10%" class="py-3">Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="isLoading">
                                    <td colspan="7" class="text-center py-5">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-else-if="filteredList.length === 0">
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="bx bx-folder-open fs-1 mb-2 d-block"></i>
                                        Chưa có dữ liệu đối tác
                                    </td>
                                </tr>
                                <tr v-for="(item, index) in filteredList" :key="item.id">
                                    <td class="text-center fw-bold">{{ index + 1 }}</td>
                                    <td class="fw-semibold">
                                        {{ item.nguoi_dung ? item.nguoi_dung.ho_ten : 'Chưa có thông tin' }}
                                        <div class="small text-muted" style="font-size: 11px;">
                                            {{ item.nguoi_dung ? item.nguoi_dung.email : '' }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success-subtle text-success radius-8 px-3 py-2 border border-success border-opacity-10">
                                            <i class="bx bx-git-branch me-1"></i>{{ item.dong_ho || 'Chưa liên kết dòng họ' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary radius-8 px-2 py-1">
                                            {{ item.ten_goi }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-success">{{ formatCurrency(item.so_tien) }}</td>
                                    <td class="small">
                                        <div><strong>Từ:</strong> {{ formatDate(item.ngay_bat_dau) }}</div>
                                        <div><strong>Đến:</strong> {{ formatDate(item.ngay_ket_thuc) }}</div>
                                    </td>
                                    <td class="text-center">
                                        <button @click="changeStatus(item.id)" :class="item.trang_thai == 1 ? 'btn-success' : 'btn-danger'" class="btn btn-sm radius-8 w-100">
                                            {{ item.trang_thai == 1 ? 'Hoạt động' : 'Đang Khóa' }}
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-sm btn-outline-primary radius-8" @click="editItem(item)" title="Sửa">
                                                <i class="bx bx-edit-alt m-0"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger radius-8" @click="deleteItem(item.id)" title="Xóa">
                                                <i class="bx bx-trash m-0"></i>
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
            searchQuery: ''
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
    },
    methods: {
        setDefaultDates() {
            const today = new Date();
            const nextYear = new Date();
            nextYear.setFullYear(today.getFullYear() + 1);

            this.formData.ngay_bat_dau = today.toISOString().split('T')[0];
            this.formData.ngay_ket_thuc = nextYear.toISOString().split('T')[0];
        },
        loadPackages() {
            axios.get('http://127.0.0.1:8000/api/goi-dich-vu/get-data', {
                headers: {
                    Authorization: 'Bearer ' + localStorage.getItem('access_token')
                }
            })
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
            axios.get('http://127.0.0.1:8000/api/admin/doi-tac/get-data', {
                headers: {
                    Authorization: 'Bearer ' + localStorage.getItem('access_token')
                }
            })
            .then(res => {
                if (res.data.status) {
                    this.listData = res.data.data;
                }
            })
            .catch(err => console.error(err))
            .finally(() => {
                this.isLoading = false;
            });
        },
        loadUsers() {
            axios.get('http://127.0.0.1:8000/api/nguoi-dung/get-data', {
                headers: {
                    Authorization: 'Bearer ' + localStorage.getItem('access_token')
                }
            })
            .then(res => {
                if (res.data.status) {
                    // Hiển thị tất cả người dùng để dễ quản lý
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

            axios.post(url, payload, {
                headers: {
                    Authorization: 'Bearer ' + localStorage.getItem('access_token')
                }
            })
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
            
            // Nếu không thuộc 3 gói chuẩn thì set là Custom
            if (!['Gói Khởi Tạo', 'Gói Hưng Thịnh', 'Gói Trường Tồn'].includes(item.ten_goi)) {
                this.formData.ten_goi = 'Gói Đối Tác Custom';
                this.customPackageName = item.ten_goi;
            }
        },
        deleteItem(id) {
            if (confirm('Bạn có chắc chắn muốn xóa đối tác này? Mọi quyền lợi đối tác của tài khoản liên kết sẽ bị thu hồi.')) {
                axios.post('http://127.0.0.1:8000/api/admin/doi-tac/delete', { id }, {
                    headers: {
                        Authorization: 'Bearer ' + localStorage.getItem('access_token')
                    }
                })
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
            axios.post('http://127.0.0.1:8000/api/admin/doi-tac/status', { id }, {
                headers: {
                    Authorization: 'Bearer ' + localStorage.getItem('access_token')
                }
            })
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
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
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
.radius-10 {
    border-radius: 10px;
}
.radius-8 {
    border-radius: 8px;
}
.table thead th {
    border-bottom: none;
    font-weight: 600;
}
.bg-primary-subtle {
    background-color: rgba(13, 110, 253, 0.12) !important;
}
.bg-success-subtle {
    background-color: rgba(25, 135, 84, 0.12) !important;
}
</style>
