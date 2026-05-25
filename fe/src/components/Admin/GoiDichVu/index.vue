<template>
    <div class="row">
        <!-- Left Column: Add/Edit Form -->
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="card shadow-sm border-0 radius-10 h-100">
                <div class="card-header bg-white py-3 border-0 border-bottom">
                    <h5 class="mb-0 fw-bold text-uppercase" style="color: #00b4d8;">
                        <i class="bx bx-plus-circle me-1"></i> {{ isEditing ? 'Cập Nhật Gói Dịch Vụ' : 'Tạo Gói Dịch Vụ Mới' }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form @submit.prevent="saveData">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tên Gói Dịch Vụ</label>
                            <input type="text" class="form-control radius-8 border-2 shadow-none" placeholder="VD: Gói Khởi Tạo" v-model="formData.ten_goi" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Giá Cả (VNĐ)</label>
                            <input type="number" class="form-control radius-8 border-2 shadow-none" placeholder="VD: 100000" v-model="formData.gia_ca" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Thời Hạn (Tháng)</label>
                                <input type="number" class="form-control radius-8 border-2 shadow-none" placeholder="VD: 12" v-model="formData.thoi_han" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Giới Hạn Đời (Thế Hệ)</label>
                                <input type="number" class="form-control radius-8 border-2 shadow-none" placeholder="VD: 5 (999 nếu vô hạn)" v-model="formData.max_doi" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Giới Hạn Thành Viên</label>
                                <input type="number" class="form-control radius-8 border-2 shadow-none" placeholder="VD: 100 (99999 nếu vô hạn)" v-model="formData.max_thanh_vien" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Trạng Thái</label>
                                <select class="form-select radius-8 border-2 shadow-none" v-model="formData.trang_thai" required>
                                    <option value="Hoạt động">Hoạt động</option>
                                    <option value="Tạm dừng">Tạm dừng</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mô Tả Gói</label>
                            <textarea rows="4" class="form-control radius-8 border-2 shadow-none" placeholder="Nhập tóm tắt các tính năng của gói..." v-model="formData.mo_ta"></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="button" class="btn btn-light radius-8 px-4" v-if="isEditing" @click="resetForm">Hủy</button>
                            <button type="submit" class="btn text-white radius-8 px-4 fw-bold shadow-sm" style="background-color: #008bf8; border-color: #008bf8;">
                                {{ isEditing ? 'Cập Nhật' : 'Tạo Mới' }}
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
                        <i class="bx bx-list-ul me-1"></i> Danh Sách Gói Dịch Vụ Hệ Thống
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="input-group mb-4 radius-8 overflow-hidden border-2">
                        <input type="text" class="form-control border-0 shadow-none ps-4" placeholder="Tìm kiếm gói dịch vụ..." v-model="searchQuery">
                        <button class="btn btn-success px-5 fw-bold" type="button" style="background-color: #00c853; border-color: #00c853;">Tìm kiếm</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="text-center text-white" style="background-color: #008bf8;">
                                <tr>
                                    <th width="5%" class="py-3">#</th>
                                    <th width="20%" class="py-3">Tên Gói</th>
                                    <th width="15%" class="py-3">Giá Cả</th>
                                    <th width="15%" class="py-3">Thời Hạn</th>
                                    <th width="20%" class="py-3">Giới Hạn Đặc Quyền</th>
                                    <th width="15%" class="py-3">Trạng Thái</th>
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
                                        Chưa có gói dịch vụ nào được cấu hình
                                    </td>
                                </tr>
                                <tr v-for="(item, index) in filteredList" :key="item.id">
                                    <td class="text-center fw-bold">{{ index + 1 }}</td>
                                    <td class="fw-bold text-dark">
                                        {{ item.ten_goi }}
                                        <div class="small text-muted fw-normal mt-1" style="font-size: 11px; white-space: pre-wrap;">{{ item.mo_ta || 'Không có mô tả' }}</div>
                                    </td>
                                    <td class="text-end fw-bold text-success">{{ formatCurrency(item.gia_ca) }}</td>
                                    <td class="text-center">{{ item.thoi_han }} tháng</td>
                                    <td class="small">
                                        <div><strong>Số đời:</strong> {{ item.max_doi >= 999 ? 'Vô hạn' : item.max_doi + ' đời' }}</div>
                                        <div><strong>Thành viên:</strong> {{ item.max_thanh_vien >= 99999 ? 'Vô hạn' : item.max_thanh_vien + ' người' }}</div>
                                    </td>
                                    <td class="text-center">
                                        <button @click="changeStatus(item.id)" :class="item.trang_thai === 'Hoạt động' ? 'btn-success' : 'btn-danger'" class="btn btn-sm radius-8 w-100 fw-bold">
                                            {{ item.trang_thai }}
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
    name: 'GoiDichVuManagement',
    data() {
        return {
            listData: [],
            formData: {
                id: null,
                ten_goi: '',
                gia_ca: 0,
                thoi_han: 12,
                max_doi: 5,
                max_thanh_vien: 100,
                mo_ta: '',
                trang_thai: 'Hoạt động'
            },
            isEditing: false,
            isLoading: false,
            searchQuery: ''
        };
    },
    computed: {
        filteredList() {
            if (!this.searchQuery) return this.listData;
            const q = this.searchQuery.toLowerCase();
            return this.listData.filter(item => 
                (item.ten_goi && item.ten_goi.toLowerCase().includes(q)) ||
                (item.mo_ta && item.mo_ta.toLowerCase().includes(q))
            );
        }
    },
    mounted() {
        this.loadData();
    },
    methods: {
        loadData() {
            this.isLoading = true;
            axios.get('http://127.0.0.1:8000/api/goi-dich-vu/get-data', {
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
        saveData() {
            const url = this.isEditing 
                ? 'http://127.0.0.1:8000/api/goi-dich-vu/update'
                : 'http://127.0.0.1:8000/api/goi-dich-vu/create';
            
            axios.post(url, this.formData, {
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
                toastr.error('Đã có lỗi xảy ra, vui lòng thử lại!');
            });
        },
        editItem(item) {
            this.isEditing = true;
            this.formData = { ...item };
        },
        deleteItem(id) {
            if (confirm('Bạn có chắc chắn muốn xóa gói dịch vụ này? Hành động này có thể ảnh hưởng đến lịch sử đăng ký của đối tác.')) {
                axios.post('http://127.0.0.1:8000/api/goi-dich-vu/delete', { id }, {
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
                    toastr.error('Lỗi khi xóa gói dịch vụ!');
                });
            }
        },
        changeStatus(id) {
            axios.post('http://127.0.0.1:8000/api/goi-dich-vu/status', { id }, {
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
                toastr.error('Lỗi khi thay đổi trạng thái gói!');
            });
        },
        resetForm() {
            this.isEditing = false;
            this.formData = {
                id: null,
                ten_goi: '',
                gia_ca: 0,
                thoi_han: 12,
                max_doi: 5,
                max_thanh_vien: 100,
                mo_ta: '',
                trang_thai: 'Hoạt động'
            };
        },
        formatCurrency(value) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
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
</style>
