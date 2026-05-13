<template>
    <div class="row">
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="card shadow-sm border-0 radius-10">
                <div class="card-header bg-white py-3 border-0 border-bottom">
                    <h5 class="mb-0 fw-bold text-uppercase" style="color: #00b4d8;">
                        <i class="bx bx-user-plus me-1"></i> {{ isEditing ? 'Cập Nhật Người Dùng' : 'Thêm Người Dùng Mới' }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form @submit.prevent="saveData">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Họ Và Tên</label>
                            <input type="text" class="form-control radius-8 border-2 shadow-none" placeholder="Nhập họ tên" v-model="formData.ho_ten" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control radius-8 border-2 shadow-none" placeholder="Nhập email" v-model="formData.email" required>
                        </div>
                        <div class="mb-3" v-if="!isEditing">
                            <label class="form-label fw-semibold">Mật Khẩu</label>
                            <input type="password" class="form-control radius-8 border-2 shadow-none" placeholder="Nhập mật khẩu" v-model="formData.mat_khau" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Số Điện Thoại</label>
                            <input type="text" class="form-control radius-8 border-2 shadow-none" placeholder="Nhập số điện thoại" v-model="formData.so_dien_thoai">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Chức Vụ</label>
                            <select class="form-select radius-8 border-2 shadow-none" v-model="formData.id_chuc_vu">
                                <option :value="null">-- Chọn Chức Vụ --</option>
                                <option v-for="cv in listChucVu" :key="cv.id" :value="cv.id">{{ cv.ten_chuc_vu }}</option>
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

        <div class="col-lg-8 col-md-12">
            <div class="card shadow-sm border-0 radius-10 h-100">
                <div class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold text-uppercase" style="color: #333;">
                        <i class="bx bx-group me-1"></i> Danh Sách Người Dùng
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="text-center text-white" style="background-color: #008bf8;">
                                <tr>
                                    <th width="5%" class="py-3">#</th>
                                    <th width="25%" class="py-3">Họ Tên</th>
                                    <th width="25%" class="py-3">Email</th>
                                    <th width="20%" class="py-3">Chức Vụ</th>
                                    <th width="10%" class="py-3">Trạng Thái</th>
                                    <th width="15%" class="py-3">Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in listData" :key="item.id">
                                    <td class="text-center fw-bold">{{ index + 1 }}</td>
                                    <td class="fw-semibold">{{ item.ho_ten }}</td>
                                    <td>{{ item.email }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-info text-white radius-10 px-3">{{ getTenChucVu(item.id_chuc_vu) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <button @click="changeStatus(item.id)" :class="item.trang_thai == 'Hoạt động' ? 'btn-success' : 'btn-danger'" class="btn btn-sm radius-8 w-100">
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
    name: 'NguoiDungManagement',
    data() {
        return {
            listData: [],
            listChucVu: [],
            formData: {
                id: null,
                ho_ten: '',
                email: '',
                mat_khau: '',
                so_dien_thoai: '',
                id_chuc_vu: null,
                trang_thai: 'Hoạt động'
            },
            isEditing: false
        }
    },
    mounted() {
        this.loadData();
        this.loadChucVu();
    },
    methods: {
        loadData() {
            axios.get('http://127.0.0.1:8000/api/nguoi-dung/get-data')
                .then(res => {
                    if (res.data.status) {
                        this.listData = res.data.data;
                    }
                });
        },
        loadChucVu() {
            axios.get('http://127.0.0.1:8000/api/chuc-vu/get-data')
                .then(res => {
                    if (res.data.status) {
                        this.listChucVu = res.data.data;
                    }
                });
        },
        getTenChucVu(id) {
            const cv = this.listChucVu.find(c => c.id === id);
            return cv ? cv.ten_chuc_vu : 'Chưa gán';
        },
        saveData() {
            const url = this.isEditing 
                ? 'http://127.0.0.1:8000/api/nguoi-dung/update'
                : 'http://127.0.0.1:8000/api/nguoi-dung/create';
            
            axios.post(url, this.formData)
                .then(res => {
                    if (res.data.status) {
                        toastr.success(res.data.message);
                        this.loadData();
                        this.resetForm();
                    } else {
                        toastr.error(res.data.message);
                    }
                });
        },
        editItem(item) {
            this.isEditing = true;
            this.formData = { ...item };
            this.formData.mat_khau = ''; // Don't show password
        },
        deleteItem(id) {
            if (confirm('Bạn có chắc chắn muốn xóa?')) {
                axios.post('http://127.0.0.1:8000/api/nguoi-dung/delete', { id })
                    .then(res => {
                        if (res.data.status) {
                            toastr.success(res.data.message);
                            this.loadData();
                        }
                    });
            }
        },
        changeStatus(id) {
            axios.post('http://127.0.0.1:8000/api/nguoi-dung/status', { id })
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
                trang_thai: 'Hoạt động'
            };
        }
    }
}
</script>

<style scoped>
.radius-10 { border-radius: 10px; }
.radius-8 { border-radius: 8px; }
</style>
