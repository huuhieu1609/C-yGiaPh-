<template>
    <div class="row">
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="card shadow-sm border-0 radius-10 h-100">
                <div class="card-header bg-white py-3 border-0 border-bottom">
                    <h5 class="mb-0 fw-bold text-uppercase" style="color: #00b4d8;">
                        <i class="bx bx-calendar-event me-1"></i>
                        {{ isEditing ? 'Cập Nhật Sự Kiện' : 'Thêm Sự Kiện Mới' }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form @submit.prevent="saveData">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tiêu Đề</label>
                            <input type="text" class="form-control radius-8 border-2 shadow-none"
                                placeholder="Nhập tên sự kiện..." v-model="formData.tieu_de" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ngày Tổ Chức</label>
                            <input type="date" class="form-control radius-8 border-2 shadow-none"
                                v-model="formData.ngay_to_chuc" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Địa Điểm</label>
                            <input type="text" class="form-control radius-8 border-2 shadow-none"
                                placeholder="Nhập địa điểm..." v-model="formData.dia_diem" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Loại Sự Kiện</label>
                            <select class="form-select radius-8 border-2 shadow-none" v-model="formData.loai" required>
                                <option disabled value="">Chọn loại sự kiện</option>
                                <option>Gặp mặt</option>
                                <option>Thăm viếng</option>
                                <option>Từ thiện</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nội Dung</label>
                            <textarea rows="4" class="form-control radius-8 border-2 shadow-none"
                                placeholder="Nhập mô tả ngắn..." v-model="formData.noi_dung"></textarea>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="button" class="btn btn-light radius-8 px-4" v-if="isEditing"
                                @click="resetForm">Hủy</button>
                            <button type="submit" class="btn text-white radius-8 px-4 fw-bold shadow-sm"
                                style="background-color: #008bf8; border-color: #008bf8;">
                                {{ isEditing ? 'Cập Nhật' : 'Thêm Mới' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-md-12">
            <div class="card shadow-sm border-0 radius-10 h-100">
                <div
                    class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold text-uppercase" style="color: #333;">
                        <i class="bx bx-list-ul me-1"></i> Danh Sách Sự Kiện
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="input-group mb-4 radius-8 overflow-hidden border-2">
                        <input type="text" class="form-control border-0 shadow-none ps-4"
                            placeholder="Tìm kiếm sự kiện..." v-model="searchQuery" />
                        <button class="btn btn-success px-5 fw-bold" type="button"
                            style="background-color: #00c853; border-color: #00c853;">Tìm kiếm</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="text-center text-white" style="background-color: #008bf8;">
                                <tr>
                                    <th width="5%" class="py-3">#</th>
                                    <th width="30%" class="py-3">Tiêu Đề</th>
                                    <th width="20%" class="py-3">Ngày</th>
                                    <th width="20%" class="py-3">Địa Điểm</th>
                                    <th width="15%" class="py-3">Loại</th>
                                    <th width="15%" class="py-3">Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="isLoading">
                                    <td colspan="6" class="text-center py-5">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-else-if="filteredList.length === 0">
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bx bx-folder-open fs-1 mb-2 d-block"></i>
                                        Chưa có dữ liệu sự kiện
                                    </td>
                                </tr>
                                <tr v-for="(item, index) in filteredList" :key="item.id">
                                    <td class="text-center fw-bold">{{ index + 1 }}</td>
                                    <td>{{ item.tieu_de }}</td>
                                    <td class="text-center">{{ formatDate(item.ngay_to_chuc) }}</td>
                                    <td>{{ item.dia_diem }}</td>
                                    <td>{{ item.loai }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-sm btn-outline-primary radius-8"
                                                @click="editItem(item)" title="Sửa">
                                                <i class="bx bx-edit-alt m-0"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger radius-8"
                                                @click="deleteItem(item.id)" title="Xóa">
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
    name: 'SuKienManagement',
    data() {
        return {
            listData: [],
            isLoading: false,
            searchQuery: '',
            isEditing: false,
            formData: {
                id: null,
                tieu_de: '',
                noi_dung: '',
                ngay_to_chuc: '',
                dia_diem: '',
                loai: ''
            }
        }
    },
    computed: {
        filteredList() {
            if (!this.searchQuery) return this.listData;
            const q = this.searchQuery.toLowerCase();
            return this.listData.filter(item =>
                item.tieu_de.toLowerCase().includes(q) ||
                item.dia_diem.toLowerCase().includes(q) ||
                item.loai.toLowerCase().includes(q)
            );
        }
    },
    mounted() {
        this.loadData();
    },
    methods: {
        getHeaders() {
            const token = localStorage.getItem('access_token');
            return {
                headers: {
                    Authorization: token ? `Bearer ${token}` : ''
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
                    console.error(err);
                    toastr.error('Không thể tải danh sách sự kiện');
                })
                .finally(() => { this.isLoading = false; });
        },
        formatDate(date) {
            if (!date) return '---';
            const d = new Date(date);
            return d.toLocaleDateString('vi-VN');
        },
        saveData() {
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
                    const message = err.response?.data?.message || 'Lỗi khi lưu sự kiện';
                    toastr.error(message);
                });
        },
        editItem(item) {
            this.isEditing = true;
            this.formData = { ...item };
        },
        deleteItem(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa sự kiện này?')) return;
            axios.post('http://127.0.0.1:8000/api/su-kien/delete', { id }, this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        toastr.success(res.data.message);
                        this.loadData();
                    } else {
                        toastr.error(res.data.message);
                    }
                })
                .catch(err => {
                    const message = err.response?.data?.message || 'Lỗi khi xóa sự kiện';
                    toastr.error(message);
                });
        },
        resetForm() {
            this.isEditing = false;
            this.formData = {
                id: null,
                tieu_de: '',
                noi_dung: '',
                ngay_to_chuc: '',
                dia_diem: '',
                loai: ''
            };
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
